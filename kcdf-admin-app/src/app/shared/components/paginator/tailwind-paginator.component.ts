import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';

export interface TailwindPageEvent {
  pageIndex: number;
  pageSize: number;
  length: number;
}

@Component({
  selector: 'app-tailwind-paginator',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="flex flex-col gap-3 border-t border-gray-200 px-4 py-3 text-sm text-gray-600 dark:border-gray-800 dark:text-gray-300 sm:flex-row sm:items-center sm:justify-between">
      <div class="flex items-center gap-2">
        <label [for]="selectId" class="whitespace-nowrap">Rows per page</label>
        <select
          [id]="selectId"
          [value]="pageSize"
          (change)="changePageSize($event)"
          class="min-h-9 rounded-lg border border-gray-300 bg-white px-2 py-1 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
          <option *ngFor="let option of pageSizeOptions" [value]="option">{{ option }}</option>
        </select>
      </div>

      <div class="flex flex-wrap items-center justify-between gap-3 sm:justify-end">
        <span aria-live="polite">
          {{ length === 0 ? 0 : pageIndex * pageSize + 1 }}–{{ rangeEnd }} of {{ length }}
        </span>
        <div class="flex items-center gap-1">
          <button type="button" aria-label="First page" [disabled]="pageIndex === 0" (click)="emitPage(0)"
            class="rounded-lg px-2 py-1.5 hover:bg-gray-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed disabled:opacity-40 dark:hover:bg-gray-800">«</button>
          <button type="button" aria-label="Previous page" [disabled]="pageIndex === 0" (click)="emitPage(pageIndex - 1)"
            class="rounded-lg px-2 py-1.5 hover:bg-gray-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed disabled:opacity-40 dark:hover:bg-gray-800">‹</button>
          <span class="min-w-16 text-center">{{ pageIndex + 1 }} / {{ pageCount }}</span>
          <button type="button" aria-label="Next page" [disabled]="pageIndex + 1 >= pageCount" (click)="emitPage(pageIndex + 1)"
            class="rounded-lg px-2 py-1.5 hover:bg-gray-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed disabled:opacity-40 dark:hover:bg-gray-800">›</button>
          <button type="button" aria-label="Last page" [disabled]="pageIndex + 1 >= pageCount" (click)="emitPage(pageCount - 1)"
            class="rounded-lg px-2 py-1.5 hover:bg-gray-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed disabled:opacity-40 dark:hover:bg-gray-800">»</button>
        </div>
      </div>
    </div>
  `,
})
export class TailwindPaginatorComponent {
  @Input() length = 0;
  @Input() pageIndex = 0;
  @Input() pageSize = 20;
  @Input() pageSizeOptions: number[] = [10, 20, 50, 100];
  @Input() selectId = 'tailwind-paginator-size';
  @Output() readonly page = new EventEmitter<TailwindPageEvent>();

  get pageCount(): number {
    return Math.max(1, Math.ceil(this.length / this.pageSize));
  }

  get rangeEnd(): number {
    return Math.min((this.pageIndex + 1) * this.pageSize, this.length);
  }

  emitPage(pageIndex: number, pageSize = this.pageSize): void {
    this.page.emit({ pageIndex, pageSize, length: this.length });
  }

  changePageSize(event: Event): void {
    const pageSize = Number((event.target as HTMLSelectElement).value);
    if (!this.pageSizeOptions.includes(pageSize) || pageSize <= 0) return;
    const firstVisibleItem = this.pageIndex * this.pageSize;
    this.emitPage(Math.floor(firstVisibleItem / pageSize), pageSize);
  }
}
