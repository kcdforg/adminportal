import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';
import {
  IonCard,
  IonCardContent,
  IonContent,
  IonIcon,
} from '@ionic/angular/standalone';
import { SAMPLE_GROUPS } from '../core/sample-content';

@Component({
  selector: 'app-groups',
  templateUrl: './groups.page.html',
  styleUrl: './groups.page.scss',
  imports: [RouterLink, IonCard, IonCardContent, IonContent, IonIcon],
})
export class GroupsPage {
  readonly groups = SAMPLE_GROUPS;
}
