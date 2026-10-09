import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ToastMessage, ToastService, ToastVariant } from './toast.service';

@Component({
  selector: 'app-toast-region',
  standalone: true,
  imports: [CommonModule],
  template: `
    <section
      aria-label="Notifications"
      class="pointer-events-none fixed inset-x-0 top-4 z-[1000] mx-auto flex max-w-lg flex-col gap-3 px-4 sm:inset-x-auto sm:right-4 sm:w-full sm:px-0">
      <article
        *ngFor="let toast of toastService.toasts()"
        [attr.role]="toast.variant === 'error' ? 'alert' : 'status'"
        aria-atomic="true"
        class="pointer-events-auto flex items-start gap-3 rounded-xl border bg-white p-4 shadow-lg shadow-gray-900/10 dark:bg-gray-900"
        [ngClass]="containerClasses(toast.variant)">
        <span
          aria-hidden="true"
          class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full"
          [ngClass]="indicatorClasses(toast.variant)">
        </span>
        <p class="min-w-0 flex-1 text-sm leading-5 text-gray-800 dark:text-gray-100">
          {{ toast.message }}
        </p>
        <button
          *ngIf="toast.actionLabel"
          type="button"
          class="shrink-0 rounded-md px-2 py-1 text-sm font-semibold text-indigo-700 hover:bg-indigo-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:text-indigo-300 dark:hover:bg-indigo-950"
          (click)="toastService.invokeAction(toast)">
          {{ toast.actionLabel }}
        </button>
        <button
          type="button"
          aria-label="Dismiss notification"
          class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
          (click)="toastService.dismiss(toast.id)">
          <svg aria-hidden="true" viewBox="0 0 20 20" fill="none" class="h-4 w-4">
            <path d="m5 5 10 10M15 5 5 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
          </svg>
        </button>
      </article>
    </section>
  `
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
