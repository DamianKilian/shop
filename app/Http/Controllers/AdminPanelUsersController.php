<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class AdminPanelUsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function users()
    {
        return view('adminPanel.users', []);
    }

    protected function getAllUsers()
    {
        $adminPermission = Permission::whereName('admin')->first();
        $admins = $adminPermission->users()->orderBy('id', 'asc')->get();
        $users = User::whereNotIn('id', $admins->pluck('id'))->get(['id', 'name', 'email',]);
        return [
            'admins' => $admins,
            'users' => $users,
        ];
    }

    public function getUsers()
    {
        $allUsers = $this->getAllUsers();
        return response()->json(['allUsers' => $allUsers]);
    }

    public function setAdmin(Request $request)
    {
        if (auth()->user()->id === $request->userId) {
            return 0;
        }
        $user = User::whereId($request->userId)->first();
        $adminPermission = Permission::whereName('admin')->first();
        if ($request->admin) {
            $user->permissions()->attach($adminPermission);
        } else {
            $user->permissions()->detach($adminPermission);
        }
        $user->save();
        return 1;
    }
}
