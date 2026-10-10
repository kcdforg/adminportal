import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { debounceTime, distinctUntilChanged } from 'rxjs/operators';
import { TrainerService } from '../../../core/services/trainer.service';
import { Trainer } from '../../../core/models';
import { TailwindDialogService } from '../../../shared/components/modal/tailwind-dialog.service';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { TrainerFormComponent } from '../trainer-form/trainer-form.component';
import { TailwindPaginatorComponent, TailwindPageEvent } from '../../../shared/components/paginator/tailwind-paginator.component';

@Component({
  selector: 'app-trainer-list',
  standalone: true,
  imports: [
    CommonModule, RouterModule, ReactiveFormsModule,
    TailwindPaginatorComponent,
    StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent, AppIconComponent,
  ],
  templateUrl: './trainer-list.component.html',
  styleUrl: './trainer-list.component.scss'
})
export class TrainerListComponent implements OnInit {
  private readonly trainerService = inject(TrainerService);
  private readonly dialog = inject(TailwindDialogService);
  readonly loading = signal(false);
  readonly trainers = signal<Trainer[]>([]);
  readonly total = signal(0);
  readonly searchCtrl = new FormControl('');
  readonly statusCtrl = new FormControl('');
  pageSize = 20;
  page = 1;

  ngOnInit(): void {
    this.load();
    this.searchCtrl.valueChanges.pipe(debounceTime(400), distinctUntilChanged()).subscribe(() => { this.page = 1; this.load(); });
    this.statusCtrl.valueChanges.subscribe(() => { this.page = 1; this.load(); });
  }

  load(): void {
    this.loading.set(true);
    this.trainerService.list({ page: this.page, per_page: this.pageSize, search: this.searchCtrl.value ?? undefined, status: this.statusCtrl.value ?? undefined }).subscribe({
      next: res => { this.trainers.set(res.data); this.total.set(res.meta.total); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  onPage(e: TailwindPageEvent): void { this.page = e.pageIndex + 1; this.pageSize = e.pageSize; this.load(); }
  openForm(trainer?: Trainer): void {
    const ref = this.dialog.open<TrainerFormComponent, Trainer | null, boolean>(TrainerFormComponent, {
      width: '520px',
      data: trainer ?? null,
      ariaLabel: trainer ? 'Edit trainer' : 'Add trainer',
    });
    ref.afterClosed().subscribe(saved => { if (saved) this.load(); });
  }
}
