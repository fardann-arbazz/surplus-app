# RantangKu — Surplus Food Marketplace

> Platform jual-beli makanan surplus (sisa stok) dari toko/restoran dengan harga diskon, dilengkapi pembayaran online dan sistem pickup berbasis kode.

---

## Daftar Isi

- [Tentang Aplikasi](#tentang-aplikasi)
- [Tech Stack](#tech-stack)
- [Fitur Utama](#fitur-utama)
- [Arsitektur & Alur Sistem](#arsitektur--alur-sistem)
- [Struktur Direktori](#struktur-direktori)
- [Database Schema](#database-schema)
- [Role & Hak Akses](#role--hak-akses)
- [Alur Order](#alur-order)
- [Instalasi](#instalasi)
- [Konfigurasi Environment](#konfigurasi-environment)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Artisan Commands](#artisan-commands)
- [Realtime Events (Pusher)](#realtime-events-pusher)

---

## Tentang Aplikasi

**RantangKu** adalah web app marketplace makanan surplus berbasis Laravel. Seller (pemilik toko/restoran) dapat mendaftarkan sisa stok makanan mereka dengan harga diskon beserta jadwal pickup. Buyer (user) dapat menemukan surplus food terdekat berdasarkan lokasi, memesan, membayar via Midtrans, lalu mengambil pesanan langsung di toko menggunakan kode pickup unik.

Tujuan utama aplikasi ini adalah mengurangi food waste sekaligus memberikan pilihan makanan murah bagi konsumen.

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | PHP 8.3 + Laravel 13 |
| Database | PostgreSQL |
| Frontend | Blade + Tailwind CSS v4 + DaisyUI v5 |
| Build Tool | Vite 8 + Laravel Vite Plugin |
| Realtime | Pusher (Laravel Echo + pusher-js) |
| Payment | Midtrans Snap |
| Queue & Session | Database Driver |
| Cache | Database |
| Mail | SMTP (Mailtrap untuk development) |
| ORM | Eloquent |

---

## Fitur Utama

### 🛒 Buyer (User)
- Registrasi dengan verifikasi email via OTP (6 digit, expire 15 menit)
- Login dengan proteksi brute-force (`LoginAttemptService`)
- Lupa & reset password via OTP email
- Melihat surplus food terdekat berdasarkan koordinat GPS (radius 5 km default, Haversine formula)
- Keranjang belanja (cart) per user — tambah, update quantity, hapus item, kosongkan
- Checkout — validasi stok, validasi expiry, cegah multi-toko dalam satu order
- Pembayaran online via Midtrans Snap (pop-up, deadline bayar 15 menit)
- Konfirmasi pickup menggunakan kode unik 6 karakter
- Riwayat order dengan filter status

### 🏪 Seller
- Registrasi toko (pending approval dari admin)
- Manajemen menu produk (CRUD) dengan kategori dan gambar
- Mendaftarkan surplus product dari menu yang ada — harga awal, harga diskon, quantity, jadwal pickup, waktu expired
- Dashboard analitik: total order, total pendapatan, persentase perubahan vs bulan lalu, grafik harian/mingguan/bulanan, menu populer, order terbaru
- Manajemen order masuk — konfirmasi pesanan siap diambil (`ready_for_pickup`)
- Notifikasi realtime status surplus via Pusher (channel per toko)

### 🔧 Admin
- Dashboard manajemen seller — approve/reject toko
- Manajemen kategori produk (CRUD)
- Manajemen user — lihat detail, suspend, aktifkan, hapus
- Filter & pencarian user berdasarkan role dan status

---

## Arsitektur & Alur Sistem

Aplikasi menggunakan pola **Controller → Service → Model**. Logika bisnis sepenuhnya dipisahkan di layer Service, bukan di Controller.

```
Request → Middleware (auth, role, verified) → Controller → Service → Model → DB
                                                        ↓
                                                   Response (Blade View)
```

**Service classes utama:**

- `AuthService` — register, login, logout, verifikasi email
- `OtpService` — generate, kirim, verifikasi OTP (email_verification / password_reset)
- `SurplusProductService` — CRUD surplus, kurangi stok (dengan row-level lock), sync status
- `OrderService` — checkout (dengan `SELECT FOR UPDATE`), handle webhook Midtrans, konfirmasi pickup, restore stok
- `MidtransService` — buat Snap token, verifikasi signature webhook
- `NearbyStoresService` — query toko terdekat dengan Haversine formula via raw SQL
- `DashboardService` — statistik seller (orders, revenue, chart data, menu populer)

---

## Struktur Direktori

```
app/
├── Console/Commands/
│   └── SyncExpiredSurplus.php      # Artisan: update status expired surplus
├── Enums/
│   └── OrderStatus.php             # Enum: pending, paid, ready_for_pickup, completed, expired
├── Events/
│   └── SurplusStatusUpdated.php    # Broadcast event ke channel store.{id}.surplus
├── Exceptions/Order/               # Custom exceptions: InsufficientStock, MultiStore, dll
├── Http/
│   ├── Controllers/
│   │   ├── Admin/                  # UserController (admin)
│   │   ├── Api/                    # MidtransWebhookController
│   │   ├── Auth/                   # AuthController, ForgotPassword, ResetPassword
│   │   ├── Seller/                 # DashboardController, OrderController
│   │   └── User/                   # CartController, OrderController, SurplusDetailController
│   ├── Middleware/
│   │   ├── CheckRole.php           # Proteksi route berdasarkan role
│   │   ├── EnsureEmailIsVerified.php
│   │   └── RedirectIfAuthenticatedByRole.php
│   └── Requests/                   # Form Request Validation per fitur
├── Models/
│   ├── User.php
│   ├── Stores.php
│   ├── Products.php
│   ├── ProductsImg.php
│   ├── CategoryProducts.php
│   ├── SurplusProduct.php
│   ├── Cart.php
│   ├── Orders.php
│   ├── OrderItems.php
│   └── OtpTokens.php
└── Services/                       # Semua business logic

database/migrations/                # 17 migration files
resources/
├── views/
│   ├── admin/                      # Dashboard, category, seller, user management
│   ├── auth/                       # Login, register, OTP, forgot/reset password
│   ├── layouts/                    # Layout admin, seller, user
│   ├── partials/                   # Komponen reusable (header, sidebar, cart-drawer, dll)
│   ├── seller/                     # Dashboard, menu management, orders
│   └── user/                       # Home, surplus menu, cart, orders, checkout
routes/
└── web.php                         # Semua route (guest, auth, verified, role:admin, role:seller)
```

---

## Database Schema

### Tabel Utama

**`users`** — `name`, `email`, `password`, `phone`, `role` (user/seller/admin), `latitude`, `longitude`, `location_updated_at`, `is_suspend`, `email_verified_at`

**`stores`** — `user_id`, `name`, `description`, `address`, `img_url`, `latitude`, `longitude`, `is_active`, `is_online`

**`products`** — `store_id`, `category_id`, `name`, `description`, `price`, `is_active`

**`products_imgs`** — `product_id`, `img_url`, `is_primary`

**`category_products`** — `name`

**`surplus_products`** — `product_id`, `initial_price`, `discount_price`, `quantity`, `remaining_quantity`, `expired_at`, `pickup_start_at`, `pickup_end_at`, `status` (active/sold_out/expired)

**`carts`** — `user_id`, `surplus_id`, `quantity`

**`orders`** — `user_id`, `store_id`, `total_price`, `status`, `payment_reference`, `snap_token`, `expires_at`, `paid_at`, `pickup_code`

**`order_items`** — `order_id`, `surplus_id`, `quantity`, `price` (snapshot harga saat order)

**`otp_tokens`** — `user_id`, `token`, `type` (email_verification/password_reset), `expires_at`, `used_at`

---

## Role & Hak Akses

| Role | Akses |
|---|---|
| `user` | Home, surplus menu, cart, checkout, orders, konfirmasi pickup |
| `seller` | Dashboard seller, menu management, surplus management, orders seller |
| `admin` | Dashboard admin, seller management, category management, user management |

Semua protected route memerlukan: **login** + **email terverifikasi**. Route seller/admin tambahan dilindungi middleware `role:seller` / `role:admin`.

---

## Alur Order

```
User checkout
    │
    ▼
Validasi stok & expiry (SELECT FOR UPDATE — cegah race condition)
    │
    ▼
Buat Order (status: pending) + kurangi stok
    │
    ▼
Buat Midtrans Snap Token (deadline 15 menit)
    │
    ▼
User bayar via Midtrans Snap
    │
    ▼
Webhook Midtrans → verifikasi signature SHA512
    │
    ├── Sukses → status: paid, generate pickup_code (6 karakter acak)
    └── Gagal/Expire → status: expired, restore stok
    │
    ▼
Seller konfirmasi siap (status: ready_for_pickup)
    │
    ▼
User input pickup_code → status: completed
```

**Status transitions yang valid (enforced via `OrderStatus::canTransitionTo()`):**
```
pending → paid | expired
paid → ready_for_pickup | expired
ready_for_pickup → completed | expired
completed → (final)
expired → (final)
```

---

## Instalasi

### Prasyarat

- PHP >= 8.3
- Composer
- Node.js & npm
- PostgreSQL

### Langkah Instalasi

**1. Clone repository**
```bash
git clone <repository-url>
cd surplus-app
```

**2. Install dependencies (otomatis via composer script)**
```bash
composer run setup
```

Script ini akan menjalankan secara berurutan:
- `composer install`
- Copy `.env.example` ke `.env`
- `php artisan key:generate`
- `php artisan migrate --force`
- `npm install --ignore-scripts`
- `npm run build`

**3. Buat symbolic link storage**
```bash
php artisan storage:link
```

**4. Jalankan seeder (opsional — seed kategori produk)**
```bash
php artisan db:seed
```

---

## Konfigurasi Environment

Salin `.env.example` ke `.env` dan isi nilai berikut:

### Database (PostgreSQL)
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=nama_database
DB_USERNAME=postgres
DB_PASSWORD=password_anda
```

### Midtrans (Payment Gateway)
```env
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxx     # dari dashboard Midtrans
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxx
MIDTRANS_IS_PRODUCTION=false               # ubah ke true di production
```

### Pusher (Realtime)
```env
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=ap1

BROADCAST_CONNECTION=pusher
```

### Mail (SMTP)
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io     # ganti dengan SMTP provider di production
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@rantangku.com
```

### Queue & Session
```env
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=10080    # 7 hari dalam menit
CACHE_STORE=database
```

> **Catatan:** Pastikan queue worker berjalan agar OTP email terkirim (dikirim via `Mail::queue()`).

---

## Menjalankan Aplikasi

### Development (semua service sekaligus)

```bash
composer run dev
```

Perintah ini menjalankan secara paralel:
- `php artisan serve` — PHP development server
- `php artisan queue:listen` — Queue worker untuk email OTP
- `php artisan pail` — Log viewer
- `npm run dev` — Vite HMR

### Production Build

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Artisan Commands

### `surplus:sync-expired`

Memperbarui status surplus product yang sudah melewati `expired_at` tetapi statusnya masih `active`.

```bash
php artisan surplus:sync-expired
```

Disarankan dijadwalkan via Cron/Laravel Scheduler setiap menit atau setiap beberapa menit:

```php
// routes/console.php
Schedule::command('surplus:sync-expired')->everyMinute();
```

Setiap surplus yang di-expire akan men-broadcast event `SurplusStatusUpdated` ke channel Pusher terkait.

---

## Realtime Events (Pusher)

### Event: `SurplusStatusUpdated`

Broadcast ke channel **`store.{storeId}.surplus`** setiap kali status surplus berubah (sold_out atau expired). Digunakan untuk update UI seller secara realtime tanpa refresh halaman.

**Payload:**
```json
{
  "id": 12,
  "status": "sold_out"
}
```

**Trigger:**
- Stok habis saat user checkout (`SurplusProductService::reduceStock`)
- Surplus di-expire oleh Artisan command `surplus:sync-expired`

Frontend menggunakan **Laravel Echo** + **pusher-js** untuk subscribe ke channel ini.

---

## Catatan Development

- **Race condition** pada checkout ditangani dengan `SELECT FOR UPDATE` di dalam DB transaction.
- **Harga snapshot** — `order_items.price` menyimpan harga saat order dibuat, bukan harga realtime, sehingga perubahan harga surplus tidak mempengaruhi order yang sudah ada.
- **Satu order = satu toko** — Validasi `MultiStoreOrderException` memastikan user tidak bisa checkout item dari toko berbeda dalam satu order.
- **Pickup code** — Generate 6 karakter acak unik, diverifikasi case-insensitive saat konfirmasi pickup.
- **OTP** — Expire 15 menit, resend cooldown 1 menit. OTP lama di-invalidate otomatis saat generate OTP baru.