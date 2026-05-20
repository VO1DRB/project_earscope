<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function createDoctor()
    {
        return view('admin.create-doctor');
    }

    public function storeDoctor(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'password' => 'required',
            'name' => 'required',
            'license_number' => 'required',
            'specialization' => 'required',
            'gender' => 'required|in:male,female',
        ]);

        // buat user
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'doctor',
        ]);

        // buat doctor
        Doctor::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'license_number' => $request->license_number,
            'specialization' => $request->specialization,
            'gender' => $request->gender,
        ]);

        return redirect('/admin/dashboard')->with('success', 'Doctor berhasil ditambahkan');
    }
}