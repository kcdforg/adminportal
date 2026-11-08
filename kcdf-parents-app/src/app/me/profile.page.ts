import { Component, inject, OnInit, signal } from '@angular/core';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import {
  IonButton,
  IonCard,
  IonCardContent,
  IonContent,
  IonItem,
  IonInput,
  IonLabel,
  IonSpinner,
} from '@ionic/angular/standalone';
import { MemberApiService, MemberAuthService, MemberProfile } from '../core/member-auth.service';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.page.html',
  styleUrl: './profile.page.scss',
  imports: [
    RouterLink,
    IonButton,
    IonCard,
    IonCardContent,
    IonContent,
    IonItem,
    IonInput,
    IonLabel,
    IonSpinner,
    ReactiveFormsModule,
  ],
})
export class ProfilePage implements OnInit {
  private readonly api = inject(MemberApiService);
  private readonly auth = inject(MemberAuthService);
  private readonly router = inject(Router);
  readonly session = this.auth.session;
  readonly profile = signal<MemberProfile | null>(null);
  readonly loading = signal(true);
  readonly error = signal('');
  readonly editing = signal(false);
  readonly saving = signal(false);
  readonly saveMessage = signal('');
  readonly editError = signal('');
  readonly editForm = new FormGroup({
    email: new FormControl('', [Validators.email, Validators.maxLength(255)]),
    mobile: new FormControl('', [Validators.pattern(/^\d{10,20}$/)]),
  });

  ngOnInit(): void {
    const profileId = this.session.profile()?.profile_id;
    if (!profileId) {
      this.error.set('Your member profile could not be identified. Please sign in again.');
      this.loading.set(false);
      return;
    }

    this.api.getProfile(profileId).subscribe({
      next: (profile) => {
        this.profile.set(profile);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('Your profile could not be loaded. Please try again later.');
        this.loading.set(false);
      },
      complete: () => this.loading.set(false),
    });
  }

  startEdit(member: MemberProfile): void {
    this.editForm.setValue({
      email: member.email ?? '',
      mobile: member.mobile ?? '',
    });
    this.editing.set(true);
    this.saveMessage.set('');
    this.editError.set('');
  }

  cancelEdit(): void {
    this.editing.set(false);
    this.editError.set('');
  }

  saveProfile(): void {
    const profileId = this.session.profile()?.profile_id;
    if (!profileId || this.editForm.invalid || this.saving()) {
      this.editForm.markAllAsTouched();
      return;
    }

    this.saving.set(true);
    this.editError.set('');
    this.saveMessage.set('');
    const values = this.editForm.getRawValue();
    this.api.updateProfile(profileId, {
      email: values.email?.trim() || null,
      mobile: values.mobile?.trim() || null,
    }).subscribe({
      next: (profile) => {
        this.profile.set(profile);
        this.editing.set(false);
        this.saving.set(false);
        this.saveMessage.set('Your profile was updated.');
      },
      error: () => {
        this.editError.set('Your changes could not be saved. Check the details and try again.');
        this.saving.set(false);
      },
    });
  }

  logout(): void {
    this.auth.logout().subscribe({
      error: (error: unknown) => {
        console.error('The server logout request failed; ending the local member session.', error);
      },
    });
    this.finishLogout();
  }

  private finishLogout(): void {
    this.auth.session.clearSession();
    void this.router.navigateByUrl('/login');
  }
}
