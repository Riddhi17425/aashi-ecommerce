<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappEnquiry extends Model
{
    protected $fillable = ['mobile', 'message'];
}