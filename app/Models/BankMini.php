<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankMini extends Model
{
    use HasFactory;
    
    protected $table = 'bank_mini';
    protected $fillable = ['name', 'saldo'];
}
