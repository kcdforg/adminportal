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
  templateUrl: './tailwind-paginator.component.html',
  styleUrl: './tailwind-paginator.component.scss',
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
