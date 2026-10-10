import { ApplicationRef, InjectionToken, Injector, Injectable, Type, inject, signal } from '@angular/core';
import { Observable, ReplaySubject } from 'rxjs';

export const TAILWIND_DIALOG_DATA = new InjectionToken<unknown>('TAILWIND_DIALOG_DATA');
export const TAILWIND_DIALOG_REF = new InjectionToken<TailwindDialogRef<unknown>>('TAILWIND_DIALOG_REF');

export interface TailwindDialogConfig<D> {
  data?: D;
  width?: string;
  ariaLabel?: string;
  closeOnBackdrop?: boolean;
}

export interface ActiveTailwindDialog {
  component: Type<unknown>;
  injector: Injector;
  ref: TailwindDialogRef<unknown>;
  ariaLabel: string;
  maxWidth: string;
  closeOnBackdrop: boolean;
}

export class TailwindDialogRef<R = unknown> {
  private readonly result = new ReplaySubject<R | undefined>(1);
  private closed = false;

  constructor(private readonly closeHandler: (result: R | undefined) => void) {}

  afterClosed(): Observable<R | undefined> {
    return this.result.asObservable();
  }

  close(result?: R): void {
    if (!this.closed) this.closeHandler(result);
  }

  complete(result: R | undefined): void {
    if (this.closed) return;
    this.closed = true;
    this.result.next(result);
    this.result.complete();
  }
}

@Injectable({ providedIn: 'root' })
export class TailwindDialogService {
  readonly activeDialog = signal<ActiveTailwindDialog | null>(null);
  private readonly applicationRef = inject(ApplicationRef);
  private readonly parentInjector = inject(Injector);
  private renderScheduled = false;

  open<C, D = unknown, R = unknown>(
    component: Type<C>,
    config: TailwindDialogConfig<D> = {},
  ): TailwindDialogRef<R> {
    const active = this.activeDialog();
    if (active) {
      this.close(active.ref);
    }

    let ref!: TailwindDialogRef<R>;
    ref = new TailwindDialogRef<R>(result => this.close(ref, result));
    const dialogInjector = Injector.create({
      providers: [
        { provide: TAILWIND_DIALOG_DATA, useValue: config.data },
        { provide: TAILWIND_DIALOG_REF, useValue: ref },
      ],
      parent: this.parentInjector,
    });

    this.activeDialog.set({
      component,
      injector: dialogInjector,
      ref: ref as TailwindDialogRef<unknown>,
      ariaLabel: config.ariaLabel ?? 'Dialog',
      maxWidth: config.width ?? '36rem',
      closeOnBackdrop: config.closeOnBackdrop ?? true,
    });
    this.scheduleRender();

    return ref;
  }

  close<R>(ref: TailwindDialogRef<R>, result?: R): void {
    const active = this.activeDialog();
    if (!active || active.ref !== ref) return;

    this.activeDialog.set(null);
    ref.complete(result);
    this.scheduleRender();
  }

  private scheduleRender(): void {
    if (this.renderScheduled) return;
    this.renderScheduled = true;
    queueMicrotask(() => {
      this.renderScheduled = false;
      this.applicationRef.tick();
    });
  }
}
