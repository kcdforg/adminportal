import { Component, EventEmitter, Input, OnChanges, Output, SimpleChanges } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AppIconComponent } from '../icon/app-icon.component';
import { TailwindPageEvent, TailwindPaginatorComponent } from '../paginator/tailwind-paginator.component';

export interface ColumnDef {
  key: string;
  label: string;
  type?: 'text' | 'date' | 'currency' | 'status' | 'actions' | 'boolean';
  sortable?: boolean;
}

@Component({
  selector: 'app-data-table',
  standalone: true,
  imports: [
    CommonModule,
    AppIconComponent,
    TailwindPaginatorComponent,
  ],
  template: `
    <div class="relative overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
      <div
        *ngIf="loading"
        class="absolute inset-0 z-10 flex items-center justify-center bg-white/70 dark:bg-gray-950/70"
        role="status"
        aria-live="polite">
        <span class="h-10 w-10 animate-spin rounded-full border-4 border-indigo-200 border-t-indigo-600" aria-hidden="true"></span>
        <span class="sr-only">Loading records</span>
      </div>

      <div class="w-full overflow-x-auto">
        <table class="w-full min-w-[640px]">
          <thead>
            <tr>
              <th
                *ngFor="let col of columns"
                scope="col"
                [attr.aria-sort]="ariaSort(col)"
                class="!bg-gray-50 !px-4 !py-3 !text-left !text-xs !font-semibold !uppercase !tracking-wide !text-gray-500 dark:!bg-gray-800/60 dark:!text-gray-300">
                <button
                  *ngIf="col.sortable !== false"
                  type="button"
                  [attr.aria-label]="'Sort by ' + col.label"
                  class="inline-flex min-h-10 items-center gap-1 border-0 bg-transparent p-0 text-left font-[inherit] text-inherit focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                  (click)="toggleSort(col)">
                  {{ col.label }}
                  <span *ngIf="sortKey === col.key" aria-hidden="true">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                </button>
                <span *ngIf="col.sortable === false">{{ col.label }}</span>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              *ngFor="let row of sortedData"
              class="transition-colors hover:!bg-gray-50 dark:hover:!bg-gray-800/50"
              [class.clickable]="rowClickable"
              (click)="rowClick.emit(row)">
              <td *ngFor="let col of columns" class="!px-4 !py-3 !text-sm !text-gray-700 dark:!text-gray-200">
                <ng-container [ngSwitch]="col.type">
                  <ng-container *ngSwitchCase="'date'">
                    {{ dateValue(row, col.key) | date:'dd MMM yyyy' }}
                  </ng-container>
                  <ng-container *ngSwitchCase="'boolean'">
                    <app-icon [name]="cellValue(row, col.key) ? 'check_circle' : 'cancel'" [style.color]="cellValue(row, col.key) ? '#2e7d32' : '#c62828'" aria-hidden="true" class="h-6 w-6"></app-icon>
                  </ng-container>
                  <ng-container *ngSwitchCase="'actions'">
                    <ng-content select="[slot=actions]"></ng-content>
                  </ng-container>
                  <ng-container *ngSwitchDefault>{{ cellValue(row, col.key) ?? '—' }}</ng-container>
                </ng-container>
              </td>
            </tr>
            <tr *ngIf="sortedData.length === 0">
              <td [attr.colspan]="displayedColumns.length" class="!px-4 !py-12">
                <div class="flex flex-col items-center gap-2 text-center text-sm text-gray-500 dark:text-gray-400">
                  <app-icon name="inbox" aria-hidden="true" class="!h-12 !w-12 !text-5xl opacity-40"></app-icon>
                  <span>No records found</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="border-t border-gray-200 dark:border-gray-800">
        <app-tailwind-paginator
          [length]="totalCount"
          [pageIndex]="pageIndex"
          [pageSize]="pageSize"
          [pageSizeOptions]="[10, 20, 50, 100]"
          selectId="data-table-page-size"
          (page)="onPage($event)">
        </app-tailwind-paginator>
      </div>
    </div>
  `,
  styles: [`
    tr.clickable { cursor: pointer; }
  `]
})
export class DataTableComponent implements OnChanges {
  @Input() columns: ColumnDef[] = [];
  @Input() dataSource: unknown[] = [];
  @Input() loading = false;
  @Input() totalCount = 0;
  @Input() pageIndex = 0;
  @Input() pageSize = 20;
  @Input() rowClickable = true;
  @Output() pageChange = new EventEmitter<TailwindPageEvent>();
  @Output() rowClick = new EventEmitter<unknown>();

  sortedData: unknown[] = [];
  private originalData: unknown[] = [];
  sortKey: string | null = null;
  sortDirection: 'asc' | 'desc' | null = null;

  get displayedColumns(): string[] {
    return this.columns.map(column => column.key);
  }

  ngOnChanges(changes: SimpleChanges): void {
    if (changes['dataSource']) {
      this.originalData = [...this.dataSource];
    }
    if (changes['dataSource'] || changes['columns']) {
      this.updateSortedData();
    }
  }

  cellValue(row: unknown, key: string): unknown {
    return (row as Record<string, unknown>)[key];
  }

  dateValue(row: unknown, key: string): string | number | Date | null {
    const value = this.cellValue(row, key);
    return typeof value === 'string' || typeof value === 'number' || value instanceof Date
      ? value
      : null;
  }

  ariaSort(column: ColumnDef): 'ascending' | 'descending' | 'none' {
    if (column.key !== this.sortKey || !this.sortDirection) return 'none';
    return this.sortDirection === 'asc' ? 'ascending' : 'descending';
  }

  toggleSort(column: ColumnDef): void {
    if (column.key !== this.sortKey) {
      this.sortKey = column.key;
      this.sortDirection = 'asc';
    } else if (this.sortDirection === 'asc') {
      this.sortDirection = 'desc';
    } else {
      this.sortKey = null;
      this.sortDirection = null;
    }
    this.updateSortedData();
  }

  onPage(event: TailwindPageEvent): void {
    this.pageIndex = event.pageIndex;
    this.pageSize = event.pageSize;
    this.pageChange.emit(event);
  }

  private updateSortedData(): void {
    if (!this.sortKey || !this.sortDirection) {
      this.sortedData = [...this.originalData];
      return;
    }

    const column = this.columns.find(item => item.key === this.sortKey);
    const direction = this.sortDirection === 'asc' ? 1 : -1;
    this.sortedData = this.originalData
      .map((row, index) => ({ row, index }))
      .sort((left, right) => {
        const comparison = this.compareValues(
          (left.row as Record<string, unknown>)[this.sortKey!],
          (right.row as Record<string, unknown>)[this.sortKey!],
          column,
        );
        return comparison === 0 ? left.index - right.index : comparison * direction;
      })
      .map(item => item.row);
  }

  private compareValues(left: unknown, right: unknown, column?: ColumnDef): number {
    if (left == null || right == null) {
      return left == null ? (right == null ? 0 : 1) : -1;
    }

    if (column?.type === 'date') {
      const leftDate = left instanceof Date ? left.getTime() : new Date(left as string | number).getTime();
      const rightDate = right instanceof Date ? right.getTime() : new Date(right as string | number).getTime();
      if (!Number.isNaN(leftDate) && !Number.isNaN(rightDate)) return leftDate - rightDate;
    }

    if (typeof left === 'number' && typeof right === 'number') return left - right;
    if (typeof left === 'boolean' && typeof right === 'boolean') return Number(left) - Number(right);
    return String(left).localeCompare(String(right), undefined, { numeric: true, sensitivity: 'base' });
  }
}
