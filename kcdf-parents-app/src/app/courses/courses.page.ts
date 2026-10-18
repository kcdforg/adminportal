import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';
import {
  IonCard,
  IonCardContent,
  IonContent,
  IonIcon,
} from '@ionic/angular/standalone';
import { SAMPLE_COURSES } from '../core/sample-content';

@Component({
  selector: 'app-courses',
  templateUrl: './courses.page.html',
  styleUrl: './courses.page.scss',
  imports: [RouterLink, IonCard, IonCardContent, IonContent, IonIcon],
})
export class CoursesPage {
  readonly courses = SAMPLE_COURSES;
}
