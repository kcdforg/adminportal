import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatTableModule } from '@angular/material/table';
import { MatPaginatorModule, PageEvent } from '@angular/material/paginator';
import { MatDialog } from '@angular/material/dialog';
import { MatDialogModule, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { GroupService } from '../../../core/services/group.service';
import { Group } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';

@Component({
  selector: 'app-group-form-dialog',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, MatDialogModule, MatFormFieldModule, MatInputModule, MatSelectModule, MatButtonModule],
  template: `
    <h2 mat-dialog-title>{{ group ? 'Edit Group' : 'Create Group' }}</h2>
    <mat-dialog-content>
      <form [formGroup]="form" class="form-col">
        <mat-form-field appearance="outline" class="full-width"><mat-label>Group Name</mat-label><input matInput formControlName="group_name" /><mat-error>Required</mat-error></mat-form-field>
        <mat-form-field appearance="outline" class="full-width"><mat-label>Description</mat-label><textarea matInput formControlName="description" rows="2"></textarea></mat-form-field>
        <mat-form-field appearance="outline" class="full-width"><mat-label>Visibility</mat-label>
          <mat-select formControlName="visibility"><mat-option value="public">Public</mat-option><mat-option value="private">Private</mat-option></mat-select>
        </mat-form-field>
      </form>
    </mat-dialog-content>
    <mat-dialog-actions align="end">
      <button mat-button mat-dialog-close>Cancel</button>
      <button mat-flat-button color="primary" (click)="save()" [disabled]="saving">{{ saving ? 'Saving...' : 'Save' }}</button>
    </mat-dialog-actions>
  `,
  styles: [`.form-col{display:flex;flex-direction:column;gap:8px;min-width:440px}.full-width{width:100%}`]
})
export class GroupFormDialogComponent {
  readonly group: Group | null = inject(MAT_DIALOG_DATA);
  private readonly groupService = inject(GroupService);
  private readonly dialogRef = inject(MatDialogRef<GroupFormDialogComponent>);
  private readonly snackBar = inject(MatSnackBar);
  saving = false;
  readonly form = new FormGroup({
    group_name: new FormControl(this.group?.group_name ?? '', { nonNullable: true, validators: [Validators.required] }),
    description: new FormControl(this.group?.description ?? ''),
    visibility: new FormControl(this.group?.visibility ?? 'public', { nonNullable: true }),
  });
  save(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.saving = true;
    const obs = this.group ? this.groupService.update(this.group.id, this.form.getRawValue()) : this.groupService.create(this.form.getRawValue());
    obs.subscribe({
      next: () => { this.snackBar.open('Group saved', 'Close', { duration: 3000 }); this.dialogRef.close(true); },
      error: (err) => { this.saving = false; this.snackBar.open(err?.error?.error?.message ?? 'Error', 'Close', { duration: 4000 }); }
    });
  }
}

@Component({
  selector: 'app-group-list',
  standalone: true,
  imports: [CommonModule, RouterModule, ReactiveFormsModule, MatCardModule, MatButtonModule, MatIconModule, MatFormFieldModule, MatSelectModule, MatTableModule, MatPaginatorModule, StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Groups" [subtitle]="'Total: ' + total()">
      <button mat-flat-button color="primary" (click)="openForm()"><mat-icon>add</mat-icon> Create Group</button>
    </app-page-header>
    <mat-card>
      <mat-card-content>
        <table mat-table [dataSource]="groups()" class="full-width">
          <ng-container matColumnDef="group_name"><th mat-header-cell *matHeaderCellDef>Name</th><td mat-cell *matCellDef="let g">{{ g.group_name }}</td></ng-container>
          <ng-container matColumnDef="visibility"><th mat-header-cell *matHeaderCellDef>Visibility</th><td mat-cell *matCellDef="let g">{{ g.visibility }}</td></ng-container>
          <ng-container matColumnDef="member_count"><th mat-header-cell *matHeaderCellDef>Members</th><td mat-cell *matCellDef="let g">{{ g.member_count ?? '—' }}</td></ng-container>
          <ng-container matColumnDef="status"><th mat-header-cell *matHeaderCellDef>Status</th><td mat-cell *matCellDef="let g"><app-status-badge [status]="g.status"></app-status-badge></td></ng-container>
          <ng-container matColumnDef="actions"><th mat-header-cell *matHeaderCellDef>Actions</th>
            <td mat-cell *matCellDef="let g">
              <button mat-icon-button [routerLink]="['/groups', g.id]"><mat-icon>visibility</mat-icon></button>
              <button mat-icon-button (click)="openForm(g); $event.stopPropagation()"><mat-icon>edit</mat-icon></button>
            </td>
          </ng-container>
          <tr mat-header-row *matHeaderRowDef="cols"></tr>
          <tr mat-row *matRowDef="let r; columns: cols;" class="clickable" [routerLink]="['/groups', r.id]"></tr>
          <tr class="mat-row" *matNoDataRow><td [colSpan]="cols.length" class="empty-row">No groups found</td></tr>
        </table>
        <mat-paginator [length]="total()" [pageSize]="20" [pageSizeOptions]="[10,20,50]" (page)="onPage($event)" showFirstLastButtons></mat-paginator>
      </mat-card-content>
    </mat-card>
  `,
  styles: [`.full-width{width:100%}.clickable{cursor:pointer}.clickable:hover{background:#f5f5f5}.empty-row{text-align:center;padding:32px;color:#999}`]
})
export class GroupListComponent implements OnInit {
  private readonly groupService = inject(GroupService);
  private readonly dialog = inject(MatDialog);
  readonly loading = signal(false);
  readonly groups = signal<Group[]>([]);
  readonly total = signal(0);
  readonly cols = ['group_name', 'visibility', 'member_count', 'status', 'actions'];
  private page = 1;

  ngOnInit(): void { this.load(); }

  load(): void {
    this.loading.set(true);
    this.groupService.list({ page: this.page, per_page: 20 }).subscribe({
      next: res => { this.groups.set(res.data); this.total.set(res.meta.total); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  onPage(e: PageEvent): void { this.page = e.pageIndex + 1; this.load(); }
  openForm(group?: Group): void {
    const ref = this.dialog.open(GroupFormDialogComponent, { width: '480px', data: group ?? null });
    ref.afterClosed().subscribe(saved => { if (saved) this.load(); });
  }
}
