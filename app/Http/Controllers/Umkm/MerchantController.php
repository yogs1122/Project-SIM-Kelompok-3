<?php

namespace App\Http\Controllers\Umkm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    public function index()
    {
        return view('umkm.dashboard');
    }
}
