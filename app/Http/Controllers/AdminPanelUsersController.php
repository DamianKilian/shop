<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;

class AdminPanelUsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function users()
    {
        $permissions = Permission::all();
        return view('adminPanel.users', [
            'permissions' => $permissions,
        ]);
    }

    protected function getAllUsers($searchUsersVal = '')
    {
        $adminPermission = Permission::whereName('admin')->first();
        $admins = $adminPermission->users()->with('permissions')->orderBy('id', 'asc')->get();
        $users = User::with('permissions')->whereNotIn('id', $admins->pluck('id'))->when($searchUsersVal, function (Builder $query, $searchUsersVal) {
            $query->where(function (Builder $query2) use ($searchUsersVal) {
                $query2->where('name', 'LIKE', "%$searchUsersVal%")
                    ->orWhere('email', 'LIKE', "%$searchUsersVal%");
            });
        })->select(['id', 'name', 'email',])->paginate(20);
        $users->withPath(route('admin-panel-search-users', [], false));
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

    public function searchUsers(Request $request)
    {
        $users = $this->getAllUsers($request->searchUsersVal)['users'];
        return response()->json(['users' => $users,]);
    }

    public function setPermission(Request $request)
    {
        if (auth()->user()->id === $request->userId) {
            return 0;
        }
        $user = User::whereId($request->userId)->first();
        if ($request->permission['checked']) {
            $user->permissions()->attach($request->permission['id']);
        } else {
            $user->permissions()->detach($request->permission['id']);
        }
        $user->save();
        return 1;
    }
}
