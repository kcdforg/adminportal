import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { ToastRegionComponent } from './shared/components/toast/toast-region.component';
import { TailwindDialogHostComponent } from './shared/components/modal/tailwind-dialog-host.component';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, ToastRegionComponent, TailwindDialogHostComponent],
  template: `
    <router-outlet></router-outlet>
    <app-toast-region></app-toast-region>
    <app-tailwind-dialog-host></app-tailwind-dialog-host>
  `
})
export class App {}
