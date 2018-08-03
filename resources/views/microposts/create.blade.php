@extends('layouts.app')

@section('content')
    <div class="row">
        <aside class="col-xs-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ $user->name }}</h3>
                    @if(Auth::id() == $user->id)
                        <a href="{{ route('users.edit', ['id' => $user->id]) }}">Edit Profile </a>
                    @endif
                </div>
                <div class="panel-body">
                    <img class="media-object img-rounded img-responsive" src="{{ Gravatar::src($user->email, 500) }}" alt="">
                </div>
            </div>
        </aside>
        <div class="col-xs-8">
            <ul class="nav nav-tabs nav-justified">
                @if (Auth::id() == $user->id)
                    <li role="presentation" class="{{ Request::is('microposts/create') ? 'active' : '' }}"><a href="{{ route('microposts.create') }}">New Post <span class="badge"></span></a></li>
                @endif
                <li><a href="{{ route('users.show', ['id' => $user->id]) }}">Photos <span class="badge">{{ $count_microposts }}</span></a></li>
                <!--<li role="presentation" class="{{ Request::is('users/' . $user->id) ? 'active' : '' }}"><a href="{{ route('users.show', ['id' => $user->id]) }}">Photos <span class="badge">{{ $count_microposts }}</span></a></li>-->
                <li role="presentation" class="{{ Request::is('users/*/followings') ? 'active' : '' }}"><a href="{{ route('users.followings', ['id' => $user->id]) }}">Followings <span class="badge">{{ $count_followings }}</span></a></li>
                <li role="presentation" class="{{ Request::is('users/*/followers') ? 'active' : '' }}"><a href="{{ route('users.followers', ['id' => $user->id]) }}">Followers <span class="badge">{{ $count_followers }}</span></a></li>
                <li role="presentation" class="{{ Request::is('users/*/favoritings') ? 'active' : '' }}"><a href="{{ route('users.favoritings', ['id' => $user->id]) }}">Favo_Photos <span class="badge">{{ $count_favoritings }}</span></a></li>
            </ul>
           
            @if (Auth::id() == $user->id)
                @if (\Session::has('success'))
                    <div class="alert alert-success">{!! \Session::get('success') !!}</div>
                @endif
                <div class="row">
                    <div class="panel-heading">
                        <h4 class="panel-title">Media Upload</h4>
                        <div class="panel-body">
                            <p>(写真サイズは3Mb以下です。)</p>
                            {!! Form::open(['route' => ['microposts.store'], 'method' => 'POST', 'files' => true]) !!}
                                {!! Form::file('filename') !!}
                                {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection