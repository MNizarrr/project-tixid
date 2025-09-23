<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TIXID</title>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.1.0/mdb.min.css" rel="stylesheet" />
    <link rel="icon" type="https://asset.tix.id/wp-content/uploads/2022/05/TIXID_app_icon.png" href="logo.png">
</head>

<body>

    <form class="w-50 d-block mx-auto my-5" method="POST" action="{{ route('auth')}}">
        @csrf
        @if (Session::get('Success'))
            {{-- Session hurufnya di awali dengan huruf besar karena dia sebuah class yang di sambungkan dengan Static di
            sambungkan dengan :: --}}
            <div class="alert alert-success my-3">{{ Session::get('Succcess') }}
            </div>
        @endif
        @if (Session::get('Error'))
            <div class="alert alert-danger">{{ Session::get('Error')}}</div>
        @endif
        <!-- Email input -->
        @error('email')
            <small class="text-danger">{{$message}}</small>
        @enderror
        <div data-mdb-input-init class="form-outline mb-4">
            <input type="email" id="form1Example1" class="form-control @error('email') is-invalid @enderror"
                name="email" />
            <label class="form-label" for="form1Example1">Email address</label>
        </div>

        <!-- Password input -->
        @error('password')
            <small class="text-danger">{{$message}}</small>
        @enderror
        <div data-mdb-input-init class="form-outline mb-4">
            <input type="password" id="form1Example2" class="form-control @error('password') is-invalid @enderror"
                name="password" />
            <label class="form-label" for="form1Example2">Password</label>
        </div>

        <!-- Submit button -->
        <button data-mdb-ripple-init type="submit" class="btn btn-warning btn-block">Log in</button>
        <div class="text-center mt-3">
            <a href="{{ route('home') }}">Kembali</a>
        </div>
    </form>

    <!-- MDB -->
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.1.0/mdb.umd.min.js"></script>
</body>

</html>
