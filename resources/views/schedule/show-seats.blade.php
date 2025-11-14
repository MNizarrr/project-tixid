@extends('templates.app')

@section('content')
    <div class="container card my-5 p-4" style="margin-bottom: 17% !important">
        <div class="card-body">
            <b>{{ $schedule['cinema']['name'] }}</b>
            {{-- mengambil tgl hari ini : now(). format('d F, Y') F nama bulan --}}
            <br>
            <b>{{ now()->format('d F, Y') }} - {{ $hour }}</b>
            <br>
            <div class="alert alert-secondary">
                <i class="fa-solid fa-info text-danger me-3"></i>Anak usia 2 tahun keatas wajib membeli tiket.
            </div>
            <div class="w-50 d-block mx-auto my-3">
                <div class="row">
                    <div class="col-4 d-flex">
                        <div style="background: #112646; width: 20px; height: 20px;"></div>
                        <span class="ms-2">Kursi Tersedia</span>
                    </div>
                    <div class="col-4 d-flex">
                        <div style="background: yellowgreen; width: 20px; height: 20px;"></div>
                        <span class="ms-2">Kursi Di Pilih</span>
                    </div>
                    <div class="col-4 d-flex">
                        <div style="background: #eaeaea; width: 20px; height: 20px;"></div>
                        <span class="ms-2">Kursi Terjual</span>
                    </div>
                </div>
            </div>

            @php
                // membuat array dengan rentang tertentu : range()
                $rows = range('A', 'I');
                $cols = range(1, 18);
            @endphp
            {{-- looping A-H ke bawah --}}
            @foreach ($rows as $row)
                <div class="d-flex justify-content-center">
                    @foreach ($cols as $col)
                        {{-- jika kursi no 10 kasi kotak kosong untuk jalan --}}
                        @if ($col == 7)
                            <div style="width: 60px;"></div>
                        @endif

                        @if ($col == 13)
                            <div style="width: 60px;"></div>
                        @endif
                        {{-- bikin style kotak no kursi --}}
                        <div style="background: #112646; color: white; width: 40px; height: 40px; margin: 5px; border-radius: 5px; text-align: center; padding-top: 3px; cursor: pointer;"
                            onclick="selectSeat('{{ $schedule->price }}', '{{ $row }}', '{{ $col }}', this)">
                            <small><b>{{ $row }} - {{ $col }}</b></small>
                        </div>
                    @endforeach
                    @if ($row == 'C')
                        <div style="height: 80px;"></div>
                    @endif

                    @if ($row == 'F')
                        <div style="height: 80px;"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="fixed-bottom">
        <div class="w-100 bg-light text-center py-3" style="border: 1px solid black;"><b>LAYAR BIOSKOP</b></div>
        <div class="row bg-light">
            <div class="col-6 text-center p-3" style="border: 1px solid black">
                <b>Total Harga</b>
                <br><b id="totalPrice">Rp. -</b>
            </div>
            <div class="col-6 text-center p-3" style="border: 1px solid black">
                <b>Kurdi di Pilih</b>
                <br><b id="selectedSeat">-</b>
            </div>
        </div>
        {{-- input:hiden menyembunyikan konten html, digunakan hanya untuk menyimpan nilai php untuk di gunakan di js --}}
        <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">
        <input type="hidden" name="schedule_id" id="schedule_id" value="{{ $schedule->id }}">
        <input type="hidden" name="hour" id="hour" value="{{ $hour }}">

        <div class="w-100 bg-light text-center py-3" id="btnCreateOrder" style="font-weight: bold">RINGKASAN ORDER</div>
    </div>
@endsection

@push('script')
    <script>
        let seats = []; //menyimpan data kursi yang sudah di pilih, bisa lebih dari 1
        // biar bisa di pake di 2 function, diddefinisikan id luar let nya
        let totalPrice = 0;

        function selectSeat(price, row, col, element) {
            // buat format a-1
            let seat = row + "-" + col;
            // cek apakah kursi tersebut ada di array seats atau tidak
            // indexOf : cek item array dan ambil index nya
            let indexSeat = seats.indexOf(seat);
            // jika ada dapet indexnya jika tidak a-1
            if (indexSeat == -1) {
                // kalau item ga ada di dalam array. tambahkan item tsb ke array
                seats.push(seat)
                // kasi warna biru terang
                element.style.background = 'yellowgreen';
            } else {
                // jika ada, maka klik kali ini untuk menghapus kursi(batal pilih)
                seats.splice(indexSeat, 1); //hapus data index ke (yg ketemu)
                // kembalikan warna ke biru gelap
                element.style.background = '#112646';
            }

            totalPrice = price * seats.length; //length : kaya count di php, itung isi array
            let totalPriceElement = document.querySelector("#totalPrice");
            totalPriceElement.innerText = "Rp. " + totalPrice;

            let selectedSeatElement = document.querySelector("#selectedSeat");
            // mengubah array jd string dipisahkan dengan koma : join()
            selectedSeatElement.innerText = seats.join(', ');

            let btnCreateOrder = document.querySelector("#btnCreateOrder");
            if (seats.length > 0) {
                btnCreateOrder.style.background = "#112646";
                btnCreateOrder.style.color = 'white';
                btnCreateOrder.classList.remove("bg-light");
                // fungsi untuk memanggil ajax, di jalankan ketika btn di klik
                btnCreateOrder.onclick = CreateOrder;
            } else {
                btnCreateOrder.style.background = '';
                btnCreateOrder.style.color = '';
                btnCreateOrder.onclick = null;
            }
        }

        function CreateOrder() {
            let data = {
                user_id: $("#user_id").val(), // ambil value dari input:hidden
                schedule_id: $("#schedule_id").val(),
                rows_of_seats: seats,
                quantity: seats.length,
                total_price: totalPrice,
                tax: 4000 * seats.length,
                hour: $("#hour").val(),
                _token: "{{ csrf_token() }}", // token csrf
            }
            // ajax (asyncronus javascript and XML) : memproses data ke atau dari border
            $.ajax({
                url: "{{ route('tickets.store') }}", //route menuju prosses data
                method: "POST", //https method
                data: data, // data yg akan di kirim ke BE
                success: function(response){
                    // kalau berhasil ngapain
                    let ticketId = response.data.id;
                    // pindah halaman  : window.location.href
                    window.location.href = `/tickets/${ticketId}/order`;
                },
                error: function(message) {
                    // kalau gagal mau ngapain
                    alert('Gagal membuat data ticket!');
                }
            })
        }
    </script>
@endpush
