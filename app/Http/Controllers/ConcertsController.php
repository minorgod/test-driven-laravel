<?php

namespace App\Http\Controllers;

use App\Concert;

class ConcertsController extends Controller
{
    function show($id){
        $concert = Concert::published()->findOrFail($id);
        return view('concerts.show', ['concert'=>$concert]);
    }
}
