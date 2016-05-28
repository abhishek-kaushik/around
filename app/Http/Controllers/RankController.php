<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\UserTweets;
use DB;

class RankController extends Controller
{
    protected $userTweets;

    protected $request;

    public function __construct(UserTweets $userTweets, Request $request)
    {
        $this->userTweets   = $userTweets;
        $this->request      = $request;
    }

    public function get()
    {
        $users = DB::table('user_tweets')
            ->select(DB::raw('DAYNAME(tweet_at) as day, HOUR(tweet_at) as hour, favorite_count + retweet_count as sum'))
            ->where('user_id', $this->request->get('user_id'))
            ->groupBy(DB::raw('DAYNAME(tweet_at)'))
            ->groupBy(DB::raw('HOUR(tweet_at)'))
            ->orderBy(DB::raw('favorite_count + retweet_count'), 'desc')
            ->get();

        dd($users);
    }
}
