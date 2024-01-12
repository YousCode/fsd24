@extends('layouts.app', ['isAuth' => true])

@section('bodyClass'){!! '' !!}@endsection

@section('content')

<div class="main-container-wrapper">

	<Talents 
		:_workspace="{{ Auth::user()->getCurrentWorkspaceAttribute() }}"
	></Talents>
</div> 

@endsection

