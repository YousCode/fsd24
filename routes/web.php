<?php

use App\Http\Controllers\API\Explorer\ExplorerRegistrationRestController;
use App\Http\Controllers\AskDemoController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\WorkspaceController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Services\Explorer\ExplorerRegistrationService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MentionController;
use App\Http\Controllers\API\TaskRestController;
use Spatie\Honeypot\ProtectAgainstSpam;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you
 register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes([
    'register' => false,
    'verify' => false
]);

//Home
Route::get('/mentions-légales','MentionController@index');
Route::get('/', [DashboardController::class, 'index'])->name('app_dashboard');

// File
Route::get('/project-file/{id}/{action?}', [AdminController::class, 'generateFile'])->name('get_file');


// Application
Route::group([
    'namespace' => '\App\Http\Controllers\Admin',
    'middleware' => ['auth']
], function () {
    // Profile
    Route::get('profile', 'UserController@profile');
    Route::post('profile', 'UserController@avatarUpdate')->name('profile');

    // Hub
    Route::get('hub', 'HubController@index')->name('app_hub');

    // Planning
    Route::get('planning', 'PlanningController@index')->name('app_planning');

    // Projects
    Route::get('projects', 'ProjectsController@index')->name('app_projects');
    Route::get('projects/{id}', 'ProjectsController@details')->name('app_project_detail');
    Route::get('api/projects/kanban', [\App\Http\Controllers\API\ProjectRestController::class, 'kanban'])->name('app_project_kanban');
    Route::get('projects/{id}/exports', 'ProjectsController@exports')->name('app_project_detail_exports');

    // Talents
    Route::get('talents', 'TalentsController@index')->name('app_talents');

    // Talent Explorer
    Route::get('/explorer', 'ExplorerController@index')->name('app_explorer');
    Route::get('/explorer/profile', 'ExplorerController@profile')->name('app_explorer_view_self_profile');
    Route::get('/explorer/profile/{id}', 'ExplorerController@profile')->name('app_explorer_view_profile');
    Route::get('/explorer/membership', 'ExplorerController@membership')->name('app_explorer_membership');
    Route::get('/explorer/messenger', 'ExplorerController@messenger')->name('app_explorer_messenger');

    // QUOTES PAYMENTS
    Route::get('/explorer/messenger/quotes/{quote_id}/checkout', [\App\Http\Controllers\API\Explorer\ExplorerPaymentController::class, 'quotePaymentPage']);
    Route::get('/explorer/messenger/account/{quote_id}/create', [\App\Http\Controllers\API\Explorer\ExplorerStripeAccountController::class, 'accountCreate']);
    Route::get('/explorer/messenger/login/{quote_id}/account', [\App\Http\Controllers\API\Explorer\ExplorerStripeFreelanceLogin::class, 'loginFreelanceToExpressStripeDashboard']);
    //Route::post('/explorer/messenger/quotes/{quote_id}/checkout', [\App\Http\Controllers\API\Explorer\ExplorerPaymentController::class, 'handleQuoteCheckout'])->name('explorer.quote.checkout');

    // Workspace
    Route::post('workspace/switch', 'WorkspaceController@switch')->name('workspace_switch');
});

//Stirpe Payment Checkout
Route::stripeWebhooks('/explorer/stripe/webhook');

/**
 * ROUTE API EXPLORER (Placée ici pour untiliser le middelware WEB pour l'auth des requêtes AJAX)
 * PROVISOIRE
 */
Route::group(
    [
        'namespace' => '\App\Http\Controllers\API\Explorer',
        'as' => 'explorer.',
        'prefix' => 'api/explorer'
    ],
    function () {
        // Register Routes
        Route::post('register', 'ExplorerRegistrationRestController@create')->name('api_registration_register');

        Route::post('unlock-access', 'ExplorerRegistrationRestController@unlockAccess')->name('api_explorer_unlock');

        // Portfolio Routes
        Route::apiResource('portfolios', 'PortfolioRestController');

    }
);

/**
 * ROUTE API EXPLORER (Placée ici pour utiliser le middelware WEB pour l'auth des requêtes AJAX)
 * PROVISOIRE
 */
Route::group(
    [
        'namespace' => '\App\Http\Controllers\API',
        'prefix' => 'api'
    ],
    function () {
        // User Routes
        Route::patch('user/password-change', 'UserRestController@passwordChange')->name('api_user_password_change');
        Route::post('user/picture-change', 'UserRestController@updatePicture')->name('api_user_picture_change');
        Route::patch('user', 'UserRestController@update')->name('api_user_update');
    }
);

