import { Component, inject, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import {
  IonCard,
  IonCardContent,
  IonContent,
  IonIcon,
} from '@ionic/angular/standalone';
import { MemberApiService, ParentGroup } from '../core/member-auth.service';

@Component({
  selector: 'app-groups',
  templateUrl: './groups.page.html',
  styleUrl: './groups.page.scss',
  imports: [CommonModule, RouterLink, IonCard, IonCardContent, IonContent, IonIcon],
})
export class GroupsPage implements OnInit {
  private readonly api = inject(MemberApiService);
  groups: Array<ParentGroup & { name: string; categoryName: string; icon: string; color: string }> = [];
  loading = true;
  error = '';

  ngOnInit(): void {
    this.api.getGroups().subscribe({
      next: (groups) => {
        this.groups = groups.map((group) => ({
          ...group,
          name: group.display_name || group.group_name,
          categoryName: group.category?.category_name ?? 'Community',
          icon: 'people-outline',
          color: 'sage',
        }));
        this.loading = false;
      },
      error: () => {
        this.error = 'Groups could not be loaded. Please try again later.';
        this.loading = false;
      },
    });
  }
}
