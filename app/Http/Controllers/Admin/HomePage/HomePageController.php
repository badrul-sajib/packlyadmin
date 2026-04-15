<?php

namespace App\Http\Controllers\Admin\HomePage;

use App\Http\Controllers\Controller;

class HomePageController extends Controller
{
    public function privacyPolicy()
    {
        return view('privacy-policy');
    }
}
