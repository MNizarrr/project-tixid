{{-- import templates --}}
@extends('templates.app')

{{-- ngisi yield --}}
@section('content')
    @if (Session::get('success'))
        {{-- Auth User() mengambil data user yang login --}}
        <div class="alert alert-success my-3">{{session::get('success')}} <b>Selamat datang, {{Auth::user()->name}}</b></div>
        {{-- Auth::user()->name : kata name di ambil dari model user - fillable --}}
    @endif

    @if (Session::get('logout'))
        <div class="alert alert-warning">{{session::get('logout')}}</div>
    @endif
    <div class="dropdown">
        <button class="btn btn-light w-100 text-start dropdown-toggle" type="button" id="dropdownMenuButton"
            data-mdb-dropdown-init data-mdb-ripple-init aria-expanded="false">
            <i class="fa-solid fa-location-dot"></i> Bogor
        </button>
        <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
            <li><a class="dropdown-item" href="#">Jakarta</a></li>
            <li><a class="dropdown-item" href="#">Bogor</a></li>
            <li><a class="dropdown-item" href="#">Depok</a></li>
            <li><a class="dropdown-item" href="#">Tangerang</a></li>
            <li><a class="dropdown-item" href="#">Bekasi</a></li>
        </ul>
    </div>

    {{-- slidder --}}
    <!-- Carousel wrapper -->
    <div id="carouselBasicExample" class="carousel slide carousel-fade" data-mdb-ride="carousel" data-mdb-carousel-init>
        <!-- Indicators -->
        <div class="carousel-indicators">
            <button type="button" data-mdb-target="#carouselBasicExample" data-mdb-slide-to="0" class="active"
                aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-mdb-target="#carouselBasicExample" data-mdb-slide-to="1"
                aria-label="Slide 2"></button>
            <button type="button" data-mdb-target="#carouselBasicExample" data-mdb-slide-to="2"
                aria-label="Slide 3"></button>
        </div>

        <!-- Inner -->
        <div class="carousel-inner">
            <!-- Single item -->
            <div class="carousel-item active">
                <img style="height: 480px" src="https://asset.tix.id/microsite_v2/d2b394a8-caae-4e0b-b455-7fdb2139ec29.webp"
                    class="d-block w-100" alt="Sunset Over the City" />
                <div class="carousel-caption d-none d-md-block">
                    <h5>Mau Nonton Apa Hari Ini ?</h5>
                    <p>Semua film tersedia kali ini dengan bonus insto 1 liter</p>
                </div>
            </div>

            <!-- Single item -->
            <div class="carousel-item">
                <img style="height: 480px" src="https://asset.tix.id/microsite_v2/c0ca475a-7eeb-44c4-b556-8adf89af790c.jpeg"
                    class="d-block w-100" alt="Canyon at Nigh" />
                <div class="carousel-caption d-none d-md-block">
                    <h5>Second slide label</h5>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                </div>
            </div>

            <!-- Single item -->
            <div class="carousel-item">
                <img style="height: 480px" src="https://asset.tix.id/microsite_v2/4b5ba50f-6183-419c-8236-9f1b06a436d9.webp"
                    class="d-block w-100" alt="Cliff Above a Stormy Sea" />
                <div class="carousel-caption d-none d-md-block">
                    <h5>Third slide label</h5>
                    <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur.</p>
                </div>
            </div>
        </div>
        <!-- Inner -->

        <!-- Controls -->
        <button class="carousel-control-prev" type="button" data-mdb-target="#carouselBasicExample" data-mdb-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-mdb-target="#carouselBasicExample" data-mdb-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    <!-- Carousel wrapper -->

    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center">
            {{-- konten kiri --}}
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-clapperboard"></i>
                <h5 class="ms-2 mt-2">Sedang Tayang</h5>
            </div>
            {{-- konten kanan --}}
            <div>
                <a href="{{ route('home.movies.all') }}" class="btn btn-warning rounded-pill">Semua</a>
            </div>
        </div>
    </div>

    <div class="container d-flex gap-2">
        {{-- gap-2 jarak antar komponen --}}
        <a href="{{ route('home.movies.all') }}" class="btn btn-outline-primary rounded-pill">Semua Film</a>
        <button class="btn btn-outline-secondary rounded-pill">XXI</button>
        <button class="btn btn-outline-secondary rounded-pill">Cinepolis</button>
        <button class="btn btn-outline-secondary rounded-pill">IMax</button>
    </div>

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

    <footer class="bg-body-tertiary text-center text-lg-start mt-5">
        <!-- Copyright -->
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.05);">
            © 2025 Copyright:
            <a class="text-body" href="https://mdbootstrap.com/">Tix.id</a>
        </div>
        <!-- Copyright -->
    </footer>
@endsection
