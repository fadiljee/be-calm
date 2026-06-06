<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $role = $request->query('role', 'student');

        $users = User::where('role', $role)
                     ->with(['screeningHistories' => fn ($q) => $q->latest()])
                     ->get();

        return $this->successResponse($users, "Daftar {$role} berhasil dimuat");
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|min:6',
            'role'      => 'required|in:student,parent',
            'nisn'      => 'nullable|string',
            'parent_id' => 'nullable|exists:users,id',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'nisn'      => $request->nisn,
            'parent_id' => $request->parent_id,
        ]);

        return $this->successResponse($user, 'User created successfully', 201);
    }

    public function show(User $user)
    {
        return $this->successResponse($user, 'User detail retrieved');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'      => 'sometimes|required',
            'email'     => 'sometimes|required|email|unique:users,email,' . $user->id,
            'role'      => 'sometimes|required|in:student,parent',
            'nisn'      => 'nullable|string',
            'parent_id' => 'nullable|exists:users,id',
        ]);

        $user->update($request->except('password'));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return $this->successResponse($user, 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return $this->successResponse(null, 'User deleted successfully');
    }

    public function importStudents(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        // TODO: Implementasi import dari file Excel/CSV
        // Gunakan package maatwebsite/excel untuk membaca file
        return $this->successResponse(null, 'Fitur import belum diimplementasikan', 501);
    }
}
