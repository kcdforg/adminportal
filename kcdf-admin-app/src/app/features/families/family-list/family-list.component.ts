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
import { MatTableModule } from '@angular/material/table';
import { MatPaginatorModule, PageEvent } from '@angular/material/paginator';
import { MatDialog } from '@angular/material/dialog';
import { debounceTime, distinctUntilChanged } from 'rxjs/operators';
import { FamilyService } from '../../../core/services/family.service';
import { Family } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { FamilyFormComponent } from '../family-form/family-form.component';

@Component({
  selector: 'app-family-list',
  standalone: true,
  imports: [
    CommonModule, RouterModule, ReactiveFormsModule,
    MatCardModule, MatButtonModule, MatIconModule,
    MatFormFieldModule, MatInputModule, MatSelectModule,
    MatTableModule, MatPaginatorModule,
    StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent,
  ],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Families" [subtitle]="'Total: ' + total()">
      <button mat-flat-button color="primary" (click)="openForm()">
        <mat-icon>add</mat-icon> Add Family
      </button>
    </app-page-header>

    <mat-card>
      <mat-card-content>
        <div class="filters">
          <mat-form-field appearance="outline">
            <mat-label>Search</mat-label>
            <input matInput [formControl]="searchCtrl" />
            <mat-icon matSuffix>search</mat-icon>
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>Status</mat-label>
            <mat-select [formControl]="statusCtrl">
              <mat-option value="">All</mat-option>
              <mat-option value="active">Active</mat-option>
              <mat-option value="inactive">Inactive</mat-option>
              <mat-option value="suspended">Suspended</mat-option>
            </mat-select>
          </mat-form-field>
        </div>

        <table mat-table [dataSource]="families()" class="full-width">
          <ng-container matColumnDef="family_code">
            <th mat-header-cell *matHeaderCellDef>Code</th>
            <td mat-cell *matCellDef="let f">{{ f.family_code }}</td>
          </ng-container>
          <ng-container matColumnDef="family_name">
            <th mat-header-cell *matHeaderCellDef>Family Name</th>
            <td mat-cell *matCellDef="let f">{{ f.family_name }}</td>
          </ng-container>
          <ng-container matColumnDef="city">
            <th mat-header-cell *matHeaderCellDef>City</th>
            <td mat-cell *matCellDef="let f">{{ f.address?.city ?? '—' }}</td>
          </ng-container>
          <ng-container matColumnDef="member_count">
            <th mat-header-cell *matHeaderCellDef>Members</th>
            <td mat-cell *matCellDef="let f">{{ f.member_count ?? '—' }}</td>
          </ng-container>
          <ng-container matColumnDef="status">
            <th mat-header-cell *matHeaderCellDef>Status</th>
            <td mat-cell *matCellDef="let f"><app-status-badge [status]="f.status"></app-status-badge></td>
          </ng-container>
          <ng-container matColumnDef="actions">
            <th mat-header-cell *matHeaderCellDef>Actions</th>
            <td mat-cell *matCellDef="let f">
              <button mat-icon-button [routerLink]="['/families', f.id]"><mat-icon>visibility</mat-icon></button>
              <button mat-icon-button (click)="openForm(f); $event.stopPropagation()"><mat-icon>edit</mat-icon></button>
            </td>
          </ng-container>
          <tr mat-header-row *matHeaderRowDef="cols"></tr>
          <tr mat-row *matRowDef="let r; columns: cols;" class="clickable" [routerLink]="['/families', r.id]"></tr>
          <tr class="mat-row" *matNoDataRow>
            <td [colSpan]="cols.length" class="empty-row">No families found</td>
          </tr>
        </table>

        <mat-paginator [length]="total()" [pageSize]="20" [pageSizeOptions]="[10,20,50]"
          (page)="onPage($event)" showFirstLastButtons></mat-paginator>
      </mat-card-content>
    </mat-card>
  `,
  styles: [`.filters{display:flex;gap:16px;flex-wrap:wrap;margin-bottom:8px}.full-width{width:100%}.clickable{cursor:pointer}.clickable:hover{background:#f5f5f5}.empty-row{text-align:center;padding:32px;color:#999}`]
})
export class FamilyListComponent implements OnInit {
  private readonly familyService = inject(FamilyService);
  private readonly dialog = inject(MatDialog);

  readonly loading = signal(false);
  readonly families = signal<Family[]>([]);
  readonly total = signal(0);
  readonly cols = ['family_code', 'family_name', 'city', 'member_count', 'status', 'actions'];
  readonly searchCtrl = new FormControl('');
  readonly statusCtrl = new FormControl('');
  private page = 1;

  ngOnInit(): void {
    this.load();
    this.searchCtrl.valueChanges.pipe(debounceTime(400), distinctUntilChanged()).subscribe(() => { this.page = 1; this.load(); });
    this.statusCtrl.valueChanges.subscribe(() => { this.page = 1; this.load(); });
  }

  load(): void {
    this.loading.set(true);
    this.familyService.list({ page: this.page, per_page: 20, search: this.searchCtrl.value ?? undefined, status: this.statusCtrl.value ?? undefined }).subscribe({
      next: res => { this.families.set(res.data); this.total.set(res.meta.total); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  onPage(e: PageEvent): void { this.page = e.pageIndex + 1; this.load(); }

  openForm(family?: Family): void {
    const ref = this.dialog.open(FamilyFormComponent, { width: '560px', data: family ?? null });
    ref.afterClosed().subscribe(saved => { if (saved) this.load(); });
  }
}
