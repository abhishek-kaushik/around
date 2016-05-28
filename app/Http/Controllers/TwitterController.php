<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Session;
use Thujohn\Twitter\Facades\Twitter;
use App\TwitterUsers;

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
}
