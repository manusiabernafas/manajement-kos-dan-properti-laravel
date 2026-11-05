<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('users.view'), 403);

        $users = User::query()
            ->with('roles')
            ->when($request->q, fn ($q) => $q->where('name', 'like', "%{$request->q}%")->orWhere('email', 'like', "%{$request->q}%"))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('users.index', ['title' => 'Users', 'users' => $users]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->can('users.create'), 403);

        return view('users.create', ['title' => 'Tambah User', 'roles' => Role::where('name', '!=', 'Tenant')->orderBy('name')->pluck('name', 'name')]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $role = $data['role'];
        unset($data['role']);
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $user->assignRole($role);

        AuditLogService::log('create', 'users', $user->id, null, $user->toArray());

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(Request $request, User $user): View
    {
        abort_unless($request->user()->can('users.update'), 403);

        return view('users.edit', [
            'title' => 'Edit User',
            'targetUser' => $user,
            'roles' => Role::where('name', '!=', 'Tenant')->orderBy('name')->pluck('name', 'name'),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $old = $user->toArray();
        $data = $request->validated();
        $role = $data['role'];
        unset($data['role']);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        $user->syncRoles([$role]);

        AuditLogService::log('update', 'users', $user->id, $old, $user->toArray());

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->can('users.delete'), 403);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        AuditLogService::log('delete', 'users', $user->id, $user->toArray(), null);

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
