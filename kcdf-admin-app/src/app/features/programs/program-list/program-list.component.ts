import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { ProgramService } from '../../../core/services/program.service';
import { Program } from '../../../core/models';
import { TailwindDialogService } from '../../../shared/components/modal/tailwind-dialog.service';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { CurrencyInrPipe } from '../../../shared/pipes/currency-inr.pipe';
import { ProgramFormComponent } from '../program-form/program-form.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { TailwindPaginatorComponent, TailwindPageEvent } from '../../../shared/components/paginator/tailwind-paginator.component';

@Component({
  selector: 'app-program-list',
  standalone: true,
  imports: [
    CommonModule, ReactiveFormsModule,
    TailwindPaginatorComponent,
    StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent, CurrencyInrPipe, AppIconComponent,
  ],
  templateUrl: './program-list.component.html',
  styleUrl: './program-list.component.scss'
})
export class ProgramListComponent implements OnInit {
  private readonly programService = inject(ProgramService);
  private readonly dialog = inject(TailwindDialogService);
  readonly loading = signal(false);
  readonly programs = signal<Program[]>([]);
  readonly total = signal(0);
  readonly statusCtrl = new FormControl('');
  pageSize = 20;
  page = 1;

  ngOnInit(): void {
    this.load();
    this.statusCtrl.valueChanges.subscribe(() => { this.page = 1; this.load(); });
  }

  load(): void {
    this.loading.set(true);
    this.programService.list({ page: this.page, per_page: this.pageSize, status: this.statusCtrl.value ?? undefined }).subscribe({
      next: res => { this.programs.set(res.data); this.total.set(res.meta.total); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  onPage(e: TailwindPageEvent): void { this.page = e.pageIndex + 1; this.pageSize = e.pageSize; this.load(); }
  openForm(program?: Program): void {
    const ref = this.dialog.open<ProgramFormComponent, Program | null, boolean>(ProgramFormComponent, {
      width: '520px',
      data: program ?? null,
      ariaLabel: program ? 'Edit program' : 'Add program',
    });
    ref.afterClosed().subscribe(saved => { if (saved) this.load(); });
  }
}
