<?php

use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'namespace' => '\App\Http\Controllers\API',
    'middleware' => ['api']
], function () {
    // Projects
    Route::get('project', 'ProjectRestController@all')->name('api_project_all');
    Route::get('project/{id}', 'ProjectRestController@get')->name('api_project_get');
    Route::get('project/{id}/planning', 'ProjectRestController@getPlanning')->name('api_project_get_planning');
    Route::post('project', 'ProjectRestController@addOrUpdate')->name('api_project_post');
    Route::post('project/conversation', 'ProjectRestController@conversation')->name('api_project_all');
    Route::post('project/update', 'ProjectRestController@updateProjectStep')->name('api_project_update_step');
    Route::delete('project/{id}', 'ProjectRestController@delete')->name('api_project_delete');

    // Project files
    Route::post('project/{id}/file', 'ProjectRestController@addFiles')->name('api_project_post_files');
    Route::get('project/{id}/file', 'ProjectRestController@getFiles')->name('api_project_get_files');
    Route::delete('file', 'ProjectRestController@deleteFiles')->name('api_project_delete_files');
    Route::delete('drive', 'ProjectRestController@deleteDrives')->name('api_project_delete_drives');

    // Appointments
    Route::get('appointment', 'AppointmentRestController@all')->name('api_appointment_all');
    Route::get('appointment/location', 'AppointmentRestController@getLocation')->name('api_appointment_get_location');
    Route::get('appointment/{id}', 'AppointmentRestController@get')->name('api_appointment_get');
    Route::put('appointment/{id}', 'AppointmentRestController@Update');
    Route::post('appointment', 'AppointmentRestController@addOrUpdate')->name('api_appointment_post');
    Route::put('viewed/appointment', 'AppointmentRestController@viewed')->name('api_appointment_update_viewed');
    Route::delete('appointment/{id}', 'AppointmentRestController@delete')->name('api_appointment_delete');

    // Talents
    Route::get('talent', 'TalentRestController@all')->name('api_talent_all');
    Route::get('talentPortfolio', 'TalentRestController@onlyPortfolio')->name('api_talent_portfolio');
    Route::get('talent/{id}', 'TalentRestController@get')->name('api_talent_get');
    Route::post('talent', 'TalentRestController@addOrUpdate')->name('api_talent_post');
    Route::patch('talent', 'TalentRestController@updateProfile')->name('api_talent_profile');
    Route::delete('talent/{id}', 'TalentRestController@delete')->name('api_talent_delete');

    // Skills
    Route::get('skill', 'SkillRestController@all')->name('api_skill_all');
    Route::post('skill', 'SkillRestController@add')->name('api_skill_add');
    Route::delete('skill/{id}', 'SkillRestController@delete')->name('api_skill_delete');

    // Jobs
    Route::get('job', 'JobRestController@all')->name('api_job_all');
    Route::post('job', 'JobRestController@add')->name('api_job_add');
    Route::delete('job/{id}', 'JobRestController@delete')->name('api_job_delete');

    // Clients
    Route::get('client', 'ClientRestController@all')->name('api_client_all');

    // Exports
    Route::get('export', 'ExportRestController@all')->name('api_export_all');
    Route::post('export', 'ExportRestController@addOrUpdate')->name('api_export_post');
    Route::post('export/updateStatus', 'ExportRestController@updateStatus')->name('api_export_updateStatus');
    Route::delete('export', 'ExportRestController@delete')->name('api_export_delete');

    // Resolutions
    Route::get('resolution', 'ResolutionRestController@all')->name('api_resolution_all');
    Route::post('resolution', 'ResolutionRestController@add')->name('api_resolution_add');
    Route::delete('resolution/{id}', 'ResolutionRestController@delete')->name('api_resolution_delete');

    // Formats
    Route::get('format', 'FormatRestController@all')->name('api_format_all');

    // Languages
    Route::get('language', 'LanguageRestController@all')->name('api_language_all');
    Route::post('language', 'LanguageRestController@add')->name('api_language_add');
    Route::delete('language/{id}', 'LanguageRestController@delete')->name('api_language_delete');

    // Project Category
    Route::get('project-category', 'ProjectCategoryRestController@all')->name('api_project_category_all');
    Route::post('project-category', 'ProjectCategoryRestController@add')->name('api_project_category_add');
    Route::delete('project-category/{id}', 'ProjectCategoryRestController@delete')->name('api_project_category_delete');

    // Tasks
    Route::get('task', 'TaskRestController@all')->name('api_task_all');
    Route::get('task/{id}', 'TaskRestController@get')->name('api_task_get');
    Route::post('task', 'TaskRestController@addOrUpdate')->name('api_task_post');
    Route::post('task/dailyTaskToClose', 'TaskRestController@dailyTaskToClose')->name('api_task_daily_task_to_close');
    Route::put('task/note/{id}', 'TaskRestController@updateNote');
    Route::put('task/date/{id}', 'TaskRestController@updateDate');
    Route::delete('task/{id}', 'TaskRestController@delete')->name('api_task_delete');
    Route::delete('parent/task/{id}', 'TaskRestController@deleteTask')->name('api_parent_task_delete');

    // TaskType
    Route::get('task-type', 'TaskTypeRestController@all')->name('api_task_type_all');
    Route::post('task-type', 'TaskTypeRestController@add')->name('api_task_type_add');
    Route::delete('task-type/{id}', 'TaskTypeRestController@delete')->name('api_task_type_delete');

    // Rooms
    Route::get('room', 'RoomRestController@all')->name('api_room_all');

    // Planning
    Route::get('planning', 'PlanningRestController@get')->name('api_planning_get');
    Route::post('group', 'PlanningRestController@addGroup')->name('api_add_planning');
    Route::get('group', 'PlanningRestController@getGroups')->name('api_get_group');
    Route::delete('group', 'PlanningRestController@deleteGroup')->name('api_delete_group');

    // Dashboard
    Route::post('dashboard/{id?}', 'DashboardRestController@get')->name('api_dashboard_get');
    Route::get('dashboard/getTaskToClose/{id?}', 'DashboardRestController@getTaskToClose')->name('api_dashboard_get_task_to_close');

});


/*******************
 * Explorer
 *******************/

Route::group(
    [
        'namespace' => '\App\Http\Controllers\API\Explorer',
        'as' => 'explorer.',
        'prefix' => 'explorer'
    ],
    function () {
        Route::apiResource('portfolios', 'PortfolioRestController');
    }
);
