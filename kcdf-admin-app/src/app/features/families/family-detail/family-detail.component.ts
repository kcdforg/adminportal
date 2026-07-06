import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatTableModule } from '@angular/material/table';
import { MatSelectModule } from '@angular/material/select';
import { MatDialog } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatDividerModule } from '@angular/material/divider';
import { forkJoin } from 'rxjs';
import { FamilyService } from '../../../core/services/family.service';
import { Family, FamilyMember } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { FamilyFormComponent } from '../family-form/family-form.component';
import { ConfirmDialogComponent } from '../../../shared/components/confirm-dialog/confirm-dialog.component';

@Component({
  selector: 'app-family-detail',
  standalone: true,
  imports: [
    CommonModule, RouterModule,
    MatCardModule, MatButtonModule, MatIconModule, MatTableModule, MatSelectModule, MatDividerModule,
    StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent,
  ],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header [title]="family()?.family_name ?? 'Family'" subtitle="Family Detail">
      <button mat-stroked-button routerLink="/families"><mat-icon>arrow_back</mat-icon> Back</button>
      <button mat-flat-button color="primary" (click)="openEdit()"><mat-icon>edit</mat-icon> Edit</button>
    </app-page-header>

    <div class="detail-grid" *ngIf="family()">
      <mat-card>
        <mat-card-header><mat-card-title>Family Info</mat-card-title></mat-card-header>
        <mat-card-content>
          <div class="info-grid">
            <div class="info-item"><span class="info-label">Family Code</span><span>{{ family()!.family_code }}</span></div>
            <div class="info-item"><span class="info-label">Family Name</span><span>{{ family()!.family_name }}</span></div>
            <div class="info-item"><span class="info-label">Status</span><app-status-badge [status]="family()!.status"></app-status-badge></div>
            <div class="info-item"><span class="info-label">Created</span><span>{{ family()!.created_at | date:'dd MMM yyyy' }}</span></div>
          </div>
          <mat-divider class="my-16"></mat-divider>
          <p class="section-label">Address</p>
          <div class="info-grid" *ngIf="family()!.address">
            <div class="info-item"><span class="info-label">Line 1</span><span>{{ family()!.address!.line1 }}</span></div>
            <div class="info-item"><span class="info-label">City</span><span>{{ family()!.address!.city }}</span></div>
            <div class="info-item"><span class="info-label">State</span><span>{{ family()!.address!.state }}</span></div>
            <div class="info-item"><span class="info-label">Pincode</span><span>{{ family()!.address!.pincode }}</span></div>
          </div>
          <p *ngIf="!family()!.address" class="empty-text">No address on record</p>
        </mat-card-content>
      </mat-card>

      <mat-card>
        <mat-card-header>
          <mat-card-title>Members ({{ members().length }})</mat-card-title>
        </mat-card-header>
        <mat-card-content>
          <table mat-table [dataSource]="members()" class="full-width">
            <ng-container matColumnDef="name">
              <th mat-header-cell *matHeaderCellDef>Name</th>
              <td mat-cell *matCellDef="let m">{{ m.member?.first_name }} {{ m.member?.last_name }}</td>
            </ng-container>
            <ng-container matColumnDef="role">
              <th mat-header-cell *matHeaderCellDef>Role</th>
              <td mat-cell *matCellDef="let m"><app-status-badge [status]="m.member_role"></app-status-badge></td>
            </ng-container>
            <ng-container matColumnDef="joined">
              <th mat-header-cell *matHeaderCellDef>Joined</th>
              <td mat-cell *matCellDef="let m">{{ m.joined_at | date:'dd MMM yyyy' }}</td>
            </ng-container>
            <ng-container matColumnDef="actions">
              <th mat-header-cell *matHeaderCellDef></th>
              <td mat-cell *matCellDef="let m">
                <button mat-icon-button color="warn" (click)="removeMember(m)" matTooltip="Remove">
                  <mat-icon>person_remove</mat-icon>
                </button>
              </td>
            </ng-container>
            <tr mat-header-row *matHeaderRowDef="memberCols"></tr>
            <tr mat-row *matRowDef="let r; columns: memberCols;"></tr>
            <tr class="mat-row" *matNoDataRow>
              <td [colSpan]="memberCols.length" class="empty-row">No members</td>
            </tr>
          </table>
        </mat-card-content>
      </mat-card>
    </div>
  `,
  styles: [`
    .detail-grid { display: grid; gap: 16px; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; padding: 8px 0; }
    .info-item { display: flex; flex-direction: column; gap: 4px; }
    .info-label { font-size: 12px; color: #666; text-transform: uppercase; font-weight: 600; }
    .full-width { width: 100%; }
    .section-label { font-size: 12px; font-weight: 600; color: #666; text-transform: uppercase; margin: 8px 0 4px; }
    .empty-text { color: #999; font-size: 14px; }
    .my-16 { margin: 16px 0; }
    .empty-row { text-align: center; padding: 24px; color: #999; }
  `]
})
export class FamilyDetailComponent implements OnInit {
  private readonly familyService = inject(FamilyService);
  private readonly route = inject(ActivatedRoute);
  private readonly dialog = inject(MatDialog);
  private readonly snackBar = inject(MatSnackBar);

  readonly loading = signal(false);
  readonly family = signal<Family | null>(null);
  readonly members = signal<FamilyMember[]>([]);
  readonly memberCols = ['name', 'role', 'joined', 'actions'];

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.loading.set(true);
    forkJoin({
      family: this.familyService.get(id),
      members: this.familyService.getMembers(id),
    }).subscribe({
      next: ({ family, members }) => {
        this.family.set(family.data);
        this.members.set(members.data);
        this.loading.set(false);
      },
      error: () => this.loading.set(false)
    });
  }

  openEdit(): void {
    const ref = this.dialog.open(FamilyFormComponent, { width: '560px', data: this.family() });
    ref.afterClosed().subscribe(saved => { if (saved) this.load(); });
  }

  removeMember(member: FamilyMember): void {
    const ref = this.dialog.open(ConfirmDialogComponent, {
      data: { title: 'Remove Member', message: 'Remove this member from the family?', danger: true }
    });
    ref.afterClosed().subscribe(confirmed => {
      if (!confirmed) return;
      this.familyService.removeMember(this.family()!.id, member.member_id).subscribe({
        next: () => { this.snackBar.open('Member removed', 'Close', { duration: 2000 }); this.load(); },
        error: () => this.snackBar.open('Failed to remove member', 'Close', { duration: 3000 })
      });
    });
  }
}
