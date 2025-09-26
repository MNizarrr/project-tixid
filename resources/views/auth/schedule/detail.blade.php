@extends('templates.app')

@section('content')

    <div class="container pt-5">
        <div class="w-75 d-block m-auto">
            <div class="d-flex">
                <div style="width: 150px; height: 200px;">
                    <img src="https://image.idntimes.com/post/20250416/snapinsai-477004280-18485421835006672-4852778433147173690-n-1080-183adc7113c71cf1ce1dc4eea11f86e1.jpg"
                        alt="" class="w-100">
                </div>
                <div class="ms-5 mt-4">
                    <h5>Pengepungan di Bukit Duri</h5>
                    <table>
                        <tr>
                            <td><b class="text-secondary">Genre</b></td>
                            <td class="px-3"></td>
                            <td>Laga / Animasi / Perang / Petualang / Drama</td>
                        </tr>
                        <tr>
                            <td><b class="text-secondary">Durasi</b></td>
                            <td class="px-3"></td>
                            <td>1 Jam 58 Menit</td>
                        </tr>
                        <tr>
                            <td><b class="text-secondary">Sutradara</b></td>
                            <td class="px-3"></td>
                            <td>Joko Anwar</td>
                        </tr>
                        <tr>
                            <td><b class="text-secondary">Rating Usia</b></td>
                            <td class="px-3"></td>
                            <td><span class="badge badge-danger">17+</span></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="w-100 row mt-5">
                <div class="col-6 pe-5">
                    <div class="d-flex flex-column justify-content-end align-items-end">
                        <div class="d-flex align-items-center">
                            <h3 class="text-warning me-2">3.7</h3>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <small>336 Vote</small>
                    </div>
                </div>
                <div class="col-6 ps-5" style="border-left: 2px solid #c7c7c7">
                    <div class="d-flex align-items-center">
                        <div class="fas fa-heart text-danger me-2">
                        </div>
                        <b>Masukan Watchlist</b>
                    </div>
                    <small>1.000 Orang</small>
                </div>
                <div class="d-flex w-100 bg-light mt-3 gap-3">
                    <div class="dropdown">
                        <button class="btn btn-light w-100 text-start dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-mdb-dropdown-init data-mdb-ripple-init aria-expanded="false">
                            <i class="fa-solid fa-location-dot"></i> Bioskop
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="#">Jakarta</a></li>
                            <li><a class="dropdown-item" href="#">Bogor</a></li>
                            <li><a class="dropdown-item" href="#">Depok</a></li>
                            <li><a class="dropdown-item" href="#">Tangerang</a></li>
                            <li><a class="dropdown-item" href="#">Bekasi</a></li>
                        </ul>
                    </div>
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
                </div>
            </div>
            <div class="mb-5">
                <div class="w-100 my-3">
                    <i class="fa-solid fa-building"></i><b class="ms-2">Lippo PLaza Ekalokasiari</b>
                    <br>
                    <small class="ms-3">Jl. Siliwangi No.123, Sukasari, Kec. Bogor Tim., Kota Bogor, Jawa Barat 16142</small>
                    <div class="d-flex gap-3 ps-3 my-2">
                        <div class="btn btn-outline -secondary">11.00</div>
                        <div class="btn btn-outline -secondary">12.00</div>
                        <div class="btn btn-outline -secondary">13.00</div>
                        <div class="btn btn-outline -secondary">14.00</div>
                        <div class="btn btn-outline -secondary">15.00</div>
                    </div>
                </div>
                <hr>
                <div class="w-100 my-3">
                    <i class="fa-solid fa-building"></i><b class="ms-2">Ramayana</b>
                    <br>
                    <small class="ms-3">Ramayana Tajur, Lt. 2, RT.04/RW.04, Muarasari, Kec. Bogor Sel., Kota Bogor, Jawa Barat 16137</small>
                    <div class="d-flex gap-3 ps-3 my-2">
                        <div class="btn btn-outline -secondary">11.00</div>
                        <div class="btn btn-outline -secondary">12.00</div>
                        <div class="btn btn-outline -secondary">13.00</div>
                        <div class="btn btn-outline -secondary">14.00</div>
                        <div class="btn btn-outline -secondary">15.00</div>
                    </div>
                </div>
            </div>
            <div class="w-100 p-2 bg-light text-center fixed-bottom">
                <a href=""><i class="fa-solid fa-ticket"></i>Beli Tiket</a>
            </div>
        </div>
    </div>
@endsection
