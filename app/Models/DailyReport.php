<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'daily_reports';

    // Primary key tabel
    protected $primaryKey = 'id';

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'report_date',
        'total_topup',
        'total_withdrawal',
        'total_transfer',
    ];

    // Kolom yang secara otomatis diatur oleh Laravel
    public $timestamps = true;

    // Format tanggal (opsional, jika diperlukan)
    protected $dates = ['report_date', 'created_at', 'updated_at'];
}
