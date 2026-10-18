import { Component, inject } from '@angular/core';
import { RouterLink } from '@angular/router';
import {
  IonButton,
  IonCard,
  IonCardContent,
  IonContent,
  IonIcon,
  IonLabel,
} from '@ionic/angular/standalone';
import { MemberSessionStore } from '../core/member-auth.service';
import { SAMPLE_POSTS } from '../core/sample-content';

@Component({
  selector: 'app-home',
  templateUrl: 'home.page.html',
  styleUrls: ['home.page.scss'],
  imports: [RouterLink, IonButton, IonCard, IonCardContent, IonContent, IonIcon, IonLabel],
})
export class HomePage {
  readonly session = inject(MemberSessionStore);
  readonly posts = SAMPLE_POSTS;
}
