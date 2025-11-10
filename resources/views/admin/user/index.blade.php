@extends('templates.app')

@section('content')
    <div class="container mt-5">
        @if (Session::get('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.users.trash') }}" class="btn btn-secondary me-2">Data Sampah</a>
            <a href="{{ route('admin.users.export') }}" class="btn btn-secondary me-2">export (.Xlsx)</a>
            <a href="{{ route('admin.users.create')}}" class="btn btn-success">Tambah Data</a>
        </div>
        <h5 class="mt-3">Data pengguna</h5>
        <table class="table table-bordered" id="usersTable">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Nama</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Role</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            {{-- @foreach ($users as $index => $item)
                <tr>
                    <th>{{ $index + 1 }}</th>
                    <th>{{ $item['name'] }}</th>
                    <th>{{ $item['email'] }}</th>
                    <th>
                        @if ($item->role === 'admin')
                            <span class="badge badge-primary">admin</span>
                        @elseif ($item->role === 'staff')
                            <span class="badge badge-success">staff</span>
                        @else
                            <span class="badge badge-secondary">{{ $item->role }}</span>
                        @endif
                    </th>
                    <th class="d-flex justify-content-center align-items-center gap-2">
                        <a href="{{ route('admin.users.edit', ['id' => $item['id']])}}" class="btn btn-primary"><i class="fa-solid fa-trash"></i></a>
                        <form action="{{ route('admin.users.delete', ['id' => $item['id']]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger"><i class="fa-solid fa-pen"></i></button>
                        </form>
                    </th>
                </tr>
            @endforeach --}}
        </table>
    </div>
@endsection

@push('script')
    <script>

        $(function () {
            $('#usersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.users.datatables') }}",
                columns: [
                    {data : 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false},
                    {data : 'name', name: 'name', orderable:true, searchable:true},
                    {data : 'email', name: 'email', orderable:true, searchable:true},
                    {data : 'role', name: 'role', orderable:true, searchable:true},
                    {data : 'action', name: 'action', orderable:false, searchable:false}
                ]
            });
        });

    </script>
@endpush
