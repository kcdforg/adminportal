import { AfterViewInit, Component, ElementRef, EventEmitter, Input, OnChanges, Output, SimpleChanges, ViewChild } from '@angular/core';
import { CommonModule } from '@angular/common';

let nextModalId = 0;

@Component({
  selector: 'app-tailwind-modal',
  standalone: true,
  imports: [CommonModule],
  template: `
    <dialog
      #dialog
      tabindex="-1"
      [attr.aria-labelledby]="titleId"
      class="m-auto max-h-[calc(100dvh-2rem)] w-[calc(100%-2rem)] overflow-hidden rounded-2xl border border-gray-200 bg-white p-0 text-gray-900 shadow-2xl shadow-gray-950/20 backdrop:bg-gray-950/60 dark:border-gray-800 dark:bg-gray-900 dark:text-white"
      [style.max-width]="maxWidth"
      (cancel)="handleCancel($event)"
      (click)="handleDialogClick($event)">
      <div class="flex items-start gap-4 border-b border-gray-200 px-5 py-4 dark:border-gray-800 sm:px-6">
        <h2 [id]="titleId" class="min-w-0 flex-1 text-lg font-semibold tracking-tight" [class.sr-only]="visuallyHiddenTitle">
          {{ title }}
        </h2>
        <button
          type="button"
          aria-label="Close dialog"
          class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
          (click)="close()">
          <svg aria-hidden="true" viewBox="0 0 20 20" fill="none" class="h-5 w-5">
            <path d="m5 5 10 10M15 5 5 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
          </svg>
        </button>
      </div>

      <div class="max-h-[calc(100dvh-11rem)] overflow-y-auto px-5 py-5 sm:px-6">
        <ng-content></ng-content>
      </div>

      <footer
        *ngIf="hasActions"
        class="flex flex-wrap items-center justify-end gap-2 border-t border-gray-200 px-5 py-4 dark:border-gray-800 sm:px-6">
        <ng-content select="[modal-actions]"></ng-content>
      </footer>
    </dialog>
  `,
  styles: [`
    dialog::backdrop {
      backdrop-filter: blur(2px);
    }
  `]
})
export class TailwindModalComponent implements AfterViewInit, OnChanges {
  @Input() open = false;
  @Input() title = 'Dialog';
  @Input() maxWidth = '36rem';
  @Input() visuallyHiddenTitle = false;
  @Input() closeOnBackdrop = true;
  @Input() hasActions = false;
  @Output() readonly closed = new EventEmitter<void>();

  @ViewChild('dialog') private readonly dialogRef?: ElementRef<HTMLDialogElement>;

  readonly titleId = `tailwind-dialog-title-${++nextModalId}`;
  private viewReady = false;

  ngAfterViewInit(): void {
    this.viewReady = true;
    this.syncOpenState();
  }

  ngOnChanges(_changes: SimpleChanges): void {
    this.syncOpenState();
  }

  close(): void {
    const dialog = this.dialogRef?.nativeElement;
    if (!dialog?.open) return;

    dialog.close();
    this.closed.emit();
  }

  handleCancel(event: Event): void {
    event.preventDefault();
    this.close();
  }

  handleDialogClick(event: MouseEvent): void {
    const dialog = this.dialogRef?.nativeElement;
    if (!this.closeOnBackdrop || !dialog || event.target !== dialog) return;
    const bounds = dialog.getBoundingClientRect();
    const clickedOutside =
      event.clientX < bounds.left ||
      event.clientX > bounds.right ||
      event.clientY < bounds.top ||
      event.clientY > bounds.bottom;

    if (clickedOutside) this.close();
  }

  private syncOpenState(): void {
    if (!this.viewReady) return;
    const dialog = this.dialogRef?.nativeElement;
    if (!dialog) return;

    if (this.open && !dialog.open) {
      dialog.showModal();
    } else if (!this.open && dialog.open) {
      dialog.close();
    }
  }
}
