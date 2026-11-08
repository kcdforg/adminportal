import { Component, OnInit, inject, signal } from '@angular/core';
import { DatePipe } from '@angular/common';
import { RouterLink } from '@angular/router';
import {
  IonButton,
  IonCard,
  IonCardContent,
  IonContent,
  IonSpinner,
} from '@ionic/angular/standalone';
import { MemberApiService, MemberNotification } from '../core/member-auth.service';

@Component({
  selector: 'app-notifications',
  templateUrl: './notifications.page.html',
  styleUrl: './notifications.page.scss',
  imports: [DatePipe, RouterLink, IonButton, IonCard, IonCardContent, IonContent, IonSpinner],
})
export class NotificationsPage implements OnInit {
  private readonly api = inject(MemberApiService);
  readonly notifications = signal<MemberNotification[]>([]);
  readonly loading = signal(true);
  readonly error = signal('');

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.loading.set(true);
    this.error.set('');
    this.api.getNotifications().subscribe({
      next: (notifications) => {
        this.notifications.set(notifications);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('Notifications could not be loaded. Please try again later.');
        this.loading.set(false);
      },
    });
  }

  markRead(notification: MemberNotification): void {
    this.api.markNotificationRead(notification.id).subscribe({
      next: (updated) => {
        this.notifications.update((items) => items.map((item) => item.id === updated.id ? updated : item));
      },
      error: () => {
        this.error.set('This notification could not be marked as read. Please try again.');
      },
    });
  }
}
