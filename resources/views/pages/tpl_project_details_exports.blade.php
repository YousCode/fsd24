@extends('layouts.app', ['isAuth' => true])

@section('bodyClass'){!! '' !!}@endsection

@section('content')

<div class="main-container-wrapper exxport-popup">
    <export-form
        :_project="{{ $project }}"
        :_exxports="{{ $exports }}"
        :_type="{{ $type }}"
        :_exxport="{{ $export }}"
    ></export-form>
</div>

@endsection