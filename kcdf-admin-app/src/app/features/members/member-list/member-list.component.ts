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
  templateUrl: './member-list.component.html',
  styleUrl: './member-list.component.scss',
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
