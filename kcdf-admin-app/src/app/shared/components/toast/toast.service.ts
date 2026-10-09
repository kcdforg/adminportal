import { Injectable, signal } from '@angular/core';

export type ToastVariant = 'success' | 'error' | 'warning' | 'info';

export interface ToastOptions {
  variant?: ToastVariant;
  durationMs?: number;
  actionLabel?: string;
  action?: () => void;
}

export interface ToastMessage {
  id: number;
  message: string;
  variant: ToastVariant;
  actionLabel?: string;
  action?: () => void;
}

@Injectable({ providedIn: 'root' })
export class ToastService {
  private readonly messages = signal<ToastMessage[]>([]);
  private readonly timers = new Map<number, ReturnType<typeof setTimeout>>();
  private nextId = 0;

  readonly toasts = this.messages.asReadonly();

  show(message: string, options: ToastOptions = {}): number {
    const id = ++this.nextId;
    const toast: ToastMessage = {
      id,
      message,
      variant: options.variant ?? 'info',
      actionLabel: options.actionLabel,
      action: options.action,
    };

    this.messages.update(messages => [...messages, toast]);

    const durationMs = options.durationMs ?? 4000;
    if (durationMs > 0) {
      this.timers.set(id, setTimeout(() => this.dismiss(id), durationMs));
    }

    return id;
  }

  dismiss(id: number): void {
    const timer = this.timers.get(id);
    if (timer !== undefined) {
      clearTimeout(timer);
      this.timers.delete(id);
    }
    this.messages.update(messages => messages.filter(message => message.id !== id));
  }

  invokeAction(toast: ToastMessage): void {
    try {
      toast.action?.();
    } finally {
      this.dismiss(toast.id);
    }
  }
}
