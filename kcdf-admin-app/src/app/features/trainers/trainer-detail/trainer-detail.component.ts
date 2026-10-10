import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { TailwindDialogService } from '../../../shared/components/modal/tailwind-dialog.service';
import { TrainerService } from '../../../core/services/trainer.service';
import { Trainer } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { TrainerFormComponent } from '../trainer-form/trainer-form.component';

@Component({
  selector: 'app-trainer-detail',
  standalone: true,
  imports: [CommonModule, RouterModule, StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent, AppIconComponent],
  templateUrl: './trainer-detail.component.html',
  styleUrl: './trainer-detail.component.scss'
})
export class TrainerDetailComponent implements OnInit {
  private readonly trainerService = inject(TrainerService);
  private readonly route = inject(ActivatedRoute);
  private readonly dialog = inject(TailwindDialogService);
  readonly loading = signal(false);
  readonly trainer = signal<Trainer | null>(null);
  trainerName = () => { const t = this.trainer(); return t ? `${t.member?.first_name ?? ''} ${t.member?.last_name ?? ''}`.trim() || t.trainer_code : 'Trainer'; };

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.loading.set(true);
    this.trainerService.get(id).subscribe({ next: res => { this.trainer.set(res.data); this.loading.set(false); }, error: () => this.loading.set(false) });
  }

  openEdit(): void {
    const ref = this.dialog.open<TrainerFormComponent, Trainer | null, boolean>(TrainerFormComponent, {
      width: '520px',
      data: this.trainer(),
      ariaLabel: 'Edit trainer',
    });
    ref.afterClosed().subscribe(saved => {
      if (saved) {
        const id = Number(this.route.snapshot.paramMap.get('id'));
        this.trainerService.get(id).subscribe(res => this.trainer.set(res.data));
      }
    });
  }
}
