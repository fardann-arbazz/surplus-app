<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function __construct(private readonly UserService $userService) {}

    public function index(Request $request)
    {
        $users = $this->userService->getFilteredUsers($request);
        return view('admin.user.user', compact('users'));
    }

    // public function show(int $id)
    // {
    //     $user = $this->userService->findUser($id);

    //     // return view('admin.user.show', compact('user'));
    // }

    public function suspend(int $id)
    {
        $this->userService->suspendUser($id);

        return back()->with('toast', [
            'type'    => 'warning',
            'message' => 'User berhasil di-suspend.',
        ]);
    }

    public function activate(int $id)
    {
        $this->userService->activateUser($id);

        return back()->with('toast', [
            'type'    => 'success',
            'message' => 'User berhasil diaktifkan.',
        ]);
    }

    public function destroy(int $id)
    {
        $this->userService->deleteUser($id);

        return back()->with('toast', [
            'type'    => 'success',
            'message' => 'User berhasil dihapus.',
        ]);
    }
}
