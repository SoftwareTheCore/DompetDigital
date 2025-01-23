<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // Nama tabel dalam database
    protected $table = 'transactions';

    // Kolom yang dapat diisi (mass assignable)
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'to_user_id',
    ];

    // Relasi ke model User (pengguna yang melakukan transaksi)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke model User (pengguna penerima untuk transaksi transfer)
    public function recipient()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
