import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { debounceTime, distinctUntilChanged } from 'rxjs/operators';
import { FamilyService } from '../../../core/services/family.service';
import { Family } from '../../../core/models';
import { authStore } from '../../../core/store/auth.store';
import { ToastService } from '../../../shared/components/toast/toast.service';
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
  templateUrl: './family-list.component.html',
  styleUrl: './family-list.component.scss',
})
export class FamilyListComponent implements OnInit {
  private readonly familyService = inject(FamilyService);
  private readonly dialog = inject(TailwindDialogService);
  private readonly toast = inject(ToastService);

  readonly loading = signal(false);
  readonly canCreateFamily = authStore.canAccess(['super_admin', 'program_manager']);
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
      next: res => {
        if (!Array.isArray(res.data) || !Number.isFinite(res.meta?.total)) {
          console.error('Failed to load families: API response must include an array of data and a numeric total.');
          this.toast.show('Unable to load families. Please try again.', { variant: 'error', durationMs: 4000 });
          this.loading.set(false);
          return;
        }
        this.families.set(res.data);
        this.total.set(res.meta.total);
        this.loading.set(false);
      },
      error: () => {
        this.loading.set(false);
        this.toast.show('Unable to load families. Please try again.', { variant: 'error', durationMs: 4000 });
      }
    });
  }

  onPage(e: TailwindPageEvent): void { this.page = e.pageIndex + 1; this.pageSize = e.pageSize; this.load(); }

  openForm(family?: Family): void {
    if (!family && !this.canCreateFamily()) {
      this.toast.show('Only super admins and program managers can create families.', {
        variant: 'warning',
        durationMs: 4000,
        actionLabel: 'Close',
      });
      return;
    }

    const ref = this.dialog.open(FamilyFormComponent, {
      width: '560px',
      ariaLabel: family ? `Edit family ${family.family_name}` : 'Create family',
      data: family ?? null,
    });
    ref.afterClosed().subscribe(saved => { if (saved) this.load(); });
  }
}
