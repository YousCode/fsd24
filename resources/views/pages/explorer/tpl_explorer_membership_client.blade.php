@extends('layouts.app', ['isAuth' => true])
@section('bodyClass'){!! '' !!}@endsection

@section('content')

    <div class="main-container-wrapper" style="background: #150c2d; padding: 0;height: calc(100vh - 90px);">
        <explorer-membership-client :user="{{ Auth::user() }}"/>
    </div>

@endsection
