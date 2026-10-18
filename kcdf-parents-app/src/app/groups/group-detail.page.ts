import { Component, inject } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import {
  IonContent,
  IonIcon,
  IonLabel,
  IonSegment,
  IonSegmentButton,
} from '@ionic/angular/standalone';
import { SAMPLE_GROUPS } from '../core/sample-content';

@Component({
  selector: 'app-group-detail',
  templateUrl: './group-detail.page.html',
  styleUrl: './group-detail.page.scss',
  imports: [RouterLink, IonContent, IonIcon, IonLabel, IonSegment, IonSegmentButton],
})
export class GroupDetailPage {
  private readonly route = inject(ActivatedRoute);
  readonly group = SAMPLE_GROUPS.find(
    (item) => item.id === Number(this.route.snapshot.paramMap.get('id')),
  );
  activeSection = 'members';

  readonly members = [
    { name: 'Anita Raj', role: 'Group facilitator' },
    { name: 'Priya Menon', role: 'Parent member' },
    { name: 'Ravi Kumar', role: 'Parent member' },
  ];

  readonly announcements = [
    { title: 'Welcome to this month’s group meet-up', date: '12 Oct 2026', body: 'Join us for a relaxed morning of conversation and family activities.' },
    { title: 'Share your reading recommendations', date: '05 Oct 2026', body: 'Post a favourite story or book that your family has enjoyed.' },
  ];

  readonly discussions = [
    { title: 'What has helped your child settle into a new routine?', author: 'Priya Menon', replies: 6, date: 'Today' },
    { title: 'Ideas for a fun family weekend', author: 'Ravi Kumar', replies: 3, date: 'Yesterday' },
  ];

  setSection(section: string | number | undefined): void {
    if (typeof section === 'string') {
      this.activeSection = section;
    }
  }
}
