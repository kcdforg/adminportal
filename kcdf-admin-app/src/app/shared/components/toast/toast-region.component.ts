import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ToastMessage, ToastService, ToastVariant } from './toast.service';

@Component({
  selector: 'app-toast-region',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './toast-region.component.html',
  styleUrl: './toast-region.component.scss'
})
export class ToastRegionComponent {
  readonly toastService = inject(ToastService);

  containerClasses(variant: ToastVariant): string {
    switch (variant) {
      case 'success':
        return 'border-emerald-200 dark:border-emerald-900';
      case 'error':
        return 'border-rose-200 dark:border-rose-900';
      case 'warning':
        return 'border-amber-200 dark:border-amber-900';
      default:
        return 'border-gray-200 dark:border-gray-700';
    }
  }

  indicatorClasses(variant: ToastVariant): string {
    switch (variant) {
      case 'success':
        return 'bg-emerald-500';
      case 'error':
        return 'bg-rose-500';
      case 'warning':
        return 'bg-amber-500';
      default:
        return 'bg-indigo-500';
    }
  }
}
