<?php

namespace App\Http\Controllers;

use App\Concert;

class ConcertsController extends Controller
{
    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $concert = Concert::published()->findOrFail($id);
        return view('concerts.show', ['concert' => $concert]);
    }

    /**
     * @param $id
     */
    public function order($id)
    {
    }
}
