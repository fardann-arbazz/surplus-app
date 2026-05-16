<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending          = 'pending';           // Order dibuat, belum bayar
    case Paid             = 'paid';              // Midtrans confirm pembayaran
    case ReadyForPickup   = 'ready_for_pickup';  // Seller konfirmasi siap diambil
    case Completed        = 'completed';         // User confirm sudah pickup
    case Expired          = 'expired';           // Tidak bayar / tidak pickup dalam batas waktu

    public function label(): string
    {
        return match ($this) {
            self::Pending        => 'Menunggu Pembayaran',
            self::Paid           => 'Dibayar – Menunggu Konfirmasi Toko',
            self::ReadyForPickup => 'Siap Diambil',
            self::Completed      => 'Selesai',
            self::Expired        => 'Kadaluarsa',
        };
    }

    /* Status yang boleh di-cancel user */
    public function isCancellable(): bool
    {
        return $this === self::Pending;
    }

    /* Transisi yang valid: dari status ini => bisa ke mana saja */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Pending        => [self::Paid, self::Expired],
            self::Paid           => [self::ReadyForPickup, self::Expired],
            self::ReadyForPickup => [self::Completed, self::Expired],
            self::Completed,
            self::Expired        => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedTransitions(), strict: true);
    }
}
