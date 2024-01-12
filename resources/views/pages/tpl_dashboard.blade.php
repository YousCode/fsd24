@extends('layouts.app', ['isAuth' => true]) @section('bodyClass'){!! '' !!}@endsection @section('content')

<div class="main-container-wrapper" style="background: #150C2D;margin-top: 25px">

    <Dashboard :user="{{ Auth::user() }}" _workspace="{{ Auth::user()->currentWorkspace }}" />

</div>

@endsection