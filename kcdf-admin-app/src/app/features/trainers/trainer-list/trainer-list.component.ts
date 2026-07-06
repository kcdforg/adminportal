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
import { TrainerService } from '../../../core/services/trainer.service';
import { Trainer } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { TrainerFormComponent } from '../trainer-form/trainer-form.component';

@Component({
  selector: 'app-trainer-list',
  standalone: true,
  imports: [
    CommonModule, RouterModule, ReactiveFormsModule,
    MatCardModule, MatButtonModule, MatIconModule, MatFormFieldModule, MatInputModule, MatSelectModule,
    MatTableModule, MatPaginatorModule,
    StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent,
  ],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Trainers" [subtitle]="'Total: ' + total()">
      <button mat-flat-button color="primary" (click)="openForm()"><mat-icon>add</mat-icon> Add Trainer</button>
    </app-page-header>
    <mat-card>
      <mat-card-content>
        <div class="filters">
          <mat-form-field appearance="outline">
            <mat-label>Search</mat-label>
            <input matInput [formControl]="searchCtrl" /><mat-icon matSuffix>search</mat-icon>
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>Status</mat-label>
            <mat-select [formControl]="statusCtrl">
              <mat-option value="">All</mat-option>
              <mat-option value="active">Active</mat-option>
              <mat-option value="inactive">Inactive</mat-option>
            </mat-select>
          </mat-form-field>
        </div>
        <table mat-table [dataSource]="trainers()" class="full-width">
          <ng-container matColumnDef="trainer_code"><th mat-header-cell *matHeaderCellDef>Code</th><td mat-cell *matCellDef="let t">{{ t.trainer_code }}</td></ng-container>
          <ng-container matColumnDef="name"><th mat-header-cell *matHeaderCellDef>Name</th><td mat-cell *matCellDef="let t">{{ t.member?.first_name }} {{ t.member?.last_name }}</td></ng-container>
          <ng-container matColumnDef="specialization"><th mat-header-cell *matHeaderCellDef>Specialization</th><td mat-cell *matCellDef="let t">{{ t.specialization ?? '—' }}</td></ng-container>
          <ng-container matColumnDef="status"><th mat-header-cell *matHeaderCellDef>Status</th><td mat-cell *matCellDef="let t"><app-status-badge [status]="t.status"></app-status-badge></td></ng-container>
          <ng-container matColumnDef="actions"><th mat-header-cell *matHeaderCellDef>Actions</th>
            <td mat-cell *matCellDef="let t">
              <button mat-icon-button [routerLink]="['/trainers', t.id]"><mat-icon>visibility</mat-icon></button>
              <button mat-icon-button (click)="openForm(t); $event.stopPropagation()"><mat-icon>edit</mat-icon></button>
            </td>
          </ng-container>
          <tr mat-header-row *matHeaderRowDef="cols"></tr>
          <tr mat-row *matRowDef="let r; columns: cols;" class="clickable" [routerLink]="['/trainers', r.id]"></tr>
          <tr class="mat-row" *matNoDataRow><td [colSpan]="cols.length" class="empty-row">No trainers found</td></tr>
        </table>
        <mat-paginator [length]="total()" [pageSize]="20" [pageSizeOptions]="[10,20,50]" (page)="onPage($event)" showFirstLastButtons></mat-paginator>
      </mat-card-content>
    </mat-card>
  `,
  styles: [`.filters{display:flex;gap:16px;margin-bottom:8px}.full-width{width:100%}.clickable{cursor:pointer}.clickable:hover{background:#f5f5f5}.empty-row{text-align:center;padding:32px;color:#999}`]
})
export class TrainerListComponent implements OnInit {
  private readonly trainerService = inject(TrainerService);
  private readonly dialog = inject(MatDialog);
  readonly loading = signal(false);
  readonly trainers = signal<Trainer[]>([]);
  readonly total = signal(0);
  readonly cols = ['trainer_code', 'name', 'specialization', 'status', 'actions'];
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
    this.trainerService.list({ page: this.page, per_page: 20, search: this.searchCtrl.value ?? undefined, status: this.statusCtrl.value ?? undefined }).subscribe({
      next: res => { this.trainers.set(res.data); this.total.set(res.meta.total); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  onPage(e: PageEvent): void { this.page = e.pageIndex + 1; this.load(); }
  openForm(trainer?: Trainer): void {
    const ref = this.dialog.open(TrainerFormComponent, { width: '520px', data: trainer ?? null });
    ref.afterClosed().subscribe(saved => { if (saved) this.load(); });
  }
}
