@extends('layouts.app', ['isAuth' => true])

@section('bodyClass'){!! '' !!}@endsection

@section('content')

<div class="main-container-wrapper">

	<Projects
		:_user="{{ Auth::user() }}"
        :_workspace="{{ Auth::user()->getCurrentWorkspaceAttribute() }}"
        :_workspace_owner="{{ $workspaceOwner }}"
	/>

</div>

@endsection
