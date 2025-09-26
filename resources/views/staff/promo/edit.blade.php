@extends('templates.app')

@section('content')
    <div class="w-75 d-block mx-auto my-5 p-4">
        <h5 class="text-center mb-3">Edit data diskon</h5>
        <form method="POST" action="{{ route('staff.promos.update', ['id' => $promo['id']]) }}">
            @csrf
            {{-- menimpa method "POST" html menjadi put--}}
            @method('PUT')
            <div class="mb-3">
                <label for="promo_code" class="form-label">Kode Promo</label>
                <input type="text" name="promo_code" class="form-control @error('promo_code') is-invalid @enderror"
                    value="{{ $promo['promo_code'] }}">
                @error('promo_code')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Tipe Promo</label>
                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror">
                    <option value="">Pilih</option>
                    <option value="percent" {{ $promo['type'] == 'percent' ? 'selected' : '' }}>%</option>
                    <option value="rupiah" {{ $promo['type'] == 'rupiah' ? 'selected' : '' }}>Rupiah</option>
                </select>
                @error('type')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label for="discount" class="form-label"> Jumlah Potongan </label>
                <input type="number" name="discount" id="discount" class="form-control @error('discount')
                 is-invalid @enderror" value="{{ $promo['discount'] }}">
                @error('discount')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Kirim</button>
            </div>
        </form>
    </div>
@endsection
