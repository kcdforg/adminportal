import { Component, inject, OnInit, signal } from '@angular/core';
import { DatePipe } from '@angular/common';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { forkJoin } from 'rxjs';
import {
  IonContent,
  IonIcon,
  IonSpinner,
} from '@ionic/angular/standalone';
import { Course, CourseBatch, MemberApiService } from '../core/member-auth.service';

@Component({
  selector: 'app-course-detail',
  templateUrl: './course-detail.page.html',
  styleUrl: './course-detail.page.scss',
  imports: [DatePipe, RouterLink, IonContent, IonIcon, IonSpinner],
})
export class CourseDetailPage implements OnInit {
  private readonly route = inject(ActivatedRoute);
  private readonly api = inject(MemberApiService);
  readonly course = signal<Course | null>(null);
  readonly batches = signal<CourseBatch[]>([]);
  readonly loading = signal(true);
  readonly error = signal('');

  ngOnInit(): void {
    const courseId = Number(this.route.snapshot.paramMap.get('id'));
    if (!Number.isInteger(courseId) || courseId < 1) {
      this.error.set('This program could not be identified.');
      this.loading.set(false);
      return;
    }

    forkJoin({
      course: this.api.getCourse(courseId),
      batches: this.api.getBatches(courseId),
    }).subscribe({
      next: ({ course, batches }) => {
        this.course.set(course);
        this.batches.set(batches);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('Program details could not be loaded. Please try again later.');
        this.loading.set(false);
      },
    });
  }
}
