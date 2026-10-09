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
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Programs" [subtitle]="'Total: ' + total()">
      <button type="button" (click)="openForm()" class="inline-flex min-h-10 items-center justify-center gap-1 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"><app-icon name="add" class="h-6 w-6"></app-icon> Add Program</button>
    </app-page-header>
    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <div class="border-b border-gray-200 p-4 dark:border-gray-800 sm:p-6">
        <div class="filters">
          <div class="filter-field">
            <label for="program-status-filter" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200">Status</label>
            <select id="program-status-filter" [formControl]="statusCtrl" class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white">
              <option value="">All</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="archived">Archived</option>
            </select>
          </div>
        </div>
      </div>
      <div class="w-full overflow-x-auto">
        <table class="w-full min-w-[640px] text-left text-sm">
          <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500 dark:bg-gray-800/60 dark:text-gray-400">
            <tr>
              <th scope="col" class="px-4 py-3 font-semibold">Name</th>
              <th scope="col" class="px-4 py-3 font-semibold">Type</th>
              <th scope="col" class="px-4 py-3 font-semibold">Fee</th>
              <th scope="col" class="px-4 py-3 font-semibold">Status</th>
              <th scope="col" class="px-4 py-3 font-semibold">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            <tr *ngFor="let p of programs()" class="text-gray-700 dark:text-gray-200">
              <td class="px-4 py-3">{{ p.name }}</td>
              <td class="px-4 py-3">{{ p.program_type }}</td>
              <td class="px-4 py-3">{{ p.fee_amount | currencyInr }}</td>
              <td class="px-4 py-3"><app-status-badge [status]="p.status"></app-status-badge></td>
              <td class="px-4 py-3">
                <button type="button" (click)="openForm(p)" aria-label="Edit program" class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-lg text-gray-600 transition hover:bg-gray-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:text-gray-300 dark:hover:bg-gray-800"><app-icon name="edit" class="h-6 w-6"></app-icon></button>
              </td>
            </tr>
            <tr *ngIf="programs().length === 0">
              <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500 dark:text-gray-400">No programs found</td>
            </tr>
          </tbody>
        </table>
      </div>
      <app-tailwind-paginator [length]="total()" [pageIndex]="page - 1" [pageSize]="pageSize"
        [pageSizeOptions]="[10, 20, 50]" selectId="program-page-size" (page)="onPage($event)"></app-tailwind-paginator>
    </section>
  `,
  styles: [`.filters{display:flex;flex-wrap:wrap;gap:16px}.filter-field{min-width:200px}`]
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
