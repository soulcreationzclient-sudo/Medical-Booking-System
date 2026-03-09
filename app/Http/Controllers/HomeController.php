<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // return view('home');
        $role=auth()->user()->role;
        // dd($role);
        return match($role){
            'super_admin'=>redirect()->route('super_admin.dashboard'),
            'hospital_admin'=>redirect()->route('hospital_admin.dashboard'),
            'doctor'=>redirect()->route('doctor.dashboard'),
            default=>abort(403),
        };
    }
}
