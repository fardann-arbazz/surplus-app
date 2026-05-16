<?php

namespace App\Exceptions\Order;

use RuntimeException;

/* Stok surplus tidak cukup saat checkout */

class InsufficientStockException extends RuntimeException
{
    public function __construct(string $productName, int $requested, int $available)
    {
        parent::__construct(
            "Stok '{$productName}' tidak cukup. Diminta: {$requested}, tersedia: {$available}."
        );
    }
}

/* Surplus sudah expired saat checkout */
class SurplusExpiredException extends RuntimeException
{
    public function __construct(string $productName)
    {
        parent::__construct("Produk surplus '{$productName}' sudah kadaluarsa.");
    }
}

/* Item dari lebih dari 1 toko dalam 1 order */
class MultiStoreOrderException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Semua item dalam 1 order harus berasal dari toko yang sama.');
    }
}

/* Transisi status tidak valid */
class InvalidStatusTransitionException extends RuntimeException
{
    public function __construct(string $from, string $to)
    {
        parent::__construct("Tidak bisa mengubah status order dari '{$from}' ke '{$to}'.");
    }
}

/* Order tidak ditemukan atau bukan milik user ini */
class OrderNotFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Order tidak ditemukan.');
    }
}
