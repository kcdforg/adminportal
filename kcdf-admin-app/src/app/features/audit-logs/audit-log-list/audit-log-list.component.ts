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
  templateUrl: './log-diff-dialog.component.html',
  styleUrl: './log-diff-dialog.component.scss'
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
  templateUrl: './audit-log-list.component.html',
  styleUrl: './audit-log-list.component.scss',
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
