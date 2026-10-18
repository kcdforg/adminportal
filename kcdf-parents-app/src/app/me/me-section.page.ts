import { Component } from '@angular/core';
import { IonRouterOutlet } from '@ionic/angular/standalone';

@Component({
  selector: 'app-me-section',
  template: '<ion-router-outlet></ion-router-outlet>',
  imports: [IonRouterOutlet],
})
export class MeSectionPage {}
