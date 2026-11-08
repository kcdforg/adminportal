import { Component, inject, OnInit, signal } from '@angular/core';
import { DatePipe } from '@angular/common';
import { ActivatedRoute, RouterLink } from '@angular/router';
import {
  IonContent,
  IonIcon,
  IonSpinner,
} from '@ionic/angular/standalone';
import { MemberApiService, MemberPost } from '../core/member-auth.service';

@Component({
  selector: 'app-post-detail',
  templateUrl: './post-detail.page.html',
  styleUrl: './post-detail.page.scss',
  imports: [DatePipe, RouterLink, IonContent, IonIcon, IonSpinner],
})
export class PostDetailPage implements OnInit {
  private readonly route = inject(ActivatedRoute);
  private readonly api = inject(MemberApiService);
  readonly post = signal<MemberPost | null>(null);
  readonly loading = signal(true);
  readonly error = signal('');

  ngOnInit(): void {
    const postId = Number(this.route.snapshot.paramMap.get('id'));
    this.api.getMemberPosts().subscribe({
      next: (posts) => {
        this.post.set(posts.find((item) => item.id === postId) ?? null);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('This post could not be loaded. Please try again later.');
        this.loading.set(false);
      },
    });
  }
}
