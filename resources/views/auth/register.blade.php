@extends('layouts.app', ['isAuth' => false])

@section('content')

@if( $user->user_status === \App\ContactRoleEnum::freelance )
<div class="register_login-wrapper js-first-login">
    <div class="explorer-form explorer-profile-infos-form row register-login_container" style="background-color: rgba(5, 1, 14, 0); padding-top: 0; height: 100% !important;padding: 0;">
        <div class="infos-form col-12 register-login_info">
            <div class="gradient"></div>
            <div class="register-wrapper_wrapper">
                <form class="register_login-form" action="{{route("freelance")}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="token" name="token" value="{{$token}}" />
                    <p class="welcome-screen__register">Hello {{$user->firstname}} !</p>
                    <div class="main-profile_register ">
                        <div class="profile-image_register profile-register @error('file') is-invalid field-error @enderror" style="width: 90px;height: 90px;background: #506D99;justify-content: center;border-radius: 50%;display: flex;position: relative;">
                            @if(is_null($user->firstlogin))
                                <div class="image_register register_profile" style="max-width: 100%;justify-content: center;position: absolute;height: 90px"><span class="user-initials" style="font-family: 'Proxima Nova';font-style: normal;font-weight: 700;font-size: 40px;line-height: 29px;">{{$result}}</span></div>
                                <div  style="max-width: 100%;justify-content: center;z-index: 2;"><span class="user-initials"></span>
                                    <img id="file-preview" src="" class="image_register" style="max-width: 100%;width: 90px;height: 90px;justify-content: center;border-radius: 50%;display: none;object-fit: cover;"></div>
                            @endif
                        </div>


                        <div class="edit-profile-image-container" style="display: grid;place-items: center;" >
                            <input id="file-input" class="edit-profile-image_register @error('avatar') is-invalid field-error @enderror" type="file" name="file" onchange="loadPreview(event)"style="margin-top: 30px;margin-bottom: 30px;">
                            <label class="edit-label" for="file-input">
                                <span class="edit-profile-image_register" style="font-size: 15px">Ajouter une photo de profil</span>
                            </label>
                            <span class="edit-span" style="font-style: normal;font-weight: 400;font-size: 14px;line-height: 8px;margin-top: 10px;margin-bottom: 20px;">Dimension minimale : 300x300px - La photo ne doit pas excéder 2 Mo</span>
                        </div>
                        @error('file')
                        <span class="invalid-feedback error-message" style="text-align: center;padding: 0;" >
                                            <strong class="red-error-message">{{ $message }}</strong>
                                        </span>
                        @enderror
                    </div>

                    <div class="infos-form-group">
                        <div class="explorer-form-main-label">
                            <div class=" icon-register flex-important register-none"><span class="picto-explorer-user"/></div>
                            <h2>Informations personnelles*</h2>
                        </div>
                        <div class="input-container_register no-margin_register">
                            <div class="register_block-important" style="width: 49.5%">
                                <input  class="border-register max-width-input margin-bottom_important register-background_important @error('firstname') is-invalid field-error @enderror" placeholder="Prénom" type="text" value="{{ old('firstname') ?? $user->firstname}}" name="firstname">
                                @error('firstname')
                                <span class="invalid-feedback error-message" >
                                            <strong class="red-error-message">{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>

                            <div class="register_block-important" style="width: 49.5%">
                                <input class="border-register max-width-input margin-bottom_important register-background_important @error('lastname') is-invalid field-error @enderror" placeholder="Nom" type="text" value="{{ old('lastname') ?? $user->lastname }}" name="lastname">
                                @error('lastname')
                                <span class="invalid-feedback error-message" >
                                            <strong class="red-error-message">{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="infos-form-group">
                        <div class="explorer-form-main-label">
                            <div class="icon-register flex-important register-none"><span class="picto-explorer-user"/></div>
                            <h2>Coordonnées*</h2>
                        </div>
                        <div>
                            <div class="input-container_register no-margin_register">
                                <div class="register_block-important" style="width: 49.5%">
                                    <input class="border-register max-width-input margin-bottom_important register-background_important  @error('email') is-invalid field-error @enderror"  placeholder="Votre mail" type="email" value="{{ old('email') ?? $user->email }}" name="email">
                                    @error('email')
                                    <span class="invalid-feedback error-message" >
                                                <strong class="red-error-message">{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="register_block-important" style="width: 49.5%">
                                    <input class="border-register max-width-input margin-bottom_important register-background_important @error('phone') is-invalid field-error @enderror" placeholder="Votre numéro" type="tel" value="{{ old('phone') ?? $user->phone }}" name="phone">
                                    @error('phone')
                                    <span class="invalid-feedback error-message" >
                                                <strong class="red-error-message">{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="infos-form-group">
                        <div class="explorer-form-main-label">
                            <div class="icon-register flex-important register-none"><span class="picto-explorer-job"/></div>
                            <h2>Métier & Compétences</h2>
                        </div>
                        <div class="input-container_register no-margin_register" style="margin-bottom: 15px;">
                            <div class="selected_wrapper-job" style="width: 49.5%;">
                                <select id="select-jobs" class="js-jobs-data js-select2 js-required filters-dropdown @error('job') is-invalid field-error @enderror" name="job" >
                                    <option></option>>
                                    @foreach($job as $jobs)
                                        <option value="{{ $jobs->id}}" {{ (collect(old('job'))->contains($jobs->id)) ? 'selected':'' }} >{{ $jobs->name }}</option>
                                    @endforeach
                                </select>
                                @error('job')
                                <span class="invalid-feedback error-message">
                                    <strong class="red-error-message">{{ $message }}</strong>
                                </span>
                                @enderror
                               {{--}} {!! Form::select('jobs', $job); !!}{{--}}
                            </div>
                            <div style="width: 49.5%;">
                                <input class="border-register register-background_important" placeholder="Portfolio" style="width: 100%;margin:0;" type="text" name="website" value="{{old("website")}}">
                                @error('website')
                                <span class="invalid-feedback error-message" >
                                                <strong class="red-error-message">{{ $message }}</strong>
                                            </span>
                                @enderror
                            </div>

                        </div>
                        <div class="input-container_register selected_wrapper-skills no-margin_register">
                            <select id="select-skills" class="js-skills-data js-select2 js-required multiple-choices filters-dropdown @error('skills') is-invalid field-error @enderror"  name="skills[]" multiple="multiple">
                                <span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span>
                                @foreach($skill as $skills)
                                    <option value="{{ $skills->id}}" {{ (collect(old('skills'))->contains($skills->id)) ? 'selected':'' }} >{{ __('skills.' . $skills->name) }}</option>
                                @endforeach
                            </select>
                                @error('skills')
                                    <span class="error-message" >
                                        <strong class="red-error-message">{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
                    </div>
                    <div class="infos-form-group">
                        <div class="explorer-form-main-label">
                            <div class="icon-register flex-important register-none"><span class="picto-explorer-job"/></div>
                            <h2>Description</h2>
                        </div>
                        <div class=" no-padding-form">
                                <textarea class="border-register register-background_important " placeholder="Décrivez-vous en quelques lignes..." type="text" name="description" style="width: 100%; margin-left: 66px;">{{old("description")}}</textarea>
                            @error('description')
                            <span class="invalid-feedback error-message" >
                                                <strong class="red-error-message">{{ $message }}</strong>
                                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="explorer-form-main-label">
                        <div class="icon-register flex-important register-none"><span class="picto-explorer-lock"/></div>
                        <h2>Mot de passe*</h2>
                    </div>
                    <div class="input-container_register no-margin_register" style="margin-bottom: 10px;">
                        <label class="color-white">Nouveau mot de passe - 8 caractères minimum avec au moins une Majuscule et 1 symbole</label>
                        <input  class="password-input border-register register-background_important @error('password') is-invalid field-error @enderror " placeholder="Votre nouveau mot de passe*" type="password" name="password" style="margin: 5px 0 0px 0;">
                        @error('password')
                        <span class="invalid-feedback error-message" >
                                            <strong class="red-error-message">{{ $message }}</strong>
                                        </span>
                        @enderror
                    </div>
                    <div class="input-container_register no-margin_register">
                        <label class="color-white">Confirmez le nouveau mot de passe</label>
                        <input  class="password-input border-register register-background_important @error('password_confirmation') is-invalid field-error @enderror" placeholder="Confirmez votre nouveau mot de passe*" type="password" name="password_confirmation">

                    </div>
                    <div class="check-label">
                        <input id="talent-register_checkbox" class="register_checkbox @error('check') is-invalid field-error @enderror" type="checkbox" name="check" style="appearance: unset;padding: 0 !important;" value="1"{{(old('check') ? "checked" : "")}}>
                        <label class="label_check" for="check" style="margin-left: 10px;">Je déclare avoir lu et accepté le <span class="info">Condition générale d'utilisation</span> et la <span class="info">Politique de confidentialité.</span></label>
                    </div>
                    @error('check')
                    <span class="invalid-feedback error-message" style="padding-left: 10.5rem;">
                                            <strong class="red-error-message">{{ $message }}</strong>
                                        </span>
                    @enderror
                    <button id="submit" class="go-button" type="submit" >Allons-y !</button>

                </form>
            </div>
        </div>
    </div>
    @endif
    @if($user->user_status === \App\ContactRoleEnum::business || $user->user_status === \App\ContactRoleEnum::producer)
        <div class="register_login-wrapper js-first-login">
            <div class="explorer-form explorer-profile-infos-form row register-login_container" style="background-color: rgba(5, 1, 14, 0);padding-top: 0;height: 100% !important;padding: 0;">
                <div class="infos-form col-12 register-login_info">
                    <div class="gradient"></div>
                    <form id="upload-image-form" class="register_login-form" method="post" enctype="multipart/form-data" action="{{route('client')}}" style="display: grid;margin: auto">
                        @csrf
                        <input type="hidden" id="token" name="token" value="{{$token}}" />
                        <p class="welcome-screen__title" style="text-align: center;background: linear-gradient(119.48deg, #7637C7 32.43%, #C957DB 80.9%);-webkit-background-clip: text;-webkit-text-fill-color: transparent;background-clip: text;text-fill-color: transparent;">Hello {{$user->firstname}} !</p>
                        <div class="main-profile_register ">
                            <div class="profile-image_register profile-register @error('file') is-invalid field-error @enderror" style="width: 90px;height: 90px;background: #506D99;justify-content: center;border-radius: 50%;display: flex;position: relative;">
                                @if(is_null($user->firstlogin))
                                    <div class="image_register register_profile" style="max-width: 100%;justify-content: center;position: absolute;height: 90px"><span class="user-initials" style="font-family: 'Proxima Nova';font-style: normal;font-weight: 700;font-size: 40px;line-height: 29px;">{{$result}}</span></div>
                                    <div  style="max-width: 100%;justify-content: center;z-index: 2;"><span class="user-initials"></span>
                                        <img id="file-preview" src="" class="image_register" style="max-width: 100%;width: 90px;height: 90px;justify-content: center;border-radius: 50%;display: none;object-fit: cover;object-fit: cover;"></div>
                                @endif
                            </div>


                            <div class="edit-profile-image-container" style="display: grid;place-items: center;" >
                                <input id="file-input" class="edit-profile-image_register @error('avatar') is-invalid field-error @enderror" type="file" name="file" onchange="loadPreview(event)"style="margin-top: 30px;margin-bottom: 30px;">
                                <label class="edit-label" for="file-input">
                                    <span class="edit-profile-image_register" style="font-size: 15px">Ajouter une photo de profil</span>
                                </label>
                                <span class="edit-span" style="font-style: normal;font-weight: 400;font-size: 14px;line-height: 8px;margin-top: 10px;margin-bottom: 20px;">Dimension minimale : 300x300px - La photo ne doit pas excéder 2 Mo</span>
                            </div>
                            @error('file')
                            <span class="invalid-feedback error-message" style="text-align: center;padding: 0;" >
                                            <strong class="red-error-message">{{ $message }}</strong>
                                        </span>
                            @enderror
                        </div>

                        <div class="infos-form-group">
                            <div class="explorer-form-main-label">
                                <div class=" icon-register flex-important register-none"><span class="picto-explorer-user"/></div>
                                <h2>Informations personnelles*</h2>
                            </div>
                            <div class="input-container_register no-margin_register" style="margin-left: 56px">
                                <div class="register_block-important" style="width: 49.5%">
                                    <input  class="border-register max-width-input margin-bottom_important register-background_important @error('firstname') is-invalid field-error @enderror" placeholder="Prénom" type="text" value="{{ old('firstname') ?? $user->firstname}}" name="firstname">
                                    @error('firstname')
                                    <span class="invalid-feedback error-message" >
                                            <strong class="red-error-message">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="register_block-important" style="width: 49.5%">
                                    <input class="border-register max-width-input margin-bottom_important register-background_important @error('lastname') is-invalid field-error @enderror" placeholder="Nom" type="text" value="{{ old('lastname') ?? $user->lastname }}" name="lastname">
                                    @error('lastname')
                                    <span class="invalid-feedback error-message" >
                                            <strong class="red-error-message">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="infos-form-group">
                            <div class="explorer-form-main-label">
                                <div class="icon-register flex-important register-none"><span class="picto-explorer-user"/></div>
                                <h2>Coordonnées*</h2>
                            </div>
                            <div>
                                <div class="input-container_register no-margin_register" style="margin-left: 56px">
                                    <input class="border-register register-background_important @error('email') is-invalid field-error @enderror"  placeholder="Votre mail" type="email" value="{{ $user->email }}" name="email">
                                    @error('email')
                                    <span class="invalid-feedback error-message" >
                                            <strong class="red-error-message">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <input class="border-register register-background_important @error('phone') is-invalid field-error @enderror" placeholder="Votre numéro" type="tel" value="{{ $user->phone }}" name="phone">
                                    @error('phone')
                                    <span class="invalid-feedback error-message" >
                                            <strong class="red-error-message">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="infos-form-group no-padding-form" >
                            <div class="explorer-form-main-label">
                                <div class="icon-register flex-important register-none"><span class="picto-explorer-job"/></div>
                                <h2>Métier & Société</h2>
                            </div>
                            <div class="input-container_register no-margin_register" style="margin-bottom: 15px;margin-left: 56px;width: 100%;">
                                <div class="selected_wrapper-job" style="width: 49.5%;">
                                    <select id="select-jobs" class="js-jobs-data js-select2 js-required multiple-choices filters-dropdown @error('job') is-invalid field-error @enderror" id="job-data" name="job" >
                                        <span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span>
                                        @foreach($job as $jobs)
                                            <option value="{{ $jobs->id}}" {{ (collect(old('job'))->contains($jobs->id)) ? 'selected':'' }} >{{ $jobs->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('job')
                                    <span class="invalid-feedback error-message">
                                            <strong class="red-error-message">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    {{--}} {!! Form::select('jobs', $job); !!}{{--}}
                                </div>
                                <div style="width: 49.5%;">
                                    <input class="border-register register-background_important"  placeholder="Nom de la société (facultatif)" style="width: 100%;margin:0;" type="text" name="company">
                                </div>

                            </div>
                        </div>
                        <div class="infos-form-group no-padding-form" >
                            <div class="explorer-form-main-label">
                                <div class="icon-register flex-important register-none"><span class="picto-explorer-lock"/></div>
                                <h2>Mot de passe*</h2>
                            </div>
                            <div class="input-container_register no-margin_register" style="margin-bottom: 10px;margin-left: 55px;width: 100%;">
                                <label for="password" class="color-white">Nouveau mot de passe - 8 caractères minimum avec au moins une Majuscule et 1 symbole</label>
                                <input  class="password-input border-register register-background_important @error('password') is-invalid field-error @enderror " placeholder="Votre nouveau mot de passe*" type="password" name="password" style="margin: 5px 0 0px 0;">
                                @error('password')
                                <span class="invalid-feedback error-message" >
                                                <strong class="red-error-message">{{ $message }}</strong>
                                            </span>
                                @enderror
                            </div>
                            <div class="input-container_register no-margin_register" style="margin-left: 55px;width: 100%;">
                                <label for="password_confirmation" class="color-white">Confirmez le nouveau mot de passe</label>
                                <input  class="password-input border-register register-background_important @error('password_confirmation') is-invalid field-error @enderror" placeholder="Confirmez votre nouveau mot de passe*" type="password" name="password_confirmation">
                            </div>
                            <div class="check-label">
                                <input id="talent-register_checkbox" class="" class="register_check @error('check') is-invalid field-error @enderror" type="checkbox" name="check" style="appearance: unset;padding: 0 !important;" value="1" {{(old('check') ? "checked" : "")}}>
                                <label class="label_check" for="check" style="margin-left: 10px;">Je déclare avoir lu et accepté le <span class="info">Condition générale d'utilisation</span> et la <span class="info">Politique de confidentialité.</span></label>
                            </div>
                            @error('check')
                            <span class="invalid-feedback error-message" style="padding-left: 10.5rem;">
                                                <strong class="red-error-message">{{ $message }}</strong>
                                            </span>
                            @enderror
                            <button id="go-button" class="go-button" type="submit" >Allons-y !</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

</div>
    @section('gradient')
        <script>

            let gradient = document.querySelector(".gradient")
            window.addEventListener("scroll", (event) => {
                let gradient = document.querySelector(".gradient")
                let scroll = this.scrollY;
                if(scroll && scroll >= 110){
                    //gradient.style.display = "none"
                    gradient.style.opacity = 0
                }else{
                   // gradient.style.display = "block"
                    gradient.style.opacity = 1
                }

            })
        </script>
    @stop
@endsection

