<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cinema extends Model
{
    //mendaftarkan softDeletes
    use SoftDeletes;

    // mendaftarkan column yang akan diiisi oleh pengguna (column migration delain id dan timestamps)
    protected $fillable = ['name', 'location'];

    //karna cinema pegang posisi pertama (one to many : cinema dan schedules)
    //mendaftarkan jenis relasinya
    //nama relasi tunggal/jamak tergantung jenisnya. schedules (many) jamak
    public function schedules() {
        // one to one : hasOne
        // one to many : hasMany
        return $this->hasMany(Schedule::class);
    }
}
