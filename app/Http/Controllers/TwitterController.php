<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Session;
use Thujohn\Twitter\Facades\Twitter;
use App\TwitterUsers;
use App\UserTweets;

/**
 * Class TwitterController
 * @package App\Http\Controllers
 */
class TwitterController extends Controller
{
    /**
     * @var bool
     */
    protected $signInTwitter    = true;

    /**
     * @var bool
     */
    protected $forceLogin       = false;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var UserTweets
     */
    protected $userTweets;

    /**
     * @var TwitterUsers
     */
    protected $twitterUsers;

    /**
     * TwitterController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request  = $request;
    }

    /**
     * @return mixed
     */
    public function login()
    {
        Twitter::reconfig(['token' => '', 'secret' => '']);
        $token = Twitter::getRequestToken(route('twitter::callback'));

        if (isset($token['oauth_token_secret'])) {
            $url = Twitter::getAuthorizeURL($token, $this->signInTwitter, $this->forceLogin);

            Session::put('oauth_state', 'start');
            Session::put('oauth_request_token', $token['oauth_token']);
            Session::put('oauth_request_token_secret', $token['oauth_token_secret']);

            return redirect($url);
        }

        return redirect('twitter::error');
    }

    /**
     * @return mixed
     */
    public function callback()
    {
        if (Session::has('oauth_request_token')) {
            $requestToken = [
                'token'  => Session::get('oauth_request_token'),
                'secret' => Session::get('oauth_request_token_secret'),
            ];

            Twitter::reconfig($requestToken);

            $oauthVerifier = false;

            if ($this->request->input('oauth_verifier')) {
                $oauthVerifier = $this->request->input('oauth_verifier');
            }

            $token = Twitter::getAccessToken($oauthVerifier);

            if (!isset($token['oauth_token_secret'])) {
                return redirect('login')
                    ->with('flash_error', 'We could not log you in on Twitter.');
            }

            $credentials = Twitter::getCredentials();

            if (is_object($credentials) && !isset($credentials->error)) {
                Session::put('access_token', $token);

                return redirect('home')
                    ->with('flash_notice', 'Congrats! You\'ve successfully signed in!');
            }

            return redirect('error')
                ->with('flash_error', 'Crap! Something went wrong while signing you up!');
        }
    }

    /**
     * @return mixed
     */
    public function logout()
    {
        Session::forget('access_token');
        return redirect('home')
            ->with('flash_notice', 'You\'ve successfully logged out!');
    }

    /**
     * @return string
     */
    public function error()
    {
        return 'Something went wrong';
    }

    /**
     *Store User Data
     */
    public function home()
    {
        $token    = $this->request->session()->get('access_token');

        $users  = new TwitterUsers;

        $users->oauth_token         = $token['oauth_token'];
        $users->oauth_token_secret  = $token['oauth_token_secret'];
        $users->user_id             = $token['user_id'];
        $users->screen_name         = $token['screen_name'];

        $users->save();
    }

    /**
     *Export Last hundred tweets by user_id
     */
    public function exportTweets()
    {
        $screenName = TwitterUsers::where('user_id', $this->request->get('user_id'))->first();

        $tweets = Twitter::getUserTimeline(
            [
                'screen_name' => $screenName['screen_name'],
                'count' => 100,
                'format' => 'array'
            ]
        );

        foreach ($tweets as $tweet) {
            if ($tweet['retweeted']) {
                continue;
            }

            $userTweet   = new UserTweets;

            $userTweet->user_id        = $tweet['user']['id'];
            $userTweet->tweet_id       = $tweet['id'];
            $userTweet->tweet_at       = date('Y-m-d H:i:s', strtotime($tweet['created_at']));
            $userTweet->favorite_count = $tweet['favorite_count'];
            $userTweet->retweet_count  = $tweet['retweet_count'];

            $userTweet->save();
        }
    }
}
