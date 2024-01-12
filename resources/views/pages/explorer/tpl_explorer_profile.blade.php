@extends('layouts.app', ['isAuth' => true])
@section('bodyClass'){!! '' !!}@endsection

@section('content')

    <div class="" style="padding-top: 50px;width: 90%;margin: auto;">
        <explorer-profile
            :user="{{ $user }}"
            :user-to-see="{{ $userToSee }}"
        />

    </div>

@endsection
