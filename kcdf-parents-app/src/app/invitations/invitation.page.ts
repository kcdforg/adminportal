import { Component, OnInit, inject, signal } from '@angular/core';
import { DatePipe } from '@angular/common';
import { ReactiveFormsModule, FormControl, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import {
  IonButton,
  IonContent,
  IonInput,
  IonItem,
  IonLabel,
  IonSpinner,
} from '@ionic/angular/standalone';
import { InvitationDetails, MemberApiService, MemberAuthService } from '../core/member-auth.service';

@Component({
  selector: 'app-invitation',
  templateUrl: './invitation.page.html',
  styleUrl: './invitation.page.scss',
  imports: [DatePipe, RouterLink, ReactiveFormsModule, IonButton, IonContent, IonInput, IonItem, IonLabel, IonSpinner],
})
export class InvitationPage implements OnInit {
  private readonly route = inject(ActivatedRoute);
  private readonly router = inject(Router);
  private readonly api = inject(MemberApiService);
  private readonly auth = inject(MemberAuthService);
  readonly invitation = signal<InvitationDetails | null>(null);
  readonly loading = signal(true);
  readonly submitting = signal(false);
  readonly error = signal('');
  readonly code = this.route.snapshot.paramMap.get('code') ?? '';
  readonly form = new FormGroup({
    first_name: new FormControl('', { nonNullable: true, validators: [Validators.required, Validators.maxLength(100)] }),
    last_name: new FormControl('', { nonNullable: true, validators: [Validators.required, Validators.maxLength(100)] }),
    email: new FormControl('', { nonNullable: true, validators: [Validators.required, Validators.email] }),
    mobile: new FormControl('', { nonNullable: true, validators: [Validators.required, Validators.pattern(/^\d{10,}$/)] }),
    password: new FormControl('', { nonNullable: true, validators: [Validators.required, Validators.minLength(8)] }),
  });

  ngOnInit(): void {
    if (!this.code) {
      this.error.set('This invitation link is invalid.');
      this.loading.set(false);
      return;
    }
    this.api.getInvitation(this.code).subscribe({
      next: (invitation) => {
        this.invitation.set(invitation);
        this.loading.set(false);
      },
      error: (error: unknown) => {
        this.error.set(this.errorMessage(error, 'This invitation is unavailable or has expired.'));
        this.loading.set(false);
      },
    });
  }

  accept(): void {
    if (this.form.invalid || this.submitting()) {
      this.form.markAllAsTouched();
      return;
    }
    this.submitting.set(true);
    this.error.set('');
    this.api.acceptInvitation(this.code, this.form.getRawValue()).subscribe({
      next: (session) => {
        this.auth.session.saveSession(session);
        void this.router.navigateByUrl('/home');
      },
      error: (error: unknown) => {
        this.error.set(this.errorMessage(error, 'The invitation could not be accepted. Check your details and try again.'));
        this.submitting.set(false);
      },
    });
  }

  private errorMessage(error: unknown, fallback: string): string {
    if (typeof error === 'object' && error !== null && 'error' in error) {
      const response = error.error;
      if (typeof response === 'object' && response !== null && 'error' in response) {
        const detail = response.error;
        if (typeof detail === 'object' && detail !== null && 'message' in detail
          && typeof detail.message === 'string') {
          return detail.message;
        }
      }
    }
    return fallback;
  }
}
