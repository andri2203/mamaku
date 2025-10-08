<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\UserRoleEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ManajemenUserController extends Controller
{

    private $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function index()
    {
        // get All users data
        $users = User::with('team')->whereNot('id', $this->user->id)->get();

        $data = [
            'title' => 'Kelola Admin',
            'users' => $users
        ];

        // Logic to display user management dashboard
        return view('manajemen_user.index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Admin',
            'roles' => UserRoleEnum::cases(),
            'teams' => Team::all(),
        ];

        // Logic to show form for creating a new user
        return view('manajemen_user.create', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'level' => [Rule::enum(UserRoleEnum::class)],
            'team_id' => 'required|exists:teams,id',
            'photo' => 'nullable|image|max:2048', // Optional profile photo
        ]);

        try {
            $user = new User();
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->password = Hash::make($validated['password']);
            $user->level = $validated['level'];
            $user->current_team_id = $validated['team_id'];
            $user->photo = null;

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('image', 'public');
                $user->photo = $path;
            }

            $user->save();

            return redirect()->route('admin.index')->with('success', 'Admin baru berhasil ditambahkan.');
        } catch (\Exception $e) {
            // delete uploaded photo if exists
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit ' . UserRoleEnum::tryFrom($user->level)->name . ' : ' . $user->name,
            'user' => $user,
            'roles' => UserRoleEnum::cases(),
            'teams' => Team::all(),
            'action_param' => ['id' => $user->id],
        ];

        if ($request->query('redirect_to')) {
            // push redirect_to to action_param|array
            $data['action_param']['redirect_to'] = $request->query('redirect_to');
        }

        // Logic to show form for editing an existing user
        return view('manajemen_user.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => $request->email !== $user->email
                ? ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)]
                : ['required', 'string', 'email', 'max:255'],
            'password_old' => 'nullable|string|min:8',
            'password' => 'nullable|string|min:8|confirmed',
            'level' => [Rule::enum(UserRoleEnum::class)],
            'team_id' => 'required|exists:teams,id',
            'photo' => 'nullable|image|max:2048', // Optional profile photo
        ]);

        try {
            $user->name = $validated['name'];
            $user->email = $validated['email'];

            // validate password_old if not null
            if (!empty($validated['password_old']) && empty($validated['password'])) {
                return redirect()->route('admin.edit', $user->id)
                    ->withInput()
                    ->with('error', 'Password lama harus diisi jika ingin mengganti password.');
            }

            if (empty($validated['password_old']) && !empty($validated['password'])) {
                return redirect()->route('admin.edit', $user->id)
                    ->withInput()
                    ->with('error', 'Password lama harus diisi jika ingin mengganti password.');
            }

            if (!empty($validated['password_old']) && !Hash::check($validated['password_old'], $user->password_old)) {
                return redirect()->route('admin.edit', $user->id)
                    ->withInput()
                    ->with('error', 'Password lama tidak sesuai.');
            }

            if (!empty($validated['password_old']) && !empty($validated['password']) && $validated['passord_old'] === $validated['password']) {
                return redirect()->route('admin.edit', $user->id)
                    ->withInput()
                    ->with('error', 'Password baru tidak boleh sama dengan password lama.');
            }

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->level = $validated['level'];
            $user->current_team_id = $validated['team_id'];

            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($user->photo) {
                    Storage::disk('public')->delete($user->photo);
                }
                $path = $request->file('photo')->store('image', 'public');
                $user->photo = $path;
            }

            $user->save();

            $route = $request->query('redirect_to') ?? 'admin.index';

            return redirect()->route($route)->with('success', 'Admin ' . $user->name .  '  berhasil diperbarui.');
        } catch (\Exception $e) {
            // delete uploaded photo if exists
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function destroy($id)
    {
        if ($id == $this->user->id) {
            return redirect()->route('admin.index')->with('error', 'Tidak dapat menghapus diri sendiri.');
        }

        $user = User::with(['transactions', 'itemOuts'])->findOrFail($id);


        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        if ($user->level === UserRoleEnum::OWNER->value) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus user dengan level owner.');
        }

        if ($user->transactions()->exists() || $user->itemOuts()->exists()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus user : ' . $user->name . '. User ini sudah melakukan transaksi penjualan atau item keluar.');
        }

        try {
            // Delete profile photo if exists
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }

            $user->delete();

            return redirect()->route('admin.index')->with('success', 'Admin berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
