<?php

// 4 routing HTTP method
// get untuk menampilkan sebuah halaman
// post untuk memproses penambahan data baru
// patch untuk mengubah data yang sudah ada
// delete untuk menghapus

use App\Http\Controllers\CinemaController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use PHPUnit\Framework\Attributes\Group;

Route::get('/', [MovieController::class, 'home'])->name('home');
Route::get('/movies/active', [MovieController::class, 'homeMovies'])->name('home.movies.all');

Route::get('/schedules/detail/{movie_id}', [MovieController::class, 'movieSchedule'])
    ->name('schedules.detail');

Route::get('/cinema', function () {
    return view('cinema');
})->name('cinema');

Route::get('/ticket', function () {
    return view(view: 'ticket');
})->name('ticket');

Route::post('/signup', [UserController::class, 'register'])->name('signup.send_data');
//   ↑ data yang di dapat dari signup akan di simpan di table usercontroller
Route::post('/auth', [UserController::class, 'authentication'])->name('auth');

Route::get('/logout', [UserController::class, 'logout'])->name('logout');

//untuk halaman admin

//prefix() : memberikan path awalan, /admin ditulis 1x bisa di pake berkali kali
Route::middleware('isAdmin')->prefix('/admin')->name('admin.')->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    //bioskop
    Route::prefix('/cinemas')->name('cinemas.')->group(function () {
        Route::get('/index', [CinemaController::class, 'index'])->name('index');
        Route::get('/create', [CinemaController::class, 'create'])->name('create');
        Route::post('/store', [CinemaController::class, 'store'])->name('store');
        // paameter placeholder - {id} : Mencari data spesifik
        Route::get('/edit/{id}', [CinemaController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [CinemaController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [CinemaController::class, 'destroy'])->name('delete');
        Route::get('/export', action: [CinemaController::class, 'export'])->name('export');
        Route::get('trash', [CinemaController::class, 'trash'])->name(name: 'trash');
        Route::patch('/restore/{id}', [CinemaController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [CinemaController::class, 'deletePermanent'])->name('delete_permanent');
    });

    //film
    Route::prefix('/movies')->name('movies.')->group(function () {
        Route::get('/', [MovieController::class, 'index'])->name('index');
        Route::get('/create', [MovieController::class, 'create'])->name('create');
        Route::post('/store', [MovieController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [MovieController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [MovieController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [MovieController::class, 'destroy'])->name('delete');
        Route::put('/nonactive/{id}', [MovieController::class, 'nonactive'])->name('nonactive');
        Route::get('/export', action: [MovieController::class, 'export'])->name('export');
        Route::get('trash', [MovieController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [MovieController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [MovieController::class, 'deletePermanent'])->name('delete_permanent');
    });

    //pengguna
    Route::prefix('/users')->name('users.')->group(function () {
        Route::get('/index', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        // paameter placeholder - {id} : Mencari data spesifik
        Route::get('/   /{id}', [UserController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('delete');
        Route::get('/export', action: [UserController::class, 'export'])->name('export');
        Route::get('trash', [UserController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [UserController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [UserController::class, 'deletePermanent'])->name('delete_permanent');
    })->name('dashboard');

});

Route::prefix('/staff')->name('staff.')->group(function () {
    Route::get('/dashboard', function () {
        return view('staff.dashboard');
    })->name('dashboard');

    Route::prefix('/promos')->name('promos.')->group(function () {
        Route::get('/index', [PromoController::class, 'index'])->name('index');
        Route::get('/create', [PromoController::class, 'create'])->name('create');
        Route::post('/store', [PromoController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PromoController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PromoController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [PromoController::class, 'destroy'])->name('delete');
        Route::get('/export', action: [PromoController::class, 'export'])->name('export');
        Route::get('trash', [PromoController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [PromoController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [PromoController::class, 'deletePermanent'])->name('delete_permanent');
    });

    //jadwal tayang
    Route::prefix('/schedules')->name('schedules.')->group(function () {
        Route::get('/', [ScheduleController::class, 'index',])->name('index');
        Route::post('store', [ScheduleController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ScheduleController::class, 'edit'])->name('edit');
        Route::patch('/update/{id}', [ScheduleController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ScheduleController::class, 'destroy'])->name('delete');
        Route::get('/export', action: [ScheduleController::class, 'export'])->name('export');
        Route::get('trash', [ScheduleController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [ScheduleController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [ScheduleController::class, 'deletePermanent'])->name('delete_permanent');
    });
});

Route::middleware('isGuest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::get('/signup', function () {
        return view('auth.signup');
    })->name('signup');
});

