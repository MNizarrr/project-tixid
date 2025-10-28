@extends('templates.app')

@section('content')
    <div class="container my-5">
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
        <h3 class="my-3">Data Sampah Petugas</h3>
        @if (Session::get('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        <table class="table table-bordered">
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
            </tr>
            @foreach ($userTrash as $key => $user)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $user['name'] }}</td>
                    <td>{{ $user['email'] }}</td>
                    <td>
                        @if ($user->role === 'admin')
                            <span class="badge badge-primary">admin</span>
                        @elseif ($user->role === 'staff')
                            <span class="badge badge-success">staff</span>
                        @else
                            <span class="badge badge-secondary">{{ $user->role }}</span>
                        @endif
                    </td>
                    <td class="d-flex">
                        <form action="{{ route('admin.users.restore', $user->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-success ms-2">Kembalikan</button>
                        </form>
                        <form action="{{ route('admin.users.delete_permanent', $user->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger ms-2">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
