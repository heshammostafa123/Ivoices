<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable=['product_name','description','section_id'];

   /// protected $guarded=[];

    public function section(){
        return $this->belongsTo('App\Models\Section');
    }
}
