import { AfterViewInit, Component, ElementRef, EventEmitter, Input, OnChanges, OnDestroy, Output, SimpleChanges, ViewChild } from '@angular/core';
import { CommonModule } from '@angular/common';

let nextModalId = 0;

@Component({
  selector: 'app-tailwind-modal',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './tailwind-modal.component.html',
  styleUrl: './tailwind-modal.component.scss'
})
export class TailwindModalComponent implements AfterViewInit, OnChanges, OnDestroy {
  @Input() open = false;
  @Input() title = 'Dialog';
  @Input() maxWidth = '36rem';
  @Input() visuallyHiddenTitle = false;
  @Input() closeOnBackdrop = true;
  @Input() hasActions = false;
  @Output() readonly closed = new EventEmitter<void>();

  @ViewChild('dialog') private readonly dialogRef?: ElementRef<HTMLDialogElement>;

  readonly titleId = `tailwind-dialog-title-${++nextModalId}`;
  private viewReady = false;

  ngAfterViewInit(): void {
    this.viewReady = true;
    this.syncOpenState();
  }

  ngOnChanges(_changes: SimpleChanges): void {
    this.syncOpenState();
  }

  ngOnDestroy(): void {
    const dialog = this.dialogRef?.nativeElement;
    if (dialog?.open) dialog.close();
  }

  close(): void {
    const dialog = this.dialogRef?.nativeElement;
    if (!dialog?.open) return;

    dialog.close();
    this.closed.emit();
  }

  handleCancel(event: Event): void {
    event.preventDefault();
    this.close();
  }

  handleDialogClick(event: MouseEvent): void {
    const dialog = this.dialogRef?.nativeElement;
    if (!this.closeOnBackdrop || !dialog || event.target !== dialog) return;
    const bounds = dialog.getBoundingClientRect();
    const clickedOutside =
      event.clientX < bounds.left ||
      event.clientX > bounds.right ||
      event.clientY < bounds.top ||
      event.clientY > bounds.bottom;

    if (clickedOutside) this.close();
  }

  private syncOpenState(): void {
    if (!this.viewReady) return;
    const dialog = this.dialogRef?.nativeElement;
    if (!dialog) return;

    if (this.open && !dialog.open) {
      dialog.showModal();
    } else if (!this.open && dialog.open) {
      dialog.close();
    }
  }
}
