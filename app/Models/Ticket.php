<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = ['users_id', 'schedule_id', 'promo_id', 'rows_of_seats', 'quantity', 'total_price', 'date', 'activated'];
}
