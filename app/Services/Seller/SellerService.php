<?php

namespace App\Services\Seller;

use App\Models\Stores;
use App\Models\User;
use App\Notifications\StoreCreatedNotification;
use Illuminate\Http\UploadedFile;

class SellerService
{
    public function createSeller(string $userId, array $data, ?UploadedFile $image = null)
    {
        $path = null;

        if ($image) {
            $path = $image->store('stores', 'public');
        }

        $seller = Stores::create([
            'user_id' => $userId,
            'name' => $data['name'],
            'description' => $data['description'],
            'address' => $data['address'],
            'img_url' => $path ?? null,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'is_active' => false
        ]);

        User::where('id', $userId)->update([
            'role' => 'seller',
        ]);

        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(new StoreCreatedNotification(($seller)));
        }

        return $seller;
    }
}
