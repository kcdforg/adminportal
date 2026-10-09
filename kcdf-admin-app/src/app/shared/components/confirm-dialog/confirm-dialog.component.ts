import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  TAILWIND_DIALOG_DATA,
  TAILWIND_DIALOG_REF,
  TailwindDialogRef,
} from '../modal/tailwind-dialog.service';

export interface ConfirmDialogData {
  title: string;
  message: string;
  confirmLabel?: string;
  cancelLabel?: string;
  danger?: boolean;
}

@Component({
  selector: 'app-confirm-dialog',
  standalone: true,
  imports: [CommonModule],
  template: `
    <h2 class="mb-1 text-xl font-semibold tracking-tight text-gray-900 dark:text-white">
      {{ data.title }}
    </h2>
    <div class="text-sm leading-6 text-gray-600 dark:text-gray-300">
      <p class="m-0">{{ data.message }}</p>
    </div>
    <div class="mt-6 flex justify-end gap-2">
      <button type="button" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:text-gray-200 dark:hover:bg-gray-800" (click)="dialogRef.close()">
        {{ data.cancelLabel ?? 'Cancel' }}
      </button>
      <button type="button" [class]="data.danger ? 'rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600' : 'rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600'" (click)="confirm()">
        {{ data.confirmLabel ?? 'Confirm' }}
      </button>
    </div>
  `
})
export class ConfirmDialogComponent {
  readonly data = inject(TAILWIND_DIALOG_DATA) as ConfirmDialogData;
  readonly dialogRef = inject(TAILWIND_DIALOG_REF) as TailwindDialogRef<boolean>;

  confirm(): void {
    this.dialogRef.close(true);
  }
}
