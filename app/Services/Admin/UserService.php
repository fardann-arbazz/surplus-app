<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    private const PER_PAGE = 10;

    /**
     * Ambil daftar user dengan filter + pagination.
     */
    public function getFilteredUsers(Request $request): LengthAwarePaginator
    {
        return User::query()
            ->search($request->input('search'))
            ->filterRole($request->input('role'))
            ->filterStatus($request->input('status'))
            ->orderByDesc('created_at')
            ->paginate(self::PER_PAGE)
            ->withQueryString(); // pertahankan query string saat pindah halaman
    }

    /**
     * Ambil detail satu user.
     */
    public function findUser(int $id): User
    {
        return User::findOrFail($id);
    }

    /**
     * Suspend user: set is_suspended = true.
     */
    public function suspendUser(int $id): User
    {
        $user = User::findOrFail($id);

        abort_if($user->is_suspended, 422, 'User sudah dalam status suspended.');
        abort_if($user->isAdmin(), 403, 'Tidak dapat men-suspend akun Admin.');

        $user->update(['is_suspended' => true]);

        return $user->fresh();
    }

    /**
     * Aktifkan user: set is_suspended = false,
     * dan pastikan email_verified_at terisi agar status menjadi 'active'.
     */
    public function activateUser(int $id): User
    {
        $user = User::findOrFail($id);

        abort_unless($user->is_suspended, 422, 'User sudah dalam status aktif.');

        $user->update([
            'is_suspended'       => false,
            'email_verified_at'  => $user->email_verified_at ?? now(),
        ]);

        return $user->fresh();
    }

    /**
     * Hapus user secara permanen.
     */
    public function deleteUser(int $id): void
    {
        $user = User::findOrFail($id);

        abort_if($user->isAdmin(), 403, 'Tidak dapat menghapus akun Admin.');

        $user->delete();
    }
}
