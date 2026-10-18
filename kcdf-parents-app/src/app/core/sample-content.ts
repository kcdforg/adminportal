export interface SamplePost {
  id: number;
  category: string;
  title: string;
  excerpt: string;
  author: string;
  publishedAt: string;
  readTime: string;
  visual: string;
  icon: string;
}

export const SAMPLE_POSTS: SamplePost[] = [
  {
    id: 1,
    category: 'Community',
    title: 'A joyful start to our new term',
    excerpt: 'Families came together for a morning of games, conversation and new beginnings at the KCDF learning centre.',
    author: 'KCDF Community Team',
    publishedAt: '18 Oct 2026',
    readTime: '3 min read',
    visual: 'sunrise',
    icon: 'heart-outline',
  },
  {
    id: 2,
    category: 'Learning',
    title: 'Small moments make big readers',
    excerpt: 'A few simple ideas to help children build a happy reading habit at home, one story at a time.',
    author: 'Meera, Learning Facilitator',
    publishedAt: '14 Oct 2026',
    readTime: '4 min read',
    visual: 'books',
    icon: 'school-outline',
  },
  {
    id: 3,
    category: 'Events',
    title: 'Save the date: family sports day',
    excerpt: 'Bring your family along for a relaxed day of friendly games, shared snacks and plenty of cheering.',
    author: 'Events & Activities',
    publishedAt: '10 Oct 2026',
    readTime: '2 min read',
    visual: 'field',
    icon: 'calendar-outline',
  },
];
