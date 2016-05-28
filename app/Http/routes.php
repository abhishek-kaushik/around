<?php

Route::group(
    [
        'as'        => 'twitter::',
        'prefix'    => '/'
    ],
    function () {
        Route::get('', [
            'as'    => 'home',
            'uses'  => 'TwitterController@home'
        ]);

        Route::get('login', [
            'as'    => 'login',
            'uses'  => 'TwitterController@login'
        ]);

        Route::get('callback', [
            'as'    => 'callback',
            'uses'  => 'TwitterController@callback'
        ]);

        Route::get('logout', [
            'as'    => 'logout',
            'uses'  => 'TwitterController@logout'
        ]);

        Route::get('tweets/{userId}', [
            'as'    => 'tweets',
            'uses'  => 'TwitterController@exportTweets'
        ]);

        Route::get('rank/{userId}', [
            'as'    => 'rank',
            'uses'  => 'RankController@get'
        ]);
    }
);
