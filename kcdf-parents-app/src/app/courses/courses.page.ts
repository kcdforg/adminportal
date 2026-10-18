import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';
import {
  IonButton,
  IonCard,
  IonCardContent,
  IonContent,
  IonIcon,
} from '@ionic/angular/standalone';

interface LearningCourse {
  id: number;
  title: string;
  category: string;
  description: string;
  duration: string;
  schedule: string;
  level: string;
  color: string;
}

@Component({
  selector: 'app-courses',
  templateUrl: './courses.page.html',
  styleUrl: './courses.page.scss',
  imports: [RouterLink, IonButton, IonCard, IonCardContent, IonContent, IonIcon],
})
export class CoursesPage {
  readonly courses: LearningCourse[] = [
    {
      id: 1,
      title: 'Positive Parenting',
      category: 'Family wellbeing',
      description: 'Practical, caring ways to build trust, confidence and connection at every age.',
      duration: '4 weeks',
      schedule: 'Saturday mornings',
      level: 'For every parent',
      color: 'green',
    },
    {
      id: 2,
      title: 'Learning Through Play',
      category: 'Early learning',
      description: 'Simple playful activities that make everyday moments a chance to learn.',
      duration: '3 weeks',
      schedule: 'Wednesday afternoons',
      level: 'Ages 3–8',
      color: 'gold',
    },
    {
      id: 3,
      title: 'Healthy Family Habits',
      category: 'Health & wellbeing',
      description: 'Small, sustainable habits for happier, healthier days as a family.',
      duration: '2 weeks',
      schedule: 'Self-paced',
      level: 'For the whole family',
      color: 'blue',
    },
  ];
}
