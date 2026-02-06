<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function home()
    {
        return view('website.home');
    }

    public function about()
    {
        return view('website.about');
    }

    public function careers()
    {
        return view('website.careers');
    }

    public function contact()
    {
        return view('website.contact');
    }

    public function loginOptions()
    {
        return view('website.login-options');
    }

    public function applyNow($id = null)
    {
        // For now, return a simple view. You can extend this to fetch job data
        return view('website.apply-now');
    }
}
