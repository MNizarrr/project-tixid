@extends('templates.app')
@section('content')
<div class="container my-5">
    <form method="POST" action="{{route('staff.schedules.update',$schedule->id)}}">
         @csrf
        {{-- route method menggunakan patch karena hanya akan mengubah bagian data : price, hours --}}
        @method('patch')
        <div class="mb-3">
            <label for="cinema_id" class="form-label">Bioskop</label>
            {{-- dibuat disabled agar tidak dapat berubah --}}
            <input type="text" name="cinema_id" id="cinema_id" value="{{$schedule['cinema']['name']}}" class="form-control" disabled>
        </div>
        <div class="mb-3">
            <label for="movie_id" class="form-label">Film:</label>
             {{-- dibuat disabled agar tidak dapat berubah --}}
            <input type="text" name="movie_id" id="movie_id" value="{{$schedule['movie']['title']}}" class="form-control" disabled>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Harga:</label>
            <input name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{$schedule['price']}}">
            @error ('price')
                <small class="text-danger">{{$message}}</small>
            @enderror
        </div>
        <div class="mb-3">
            <label for="hours" class="form-label">Jam Tayang:</label>
            {{-- munculkan sejumlah array --}}
            @foreach ($schedule['hours'] as $index=>$hours)
                <div class="d-flex align-items-center hour-item">
                     <input type="time" name="hours[]" id="hours" class="form-control my-2" value="{{$hours}}">
                     @if ($index > 0)
                        <i class="fa-solid fa-circle-xmark text-danger" style="font-size: 1.5rem; cursor: pointer" onclick="this.closest('.hour-item').remove()"></i>
                     @endif
                </div>
            @endforeach
            <div id="aditionalInput"></div>
            <span class="text-primary my-3" style="cursor:pointer" onclick="addInput()">+Tambah Input Jam</span>
            @if ($errors->has('hours.*'))
                <br>
                <small class="text-danger">{{$errors->first('hours.*')}}</small>
            @endif
        </div>
        <button type="submit" class="btn btn-primary">Kirim</button>
    </form>
</div>
@endsection

@push('script')
    <script>
        function addInput() {
            let content =  `<div class="d-flex align-items-center hour-aditional">
                     <input type="time" name="hours[]" id="hours" class="form-control my-2">
                      <i class="fa-solid fa-circle-xmark text-danger" style="font-size: 1.5rem;
                      cursor: pointer" onclick="this.closest('.hour-aditional').remove()"></i>
                      </div>`;
            document.querySelector("#aditionalInput").innerHTML += content;
        }
    </script>
@endpush
