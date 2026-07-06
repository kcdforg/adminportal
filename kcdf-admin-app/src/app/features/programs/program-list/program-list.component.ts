import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatSelectModule } from '@angular/material/select';
import { MatTableModule } from '@angular/material/table';
import { MatPaginatorModule, PageEvent } from '@angular/material/paginator';
import { MatDialog } from '@angular/material/dialog';
import { ProgramService } from '../../../core/services/program.service';
import { Program } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { CurrencyInrPipe } from '../../../shared/pipes/currency-inr.pipe';
import { ProgramFormComponent } from '../program-form/program-form.component';

@Component({
  selector: 'app-program-list',
  standalone: true,
  imports: [
    CommonModule, ReactiveFormsModule,
    MatCardModule, MatButtonModule, MatIconModule, MatFormFieldModule, MatSelectModule,
    MatTableModule, MatPaginatorModule,
    StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent, CurrencyInrPipe,
  ],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Programs" [subtitle]="'Total: ' + total()">
      <button mat-flat-button color="primary" (click)="openForm()"><mat-icon>add</mat-icon> Add Program</button>
    </app-page-header>
    <mat-card>
      <mat-card-content>
        <div class="filters">
          <mat-form-field appearance="outline">
            <mat-label>Status</mat-label>
            <mat-select [formControl]="statusCtrl">
              <mat-option value="">All</mat-option>
              <mat-option value="active">Active</mat-option>
              <mat-option value="inactive">Inactive</mat-option>
              <mat-option value="archived">Archived</mat-option>
            </mat-select>
          </mat-form-field>
        </div>
        <table mat-table [dataSource]="programs()" class="full-width">
          <ng-container matColumnDef="name"><th mat-header-cell *matHeaderCellDef>Name</th><td mat-cell *matCellDef="let p">{{ p.name }}</td></ng-container>
          <ng-container matColumnDef="program_type"><th mat-header-cell *matHeaderCellDef>Type</th><td mat-cell *matCellDef="let p">{{ p.program_type }}</td></ng-container>
          <ng-container matColumnDef="fee_amount"><th mat-header-cell *matHeaderCellDef>Fee</th><td mat-cell *matCellDef="let p">{{ p.fee_amount | currencyInr }}</td></ng-container>
          <ng-container matColumnDef="status"><th mat-header-cell *matHeaderCellDef>Status</th><td mat-cell *matCellDef="let p"><app-status-badge [status]="p.status"></app-status-badge></td></ng-container>
          <ng-container matColumnDef="actions"><th mat-header-cell *matHeaderCellDef>Actions</th>
            <td mat-cell *matCellDef="let p"><button mat-icon-button (click)="openForm(p)"><mat-icon>edit</mat-icon></button></td>
          </ng-container>
          <tr mat-header-row *matHeaderRowDef="cols"></tr>
          <tr mat-row *matRowDef="let r; columns: cols;"></tr>
          <tr class="mat-row" *matNoDataRow><td [colSpan]="cols.length" class="empty-row">No programs found</td></tr>
        </table>
        <mat-paginator [length]="total()" [pageSize]="20" [pageSizeOptions]="[10,20,50]" (page)="onPage($event)" showFirstLastButtons></mat-paginator>
      </mat-card-content>
    </mat-card>
  `,
  styles: [`.filters{display:flex;gap:16px;margin-bottom:8px}.full-width{width:100%}.empty-row{text-align:center;padding:32px;color:#999}`]
})
export class ProgramListComponent implements OnInit {
  private readonly programService = inject(ProgramService);
  private readonly dialog = inject(MatDialog);
  readonly loading = signal(false);
  readonly programs = signal<Program[]>([]);
  readonly total = signal(0);
  readonly cols = ['name', 'program_type', 'fee_amount', 'status', 'actions'];
  readonly statusCtrl = new FormControl('');
  private page = 1;

  ngOnInit(): void {
    this.load();
    this.statusCtrl.valueChanges.subscribe(() => { this.page = 1; this.load(); });
  }

  load(): void {
    this.loading.set(true);
    this.programService.list({ page: this.page, per_page: 20, status: this.statusCtrl.value ?? undefined }).subscribe({
      next: res => { this.programs.set(res.data); this.total.set(res.meta.total); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  onPage(e: PageEvent): void { this.page = e.pageIndex + 1; this.load(); }
  openForm(program?: Program): void {
    const ref = this.dialog.open(ProgramFormComponent, { width: '520px', data: program ?? null });
    ref.afterClosed().subscribe(saved => { if (saved) this.load(); });
  }
}
