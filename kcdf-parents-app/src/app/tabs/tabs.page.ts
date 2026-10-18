import { Component, inject } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import {
  IonButton,
  IonButtons,
  IonContent,
  IonHeader,
  IonIcon,
  IonItem,
  IonLabel,
  IonList,
  IonMenu,
  IonMenuButton,
  IonMenuToggle,
  IonPopover,
  IonRouterOutlet,
  IonTabBar,
  IonTabButton,
  IonTabs,
  IonToolbar,
} from '@ionic/angular/standalone';
import { MemberAuthService } from '../core/member-auth.service';

@Component({
  selector: 'app-tabs',
  templateUrl: './tabs.page.html',
  styleUrl: './tabs.page.scss',
  imports: [
    RouterLink,
    IonButton,
    IonButtons,
    IonContent,
    IonHeader,
    IonIcon,
    IonItem,
    IonLabel,
    IonList,
    IonMenu,
    IonMenuButton,
    IonMenuToggle,
    IonPopover,
    IonRouterOutlet,
    IonTabBar,
    IonTabButton,
    IonTabs,
    IonToolbar,
  ],
})
export class TabsPage {
  private readonly auth = inject(MemberAuthService);
  private readonly router = inject(Router);

  readonly profile = this.auth.session.profile;

  logout(): void {
    this.auth.logout().subscribe({
      error: (error: unknown) => {
        console.error('The server logout request failed; ending the local member session.', error);
      },
    });
    this.auth.session.clearSession();
    void this.router.navigateByUrl('/login');
  }
}
