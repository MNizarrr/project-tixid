<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::whereIn('role', ['admin', 'staff'])->get();
        return view('admin.user.index', compact('users'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|min:1',
            'last_name' => 'required|min:1',
            'email' => 'required|email:dns',
            'password' => 'required|min:8'
        ], [
            'first_name.required' => 'First name wajib di isi',
            'first_name.min' => 'First name minimal 1',
            'last_name.required' => 'Last name wajib di isi',
            'last_name.min' => 'Last name minimal 1',
            'email_required' => 'Email wajib di isi',
            'email.email' => 'Email tidak valid',
            'password.required' => 'Password wajib di isi',
            'password.min' => 'Password minimal 8 karakter',
        ]);

        $createData = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user'
        ]);

        if ($createData) {
            return redirect()->route('login')->with('Success', 'Berhasil membuat akun Silahkan login!');
        } else {
            return redirect()->route('signup')->with('Error', 'Gagal memperoleh data! Silahkan coba lagi!');
        }
    }

    public function authentication(Request $request)
    {
        $request->validate([
            'email' => 'required|email:dns',
            'password' => 'required'
        ], [
            'email.required' => 'Email Harus Diisi',
            'email.email' => 'Email tidak valid',
            'password.required' => 'Passwoord Harus Diisi'
        ]);

        $data = $request->only(['email', 'password']);
        if (Auth::attempt($data)) {
            if (Auth::user()->role == 'admin') {
                return redirect()->route('admin.dashboard')->with('Success', 'Berhasil Login!');
            } elseif (Auth::user()->role == 'staff') {
                return redirect()->route('staff.dashboard')->with('Success', 'Berhasil Login!');
            }else {
                return redirect()->route('home')->with('Success', 'Berhasil Login!');
            }
        } else {
            return redirect()->back()->with('error', 'Gagal! Pastikan Email dan Password Benar');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home')->with('logout', 'Anda telah logout! Silahkan login kembali untuk akses lengkap');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ], [
            'name.required' => 'Nama pengguna wajib di isi',
            'email.required' => 'Email pengguna wajib di isi',
            'email.unique' => 'Email sudah pernah di gunakan',
            'email.email' => 'Email tidak valid',
        ]);

        $createData = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'staff',
            'password' => $request->password,
        ]);

        if ($createData) {
            return redirect()->route('admin.users.index')->with('Success', 'Berhasil membuat data baru!');
        } else {
            return redirect()->back()->with('Error', 'Gagal, silahkan coba lagi!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::find($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email:dns',
        ], [
            'name.required' => 'Nama wajib di isi',
            'email.required' => 'Email wajib di isi',
            'email.email' => 'Email tidak valid',
            'role' => 'staff',
        ]);

        $updateData = User::where('id', $id)->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($updateData) {
            return redirect()->route('admin.users.index')->with('Success', 'Berhasil mengubah data');
        } else {
            return redirect()->back()->with('Error', 'Gagal! silahkan coba lagi');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        User::where('id', $id)->delete();
        return redirect()->route('admin.users.index')->with('Success', 'Berhasil menghapus data!');
    }

    public function export()
    {
        $fileName = "data-pengguna.xlsx";
        return Excel::download(new UserExport, $fileName);
    }
}
