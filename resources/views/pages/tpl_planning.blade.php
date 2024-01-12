@extends('layouts.app', ['isAuth' => true])

@section('bodyClass'){!! '' !!}@endsection

@section('content')

<div class="main-container-wrapper">

    <Planning
        :_workspace="{{ Auth::user()->getCurrentWorkspaceAttribute() }}"
        :user="{{ Auth::user() }}"
    />

</div>

@endsection
