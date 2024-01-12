@extends('layouts.app', ['isAuth'=>true])
@section('bodyClass'){!! '' !!}@endsection

@section('content')

    <div class="main-container-wrapper" style="background: #150c2d; padding: 0;overflow: hidden;">
        @if(Auth::user()->is_explorer)
            <explorer
                :user="{{ Auth::user() }}"
                :user_role="{{ Auth::user()->role }}"
                :userToSee="{{\Auth::user()}}"
            />
        @else
            <explorer-home :user="{{ Auth::user() }}"/>
        @endif
    </div>
@endsection
