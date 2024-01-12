@extends('layouts.app')
@section('content')
    @section('partials.header')
    @endsection
<div class="main-container-wrapper">
    @if(Auth::user()->firstlogin == null )

       <Register
           :user="{{ $user }}"
           :user-to-see="{{ $userToSee }}"
           :isFreelance="isFreelance"
       />
    @else
    <Dashboard
        :user="{{ Auth::user() }}"
        />
    @endif

</div>

@endsection

