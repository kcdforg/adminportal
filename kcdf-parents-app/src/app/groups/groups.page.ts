import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';
import {
  IonButton,
  IonCard,
  IonCardContent,
  IonContent,
  IonIcon,
} from '@ionic/angular/standalone';

interface CommunityGroup {
  id: number;
  name: string;
  description: string;
  category: string;
  members: number;
  meeting: string;
  icon: string;
  color: string;
}

@Component({
  selector: 'app-groups',
  templateUrl: './groups.page.html',
  styleUrl: './groups.page.scss',
  imports: [RouterLink, IonButton, IonCard, IonCardContent, IonContent, IonIcon],
})
export class GroupsPage {
  readonly groups: CommunityGroup[] = [
    {
      id: 1,
      name: 'Growing Together',
      description: 'A welcoming circle for parents to share ideas, celebrate milestones and support one another.',
      category: 'Parent circle',
      members: 28,
      meeting: 'Every 2nd Saturday',
      icon: 'people-outline',
      color: 'sage',
    },
    {
      id: 2,
      name: 'Little Readers Club',
      description: 'Stories, playful activities and a love of reading for children and their families.',
      category: 'Learning',
      members: 19,
      meeting: 'Every Wednesday',
      icon: 'school-outline',
      color: 'peach',
    },
    {
      id: 3,
      name: 'Active Families',
      description: 'Make movement part of family life with friendly games, walks and weekend meetups.',
      category: 'Wellbeing',
      members: 34,
      meeting: 'Weekend meetups',
      icon: 'heart-outline',
      color: 'blue',
    },
  ];
}
