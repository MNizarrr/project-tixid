@extends('templates.app')

@section('content')
    <div class="container mt-5">
        <h5>Grafik Pembelian Tiket</h5>
        @if (Session::get('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
                <b>Selamat Datang, {{ Auth::user()->name }}</b>
            </div>
        @endif
        <div class="row">
            <div class="col-6">
                <h5>Data Pembelian Tiket Bulan {{ now()->format('F') }}</h5>
                <canvas id="chartBar"></canvas>
            </div>
            <div class="col-6">
                <h5>Perbandingan Film Aktif dan Non-Aktif</h5>
                <canvas id="chartDoughnut" style="width: 100px; height: 100px;"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    $(function () {

        let labelsBar = [];
        let dataBar = [];
        let dataDoughnut = [];

        // Chart Bar
        $.ajax({
            url: "{{ route('admin.tickets.chart') }}",
            method: "GET",
            success: function (response) {
                labelsBar = response.labels;
                dataBar = response.data;
                showChartBar();
            },
            error: function () {
                alert('Gagal mengambil data chart tiket!')
            }
        });

        // Chart Doughnut
        $.ajax({
            url: "{{ route('admin.movies.chart') }}",
            method: "GET",
            success: function (response) {
                dataDoughnut = response.data;
                showChartDoughnut(); // <-- WAJIB dipanggil
            },
            error: function () {
                alert('Gagal mengambil data chart film!')
            }
        });

        function showChartBar() {
            const ctx = document.getElementById('chartBar');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labelsBar,
                    datasets: [{
                        label: 'Jumlah Tiket',
                        data: dataBar,
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        function showChartDoughnut() {
            const ctx2 = document.getElementById('chartDoughnut'); // FIXED

            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: ['Film Aktif', 'Film Non-Aktif'],
                    datasets: [{
                        label: 'Perbandingan Film',
                        data: dataDoughnut,
                        backgroundColor: [
                            'rgb(0, 255, 0)',
                            'rgb(255, 0, 0)'
                        ],
                        hoverOffset: 4
                    }]
                }
            });
        }
    });
</script>
@endpush

