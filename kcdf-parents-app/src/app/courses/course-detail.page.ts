import { Component, inject } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import {
  IonContent,
  IonIcon,
  IonLabel,
  IonSegment,
  IonSegmentButton,
} from '@ionic/angular/standalone';
import { SAMPLE_COURSES } from '../core/sample-content';

@Component({
  selector: 'app-course-detail',
  templateUrl: './course-detail.page.html',
  styleUrl: './course-detail.page.scss',
  imports: [RouterLink, IonContent, IonIcon, IonLabel, IonSegment, IonSegmentButton],
})
export class CourseDetailPage {
  private readonly route = inject(ActivatedRoute);
  readonly course = SAMPLE_COURSES.find(
    (item) => item.id === Number(this.route.snapshot.paramMap.get('id')),
  );
  activeSection = 'classes';

  readonly classes = [
    { title: 'Building connection at home', date: '10 Oct 2026', status: 'Conducted' },
    { title: 'Positive communication', date: '03 Oct 2026', status: 'Conducted' },
    { title: 'Understanding behaviour', date: '26 Sep 2026', status: 'Conducted' },
  ];

  readonly students = [
    { name: 'Anita Raj', status: 'Enrolled' },
    { name: 'Priya Menon', status: 'Enrolled' },
    { name: 'Ravi Kumar', status: 'Enrolled' },
  ];

  setSection(section: string | number | undefined): void {
    if (typeof section === 'string') {
      this.activeSection = section;
    }
  }
}
