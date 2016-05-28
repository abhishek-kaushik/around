<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\UserTweets;
use DB;

/**
 * Class RankController
 * @package App\Http\Controllers
 */
class RankController extends Controller
{
    /**
     * @var
     */
    protected $request;

    /**
     * RankController constructor.
     * @param UserTweets $userTweets
     */
    public function __construct(UserTweets $userTweets)
    {
        $this->userTweets   = $userTweets;
    }

    /**
     * @param $userId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function get($userId)
    {
        $rank = DB::table('user_tweets')
            ->select(DB::raw('DAYNAME(tweet_at) as day, HOUR(tweet_at) as hour, favorite_count + retweet_count as sum'))
            ->where('user_id', $userId)
            ->groupBy(DB::raw('DAYNAME(tweet_at)'))
            ->groupBy(DB::raw('HOUR(tweet_at)'))
            ->orderBy(DB::raw('favorite_count + retweet_count'), 'desc')
            ->get();

        return view('rank', [
            'rank'  => $rank
        ]);
    }
}
