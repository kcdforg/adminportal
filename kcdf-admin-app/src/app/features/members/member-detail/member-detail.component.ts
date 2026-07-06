import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatChipsModule } from '@angular/material/chips';
import { MatDialog } from '@angular/material/dialog';
import { MatDividerModule } from '@angular/material/divider';
import { MemberService } from '../../../core/services/member.service';
import { MemberProfile } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { MemberFormComponent } from '../member-form/member-form.component';

@Component({
  selector: 'app-member-detail',
  standalone: true,
  imports: [
    CommonModule,
    RouterModule,
    MatCardModule,
    MatButtonModule,
    MatIconModule,
    MatChipsModule,
    MatDividerModule,
    StatusBadgeComponent,
    PageHeaderComponent,
    LoadingOverlayComponent,
  ],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header [title]="memberName()" subtitle="Member Profile">
      <button mat-stroked-button routerLink="/members"><mat-icon>arrow_back</mat-icon> Back</button>
      <button mat-flat-button color="primary" (click)="openEdit()"><mat-icon>edit</mat-icon> Edit</button>
    </app-page-header>

    <div class="detail-grid" *ngIf="member()">
      <mat-card>
        <mat-card-header><mat-card-title>Personal Information</mat-card-title></mat-card-header>
        <mat-card-content>
          <div class="info-grid">
            <div class="info-item"><span class="info-label">First Name</span><span>{{ member()!.first_name }}</span></div>
            <div class="info-item"><span class="info-label">Last Name</span><span>{{ member()!.last_name }}</span></div>
            <div class="info-item"><span class="info-label">Email</span><span>{{ member()!.email ?? '—' }}</span></div>
            <div class="info-item"><span class="info-label">Mobile</span><span>{{ member()!.mobile ?? '—' }}</span></div>
            <div class="info-item"><span class="info-label">Gender</span><span>{{ member()!.gender ?? '—' }}</span></div>
            <div class="info-item"><span class="info-label">Date of Birth</span><span>{{ (member()!.date_of_birth | date:'dd MMM yyyy') ?? '—' }}</span></div>
            <div class="info-item"><span class="info-label">Status</span><app-status-badge [status]="member()!.status"></app-status-badge></div>
            <div class="info-item"><span class="info-label">Has Login</span>
              <mat-icon [style.color]="member()!.has_login ? '#2e7d32' : '#c62828'">
                {{ member()!.has_login ? 'check_circle' : 'cancel' }}
              </mat-icon>
            </div>
            <div class="info-item"><span class="info-label">Member Since</span><span>{{ member()!.created_at | date:'dd MMM yyyy' }}</span></div>
          </div>
        </mat-card-content>
      </mat-card>
    </div>
  `,
  styles: [`
    .detail-grid { display: grid; gap: 16px; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; padding: 8px 0; }
    .info-item { display: flex; flex-direction: column; gap: 4px; }
    .info-label { font-size: 12px; color: #666; text-transform: uppercase; font-weight: 600; }
  `]
})
export class MemberDetailComponent implements OnInit {
  private readonly memberService = inject(MemberService);
  private readonly route = inject(ActivatedRoute);
  private readonly dialog = inject(MatDialog);

  readonly loading = signal(false);
  readonly member = signal<MemberProfile | null>(null);

  memberName = () => {
    const m = this.member();
    return m ? `${m.first_name} ${m.last_name}` : 'Loading...';
  };

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.loading.set(true);
    this.memberService.get(id).subscribe({
      next: (res) => { this.member.set(res.data); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  openEdit(): void {
    const ref = this.dialog.open(MemberFormComponent, { width: '520px', data: this.member() });
    ref.afterClosed().subscribe(saved => {
      if (saved) {
        const id = Number(this.route.snapshot.paramMap.get('id'));
        this.memberService.get(id).subscribe(res => this.member.set(res.data));
      }
    });
  }
}
