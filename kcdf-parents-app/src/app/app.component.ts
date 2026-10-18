import { Component } from '@angular/core';
import { IonApp, IonRouterOutlet } from '@ionic/angular/standalone';
import { addIcons } from 'ionicons';
import {
  arrowForwardOutline,
  calendarOutline,
  chatbubbleEllipsesOutline,
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
      arrowForwardOutline,
      calendarOutline,
      chatbubbleEllipsesOutline,
      heartOutline,
      homeOutline,
      logOutOutline,
      menuOutline,
      peopleOutline,
      personCircleOutline,
      personOutline,
      schoolOutline,
      timeOutline,
    });
  }
}
