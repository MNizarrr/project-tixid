@extends('templates.app')

@section('content')
    <div class="container mt-5">
        @if (Session::get('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        @if (Session::get('error'))
            <div class="alert alert-danger">{{ Session::get('error') }}</div>
        @endif
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.cinemas.trash') }}" class="btn btn-secondary me-2">Data Sampah</a>
            <a href="{{ route('admin.cinemas.export') }}" class="btn btn-secondary me-2">export (.Xlsx)</a>
            <a href="{{ route('admin.cinemas.create')}}" class="btn btn-success">Tambah Data</a>
        </div>
        <h5 class="mt-3">Data Bioskop</h5>
        <table class="table table-bordered">
            <tr>
                <th>No</th>
                <th>Nama Bioskop</th>
                <th>Lokasi Bioskop</th>
                <th>Aksi</th>
            </tr>
            {{-- $cinemas : dari compact, karna pake all jadi array dimensi --}}
            @foreach ($cinemas as $index => $item)
                <tr>
                    {{-- $index dari nol, biar muncul 1 -> +1 --}}
                    <th>{{ $index + 1 }}</th>
                    {{-- name. location dari fillable model cinemas --}}
                    <th>{{ $item['name'] }}</th>
                    <th>{{ $item['location'] }}</th>
                    <th class="d-flex">
                        {{-- ['id' => $item['id']] : mengirimkan $item['id'] ke route {'id'} --}}
                        <a href="{{ route('admin.cinemas.edit', ['id' => $item['id']])}}" class="btn btn-secondary">Edit</a>
                        <form action="{{ route('admin.cinemas.delete', ['id' => $item['id']]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger">Hapus</button>
                        </form>
                    </th>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
