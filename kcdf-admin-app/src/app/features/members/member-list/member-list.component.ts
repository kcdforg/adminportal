import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatPaginatorModule, PageEvent } from '@angular/material/paginator';
import { MatTableModule } from '@angular/material/table';
import { MatDialog } from '@angular/material/dialog';
import { debounceTime, distinctUntilChanged } from 'rxjs/operators';
import { MemberService } from '../../../core/services/member.service';
import { MemberProfile } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { MemberFormComponent } from '../member-form/member-form.component';

@Component({
  selector: 'app-member-list',
  standalone: true,
  imports: [
    CommonModule,
    RouterModule,
    ReactiveFormsModule,
    MatCardModule,
    MatButtonModule,
    MatIconModule,
    MatFormFieldModule,
    MatInputModule,
    MatSelectModule,
    MatPaginatorModule,
    MatTableModule,
    StatusBadgeComponent,
    PageHeaderComponent,
    LoadingOverlayComponent,
  ],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Members" [subtitle]="'Total: ' + total()">
      <button mat-flat-button color="primary" (click)="openForm()">
        <mat-icon>add</mat-icon> Add Member
      </button>
    </app-page-header>

    <mat-card>
      <mat-card-content>
        <div class="filters">
          <mat-form-field appearance="outline" class="filter-field">
            <mat-label>Search by name</mat-label>
            <input matInput [formControl]="searchCtrl" placeholder="Type to search..." />
            <mat-icon matSuffix>search</mat-icon>
          </mat-form-field>
          <mat-form-field appearance="outline" class="filter-field">
            <mat-label>Status</mat-label>
            <mat-select [formControl]="statusCtrl">
              <mat-option value="">All</mat-option>
              <mat-option value="active">Active</mat-option>
              <mat-option value="inactive">Inactive</mat-option>
              <mat-option value="suspended">Suspended</mat-option>
            </mat-select>
          </mat-form-field>
        </div>

        <table mat-table [dataSource]="members()" class="full-width">
          <ng-container matColumnDef="name">
            <th mat-header-cell *matHeaderCellDef>Name</th>
            <td mat-cell *matCellDef="let m">{{ m.first_name }} {{ m.last_name }}</td>
          </ng-container>
          <ng-container matColumnDef="email">
            <th mat-header-cell *matHeaderCellDef>Email</th>
            <td mat-cell *matCellDef="let m">{{ m.email ?? '—' }}</td>
          </ng-container>
          <ng-container matColumnDef="mobile">
            <th mat-header-cell *matHeaderCellDef>Mobile</th>
            <td mat-cell *matCellDef="let m">{{ m.mobile ?? '—' }}</td>
          </ng-container>
          <ng-container matColumnDef="status">
            <th mat-header-cell *matHeaderCellDef>Status</th>
            <td mat-cell *matCellDef="let m"><app-status-badge [status]="m.status"></app-status-badge></td>
          </ng-container>
          <ng-container matColumnDef="actions">
            <th mat-header-cell *matHeaderCellDef>Actions</th>
            <td mat-cell *matCellDef="let m">
              <button mat-icon-button [routerLink]="['/members', m.id]" matTooltip="View details">
                <mat-icon>visibility</mat-icon>
              </button>
              <button mat-icon-button (click)="openForm(m)" matTooltip="Edit">
                <mat-icon>edit</mat-icon>
              </button>
            </td>
          </ng-container>

          <tr mat-header-row *matHeaderRowDef="displayedColumns"></tr>
          <tr mat-row *matRowDef="let row; columns: displayedColumns;" class="clickable"
              [routerLink]="['/members', row.id]"></tr>
          <tr class="mat-row" *matNoDataRow>
            <td [colSpan]="displayedColumns.length" class="empty-row">No members found</td>
          </tr>
        </table>

        <mat-paginator
          [length]="total()"
          [pageSize]="pageSize"
          [pageSizeOptions]="[10, 20, 50]"
          (page)="onPage($event)"
          showFirstLastButtons>
        </mat-paginator>
      </mat-card-content>
    </mat-card>
  `,
  styles: [`
    .filters { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 8px; }
    .filter-field { min-width: 200px; }
    .full-width { width: 100%; }
    .clickable { cursor: pointer; }
    .clickable:hover { background: #f5f5f5; }
    .empty-row { text-align: center; padding: 32px; color: #999; }
  `]
})
export class MemberListComponent implements OnInit {
  private readonly memberService = inject(MemberService);
  private readonly dialog = inject(MatDialog);

  readonly loading = signal(false);
  readonly members = signal<MemberProfile[]>([]);
  readonly total = signal(0);
  readonly pageSize = 20;
  private page = 1;

  readonly displayedColumns = ['name', 'email', 'mobile', 'status', 'actions'];
  readonly searchCtrl = new FormControl('');
  readonly statusCtrl = new FormControl('');

  ngOnInit(): void {
    this.load();
    this.searchCtrl.valueChanges.pipe(debounceTime(400), distinctUntilChanged()).subscribe(() => {
      this.page = 1;
      this.load();
    });
    this.statusCtrl.valueChanges.subscribe(() => {
      this.page = 1;
      this.load();
    });
  }

  load(): void {
    this.loading.set(true);
    this.memberService.list({
      page: this.page,
      per_page: this.pageSize,
      search: this.searchCtrl.value ?? undefined,
      status: this.statusCtrl.value ?? undefined,
    }).subscribe({
      next: (res) => {
        this.members.set(res.data);
        this.total.set(res.meta.total);
        this.loading.set(false);
      },
      error: () => this.loading.set(false)
    });
  }

  onPage(event: PageEvent): void {
    this.page = event.pageIndex + 1;
    this.load();
  }

  openForm(member?: MemberProfile): void {
    const ref = this.dialog.open(MemberFormComponent, {
      width: '520px',
      data: member ?? null,
    });
    ref.afterClosed().subscribe(saved => { if (saved) this.load(); });
  }
}
