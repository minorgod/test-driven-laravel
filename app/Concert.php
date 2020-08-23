<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    // This is generally safe as long as you don't pass in user input into the constructor of your models
    protected $guarded = [];
}
