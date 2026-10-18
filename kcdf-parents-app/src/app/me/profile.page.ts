import { Component, inject, OnInit, signal } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import {
  IonButton,
  IonCard,
  IonCardContent,
  IonContent,
  IonItem,
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
    IonLabel,
    IonSpinner,
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

  ngOnInit(): void {
    const profileId = this.session.profile()?.profile_id;
    if (!profileId) {
      this.error.set('Your member profile could not be identified. Please sign in again.');
      this.loading.set(false);
      return;
    }

    this.api.getProfile(profileId).subscribe({
      next: (profile) => this.profile.set(profile),
      error: () => {
        this.error.set('Your profile could not be loaded. Please try again later.');
        this.loading.set(false);
      },
      complete: () => this.loading.set(false),
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
