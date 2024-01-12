@extends('layouts.app', ['isAuth' => true])
@section('bodyClass'){!! '' !!}@endsection

@section('content')
    <div class="main-container-wrapper" style="background: #150c2d; padding: 0;">
        <explorer-messenger :user="{{ $user}}" :_selected-conversation-id="{{ $conversationId }}" :_currencies="{{ $currencies }}" />
    </div>
@endsection
