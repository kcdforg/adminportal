import { Component, OnInit, inject, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import {
  IonCard,
  IonCardContent,
  IonContent,
  IonIcon,
  IonSpinner,
} from '@ionic/angular/standalone';
import { Course, MemberApiService } from '../core/member-auth.service';

@Component({
  selector: 'app-courses',
  templateUrl: './courses.page.html',
  styleUrl: './courses.page.scss',
  imports: [RouterLink, IonCard, IonCardContent, IonContent, IonIcon, IonSpinner],
})
export class CoursesPage implements OnInit {
  private readonly api = inject(MemberApiService);
  readonly courses = signal<Course[]>([]);
  readonly loading = signal(true);
  readonly error = signal('');

  ngOnInit(): void {
    this.api.getCourses().subscribe({
      next: (courses) => {
        this.courses.set(courses.filter((course) => course.status === undefined || course.status === 'active'));
        this.loading.set(false);
      },
      error: () => {
        this.error.set('Programs could not be loaded. Please try again later.');
        this.loading.set(false);
      },
    });
  }
}
