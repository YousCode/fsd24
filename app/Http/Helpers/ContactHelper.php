<?php

namespace App\Http\Helpers;

use App\ContactStatusEnum;
use App\Models\Contact;
use App\Notifications\NewContactRegistration;
use App\User;
use Auth;
use Illuminate\Contracts\Validation;
use DB;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use SendinBlue\Client\ApiException;


class ContactHelper
{

}
