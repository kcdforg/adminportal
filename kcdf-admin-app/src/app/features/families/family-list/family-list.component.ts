import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { debounceTime, distinctUntilChanged } from 'rxjs/operators';
import { FamilyService } from '../../../core/services/family.service';
import { Family } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { FamilyFormComponent } from '../family-form/family-form.component';
import { TailwindDialogService } from '../../../shared/components/modal/tailwind-dialog.service';
import { TailwindPaginatorComponent, TailwindPageEvent } from '../../../shared/components/paginator/tailwind-paginator.component';

@Component({
  selector: 'app-family-list',
  standalone: true,
  imports: [
    CommonModule, RouterModule, ReactiveFormsModule,
    TailwindPaginatorComponent, AppIconComponent,
    StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent,
  ],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Families" [subtitle]="'Total: ' + total()">
      <button type="button" (click)="openForm()"
        class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
        <app-icon name="add" aria-hidden="true" class="h-6 w-6"></app-icon> Add Family
      </button>
    </app-page-header>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <div class="flex flex-col gap-3 border-b border-gray-200 p-4 dark:border-gray-800 sm:flex-row sm:items-center">
        <div class="relative w-full sm:max-w-sm">
          <label for="family-search" class="sr-only">Search</label>
          <input id="family-search" type="text" [formControl]="searchCtrl"
            class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white py-2 pl-3 pr-10 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:placeholder:text-gray-500"
            placeholder="Search families..." />
          <app-icon name="search" class="pointer-events-none absolute right-3 top-1/2 !flex -translate-y-1/2 items-center !text-xl text-gray-400 h-5 w-5" aria-hidden="true"></app-icon>
        </div>
        <div>
          <label for="family-status" class="sr-only">Status</label>
          <select id="family-status" [formControl]="statusCtrl"
            class="min-h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white sm:w-48">
            <option value="">All statuses</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="suspended">Suspended</option>
          </select>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full min-w-[640px] text-left text-sm">
          <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500 dark:bg-gray-800/60 dark:text-gray-400">
            <tr>
              <th scope="col" class="px-5 py-3 font-medium">Code</th>
              <th scope="col" class="px-5 py-3 font-medium">Family Name</th>
              <th scope="col" class="px-5 py-3 font-medium">City</th>
              <th scope="col" class="px-5 py-3 font-medium">Members</th>
              <th scope="col" class="px-5 py-3 font-medium">Status</th>
              <th scope="col" class="px-5 py-3 text-right font-medium">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            <tr *ngFor="let f of families()" class="cursor-pointer text-gray-700 transition hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800/50"
              [routerLink]="['/families', f.id]">
              <td class="whitespace-nowrap px-5 py-4">{{ f.family_code }}</td>
              <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">{{ f.family_name }}</td>
              <td class="px-5 py-4">{{ f.address?.city ?? '—' }}</td>
              <td class="px-5 py-4">{{ f.member_count ?? '—' }}</td>
              <td class="px-5 py-4"><app-status-badge [status]="f.status"></app-status-badge></td>
              <td class="px-5 py-4 text-right">
                <button type="button" [routerLink]="['/families', f.id]"
                  class="mr-1 inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 transition hover:bg-gray-100 hover:text-indigo-600 focus-visible:outline-2 focus-visible:outline-indigo-600 dark:text-gray-400 dark:hover:bg-gray-700"
                  aria-label="View family"><app-icon name="visibility" aria-hidden="true" class="h-6 w-6"></app-icon></button>
                <button type="button" (click)="openForm(f); $event.stopPropagation()"
                  class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 transition hover:bg-gray-100 hover:text-indigo-600 focus-visible:outline-2 focus-visible:outline-indigo-600 dark:text-gray-400 dark:hover:bg-gray-700"
                  aria-label="Edit family"><app-icon name="edit" aria-hidden="true" class="h-6 w-6"></app-icon></button>
              </td>
            </tr>
            <tr *ngIf="families().length === 0">
              <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-500 dark:text-gray-400">No families found</td>
            </tr>
          </tbody>
        </table>
      </div>
      <app-tailwind-paginator [length]="total()" [pageIndex]="page - 1" [pageSize]="pageSize"
        [pageSizeOptions]="[10, 20, 50]" selectId="family-page-size" (page)="onPage($event)"></app-tailwind-paginator>
    </section>
  `,
})
export class FamilyListComponent implements OnInit {
  private readonly familyService = inject(FamilyService);
  private readonly dialog = inject(TailwindDialogService);

  readonly loading = signal(false);
  readonly families = signal<Family[]>([]);
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
    this.familyService.list({ page: this.page, per_page: this.pageSize, search: this.searchCtrl.value ?? undefined, status: this.statusCtrl.value ?? undefined }).subscribe({
      next: res => { this.families.set(res.data); this.total.set(res.meta.total); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  onPage(e: TailwindPageEvent): void { this.page = e.pageIndex + 1; this.pageSize = e.pageSize; this.load(); }

  openForm(family?: Family): void {
    const ref = this.dialog.open(FamilyFormComponent, {
      width: '560px',
      ariaLabel: family ? `Edit family ${family.family_name}` : 'Create family',
      data: family ?? null,
    });
    ref.afterClosed().subscribe(saved => { if (saved) this.load(); });
  }
}
