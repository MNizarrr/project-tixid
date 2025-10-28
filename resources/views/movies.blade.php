@extends('templates.app')
@section('content')
    <div class="container my-5">
        <h5 class="mb-5">Seluruh Film Sedang Tayang</h5>
        {{-- kalau form untuk searching gunakan GET & action kosong mengacu agar tetap dihalaman ini dan memproses ke route ini --}}
        <form class="row mb-3" method="GET" action="">
            @csrf
            <div class="col-10">
                <input type="text" name="search_movie" placeholder="Cari judul Film..." class="form-control">
            </div>
            <div class="col-2">
                <button type="submit" class="btn btn-primary">Cari</button>
            </div>
        </form>
        <div class="contianer d-flex gap-2 mt-4 justify-content-center">
            @foreach ($movies as $key => $item)
                <div class="card" style="width: 18rem;">
                    <img src="{{ asset('storage/' . $item['poster']) }}" class="card-img-top" alt="poster"
                        style="height: 400px; object-fit: cover;" />
                    {{-- objeck-fit : cover -> gambar ukurannya sesuai dengan aturan height dan width --}}
                    <div class="card-body bg-primary text-warning" style="padding: 0 !important">
                        {{-- karna default card-text ada paddingnya, biar padding yg di baca dr style jd dikasi
                        !important(memprioritaskan styling) --}}
                        <p class="card-text" style="padding: 0 !important; text-align: center; font-weight: bold;">
                            <a href="{{ Route('schedules.detail', $item['id']) }}" class="text-warning">Beli Tiket</a>
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
