import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-status-badge',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './status-badge.component.html',
  styleUrl: './status-badge.component.scss'
})
export class StatusBadgeComponent {
  @Input() status = '';

  get toneClasses(): string {
    switch (this.status.trim().toLowerCase()) {
      case 'active':
      case 'completed':
      case 'present':
      case 'paid':
      case 'sent':
      case 'delivered':
        return 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/20';
      case 'inactive':
      case 'cancelled':
      case 'absent':
      case 'failed':
        return 'bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-300 dark:ring-rose-500/20';
      case 'pending':
      case 'scheduled':
      case 'upcoming':
        return 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300 dark:ring-amber-500/20';
      case 'suspended':
      case 'overdue':
        return 'bg-pink-50 text-pink-700 ring-1 ring-inset ring-pink-600/20 dark:bg-pink-500/10 dark:text-pink-300 dark:ring-pink-500/20';
      case 'late':
      case 'makeup':
        return 'bg-yellow-50 text-yellow-800 ring-1 ring-inset ring-yellow-600/20 dark:bg-yellow-500/10 dark:text-yellow-300 dark:ring-yellow-500/20';
      case 'excused':
      case 'waived':
      case 'archived':
        return 'bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-600/20 dark:bg-purple-500/10 dark:text-purple-300 dark:ring-purple-500/20';
      case 'locked':
        return 'bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-600/20 dark:bg-indigo-500/10 dark:text-indigo-300 dark:ring-indigo-500/20';
      default:
        return 'bg-gray-100 text-gray-700 ring-1 ring-inset ring-gray-600/10 dark:bg-gray-500/10 dark:text-gray-300 dark:ring-gray-400/20';
    }
  }
}
