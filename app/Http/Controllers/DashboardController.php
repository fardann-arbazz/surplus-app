<?php

namespace App\Http\Controllers;

use App\Models\Stores;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $notifications = $user->notifications()->latest()->take(10)->get();
        $unreadCount = $user->unreadNotifications()->count();

        $countUser = User::where('role', 'user')->count();
        $countSellerActive = Stores::where('is_active', true)->count();
        $countSellerPending = Stores::where('is_active', false)->count();


        return view('admin.dashboard', [
            'countUser' => $countUser,
            'countSellerActive' => $countSellerActive,
            'countSellerPending' => $countSellerPending,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function sellerManagement(Request $request): View
    {
        $query = Stores::with('user');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter status
        if ($request->status && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        // Sort
        match ($request->sort) {
            'oldest'    => $query->oldest(),
            'name_asc'  => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            default     => $query->latest(),
        };

        $sellers = $query->paginate(10)->withQueryString();

        return view('admin.seller-management', [
            'sellers' => $sellers
        ]);
    }

    public function updateStoresStatus(Request $request): JsonResponse
    {
        $request->validate([
            'seller_id' => 'required|exists:stores,id',
            'is_active' => 'required|boolean',
        ]);

        $store = Stores::findOrFail($request->seller_id);
        $store->is_active = $request->is_active;
        $store->save();

        return response()->json([
            'success' => true,
            'message' => $request->is_active
                ? 'Seller activated successfully.'
                : 'Seller deactivated successfully.',
        ]);
    }

    public function getDetailStore(string $id): JsonResponse
    {
        $seller = Stores::where('id', $id)->with('user')->first();

        if (!$seller) {
            return response()->json(['message' => 'Store not found', 'status' => false]);
        }

        return response()->json(['seller' => $seller, 'success' => true]);
    }


    public function readNotif(): JsonResponse
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }
}
