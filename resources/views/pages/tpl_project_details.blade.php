@extends('layouts.app', ['isAuth' => $userAuth, 'isShared' => true])

@section('bodyClass'){!! '' !!}@endsection

@section('content')

<div class="main-container-wrapper">
	<project-detail
		:user="{{ $user }}"
		:is_client="{{ $is_client }}"
		:_project="{{ $project }}"
		:_conversation="{{ $conversation }}"
		:_talents="{{ json_encode($talents) }}"
		:_path="{{ $path }}"
	/>
</div>

@endsection