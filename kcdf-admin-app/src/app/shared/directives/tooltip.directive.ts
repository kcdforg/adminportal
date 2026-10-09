import { Directive, ElementRef, Input, OnChanges, OnDestroy, Renderer2, SimpleChanges, inject } from '@angular/core';

let nextTooltipId = 0;

@Directive({
  selector: '[appTooltip]',
  standalone: true,
})
export class TooltipDirective implements OnChanges, OnDestroy {
  @Input('appTooltip') text = '';

  private readonly host = inject(ElementRef<HTMLElement>).nativeElement;
  private readonly renderer = inject(Renderer2);
  private readonly tooltipId = `kcdf-tooltip-${++nextTooltipId}`;
  private readonly tooltip = this.renderer.createElement('span') as HTMLSpanElement;
  private readonly originalDescription = this.host.getAttribute('aria-describedby');
  private readonly describedBy = [this.originalDescription, this.tooltipId].filter(Boolean).join(' ');

  constructor() {
    this.renderer.addClass(this.host, 'relative');
    this.renderer.addClass(this.host, 'group');
    this.renderer.setAttribute(this.tooltip, 'id', this.tooltipId);
    this.renderer.setAttribute(this.tooltip, 'role', 'tooltip');
    const tooltipClasses = 'pointer-events-none absolute bottom-full left-1/2 z-50 mb-2 -translate-x-1/2 whitespace-nowrap rounded-md bg-gray-900 px-2 py-1 text-xs font-medium text-white opacity-0 shadow transition-opacity group-hover:opacity-100 group-focus-visible:opacity-100 dark:bg-gray-700';
    for (const className of tooltipClasses.split(' ')) {
      this.renderer.addClass(this.tooltip, className);
    }
    this.renderer.appendChild(this.host, this.tooltip);
    this.renderer.setAttribute(this.host, 'aria-describedby', this.describedBy);
  }

  ngOnChanges(_changes: SimpleChanges): void {
    this.renderer.setProperty(this.tooltip, 'textContent', this.text);
  }

  ngOnDestroy(): void {
    this.renderer.removeChild(this.host, this.tooltip);
    if (this.originalDescription) {
      this.renderer.setAttribute(this.host, 'aria-describedby', this.originalDescription);
    } else {
      this.renderer.removeAttribute(this.host, 'aria-describedby');
    }
  }
}
