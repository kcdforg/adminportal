import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterModule } from '@angular/router';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { GroupService } from '../../../core/services/group.service';
import { Group, GroupCategory } from '../../../core/models';
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
  readonly dialogRef = inject(TAILWIND_DIALOG_REF) as TailwindDialogRef<boolean | number>;
  private readonly toastService = inject(ToastService);
  readonly categories = signal<GroupCategory[]>([]);
  readonly lookupError = signal('');
  saving = false;
  readonly form = new FormGroup({
    group_name: new FormControl(this.group?.group_name ?? '', { nonNullable: true, validators: [Validators.required, Validators.maxLength(255)] }),
    display_name: new FormControl(this.group?.display_name ?? '', { nonNullable: true, validators: [Validators.maxLength(255)] }),
    description: new FormControl(this.group?.description ?? '', { validators: [Validators.maxLength(20000)] }),
    category_id: new FormControl<number | null>(this.group?.category_id ?? null),
  });

  constructor() {
    this.groupService.getCategories().subscribe({
      next: (response) => this.categories.set(response.data.filter((category) => category.status === 'active')),
      error: () => this.lookupError.set('Group categories could not be loaded.'),
    });
  }

  save(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }
    this.saving = true;
    const values = this.form.getRawValue();
    const obs = this.group
      ? this.groupService.update(this.group.id, {
          group_name: values.group_name,
          display_name: values.display_name || null,
          description: values.description || null,
          category_id: values.category_id,
        })
      : this.groupService.create({
          group_name: values.group_name,
          display_name: values.display_name || null,
          description: values.description || null,
          category_id: values.category_id,
        });
    obs.subscribe({
      next: (response) => {
        this.toastService.show('Group saved', { variant: 'success', durationMs: 3000, actionLabel: 'Close' });
        this.dialogRef.close(this.group ? true : response.data.id);
      },
      error: (err) => {
        this.saving = false;
        this.toastService.show(err?.error?.error?.message ?? 'Group could not be saved.', { variant: 'error', durationMs: 4000, actionLabel: 'Close' });
      }
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
  private readonly router = inject(Router);
  readonly loading = signal(false);
  readonly groups = signal<Group[]>([]);
  readonly total = signal(0);
  readonly loadError = signal('');
  readonly categories = signal<GroupCategory[]>([]);
  readonly categoriesOpen = signal(false);
  readonly categoryName = signal('');
  readonly categoryDescription = signal('');
  readonly categoryError = signal('');
  readonly categorySaving = signal(false);
  readonly editingCategoryId = signal<number | null>(null);
  readonly editingCategoryName = signal('');
  readonly editingCategoryDescription = signal('');
  page = 1;
  readonly pageSize = signal(20);

  ngOnInit(): void {
    this.load();
    this.loadCategories();
  }

  load(): void {
    this.loading.set(true);
    this.loadError.set('');
    this.groupService.list({ page: this.page, per_page: this.pageSize() }).subscribe({
      next: res => { this.groups.set(res.data); this.total.set(res.meta.total); this.loading.set(false); },
      error: () => {
        this.loadError.set('Groups could not be loaded.');
        this.loading.set(false);
      }
    });
  }

  onPage(e: TailwindPageEvent): void { this.page = e.pageIndex + 1; this.pageSize.set(e.pageSize); this.load(); }

  loadCategories(): void {
    this.groupService.getCategories().subscribe({
      next: (response) => this.categories.set(response.data),
      error: () => this.categoryError.set('Group categories could not be loaded.'),
    });
  }

  saveCategory(category?: GroupCategory): void {
    const categoryName = (category ? this.editingCategoryName() : this.categoryName()).trim();
    const description = (category ? this.editingCategoryDescription() : this.categoryDescription()).trim();
    if (!categoryName) {
      this.categoryError.set('Enter a category name.');
      return;
    }
    this.categorySaving.set(true);
    this.categoryError.set('');
    const request = category
      ? this.groupService.updateCategory(category.id, { category_name: categoryName, description: description || null })
      : this.groupService.createCategory({ category_name: categoryName, description: description || null });
    request.subscribe({
      next: () => {
        this.categorySaving.set(false);
        this.categoryName.set('');
        this.categoryDescription.set('');
        this.editingCategoryId.set(null);
        this.loadCategories();
      },
      error: (error) => {
        this.categorySaving.set(false);
        this.categoryError.set(error?.error?.error?.message ?? 'Category could not be saved.');
      },
    });
  }

  editCategory(category: GroupCategory): void {
    this.editingCategoryId.set(category.id);
    this.editingCategoryName.set(category.category_name);
    this.editingCategoryDescription.set(category.description ?? '');
    this.categoryError.set('');
  }

  toggleCategoryStatus(category: GroupCategory): void {
    const status = category.status === 'active' ? 'inactive' : 'active';
    this.groupService.updateCategoryStatus(category.id, status).subscribe({
      next: () => this.loadCategories(),
      error: (error) => this.categoryError.set(error?.error?.error?.message ?? 'Category status could not be changed.'),
    });
  }

  openForm(group?: Group): void {
    const ref = this.dialog.open<GroupFormDialogComponent, Group | null, boolean | number>(GroupFormDialogComponent, { width: '480px', data: group ?? null, ariaLabel: group ? 'Edit group' : 'Create group' });
    ref.afterClosed().subscribe((result) => {
      if (typeof result === 'number') {
        this.router.navigate(['/groups', result]);
      } else if (result) {
        this.load();
      }
    });
  }
}
