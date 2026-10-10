import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';

const ICON_PATHS: Readonly<Record<string, readonly string[]>> = {
  add: ['M12 5v14', 'M5 12h14'],
  admin_panel_settings: ['M12 3 20 7v5c0 4.5-3.1 7.7-8 9-4.9-1.3-8-4.5-8-9V7l8-4Z', 'm9 12 2 2 4-4'],
  arrow_back: ['M19 12H5', 'm12 19-7-7 7-7'],
  assignment: ['M9 5h6', 'M9 3h6v4H9z', 'M6 5H5v16h14V5h-1', 'M9 12h6', 'M9 16h6'],
  auto_stories: ['M12 7v14', 'M3 5.5C6.5 4 9.5 4.5 12 7v14c-2.5-2.5-5.5-3-9-1.5z', 'M21 5.5C17.5 4 14.5 4.5 12 7v14c2.5-2.5 5.5-3 9-1.5z'],
  bar_chart: ['M4 19V10h4v9z', 'M10 19V5h4v14z', 'M16 19v-7h4v7z'],
  block: ['M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18Z', 'm5.6 5.6 12.8 12.8'],
  cancel: ['M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18Z', 'm9 9 6 6', 'm15 9-6 6'],
  check_circle: ['M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18Z', 'm8 12 2.5 2.5L16 9'],
  dashboard: ['M3 3h8v8H3z', 'M13 3h8v5h-8z', 'M13 10h8v11h-8z', 'M3 13h8v8H3z'],
  diff: ['M4 4h16v16H4z', 'M8 9h8', 'M8 13h5', 'M16 16h.01'],
  download: ['M12 3v12', 'm7 10 5 5 5-5', 'M5 20h14'],
  edit: ['M4 20h4l11-11a2.1 2.1 0 0 0-4-4L4 16z', 'm13.5 6.5 4 4'],
  expand_more: ['m6 9 6 6 6-6'],
  family_restroom: ['M9 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z', 'M17 12a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z', 'M3 20v-2a6 6 0 0 1 12 0v2', 'M15 15a5 5 0 0 1 6 5'],
  fact_check: ['M5 3h14v18H5z', 'M8 8h.01', 'M11 8h5', 'M8 12h.01', 'M11 12h5', 'm8 16 1.5 1.5L12 15'],
  forum: ['M4 5h16v11H9l-5 4z', 'M8 9h8', 'M8 12h5'],
  groups: ['M16 11a3 3 0 1 0 0-6', 'M8 11a3 3 0 1 0 0-6', 'M2 20v-1a6 6 0 0 1 12 0v1', 'M15 14a5 5 0 0 1 7 5v1'],
  inbox: ['M4 4h16l2 11v5H2v-5z', 'M2 15h6l2 3h4l2-3h6'],
  keyboard_return: ['M20 6v5H5', 'm9 15-4-4 4-4'],
  lock: ['M5 10h14v11H5z', 'M8 10V7a4 4 0 0 1 8 0v3', 'M12 14v3'],
  lock_open: ['M5 10h14v11H5z', 'M8 10V7a4 4 0 0 1 7.5-2', 'M12 14v3'],
  logout: ['M10 17l5-5-5-5', 'M15 12H3', 'M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7'],
  manage_search: ['M11 18a7 7 0 1 0 0-14 7 7 0 0 0 0 14Z', 'm16 16 5 5', 'M8 8h6', 'M8 11h4'],
  menu: ['M4 6h16', 'M4 12h16', 'M4 18h16'],
  notifications: ['M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9', 'M10 21h4'],
  payments: ['M3 6h18v13H3z', 'M3 10h18', 'M7 15h4'],
  people: ['M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2', 'M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z', 'M20 21v-2a4 4 0 0 0-3-3.87', 'M16 3.13a4 4 0 0 1 0 7.75'],
  person: ['M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z', 'M4 21a8 8 0 0 1 16 0'],
  person_remove: ['M15 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2', 'M8 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z', 'M16 11h7'],
  receipt_long: ['M5 3h14v18l-3-2-4 2-4-2-3 2z', 'M8 8h8', 'M8 12h8', 'M8 16h5'],
  save: ['M5 3h12l4 4v14H3V3z', 'M7 3v6h10V3', 'M7 21v-8h10v8'],
  school: ['M3 9 12 4l9 5-9 5z', 'M7 12v5c3 2.5 7 2.5 10 0v-5', 'M21 9v6'],
  search: ['M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z', 'm17 17 4 4'],
  send: ['m22 2-7 20-4-9-9-4z', 'M22 2 11 13'],
  visibility: ['M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z', 'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z'],
  payments_card: ['M3 5h18v14H3z', 'M3 9h18', 'M7 15h4'],
};

@Component({
  selector: 'app-icon',
  standalone: true,
  imports: [CommonModule],
  host: {
    class: 'inline-flex shrink-0 align-middle'
  },
  templateUrl: './icon.component.html',
  styleUrl: './icon.component.scss'
})
export class AppIconComponent {
  @Input() name = '';

  get paths(): readonly string[] {
    return ICON_PATHS[this.name] ?? [];
  }
}
