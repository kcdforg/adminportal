import { Component } from '@angular/core';
import { IonApp, IonRouterOutlet } from '@ionic/angular/standalone';
import { addIcons } from 'ionicons';
import {
  arrowBackOutline,
  arrowForwardOutline,
  calendarOutline,
  chatbubbleEllipsesOutline,
  chevronForwardOutline,
  heartOutline,
  homeOutline,
  logOutOutline,
  menuOutline,
  peopleOutline,
  personCircleOutline,
  personOutline,
  schoolOutline,
  timeOutline,
} from 'ionicons/icons';

@Component({
  selector: 'app-root',
  templateUrl: 'app.component.html',
  imports: [IonApp, IonRouterOutlet],
})
export class AppComponent {
  constructor() {
    addIcons({
      'arrow-back-outline': arrowBackOutline,
      'arrow-forward-outline': arrowForwardOutline,
      'calendar-outline': calendarOutline,
      'chatbubble-ellipses-outline': chatbubbleEllipsesOutline,
      'chevron-forward-outline': chevronForwardOutline,
      'heart-outline': heartOutline,
      'home-outline': homeOutline,
      'log-out-outline': logOutOutline,
      'menu-outline': menuOutline,
      'people-outline': peopleOutline,
      'person-circle-outline': personCircleOutline,
      'person-outline': personOutline,
      'school-outline': schoolOutline,
      'time-outline': timeOutline,
    });
  }
}
