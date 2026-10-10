import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { MemberService } from '../../../core/services/member.service';
import { MemberProfile } from '../../../core/models';
import { StatusBadgeComponent } from '../../../shared/components/status-badge/status-badge.component';
import { PageHeaderComponent } from '../../../shared/components/page-header/page-header.component';
import { LoadingOverlayComponent } from '../../../shared/components/loading-overlay/loading-overlay.component';
import { AppIconComponent } from '../../../shared/components/icon/app-icon.component';
import { MemberFormComponent } from '../member-form/member-form.component';
import { TailwindDialogService } from '../../../shared/components/modal/tailwind-dialog.service';

@Component({
  selector: 'app-member-detail',
  standalone: true,
  imports: [
    CommonModule,
    RouterModule,
    AppIconComponent,
    StatusBadgeComponent,
    PageHeaderComponent,
    LoadingOverlayComponent,
  ],
  templateUrl: './member-detail.component.html',
  styleUrl: './member-detail.component.scss',
})
export class MemberDetailComponent implements OnInit {
  private readonly memberService = inject(MemberService);
  private readonly route = inject(ActivatedRoute);
  private readonly dialog = inject(TailwindDialogService);

  readonly loading = signal(false);
  readonly member = signal<MemberProfile | null>(null);

  memberName = () => {
    const m = this.member();
    return m ? `${m.first_name} ${m.last_name}` : 'Loading...';
  };

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.loading.set(true);
    this.memberService.get(id).subscribe({
      next: (res) => { this.member.set(res.data); this.loading.set(false); },
      error: () => this.loading.set(false)
    });
  }

  openEdit(): void {
    const ref = this.dialog.open(MemberFormComponent, {
      width: '520px',
      ariaLabel: `Edit member ${this.memberName()}`,
      data: this.member(),
    });
    ref.afterClosed().subscribe(saved => {
      if (saved) {
        const id = Number(this.route.snapshot.paramMap.get('id'));
        this.memberService.get(id).subscribe(res => this.member.set(res.data));
      }
    });
  }
}
