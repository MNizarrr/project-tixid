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
            return redirect()->route('login')->with('success', 'Berhasil membuat akun Silahkan login!');
        } else {
            return redirect()->route('signup')->with('error', 'Gagal memperoleh data! Silahkan coba lagi!');
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
                return redirect()->route('admin.dashboard')->with('success', 'Berhasil Login!');
            } elseif (Auth::user()->role == 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Berhasil Login!');
            } else {
                return redirect()->route('home')->with('success', 'Berhasil Login!');
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
            'role' => 'admin',
            'password' => $request->password,
        ]);

        if ($createData) {
            return redirect()->route('admin.users.index')->with('success', 'Berhasil membuat data baru!');
        } else {
            return redirect()->back()->with('error', 'Gagal, silahkan coba lagi!');
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
            'role' => 'admin',
        ]);

        $updateData = User::where('id', $id)->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($updateData) {
            return redirect()->route('admin.users.index')->with('success', 'Berhasil mengubah data');
        } else {
            return redirect()->back()->with('error', 'Gagal! silahkan coba lagi');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        User::where('id', $id)->delete();
        return redirect()->route('admin.users.index')->with('success', 'Berhasil menghapus data!');
    }

    public function export()
    {
        return Excel::download(new UserExport, 'users.xlsx');
    }

    public function trash()
    {
        // onlytrashed() -> filter data yang sudah di hapus, delete_at BUKAN NULL
        $userTrash = User::onlyTrashed()->get();
        return view('admin.user.trash', compact('userTrash'));
    }

    public function restore($id)
    {
        // restore()-> mengembalikan data yang sudah di hapus (menghapus nilai tanggal pada delete_at)
        $user = User::onlyTrashed()->find($id);
        $user->restore();
        return redirect()->route('admin.users.index')->with('success', 'Berhasil mengambil data!');
    }

    public function deletePermanent($id)
    {
        $user = User::onlyTrashed()->find($id);
        $user->forceDelete();
        return redirect()->back()->with('success', 'Berhasil menghapus seutuhnya!');
    }
}
