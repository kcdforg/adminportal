import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatTableModule } from '@angular/material/table';
import { MatDialog } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { forkJoin } from 'rxjs';
import { GroupService } from '../../../core/services/group.service';
import { Group, GroupMember } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { ConfirmDialogComponent } from '../../../shared/components/confirm-dialog/confirm-dialog.component';

@Component({
  selector: 'app-group-detail',
  standalone: true,
  imports: [CommonModule, RouterModule, MatCardModule, MatButtonModule, MatIconModule, MatTableModule, StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header [title]="group()?.group_name ?? 'Group'" subtitle="Group Detail">
      <button mat-stroked-button routerLink="/groups"><mat-icon>arrow_back</mat-icon> Back</button>
    </app-page-header>

    <div class="detail-grid" *ngIf="group()">
      <mat-card>
        <mat-card-header><mat-card-title>Info</mat-card-title></mat-card-header>
        <mat-card-content>
          <div class="info-grid">
            <div class="info-item"><span class="info-label">Name</span><span>{{ group()!.group_name }}</span></div>
            <div class="info-item"><span class="info-label">Visibility</span><span>{{ group()!.visibility }}</span></div>
            <div class="info-item"><span class="info-label">Status</span><app-status-badge [status]="group()!.status"></app-status-badge></div>
            <div class="info-item"><span class="info-label">Members</span><span>{{ members().length }}</span></div>
            <div class="info-item" style="grid-column:1/-1"><span class="info-label">Description</span><span>{{ group()!.description ?? '—' }}</span></div>
          </div>
        </mat-card-content>
      </mat-card>

      <mat-card>
        <mat-card-header><mat-card-title>Members</mat-card-title></mat-card-header>
        <mat-card-content>
          <table mat-table [dataSource]="members()" class="full-width">
            <ng-container matColumnDef="name"><th mat-header-cell *matHeaderCellDef>Member</th><td mat-cell *matCellDef="let m">{{ m.member?.first_name }} {{ m.member?.last_name }}</td></ng-container>
            <ng-container matColumnDef="joined"><th mat-header-cell *matHeaderCellDef>Joined</th><td mat-cell *matCellDef="let m">{{ m.joined_at | date:'dd MMM yyyy' }}</td></ng-container>
            <ng-container matColumnDef="banned"><th mat-header-cell *matHeaderCellDef>Banned</th>
              <td mat-cell *matCellDef="let m"><mat-icon [style.color]="m.is_banned ? '#c62828' : '#999'">{{ m.is_banned ? 'block' : 'check_circle' }}</mat-icon></td>
            </ng-container>
            <ng-container matColumnDef="actions"><th mat-header-cell *matHeaderCellDef>Actions</th>
              <td mat-cell *matCellDef="let m">
                <button mat-icon-button (click)="banMember(m)" [disabled]="m.is_banned" matTooltip="Ban"><mat-icon>block</mat-icon></button>
                <button mat-icon-button color="warn" (click)="removeMember(m)" matTooltip="Remove"><mat-icon>person_remove</mat-icon></button>
              </td>
            </ng-container>
            <tr mat-header-row *matHeaderRowDef="cols"></tr>
            <tr mat-row *matRowDef="let r; columns: cols;"></tr>
            <tr class="mat-row" *matNoDataRow><td [colSpan]="cols.length" class="empty-row">No members</td></tr>
          </table>
        </mat-card-content>
      </mat-card>
    </div>
  `,
  styles: [`.detail-grid{display:grid;gap:16px}.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;padding:8px 0}.info-item{display:flex;flex-direction:column;gap:4px}.info-label{font-size:12px;color:#666;text-transform:uppercase;font-weight:600}.full-width{width:100%}.empty-row{text-align:center;padding:24px;color:#999}`]
})
export class GroupDetailComponent implements OnInit {
  private readonly groupService = inject(GroupService);
  private readonly route = inject(ActivatedRoute);
  private readonly dialog = inject(MatDialog);
  private readonly snackBar = inject(MatSnackBar);
  readonly loading = signal(false);
  readonly group = signal<Group | null>(null);
  readonly members = signal<GroupMember[]>([]);
  readonly cols = ['name', 'joined', 'banned', 'actions'];

  ngOnInit(): void { this.load(); }

  load(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.loading.set(true);
    forkJoin({ group: this.groupService.get(id), members: this.groupService.getMembers(id) }).subscribe({
      next: ({ group, members }) => { this.group.set(group.data); this.members.set(members.data); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  banMember(member: GroupMember): void {
    const ref = this.dialog.open(ConfirmDialogComponent, { data: { title: 'Ban Member', message: 'Ban this member from the group?', danger: true } });
    ref.afterClosed().subscribe(c => {
      if (!c) return;
      this.groupService.banMember(this.group()!.id, member.member_id).subscribe({ next: () => { this.snackBar.open('Member banned', 'Close', { duration: 2000 }); this.load(); } });
    });
  }

  removeMember(member: GroupMember): void {
    const ref = this.dialog.open(ConfirmDialogComponent, { data: { title: 'Remove Member', message: 'Remove this member from the group?', danger: true } });
    ref.afterClosed().subscribe(c => {
      if (!c) return;
      this.groupService.removeMember(this.group()!.id, member.member_id).subscribe({ next: () => { this.snackBar.open('Member removed', 'Close', { duration: 2000 }); this.load(); } });
    });
  }
}
