@extends('templates.app')

@section('content')
    <div class="container mt-5">
        @if (Session::get('Success'))
            <div class="alert alert-success">{{ Session::get('Success') }}</div>
        @endif
        <div class="d-flex justify-content-end">
            <a href="{{ route('staff.promos.create')}}" class="btn btn-success">Tambah Data</a>
        </div>
        <h5 class="mt-3">Data Promo</h5>
        <table class="table table-bordered">
            <tr>
                <th>No</th>
                <th>Kode Promo</th>
                <th>Total Potongan</th>
                <th>Aksi</th>
            </tr>
            {{-- $promos : dari compact, karna pake all jadi array dimensi --}}
            @foreach ($promos as $index => $item)
                <tr>
                    {{-- $index dari nol, biar muncul 1 -> +1 --}}
                    <th>{{ $index + 1 }}</th>
                    {{-- name. location dari fillable model promos --}}
                    <th>{{ $item['promo_code'] }}</th>
                    {{-- <th>{{ $item['discount'] }}</th> --}}
                    <th>
                        @if ($item['type'] == 'percent')
                            <span class="badge badge-warning">{{ $item['discount'] }} % </span>
                        @elseif ($item['type'] == 'rupiah')
                            <span class="badge badge-success">Rp . {{ number_format($item['discount'], 0, ',', '.') }}</span>
                        @endif
                    </th>
                    <th class="d-flex">
                        {{-- ['id' => $item['id']] : mengirimkan $item['id'] ke route {'id'} --}}
                        <a href="{{ route('staff.promos.edit', ['id' => $item['id']])}}" class="btn btn-primary me-2">Edit</a>
                        <form action="{{ route('staff.promos.delete', ['id' => $item['id']]) }}" method="POST">
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
