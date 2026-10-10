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
  templateUrl: './data-table.component.html',
  styleUrl: './data-table.component.scss'
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
