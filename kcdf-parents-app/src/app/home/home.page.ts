import { Component, OnInit, inject, signal } from '@angular/core';
import { DatePipe } from '@angular/common';
import { RouterLink } from '@angular/router';
import {
  IonButton,
  IonCard,
  IonCardContent,
  IonContent,
  IonIcon,
} from '@ionic/angular/standalone';
import { MemberApiService, MemberPost, MemberSessionStore } from '../core/member-auth.service';

@Component({
  selector: 'app-home',
  templateUrl: 'home.page.html',
  styleUrls: ['home.page.scss'],
  imports: [DatePipe, RouterLink, IonButton, IonCard, IonCardContent, IonContent, IonIcon],
})
export class HomePage implements OnInit {
  private readonly api = inject(MemberApiService);
  readonly session = inject(MemberSessionStore);
  readonly posts = signal<MemberPost[]>([]);
  readonly error = signal('');

  ngOnInit(): void {
    this.api.getMemberPosts().subscribe({
      next: (posts) => this.posts.set(posts.slice(0, 3)),
      error: () => this.error.set('Recent posts could not be loaded. Please try again later.'),
    });
  }
}
