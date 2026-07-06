import {
  Component, Input, Output, EventEmitter, OnChanges, SimpleChanges, ViewChild
} from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatTableModule, MatTableDataSource } from '@angular/material/table';
import { MatPaginatorModule, MatPaginator, PageEvent } from '@angular/material/paginator';
import { MatSortModule, MatSort } from '@angular/material/sort';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatIconModule } from '@angular/material/icon';

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
    MatTableModule,
    MatPaginatorModule,
    MatSortModule,
    MatProgressSpinnerModule,
    MatIconModule,
  ],
  template: `
    <div class="table-wrapper">
      <div *ngIf="loading" class="table-loading">
        <mat-spinner diameter="40"></mat-spinner>
      </div>

      <table mat-table [dataSource]="dataSource" matSort class="full-width">
        <ng-container *ngFor="let col of columns" [matColumnDef]="col.key">
          <th mat-header-cell *matHeaderCellDef [mat-sort-header]="col.sortable !== false ? col.key : ''">
            {{ col.label }}
          </th>
          <td mat-cell *matCellDef="let row">
            <ng-container [ngSwitch]="col.type">
              <ng-container *ngSwitchCase="'date'">
                {{ row[col.key] | date:'dd MMM yyyy' }}
              </ng-container>
              <ng-container *ngSwitchCase="'boolean'">
                <mat-icon [style.color]="row[col.key] ? '#2e7d32' : '#c62828'">
                  {{ row[col.key] ? 'check_circle' : 'cancel' }}
                </mat-icon>
              </ng-container>
              <ng-container *ngSwitchCase="'actions'">
                <ng-content select="[slot=actions]"></ng-content>
              </ng-container>
              <ng-container *ngSwitchDefault>{{ row[col.key] ?? '—' }}</ng-container>
            </ng-container>
          </td>
        </ng-container>

        <tr mat-header-row *matHeaderRowDef="displayedColumns"></tr>
        <tr mat-row *matRowDef="let row; columns: displayedColumns;"
            [class.clickable]="rowClickable"
            (click)="rowClick.emit(row)"></tr>

        <tr class="mat-row" *matNoDataRow>
          <td [colSpan]="displayedColumns.length" class="empty-row">
            <mat-icon>inbox</mat-icon>
            <span>No records found</span>
          </td>
        </tr>
      </table>

      <mat-paginator
        [length]="totalCount"
        [pageSize]="pageSize"
        [pageSizeOptions]="[10, 20, 50, 100]"
        (page)="pageChange.emit($event)"
        showFirstLastButtons>
      </mat-paginator>
    </div>
  `,
  styles: [`
    .table-wrapper { position: relative; }
    .table-loading {
      position: absolute; inset: 0; display: flex;
      align-items: center; justify-content: center;
      background: rgba(255,255,255,0.7); z-index: 10;
    }
    .full-width { width: 100%; }
    .empty-row {
      text-align: center; padding: 48px 0;
      color: #999; display: flex;
      flex-direction: column; align-items: center; gap: 8px;
    }
    .empty-row mat-icon { font-size: 48px; height: 48px; width: 48px; opacity: 0.4; }
    tr.mat-row.clickable { cursor: pointer; }
    tr.mat-row.clickable:hover { background: #f5f5f5; }
  `]
})
export class DataTableComponent implements OnChanges {
  @Input() columns: ColumnDef[] = [];
  @Input() dataSource: unknown[] = [];
  @Input() loading = false;
  @Input() totalCount = 0;
  @Input() pageSize = 20;
  @Input() rowClickable = true;
  @Output() pageChange = new EventEmitter<PageEvent>();
  @Output() rowClick = new EventEmitter<unknown>();

  @ViewChild(MatSort) sort!: MatSort;
  @ViewChild(MatPaginator) paginator!: MatPaginator;

  matDataSource = new MatTableDataSource<unknown>([]);

  get displayedColumns(): string[] {
    return this.columns.map(c => c.key);
  }

  ngOnChanges(changes: SimpleChanges): void {
    if (changes['dataSource']) {
      this.matDataSource.data = this.dataSource;
    }
  }
}
