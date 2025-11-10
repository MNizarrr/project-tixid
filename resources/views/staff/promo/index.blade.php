@extends('templates.app')

@section('content')
    <div class="container mt-5">
        @if (Session::get('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        <div class="d-flex justify-content-end">
            <a href="{{ route('staff.promos.trash') }}" class="btn btn-secondary me-2">Data Sampah</a>
            <a href="{{ route('staff.promos.export') }}" class="btn btn-secondary me-2">export (.Xlsx)</a>
            <a href="{{ route('staff.promos.create')}}" class="btn btn-success">Tambah Data</a>
        </div>
        <h5 class="mt-3">Data Promo</h5>
        <table class="table table-bordered" id="promosTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Promo</th>
                    <th>Total Potongan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            {{-- $promos : dari compact, karna pake all jadi array dimensi --}}
            {{-- @foreach ($promos as $index => $item)
                <tr>
                    <th>{{ $index + 1 }}</th>
                    <th>{{ $item['promo_code'] }}</th>
                    <th>
                        @if ($item['type'] == 'percent')
                            <span class="badge badge-warning">{{ $item['discount'] }} % </span>
                        @elseif ($item['type'] == 'rupiah')
                            <span class="badge badge-success">Rp . {{ number_format($item['discount'], 0, ',', '.') }}</span>
                        @endif
                    </th>
                    <th class="d-flex">
                        <a href="{{ route('staff.promos.edit', ['id' => $item['id']])}}" class="btn btn-primary me-2">Edit</a>
                        <form action="{{ route('staff.promos.delete', ['id' => $item['id']]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger">Hapus</button>
                        </form>
                    </th>
                </tr>
            @endforeach --}}
        </table>
    </div>
@endsection

@push('script')

    <script>

        $(function() {
            $('#promosTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('staff.promos.datatables') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false},
                    {data: 'promo_code', name: 'promo_code', orderable:true, searchable:true},
                    {data: 'discount', name: 'discount', orderable:false, searchable:false},
                    {data: 'action', name: 'action', orderable:false, searchable:false},
                ]
            });
        });
    </script>

@endpush
