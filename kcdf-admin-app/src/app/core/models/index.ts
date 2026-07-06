// ─── API Envelope ────────────────────────────────────────────────────────────
export interface ApiResponse<T> {
  success: boolean;
  data: T;
  message?: string;
}

export interface ApiListResponse<T> {
  success: boolean;
  data: T[];
  meta: PaginationMeta;
}

export interface PaginationMeta {
  total: number;
  per_page: number;
  current_page: number;
  last_page: number;
}

export interface ApiError {
  success: false;
  error: {
    code: string;
    message: string;
    details?: Record<string, string[]>;
  };
}

// ─── Auth ────────────────────────────────────────────────────────────────────
export interface LoginRequest {
  username: string;
  password: string;
}

export interface AuthTokens {
  access_token: string;
  refresh_token: string;
  token_type: string;
  expires_in: number;
}

export interface JwtPayload {
  sub: number;
  profile_id: number;
  username: string;
  roles: string[];
  family_ids: number[];
  iat: number;
  exp: number;
}

export interface AdminUser {
  id: number;
  profile_id: number;
  username: string;
  admin_role: AdminRole;
  first_name: string;
  last_name: string;
  email: string;
}

export type AdminRole = 'super_admin' | 'program_manager' | 'accounts' | 'readonly';

// ─── Member ──────────────────────────────────────────────────────────────────
export type Gender = 'male' | 'female' | 'other';
export type MemberStatus = 'active' | 'inactive' | 'suspended';

export interface MemberProfile {
  id: number;
  first_name: string;
  last_name: string;
  email: string | null;
  mobile: string | null;
  gender: Gender | null;
  date_of_birth: string | null;
  profile_photo_url: string | null;
  status: MemberStatus;
  has_login: boolean;
  created_at: string;
  updated_at: string;
}

export interface CreateMemberRequest {
  first_name: string;
  last_name: string;
  email?: string;
  mobile?: string;
  gender?: Gender;
  date_of_birth?: string;
}

export interface UpdateMemberRequest extends Partial<CreateMemberRequest> {
  status?: MemberStatus;
}

// ─── Family ──────────────────────────────────────────────────────────────────
export type FamilyStatus = 'active' | 'inactive' | 'suspended';
export type FamilyMemberRole = 'primary' | 'normal' | 'student';

export interface Family {
  id: number;
  family_code: string;
  family_name: string;
  status: FamilyStatus;
  address_id: number | null;
  address?: Address;
  member_count?: number;
  created_at: string;
  updated_at: string;
}

export interface FamilyMember {
  id: number;
  family_id: number;
  member_id: number;
  member_role: FamilyMemberRole;
  joined_at: string;
  member?: MemberProfile;
}

export interface Address {
  id: number;
  line1: string;
  line2: string | null;
  city: string;
  state: string;
  pincode: string;
  country: string;
}

export interface CreateFamilyRequest {
  family_name: string;
  address?: Partial<Address>;
}

// ─── Trainer ─────────────────────────────────────────────────────────────────
export type TrainerStatus = 'active' | 'inactive';

export interface Trainer {
  id: number;
  trainer_code: string;
  member_id: number;
  specialization: string | null;
  bio: string | null;
  status: TrainerStatus;
  address_id: number | null;
  address?: Address;
  member?: MemberProfile;
  created_at: string;
  updated_at: string;
}

export interface CreateTrainerRequest {
  member_id: number;
  specialization?: string;
  bio?: string;
}

// ─── Program ─────────────────────────────────────────────────────────────────
export type ProgramType = 'quran' | 'arabic' | 'islamic_studies' | 'other';
export type ProgramStatus = 'active' | 'inactive' | 'archived';

export interface Program {
  id: number;
  name: string;
  description: string | null;
  program_type: ProgramType;
  fee_amount: number;
  fee_frequency: string;
  status: ProgramStatus;
  created_at: string;
  updated_at: string;
}

export interface CreateProgramRequest {
  name: string;
  description?: string;
  program_type: ProgramType;
  fee_amount: number;
  fee_frequency: string;
}

// ─── Batch ───────────────────────────────────────────────────────────────────
export type BatchStatus = 'upcoming' | 'active' | 'completed' | 'cancelled';

export interface Batch {
  id: number;
  batch_name: string;
  program_id: number;
  trainer_id: number;
  capacity: number;
  start_date: string;
  end_date: string | null;
  schedule_days: string | null;
  start_time: string | null;
  end_time: string | null;
  status: BatchStatus;
  enrolled_count?: number;
  program?: Program;
  trainer?: Trainer;
  created_at: string;
  updated_at: string;
}

export interface CreateBatchRequest {
  batch_name: string;
  program_id: number;
  trainer_id: number;
  capacity: number;
  start_date: string;
  end_date?: string;
  schedule_days?: string;
  start_time?: string;
  end_time?: string;
}

