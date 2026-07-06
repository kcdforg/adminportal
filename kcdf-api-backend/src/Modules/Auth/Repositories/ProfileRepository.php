<?php

declare(strict_types=1);

namespace App\Modules\Auth\Repositories;

use App\Core\BaseRepository;
use App\Modules\Auth\Models\MemberProfile;
use Illuminate\Database\Capsule\Manager as DB;

class ProfileRepository extends BaseRepository
{
    protected string $modelClass = MemberProfile::class;

    public function getRolesForProfile(int $profileId): array
    {
        $roles = [];
        $familyIds = [];

        // Family roles
        $familyMemberships = DB::table('family_members')
            ->where('profile_id', $profileId)
            ->get(['member_role', 'family_id']);

        foreach ($familyMemberships as $membership) {
            $familyIds[] = $membership->family_id;
            $roles[] = match ($membership->member_role) {
                'primary' => 'family_primary',
                'normal'  => 'family_normal',
                'student' => 'family_student',
                default   => null,
            };
        }

        // Trainer role
        $trainer = DB::table('trainers')
            ->where('profile_id', $profileId)
            ->where('status', 'active')
            ->first();
        if ($trainer) {
            $roles[] = 'trainer';
        }

        // Admin role
        $admin = DB::table('admins')
            ->where('profile_id', $profileId)
            ->where('status', 'active')
            ->first();
        if ($admin) {
            $roles[] = match ($admin->admin_role) {
                'super_admin'      => 'admin_super',
                'program_manager'  => 'admin_program_manager',
                'accounts'         => 'admin_accounts',
                'readonly'         => 'admin_readonly',
                default            => null,
            };
        }

        return [
            'roles'      => array_values(array_filter(array_unique($roles))),
            'family_ids' => array_unique($familyIds),
        ];
    }
}
