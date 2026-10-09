import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-loading-overlay',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div
      *ngIf="loading"
      class="fixed inset-x-0 top-0 z-[9999] h-1 overflow-hidden bg-indigo-100 dark:bg-indigo-950"
      role="progressbar"
      aria-label="Loading">
      <span class="block h-full w-1/3 animate-[loading-slide_1.4s_ease-in-out_infinite] bg-indigo-600"></span>
    </div>
  `,
  styles: [`
    @keyframes loading-slide {
      from { transform: translateX(-100%); }
      to { transform: translateX(300%); }
    }
  `]
})
export class LoadingOverlayComponent {
  @Input() loading = false;
}
