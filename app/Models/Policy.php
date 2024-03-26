<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','description','company_id'
    ];

    protected $casts = [
        'company_id' => 'integer',
    ];


    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

}
