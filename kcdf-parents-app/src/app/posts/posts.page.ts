import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';
import {
  IonCard,
  IonCardContent,
  IonContent,
  IonIcon,
  IonLabel,
} from '@ionic/angular/standalone';
import { SAMPLE_POSTS } from '../core/sample-content';

@Component({
  selector: 'app-posts',
  templateUrl: './posts.page.html',
  styleUrl: './posts.page.scss',
  imports: [RouterLink, IonCard, IonCardContent, IonContent, IonIcon, IonLabel],
})
export class PostsPage {
  readonly posts = SAMPLE_POSTS;
}
