@extends('templates.app')

@section('content')
    <div class="container my-5">
        @if (Session::get('success'))
            <div class="alert alert-success">{{ Session::get
            ('success') }}</div>
        @endif
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.movies.create') }}" class="btn btn-success">Tambah Data</a>
        </div>
        <h5 class="mb-3">Data Film</h5>
        <table class="table table-bordered">
            <tr>
                <th>#</th>
                <th>Poster</th>
                <th>Judul Film</th>
                <th>Status Aktif</th>
                <th>Aktif</th>
            </tr>
            @foreach ($movies as $key => $item)
                <tr>
                    <th>{{ $key + 1 }}</th>
                    <th>
                        <img src="{{ asset('storage/' . $item['poster']) }}" width="120">
                    </th>
                    <th>{{ $item['title'] }}</th>
                    <th>
                        @if ($item['activated'] == 1)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-danger">Non Aktif</span>
                        @endif
                    </th>
                    <th class="d-flex">
                        {{-- event (tanda depan on-) JS : menentukan kapan js di baca --}}
                        {{-- on click : menjalankan JS ketika btn di klik --}}
                        <button class="btn btn-secondary me-2" onclick="showModal({{ $item }})">Detail</button>
                        <a href="" class="btn btn-primary me-2">Edit</a>
                        <button class="btn btn-danger me-2">Hapus</button>
                        @if ($item['activated'] == 1) {{-- dimunculkan hanya jika filmnya aktif --}}
                            <button class="btn btn-warning">Non-Aktif Film</button>
                        @endif
                    </th>
                </tr>
            @endforeach
        </table>

        <!-- Modal -->
        <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Detail Film</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalDetailBody">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- mengisi stack js --}}
@push('script')
    <script>
        function showModal(item) {
            // console.log(item)
            // pengambilan gambar di public
            // asset() fungsi php sehingga gunakan kurung kurawal 2 kali
            let image = "{{ asset('storage/') }}" + "/" + item.poster;
            // membuat konten yg akan di tambahkan
            // backtip (diatas tab) : menulis strin glebih dari 1 baris
            let content = `
            <img src="${image}" width="120" class="d-block mx-auto my-3">
            <ul>
                <li>Judul : ${item.title}</li>
                <li>Durasi : ${item.duration}</li>
                <li>Genre : ${item.genre}</li>
                <li>Sutradara : ${item.director}</li>
                <li>Usia Minimal : <span class="badge badge-danger">${item.age_rating}</span></li>
                <li>Sinopsis : ${item.description}</li>
            </ul>
            `;
            // mengambil element html yg akan di simpan di konten di atas : document.querySelector()
            let modalDetailBody = document.querySelector("#modalDetailBody");
            // isi html di atas ke id="modalDetailBody"
            modalDetailBody.innerHTML = content;
            let modalDetail = document.querySelector("#modalDetail");
            // munculkan modal bootstrap
            new bootstrap.Modal(modalDetail).show();
        }
    </script>
@endpush
