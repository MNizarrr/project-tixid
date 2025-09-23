@extends('templates.app')

@section('content')
    <div class="container mt-5">
        <h5>Grafik Pembelian</h5>
        @if (Session::get('Success'))
            <div class="alert alert-success">{{Session::get('Success')}}<b> Selamat Datang, {{Auth::user()->name}}</b></div>
        @endif
    </div>
@endsection
