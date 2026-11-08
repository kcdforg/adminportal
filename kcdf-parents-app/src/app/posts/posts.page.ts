import { Component, OnInit, inject, signal } from '@angular/core';
import { DatePipe } from '@angular/common';
import { RouterLink } from '@angular/router';
import {
  IonCard,
  IonCardContent,
  IonContent,
  IonIcon,
  IonSpinner,
} from '@ionic/angular/standalone';
import { MemberApiService, MemberPost } from '../core/member-auth.service';

@Component({
  selector: 'app-posts',
  templateUrl: './posts.page.html',
  styleUrl: './posts.page.scss',
  imports: [DatePipe, RouterLink, IonCard, IonCardContent, IonContent, IonIcon, IonSpinner],
})
export class PostsPage implements OnInit {
  private readonly api = inject(MemberApiService);
  readonly posts = signal<MemberPost[]>([]);
  readonly loading = signal(true);
  readonly error = signal('');

  ngOnInit(): void {
    this.api.getMemberPosts().subscribe({
      next: (posts) => {
        this.posts.set(posts);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('Posts could not be loaded. Please try again later.');
        this.loading.set(false);
      },
    });
  }
}
