@extends('templates.app')
@section('content')
    <div class="container my-5">
        <h5 class="mb-5">Seluruh Film Sedang Tayang</h5>
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
                            <a href="{{ Route('schedules.detail') }}" class="text-warning">Beli Tiket</a>
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
