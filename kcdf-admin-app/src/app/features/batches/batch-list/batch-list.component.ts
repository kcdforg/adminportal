import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { BatchService } from '../../../core/services/batch.service';
import { Batch } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { TailwindPaginatorComponent, TailwindPageEvent } from '../../../shared/components/paginator/tailwind-paginator.component';

@Component({
  selector: 'app-batch-list',
  standalone: true,
  imports: [
    CommonModule, RouterModule, ReactiveFormsModule,
    TailwindPaginatorComponent,
    StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent, AppIconComponent,
  ],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Batches" [subtitle]="'Total: ' + total()">
      <button type="button" class="inline-flex min-h-10 items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600" routerLink="/batches/new"><app-icon name="add" class="h-6 w-6"></app-icon> New Batch</button>
    </app-page-header>
    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <div class="border-b border-gray-200 p-4 dark:border-gray-800 sm:p-6">
        <div class="filters">
          <label for="batch-status-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Status
            <select id="batch-status-filter" [formControl]="statusCtrl" class="mt-1 block w-full min-w-[200px] rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
              <option value="">All</option>
              <option value="upcoming">Upcoming</option>
              <option value="active">Active</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </label>
        </div>
      </div>
      <div class="w-full overflow-x-auto">
        <table class="w-full min-w-[800px] text-left text-sm">
          <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500 dark:bg-gray-800/60 dark:text-gray-400">
            <tr>
              <th scope="col" class="px-4 py-3 font-semibold">Batch Name</th>
              <th scope="col" class="px-4 py-3 font-semibold">Program</th>
              <th scope="col" class="px-4 py-3 font-semibold">Trainer</th>
              <th scope="col" class="px-4 py-3 font-semibold">Start Date</th>
              <th scope="col" class="px-4 py-3 font-semibold">Capacity</th>
              <th scope="col" class="px-4 py-3 font-semibold">Status</th>
              <th scope="col" class="px-4 py-3 font-semibold">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            <tr *ngFor="let b of batches()" class="cursor-pointer text-gray-700 transition hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-800/50"
              [routerLink]="['/batches', b.id]">
              <td class="px-4 py-3">{{ b.batch_name }}</td>
              <td class="px-4 py-3">{{ b.program?.name ?? '—' }}</td>
              <td class="px-4 py-3">{{ b.trainer?.member?.first_name ?? '—' }}</td>
              <td class="px-4 py-3">{{ b.start_date | date:'dd MMM yyyy' }}</td>
              <td class="px-4 py-3">{{ b.enrolled_count ?? 0 }}/{{ b.capacity }}</td>
              <td class="px-4 py-3"><app-status-badge [status]="b.status"></app-status-badge></td>
              <td class="px-4 py-3">
                <button type="button" [routerLink]="['/batches', b.id]" aria-label="View batch" class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-lg text-gray-600 transition hover:bg-gray-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:text-gray-300 dark:hover:bg-gray-800"><app-icon name="visibility" class="h-6 w-6"></app-icon></button>
                <button type="button" [routerLink]="['/batches', b.id, 'edit']" aria-label="Edit batch" class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-lg text-gray-600 transition hover:bg-gray-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:text-gray-300 dark:hover:bg-gray-800"><app-icon name="edit" class="h-6 w-6"></app-icon></button>
              </td>
            </tr>
            <tr *ngIf="batches().length === 0">
              <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500 dark:text-gray-400">No batches found</td>
            </tr>
          </tbody>
        </table>
      </div>
      <app-tailwind-paginator [length]="total()" [pageIndex]="page - 1" [pageSize]="pageSize"
        [pageSizeOptions]="[10, 20, 50]" selectId="batch-page-size" (page)="onPage($event)"></app-tailwind-paginator>
    </section>
  `,
  styles: [`.filters{display:flex;flex-wrap:wrap;gap:16px}`]
})
export class BatchListComponent implements OnInit {
  private readonly batchService = inject(BatchService);
  readonly loading = signal(false);
  readonly batches = signal<Batch[]>([]);
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
    this.batchService.list({ page: this.page, per_page: this.pageSize, status: this.statusCtrl.value ?? undefined }).subscribe({
      next: res => { this.batches.set(res.data); this.total.set(res.meta.total); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  onPage(e: TailwindPageEvent): void { this.page = e.pageIndex + 1; this.pageSize = e.pageSize; this.load(); }
}
