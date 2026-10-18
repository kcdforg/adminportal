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

export interface CommunityGroup {
  id: number;
  name: string;
  description: string;
  category: string;
  members: number;
  meeting: string;
  icon: string;
  color: string;
}

export interface LearningCourse {
  id: number;
  title: string;
  trainer: string;
  students: number;
  category: string;
  description: string;
  duration: string;
  schedule: string;
  level: string;
  color: string;
}

export const SAMPLE_GROUPS: CommunityGroup[] = [
  {
    id: 1,
    name: 'Growing Together',
    description: 'A welcoming circle for parents to share ideas, celebrate milestones and support one another.',
    category: 'Parent circle',
    members: 28,
    meeting: 'Every 2nd Saturday',
    icon: 'people-outline',
    color: 'sage',
  },
  {
    id: 2,
    name: 'Little Readers Club',
    description: 'Stories, playful activities and a love of reading for children and their families.',
    category: 'Learning',
    members: 19,
    meeting: 'Every Wednesday',
    icon: 'school-outline',
    color: 'peach',
  },
  {
    id: 3,
    name: 'Active Families',
    description: 'Make movement part of family life with friendly games, walks and weekend meetups.',
    category: 'Wellbeing',
    members: 34,
    meeting: 'Weekend meetups',
    icon: 'heart-outline',
    color: 'blue',
  },
];

export const SAMPLE_COURSES: LearningCourse[] = [
  {
    id: 1,
    title: 'Positive Parenting',
    trainer: 'Meera Nair',
    students: 24,
    category: 'Family wellbeing',
    description: 'Practical, caring ways to build trust, confidence and connection at every age.',
    duration: '4 weeks',
    schedule: 'Saturday mornings',
    level: 'For every parent',
    color: 'green',
  },
  {
    id: 2,
    title: 'Learning Through Play',
    trainer: 'Arun Kumar',
    students: 18,
    category: 'Early learning',
    description: 'Simple playful activities that make everyday moments a chance to learn.',
    duration: '3 weeks',
    schedule: 'Wednesday afternoons',
    level: 'Ages 3–8',
    color: 'gold',
  },
  {
    id: 3,
    title: 'Healthy Family Habits',
    trainer: 'Dr. Leela Thomas',
    students: 21,
    category: 'Health & wellbeing',
    description: 'Small, sustainable habits for happier, healthier days as a family.',
    duration: '2 weeks',
    schedule: 'Self-paced',
    level: 'For the whole family',
    color: 'blue',
  },
];

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
