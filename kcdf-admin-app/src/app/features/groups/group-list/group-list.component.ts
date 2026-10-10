import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { GroupService } from '../../../core/services/group.service';
import { Group } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { ToastService } from '../../../shared/components/toast/toast.service';
import { TAILWIND_DIALOG_DATA, TAILWIND_DIALOG_REF, TailwindDialogRef, TailwindDialogService } from '../../../shared/components/modal/tailwind-dialog.service';
import { TailwindPaginatorComponent, TailwindPageEvent } from '../../../shared/components/paginator/tailwind-paginator.component';

@Component({
  selector: 'app-group-form-dialog',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './group-form-dialog.component.html',
  styleUrl: './group-form-dialog.component.scss'
})
export class GroupFormDialogComponent {
  readonly group = inject(TAILWIND_DIALOG_DATA) as Group | null;
  private readonly groupService = inject(GroupService);
  readonly dialogRef = inject(TAILWIND_DIALOG_REF) as TailwindDialogRef<boolean>;
  private readonly toastService = inject(ToastService);
  saving = false;
  readonly form = new FormGroup({
    group_name: new FormControl(this.group?.group_name ?? '', { nonNullable: true, validators: [Validators.required] }),
    description: new FormControl(this.group?.description ?? ''),
    visibility: new FormControl(this.group?.visibility ?? 'public', { nonNullable: true }),
  });
  save(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.saving = true;
    const obs = this.group ? this.groupService.update(this.group.id, this.form.getRawValue()) : this.groupService.create(this.form.getRawValue());
    obs.subscribe({
      next: () => { this.toastService.show('Group saved', { variant: 'success', durationMs: 3000, actionLabel: 'Close' }); this.dialogRef.close(true); },
      error: (err) => { this.saving = false; this.toastService.show(err?.error?.error?.message ?? 'Error', { variant: 'error', durationMs: 4000, actionLabel: 'Close' }); }
    });
  }
}

@Component({
  selector: 'app-group-list',
  standalone: true,
  imports: [CommonModule, RouterModule, ReactiveFormsModule, AppIconComponent, TailwindPaginatorComponent, StatusBadgeComponent, PageHeaderComponent, LoadingOverlayComponent],
  templateUrl: './group-list.component.html',
  styleUrl: './group-list.component.scss',
})
export class GroupListComponent implements OnInit {
  private readonly groupService = inject(GroupService);
  private readonly dialog = inject(TailwindDialogService);
  readonly loading = signal(false);
  readonly groups = signal<Group[]>([]);
  readonly total = signal(0);
  page = 1;
  readonly pageSize = signal(20);

  ngOnInit(): void { this.load(); }

  load(): void {
    this.loading.set(true);
    this.groupService.list({ page: this.page, per_page: this.pageSize() }).subscribe({
      next: res => { this.groups.set(res.data); this.total.set(res.meta.total); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  onPage(e: TailwindPageEvent): void { this.page = e.pageIndex + 1; this.pageSize.set(e.pageSize); this.load(); }
  openForm(group?: Group): void {
    const ref = this.dialog.open<GroupFormDialogComponent, Group | null, boolean>(GroupFormDialogComponent, { width: '480px', data: group ?? null, ariaLabel: group ? 'Edit group' : 'Create group' });
    ref.afterClosed().subscribe(saved => { if (saved) this.load(); });
  }
}
