<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserTweets extends Model
{
    /**
     * @var string
     */
    protected $connection   = 'mysql';

    /**
     * @var string
     */
    protected $table        = 'user_tweets';

    /**
     * @var string
     */
    protected $primaryKey   = 'id';

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'tweet_at', 'favorite_count', 'retweet_count'];
}
