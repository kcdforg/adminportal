import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { debounceTime, distinctUntilChanged } from 'rxjs/operators';
import { MemberService } from '../../../core/services/member.service';
import { MemberProfile } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { TooltipDirective } from '../../../shared/directives/tooltip.directive';
import { MemberFormComponent } from '../member-form/member-form.component';
import { TailwindDialogService } from '../../../shared/components/modal/tailwind-dialog.service';
import { TailwindPaginatorComponent, TailwindPageEvent } from '../../../shared/components/paginator/tailwind-paginator.component';

@Component({
  selector: 'app-member-list',
  standalone: true,
  imports: [
    CommonModule,
    RouterModule,
    ReactiveFormsModule,
    TailwindPaginatorComponent,
    AppIconComponent,
    TooltipDirective,
    StatusBadgeComponent,
    PageHeaderComponent,
    LoadingOverlayComponent,
  ],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Members" [subtitle]="'Total: ' + total()">
      <button type="button" (click)="openForm()"
        class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
        <app-icon name="add" aria-hidden="true" class="h-6 w-6"></app-icon> Add Member
      </button>
    </app-page-header>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <div class="flex flex-col gap-3 border-b border-gray-200 p-4 dark:border-gray-800 sm:flex-row sm:items-center">
        <div class="relative w-full sm:max-w-sm">
          <label for="member-search" class="sr-only">Search by name</label>
          <input id="member-search" type="text" [formControl]="searchCtrl" placeholder="Type to search..."
            class="block min-h-10 w-full rounded-lg border border-gray-300 bg-white py-2 pl-3 pr-10 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-950 dark:text-white dark:placeholder:text-gray-500" />
          <app-icon name="search" class="pointer-events-none absolute right-3 top-1/2 !flex -translate-y-1/2 items-center !text-xl text-gray-400 h-5 w-5" aria-hidden="true"></app-icon>
        </div>
        <div>
          <label for="member-status" class="sr-only">Status</label>
          <select id="member-status" [formControl]="statusCtrl"
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
              <th scope="col" class="px-5 py-3 font-medium">Name</th>
              <th scope="col" class="px-5 py-3 font-medium">Email</th>
              <th scope="col" class="px-5 py-3 font-medium">Mobile</th>
              <th scope="col" class="px-5 py-3 font-medium">Status</th>
              <th scope="col" class="px-5 py-3 text-right font-medium">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            <tr *ngFor="let row of members()" class="cursor-pointer text-gray-700 transition hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800/50"
              [routerLink]="['/members', row.id]">
              <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">{{ row.first_name }} {{ row.last_name }}</td>
              <td class="px-5 py-4">{{ row.email ?? '—' }}</td>
              <td class="px-5 py-4">{{ row.mobile ?? '—' }}</td>
              <td class="px-5 py-4"><app-status-badge [status]="row.status"></app-status-badge></td>
              <td class="px-5 py-4 text-right">
                <button type="button" [routerLink]="['/members', row.id]" [appTooltip]="'View details'"
                  class="mr-1 inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 transition hover:bg-gray-100 hover:text-indigo-600 focus-visible:outline-2 focus-visible:outline-indigo-600 dark:text-gray-400 dark:hover:bg-gray-700"
                  aria-label="View details"><app-icon name="visibility" aria-hidden="true" class="h-6 w-6"></app-icon></button>
                <button type="button" (click)="openForm(row)" [appTooltip]="'Edit'"
                  class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 transition hover:bg-gray-100 hover:text-indigo-600 focus-visible:outline-2 focus-visible:outline-indigo-600 dark:text-gray-400 dark:hover:bg-gray-700"
                  aria-label="Edit member"><app-icon name="edit" aria-hidden="true" class="h-6 w-6"></app-icon></button>
              </td>
            </tr>
            <tr *ngIf="members().length === 0">
              <td colspan="5" class="px-5 py-12 text-center text-sm text-gray-500 dark:text-gray-400">No members found</td>
            </tr>
          </tbody>
        </table>
      </div>
      <app-tailwind-paginator [length]="total()" [pageIndex]="page - 1" [pageSize]="pageSize"
        [pageSizeOptions]="[10, 20, 50]" selectId="member-page-size" (page)="onPage($event)"></app-tailwind-paginator>
    </section>
  `,
})
export class MemberListComponent implements OnInit {
  private readonly memberService = inject(MemberService);
  private readonly dialog = inject(TailwindDialogService);

  readonly loading = signal(false);
  readonly members = signal<MemberProfile[]>([]);
  readonly total = signal(0);
  pageSize = 20;
  page = 1;

  readonly searchCtrl = new FormControl('');
  readonly statusCtrl = new FormControl('');

  ngOnInit(): void {
    this.load();
    this.searchCtrl.valueChanges.pipe(debounceTime(400), distinctUntilChanged()).subscribe(() => {
      this.page = 1;
      this.load();
    });
    this.statusCtrl.valueChanges.subscribe(() => {
      this.page = 1;
      this.load();
    });
  }

  load(): void {
    this.loading.set(true);
    this.memberService.list({
      page: this.page,
      per_page: this.pageSize,
      search: this.searchCtrl.value ?? undefined,
      status: this.statusCtrl.value ?? undefined,
    }).subscribe({
      next: (res) => {
        this.members.set(res.data);
        this.total.set(res.meta.total);
        this.loading.set(false);
      },
      error: () => this.loading.set(false)
    });
  }

  onPage(event: TailwindPageEvent): void {
    this.page = event.pageIndex + 1;
    this.pageSize = event.pageSize;
    this.load();
  }

  openForm(member?: MemberProfile): void {
    const ref = this.dialog.open(MemberFormComponent, {
      width: '520px',
      ariaLabel: member ? `Edit member ${member.first_name} ${member.last_name}` : 'Add member',
      data: member ?? null,
    });
    ref.afterClosed().subscribe(saved => { if (saved) this.load(); });
  }
}