// ─── Session ─────────────────────────────────────────────────────────────────
export type SessionType = 'regular' | 'makeup' | 'assessment' | 'event';
export type SessionStatus = 'scheduled' | 'completed' | 'cancelled';

export interface Session {
  id: number;
  batch_id: number;
  session_date: string;
  start_time: string;
  end_time: string;
  title: string | null;
  session_type: SessionType;
  trainer_id: number | null;
  topics_covered: string | null;
  homework: string | null;
  notes: string | null;
  status: SessionStatus;
  attendance_locked: boolean;
  created_at: string;
  updated_at: string;
}

export interface CreateSessionRequest {
  batch_id: number;
  session_date: string;
  start_time: string;
  end_time: string;
  title?: string;
  session_type?: SessionType;
  trainer_id?: number;
  topics_covered?: string;
  homework?: string;
  notes?: string;
}

// ─── Attendance ───────────────────────────────────────────────────────────────
export type AttendanceStatus = 'present' | 'absent' | 'late' | 'excused';

export interface Attendance {
  id: number;
  session_id: number;
  enrollment_id: number;
  member_id: number;
  status: AttendanceStatus;
  notes: string | null;
  marked_by: number | null;
  member?: MemberProfile;
}

// ─── Enrollment ──────────────────────────────────────────────────────────────
export type EnrollmentStatus = 'active' | 'cancelled' | 'completed';
export type PaymentStatus = 'pending' | 'paid' | 'overdue' | 'waived';

export interface Enrollment {
  id: number;
  batch_id: number;
  member_id: number;
  family_id: number;
  enrolled_at: string;
  status: EnrollmentStatus;
  payment_status: PaymentStatus;
  notes: string | null;
  member?: MemberProfile;
  batch?: Batch;
  family?: Family;
}

export interface CreateEnrollmentRequest {
  batch_id: number;
  member_id: number;
  family_id: number;
  notes?: string;
}

// ─── Payment ─────────────────────────────────────────────────────────────────
export type PaymentType = 'class_fee' | 'donation' | 'event_fee' | 'refund';
export type PaymentMethod = 'cash' | 'bank_transfer' | 'upi' | 'cheque' | 'other';
export type PaymentRecordStatus = 'completed' | 'pending' | 'failed' | 'refunded';

export interface Payment {
  id: number;
  family_id: number;
  enrollment_id: number | null;
  payment_type: PaymentType;
  amount: number;
  payment_method: PaymentMethod;
  status: PaymentRecordStatus;
  payment_date: string;
  reference_number: string | null;
  notes: string | null;
  recorded_by: number | null;
  family?: Family;
  enrollment?: Enrollment;
  created_at: string;
}

export interface CreatePaymentRequest {
  family_id: number;
  enrollment_id?: number;
  payment_type: PaymentType;
  amount: number;
  payment_method: PaymentMethod;
  payment_date: string;
  reference_number?: string;
  notes?: string;
}

// ─── Group ───────────────────────────────────────────────────────────────────
export type GroupVisibility = 'public' | 'private';
export type GroupStatus = 'active' | 'archived';

export interface Group {
  id: number;
  group_name: string;
  description: string | null;
  visibility: GroupVisibility;
  status: GroupStatus;
  created_by: number;
  member_count?: number;
  created_at: string;
  updated_at: string;
}

export interface GroupMember {
  id: number;
  group_id: number;
  member_id: number;
  joined_at: string;
  is_banned: boolean;
  member?: MemberProfile;
}

// ─── Notification ─────────────────────────────────────────────────────────────
export type NotificationChannel = 'in_app' | 'email' | 'sms';
export type NotificationStatus = 'sent' | 'delivered' | 'failed';

export interface Notification {
  id: number;
  sender_id: number | null;
  member_id: number;
  title: string;
  body: string;
  channel: NotificationChannel;
  status: NotificationStatus;
  read_at: string | null;
  sent_at: string;
  member?: MemberProfile;
}

export interface SendNotificationRequest {
  member_ids?: number[];
  target_type?: 'batch' | 'group' | 'all_families';
  target_id?: number;
  title: string;
  body: string;
  channel?: NotificationChannel;
}

// ─── Activity Log ─────────────────────────────────────────────────────────────
export interface ActivityLog {
  id: number;
  actor_id: number | null;
  actor_type: string;
  action: string;
  entity_type: string;
  entity_id: number | null;
  old_values: Record<string, unknown> | null;
  new_values: Record<string, unknown> | null;
  ip_address: string | null;
  user_agent: string | null;
  created_at: string;
  actor?: MemberProfile;
}

// ─── Filters ─────────────────────────────────────────────────────────────────
export interface ListParams {
  page?: number;
  per_page?: number;
  sort?: string;
  order?: 'asc' | 'desc';
  [key: string]: unknown;
}
