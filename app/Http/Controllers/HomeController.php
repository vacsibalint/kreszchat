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

    /**
     * Show stats.
     *
     * @return \Illuminate\Http\Response
     */
    public function stats($userId)
    {
        $user = DB::table('bot_users')->select('*')->where('BotUserID', $userId)->first();
        $tests = DB::table('tests')->select('*')->where('BotUserID', $userId)->get();
        
        return view('stats', [
            'user' => $user,
            'tests' => $tests,
            'userId' => $userId
        ]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile($userId)
    {
        $user = DB::table('bot_users')->select('*')->where('BotUserID', $userId)->first();

        return view('profile', [
            'user' => $user,
            'userId' => $userId
        ]);
    }
}
