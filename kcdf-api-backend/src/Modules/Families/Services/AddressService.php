<?php

declare(strict_types=1);

namespace App\Modules\Families\Services;

use App\Modules\Families\Models\Address;
use App\Modules\Families\Repositories\AddressRepository;

class AddressService
{
    public function __construct(private readonly AddressRepository $addressRepo) {}

    public function createAddress(array $data): Address
    {
        return $this->addressRepo->create([
            'address_label'  => $data['address_label'] ?? 'home',
            'address_line_1' => $data['address_line_1'],
            'address_line_2' => $data['address_line_2'] ?? null,
            'city'           => $data['city'],
            'state'          => $data['state'] ?? null,
            'postal_code'    => $data['postal_code'] ?? null,
            'country'        => $data['country'] ?? 'India',
        ]);
    }

    public function updateAddress(Address $address, array $data): Address
    {
        $updateData = [];
        $fields = ['address_label', 'address_line_1', 'address_line_2', 'city', 'state', 'postal_code', 'country'];
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }
        return $this->addressRepo->update($address, $updateData);
    }
}