Route::group(
    [
        'namespace' => '\App\Http\Controllers\API\Explorer',
        'as' => 'api.explorer.missions.',
        'prefix' => 'api/explorer/missions'
    ],
    function () {
        // PROPOSITION
        Route::patch('propositions/{mission_proposition_id}', 'ExplorerMessengerRestController@patchMissionProposition')->name('patch_mission_proposition');
        Route::patch('propositions/mission/{mission_id}', 'ExplorerMessengerRestController@patchMission')->name('patch_mission');
        Route::post('propositions', 'ExplorerMessengerRestController@postNewMissionProposition')->name('post_mission_proposition');

        // MESSAGES

        Route::get('conversations/{id}/messages', 'ExplorerMessengerRestController@getConversationMessages')->name('conversation_messages_get');
        Route::get('conversations/{conversation_id}/drive', 'ExplorerMessengerRestController@getConversationDrive')->name('conversation_messages_get');
        Route::get('projectConversations/{id}/messages', 'ExplorerMessengerRestController@getProjectConversationMessages')->name('project_conversation_messages_get');
        Route::post('conversations/{conversation_id}/messages', 'ExplorerMessengerRestController@postConversationMessage')->name('conversation_messages_post');
        Route::post('conversations/{conversation_id}/postDrive', 'ExplorerMessengerRestController@postConversationDrive')->name('conversation_messages_post');
        Route::post('conversations/{conversation_id}/markAsRead', 'ExplorerMessengerRestController@markAsRead')->name('conversation_mark_as_read');


        //QUOTES
        Route::post('conversations/{conversation_id}/quotes', 'ExplorerMessengerRestController@postNewQuote')->name('conversation_quote_post');

        // CONVERSATIONS
        Route::get('conversations', 'ExplorerMessengerRestController@getConversationsList')->name('conversations_list_get');
        Route::get('conversations/{conversationId}', 'ExplorerMessengerRestController@getConversations')->name('conversations__get');
        Route::get('conv','ExplorerMessengerRestController@getConversationLastMessage')->name('listconv');
        Route::patch('conversations/{id}', 'ExplorerMessengerRestController@patchConversation')->name('patch_conversation');

        //MISSIONS
        Route::get('', 'ExplorerMessengerRestController@getClientProposedMission')->name('get_explorer_client_proposed_mission');
    }
);


// Auth
Route::group([
    'namespace'  => '\App\Http\Controllers\Auth',
    'middleware' => ['auth']
], function () {

    // Notifications
    Route::get('api/notification', 'NotificationRestController@get')->name('api_notification_get');
    Route::get('api/notification/{id}', 'NotificationRestController@getByElemId')->name('api_notification_get');
    Route::post('api/notification', 'NotificationRestController@save')->name('api_notification_post');
    // Logout
    Route::get('logout', 'LoginController@logout')->name('app_logout');


    //Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');

    Route::middleware(['admin'])->group(function () {
        Route::get('admin', [AdminController::class, 'index'])->name('unlock');
        Route::get('approve/{contact}/approve', [UserController::class, 'newUserCreated'])->name('unlockAccess');
        Route::get('approve/{contact}/denied', [UserController::class, 'DeniedUser'])->name('DeniedAccess');
        Route::get('approve/{certified}/certify',[UserController::class,'getCertification'])->name('certified');

        Route::get('approve/{client}/approved', [ExplorerRegistrationRestController::class, 'newExplorerCreated'])->name('clientAccessExplorer');
        Route::get('approve/{client}/denied', [ExplorerRegistrationRestController::class, 'newExplorerDelete'])->name('clientDeniedExplorer');

        Route::get('approve/{talent}/success', [ExplorerRegistrationRestController::class, 'newExplorerCreated'])->name('talentAccessExplorer');
        Route::get('approve/{talent}/deny', [ExplorerRegistrationRestController::class, 'newExplorerDelete'])->name('talentDeniedExplorer');

        Route::post('workspace/update', [WorkspaceController::class, 'updateOwner'])->name('workspace_update_owner');
        Route::post('workspace/add', [WorkspaceController::class, 'addMember'])->name('workspace_add_member');
        
        Route::post('user/explorerCodeGenerate', [userController::class, 'explorerCodeGenerate'])->name('user_explorer_code_generate');
        Route::post('user/explorerCodeActions', [userController::class, 'explorerCodeActions'])->name('user_explorer_code_actions');
    });

    Route::put('task/viewed/{id}', [TaskRestController::class, 'Update'])->name('task_update_viewed');
    Route::patch('/api/task/{id}', [TaskRestController::class, 'patch'])->name('api_task_patch');

    // Route::get('onboarding', 'FirstLoginController@index')->name('app_onboarding');

    Route::middleware(['approved'])->group(function () {

    });
});

Route::group([
    'namespace' => '\App\Http\Controllers\Auth'
], function (){
    Route::post('checks',[ForgotPasswordController::class,'checkInput'])->name('checkInput');
    Route::post('checked',[ResetPasswordController::class,'checkMyInput'])->name('checkIt');
});


Route::get('register/{id}', "RegisterController@showRegistrationForm");
Route::post('register/free',"RegisterController@freelanceValidator")->name("freelance");
Route::post('register/client',"RegisterController@updateClient")->name("client");




Route::get('sharing/project/{token}', [\App\Http\Controllers\Admin\ProjectsController::class, 'details'])->name('share_project');
Route::post('project/createContact', [\App\Http\Controllers\Admin\ProjectsController::class, 'createContact'])->name('project_create_contact');

Route::get('contact', [AskDemoController::class, 'ContactViewForm']);
Route::post('contact', [AskDemoController::class, 'Create'])->name('contact')->middleware(ProtectAgainstSpam::class);
Route::post('check',[AskDemoController::class,'checkInputBeforeValidation'])->name('check');
// Project files
Route::post('project/{id}/file', [\App\Http\Controllers\Admin\ProjectsController::class, 'addFiles'])->name('api_project_post_files');