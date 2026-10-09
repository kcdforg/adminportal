import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NgComponentOutlet } from '@angular/common';
import { TailwindDialogService } from './tailwind-dialog.service';
import { TailwindModalComponent } from './tailwind-modal.component';

@Component({
  selector: 'app-tailwind-dialog-host',
  standalone: true,
  imports: [CommonModule, NgComponentOutlet, TailwindModalComponent],
  template: `
    <app-tailwind-modal
      [open]="dialogService.activeDialog() !== null"
      [title]="dialogService.activeDialog()?.ariaLabel ?? 'Dialog'"
      [maxWidth]="dialogService.activeDialog()?.maxWidth ?? '36rem'"
      [visuallyHiddenTitle]="true"
      [closeOnBackdrop]="dialogService.activeDialog()?.closeOnBackdrop ?? true"
      (closed)="dialogService.activeDialog()?.ref?.close()">
      <ng-container *ngIf="dialogService.activeDialog() as dialog">
        <ng-container *ngComponentOutlet="dialog.component; injector: dialog.injector"></ng-container>
      </ng-container>
    </app-tailwind-modal>
  `,
})
export class TailwindDialogHostComponent {
  readonly dialogService = inject(TailwindDialogService);
}
