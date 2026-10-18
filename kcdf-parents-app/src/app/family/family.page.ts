import { Component, inject, OnInit, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { forkJoin } from 'rxjs';
import {
  IonCard,
  IonCardContent,
  IonContent,
  IonSpinner,
} from '@ionic/angular/standalone';
import { FamilyMember, MemberApiService, MemberAuthService } from '../core/member-auth.service';

@Component({
  selector: 'app-family',
  templateUrl: './family.page.html',
  styleUrl: './family.page.scss',
  imports: [RouterLink, IonCard, IonCardContent, IonContent, IonSpinner],
})
export class FamilyPage implements OnInit {
  private readonly api = inject(MemberApiService);
  private readonly auth = inject(MemberAuthService);
  readonly members = signal<FamilyMember[]>([]);
  readonly loading = signal(true);
  readonly error = signal('');
  readonly hasFamilies = signal(true);

  ngOnInit(): void {
    const familyIds = this.auth.session.profile()?.family_ids ?? [];
    if (familyIds.length === 0) {
      this.hasFamilies.set(false);
      this.loading.set(false);
      return;
    }

    forkJoin(familyIds.map((familyId) => this.api.getFamilyMembers(familyId))).subscribe({
      next: (families) => this.members.set(
        families.reduce((members, family) => members.concat(family), [] as FamilyMember[]),
      ),
      error: () => {
        this.error.set('Family details could not be loaded. Please try again later.');
        this.loading.set(false);
      },
      complete: () => this.loading.set(false),
    });
  }
}
