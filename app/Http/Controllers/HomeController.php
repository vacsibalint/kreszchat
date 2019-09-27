<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stats = [
            'registeredUsers' => DB::table('bot_users')->count(),
            'answeredQuestions'=> DB::table('answers')->count(),
            'correctQuestions' => DB::table('answers')->where('correct', true)->count(),
            'incorrectQuestions' => DB::table('answers')->where('correct', false)->count(),
        ];

        return view('home', [
            'stats' => $stats
        ]);
    }
}
