<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TwitterUsers
 * @package App
 */
class TwitterUsers extends Model
{
    /**
     * @var string
     */
    protected $connection   = 'mysql';

    /**
     * @var string
     */
    protected $table        = 'twitter_users';

    /**
     * @var string
     */
    protected $primaryKey   = 'id';

    /**
     * @var array
     */
    protected $fillable = ['oauth_token', 'oauth_token_secret', 'user_id', 'screen_name'];
}
