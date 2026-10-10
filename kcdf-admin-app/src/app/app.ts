import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { ToastRegionComponent } from './shared/components/toast/toast-region.component';
import { TailwindDialogHostComponent } from './shared/components/modal/tailwind-dialog-host.component';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, ToastRegionComponent, TailwindDialogHostComponent],
  templateUrl: './root.component.html',
  styleUrl: './root.component.scss'
})
export class App {}
