import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatChipsModule } from '@angular/material/chips';

@Component({
  selector: 'app-status-badge',
  standalone: true,
  imports: [CommonModule, MatChipsModule],
  template: `
    <span class="status-badge" [class]="'status-' + status">{{ status | titlecase }}</span>
  `,
  styles: [`
    .status-badge {
      display: inline-block;
      padding: 2px 10px;
      border-radius: 12px;
      font-size: 12px;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .status-active, .status-completed, .status-present, .status-paid, .status-sent, .status-delivered { background: #e8f5e9; color: #2e7d32; }
    .status-inactive, .status-cancelled, .status-absent, .status-failed { background: #ffebee; color: #c62828; }
    .status-pending, .status-scheduled, .status-upcoming { background: #fff3e0; color: #e65100; }
    .status-suspended, .status-overdue { background: #fce4ec; color: #880e4f; }
    .status-late, .status-makeup { background: #fff8e1; color: #f57f17; }
    .status-excused, .status-waived, .status-archived { background: #f3e5f5; color: #6a1b9a; }
    .status-locked { background: #e8eaf6; color: #283593; }
  `]
})
export class StatusBadgeComponent {
  @Input() status = '';
}
