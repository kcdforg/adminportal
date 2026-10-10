import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NgComponentOutlet } from '@angular/common';
import { TailwindDialogService } from './tailwind-dialog.service';
import { TailwindModalComponent } from './tailwind-modal.component';

@Component({
  selector: 'app-tailwind-dialog-host',
  standalone: true,
  imports: [CommonModule, NgComponentOutlet, TailwindModalComponent],
  templateUrl: './tailwind-dialog-host.component.html',
  styleUrl: './tailwind-dialog-host.component.scss',
})
export class TailwindDialogHostComponent {
  readonly dialogService = inject(TailwindDialogService);
}
