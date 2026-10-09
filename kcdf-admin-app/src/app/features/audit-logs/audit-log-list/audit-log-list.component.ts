import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { debounceTime, distinctUntilChanged } from 'rxjs/operators';
import { ActivityLogService } from '../../../core/services/activity-log.service';
import { ActivityLog } from '../../../core/models';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { TAILWIND_DIALOG_DATA, TAILWIND_DIALOG_REF, TailwindDialogRef, TailwindDialogService } from '../../../shared/components/modal/tailwind-dialog.service';
import { TailwindPaginatorComponent, TailwindPageEvent } from '../../../shared/components/paginator/tailwind-paginator.component';

@Component({
  selector: 'app-log-diff-dialog',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="px-6 pt-6 text-xl font-semibold tracking-tight text-gray-900 dark:text-white">Audit Log Detail</div>
    <div class="px-6 py-4">
      <div class="mb-5 flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-700 dark:text-gray-200">
        <span><strong>Action:</strong> {{ log.action }}</span><span><strong>Entity:</strong> {{ log.entity_type }} #{{ log.entity_id }}</span><span><strong>Actor:</strong> {{ log.actor?.first_name ?? log.actor_type }} #{{ log.actor_id }}</span><span><strong>Time:</strong> {{ log.created_at | date:'dd MMM yyyy HH:mm:ss' }}</span>
      </div>
      <div class="grid min-w-0 grid-cols-1 gap-4 lg:min-w-[37.5rem] lg:grid-cols-2">
        <div>
          <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Old Values</p>
          <pre class="max-h-72 overflow-auto rounded-lg bg-gray-50 p-3 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-200">{{ log.old_values | json }}</pre>
        </div>
        <div>
          <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">New Values</p>
          <pre class="max-h-72 overflow-auto rounded-lg bg-gray-50 p-3 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-200">{{ log.new_values | json }}</pre>
        </div>
      </div>
    </div>
    <div class="flex justify-end px-6 pb-5">
      <button type="button" (click)="dialogRef.close()" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">Close</button>
    </div>
  `
})
export class LogDiffDialogComponent {
  readonly log = inject(TAILWIND_DIALOG_DATA) as ActivityLog;
  readonly dialogRef = inject(TAILWIND_DIALOG_REF) as TailwindDialogRef<void>;
}

@Component({
  selector: 'app-audit-log-list',
  standalone: true,
  imports: [
    CommonModule, ReactiveFormsModule,
    AppIconComponent, TailwindPaginatorComponent,
    PageHeaderComponent, LoadingOverlayComponent,
  ],
  template: `
    <app-loading-overlay [loading]="loading()"></app-loading-overlay>
    <app-page-header title="Audit Logs" [subtitle]="'Total: ' + total()"></app-page-header>
    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <div class="grid grid-cols-1 gap-4 border-b border-gray-200 p-4 sm:grid-cols-3 dark:border-gray-800">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Entity Type<input [formControl]="entityCtrl" placeholder="e.g. Family, Member" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" /></label>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Action<input [formControl]="actionCtrl" placeholder="e.g. create, update" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" /></label>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">From Date<input type="date" [formControl]="dateFromCtrl" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" /></label>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:bg-gray-800/60 dark:text-gray-300"><tr><th class="px-4 py-3">Actor</th><th class="px-4 py-3">Action</th><th class="px-4 py-3">Entity</th><th class="px-4 py-3">ID</th><th class="px-4 py-3">Time</th><th class="px-4 py-3"><span class="sr-only">Details</span></th></tr></thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            <tr *ngFor="let l of logs()" tabindex="0" (click)="viewDiff(l)" (keydown.enter)="viewDiff(l)" (keydown.space)="viewDiff(l); $event.preventDefault()" class="cursor-pointer text-gray-700 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500 dark:text-gray-200 dark:hover:bg-gray-800/50">
              <td class="whitespace-nowrap px-4 py-3">{{ l.actor?.first_name ?? l.actor_type }} {{ l.actor?.last_name ?? '' }}</td><td class="whitespace-nowrap px-4 py-3">{{ l.action }}</td><td class="whitespace-nowrap px-4 py-3">{{ l.entity_type }}</td><td class="whitespace-nowrap px-4 py-3">{{ l.entity_id ?? '—' }}</td><td class="whitespace-nowrap px-4 py-3">{{ l.created_at | date:'dd MMM yyyy HH:mm' }}</td>
              <td class="whitespace-nowrap px-4 py-3"><button type="button" (click)="viewDiff(l); $event.stopPropagation()" aria-label="View audit log details" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-indigo-600 dark:hover:bg-gray-800"><app-icon name="diff" aria-hidden="true" class="h-6 w-6"></app-icon></button></td>
            </tr>
            <tr *ngIf="!logs().length"><td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500 dark:text-gray-400">No audit logs found</td></tr>
          </tbody>
        </table>
      </div>
      <app-tailwind-paginator [length]="total()" [pageIndex]="page - 1" [pageSize]="pageSize()" [pageSizeOptions]="[10,20,50]" selectId="audit-log-page-size" (page)="onPage($event)"></app-tailwind-paginator>
    </section>
  `,
})
export class AuditLogListComponent implements OnInit {
  private readonly logService = inject(ActivityLogService);
  private readonly dialog = inject(TailwindDialogService);

  readonly loading = signal(false);
  readonly logs = signal<ActivityLog[]>([]);
  readonly total = signal(0);
  readonly entityCtrl = new FormControl('');
  readonly actionCtrl = new FormControl('');
  readonly dateFromCtrl = new FormControl('');
  page = 1;
  readonly pageSize = signal(20);

  ngOnInit(): void {
    this.load();
    [this.entityCtrl, this.actionCtrl, this.dateFromCtrl].forEach(ctrl =>
      ctrl.valueChanges.pipe(debounceTime(400), distinctUntilChanged()).subscribe(() => { this.page = 1; this.load(); })
    );
  }

  load(): void {
    this.loading.set(true);
    this.logService.list({
      page: this.page, per_page: this.pageSize(),
      entity_type: this.entityCtrl.value ?? undefined,
      action: this.actionCtrl.value ?? undefined,
      date_from: this.dateFromCtrl.value ?? undefined,
    }).subscribe({
      next: res => { this.logs.set(res.data); this.total.set(res.meta.total); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  onPage(e: TailwindPageEvent): void { this.page = e.pageIndex + 1; this.pageSize.set(e.pageSize); this.load(); }

  viewDiff(log: ActivityLog): void {
    this.dialog.open<LogDiffDialogComponent, ActivityLog, void>(LogDiffDialogComponent, { data: log, width: '680px', ariaLabel: 'Audit log detail' });
  }
}
