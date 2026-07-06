import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatDialog } from '@angular/material/dialog';
import { TrainerService } from '../../../core/services/trainer.service';
import { Trainer } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { TrainerFormComponent } from '../trainer-form/trainer-form.component';

@Component({
  selector: 'app-trainer-detail',
  standalone: true,
  imports: [CommonModule, RouterModule, MatCardModule, MatButtonModule, MatIconModule, StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header [title]="trainerName()" subtitle="Trainer Profile">
      <button mat-stroked-button routerLink="/trainers"><mat-icon>arrow_back</mat-icon> Back</button>
      <button mat-flat-button color="primary" (click)="openEdit()"><mat-icon>edit</mat-icon> Edit</button>
    </app-page-header>
    <mat-card *ngIf="trainer()">
      <mat-card-content>
        <div class="info-grid">
          <div class="info-item"><span class="info-label">Trainer Code</span><span>{{ trainer()!.trainer_code }}</span></div>
          <div class="info-item"><span class="info-label">Name</span><span>{{ trainer()!.member?.first_name }} {{ trainer()!.member?.last_name }}</span></div>
          <div class="info-item"><span class="info-label">Specialization</span><span>{{ trainer()!.specialization ?? '—' }}</span></div>
          <div class="info-item"><span class="info-label">Status</span><app-status-badge [status]="trainer()!.status"></app-status-badge></div>
          <div class="info-item" style="grid-column:1/-1"><span class="info-label">Bio</span><span>{{ trainer()!.bio ?? '—' }}</span></div>
        </div>
      </mat-card-content>
    </mat-card>
  `,
  styles: [`.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;padding:8px 0}.info-item{display:flex;flex-direction:column;gap:4px}.info-label{font-size:12px;color:#666;text-transform:uppercase;font-weight:600}`]
})
export class TrainerDetailComponent implements OnInit {
  private readonly trainerService = inject(TrainerService);
  private readonly route = inject(ActivatedRoute);
  private readonly dialog = inject(MatDialog);
  readonly loading = signal(false);
  readonly trainer = signal<Trainer | null>(null);
  trainerName = () => { const t = this.trainer(); return t ? `${t.member?.first_name ?? ''} ${t.member?.last_name ?? ''}`.trim() || t.trainer_code : 'Trainer'; };

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.loading.set(true);
    this.trainerService.get(id).subscribe({ next: res => { this.trainer.set(res.data); this.loading.set(false); }, error: () => this.loading.set(false) });
  }

  openEdit(): void {
    const ref = this.dialog.open(TrainerFormComponent, { width: '520px', data: this.trainer() });
    ref.afterClosed().subscribe(saved => {
      if (saved) {
        const id = Number(this.route.snapshot.paramMap.get('id'));
        this.trainerService.get(id).subscribe(res => this.trainer.set(res.data));
      }
    });
  }
}
