<?php

namespace App\Http\Controllers\Umkm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UmkmOrderController extends Controller
{
    public function index()
    {
        // TODO: implement order listing for merchant
        return view('umkm.orders.index');
    }
}
