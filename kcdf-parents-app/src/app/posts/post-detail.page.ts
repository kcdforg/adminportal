import { Component, inject } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import {
  IonContent,
  IonIcon,
  IonLabel,
} from '@ionic/angular/standalone';
import { SAMPLE_POSTS } from '../core/sample-content';

@Component({
  selector: 'app-post-detail',
  templateUrl: './post-detail.page.html',
  styleUrl: './post-detail.page.scss',
  imports: [RouterLink, IonContent, IonIcon, IonLabel],
})
export class PostDetailPage {
  private readonly route = inject(ActivatedRoute);
  readonly post = SAMPLE_POSTS.find(
    (item) => item.id === Number(this.route.snapshot.paramMap.get('id')),
  );
}
