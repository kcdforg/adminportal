<?php

declare(strict_types=1);

namespace App\Modules\Families\Repositories;

use App\Core\BaseRepository;
use App\Modules\Families\Models\Address;

class AddressRepository extends BaseRepository
{
    protected string $modelClass = Address::class;
}
