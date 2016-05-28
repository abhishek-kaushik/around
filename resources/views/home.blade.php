@extends('master')
@section('content')
    <div class="container">
        <div class="row col-md-12">
            <table class="table">
                <thead>
                <tr>
                    <th>Screen Name</th>
                    <th>Get Rank</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{$user->screen_name}}</td>
                        <td><a class="btn btn-primary" href="{{ URL::to('tweets/' . $user->user_id) }}">Get Rank</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop