@extends('layouts.app', ['isAuth' => false])

@section('content')
<div class="container">
    <div class="row justify-content-center relative">
        <div class="col-md-8 profile-wrapper">
            <div class="card profile-card">
                <div class="profile-blur"></div>
                <img class="blur-back" src="images/CARTE_JOURNEE-neige.gif">
                <img class="img-profile-card" src="/upload/avatars/{{$user->avatar}}" alt="logo" style="z-index: 99;width: 101px; height:140px; float:left;border-radius:50%;">
                <h2>{{ $user->name}} Profile</h2>
                <form class="profile-form__card" enctype="multipart/form-data" action="/profile" method="POST">

                    <label for=""> Update Profile Image</label>
                        <div style="margin-top: 71px;">
                            <input id="file-input" class="file-input__input" type="file" name="avatar" style="margin-top: 30px;margin-bottom: 30px;">
                            <label class="file-input__label" for="file-input">
                                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="upload" class="svg-inline--fa fa-upload fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                    <path
                                        fill="currentColor" d="M296 384h-80c-13.3 0-24-10.7-24-24V192h-87.7c-17.8 0-26.7-21.5-14.1-34.1L242.3 5.7c7.5-7.5 19.8-7.5 27.3 0l152.2 152.2c12.6 12.6 3.7 34.1-14.1 34.1H320v168c0 13.3-10.7 24-24 24zm216-8v112c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24V376c0-13.3 10.7-24 24-24h136v8c0 30.9 25.1 56 56 56h80c30.9 0 56-25.1 56-56v-8h136c13.3 0 24 10.7 24 24zm-124 88c0-11-9-20-20-20s-20 9-20 20 9 20 20 20 20-9 20-20zm64 0c0-11-9-20-20-20s-20 9-20 20 9 20 20 20 20-9 20-20z">
                                    </path>
                                </svg>
                                <span>Upload file</span></label>
                        </div>
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input class="card-submit" type="submit">
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
