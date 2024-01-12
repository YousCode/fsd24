<?php

use Illuminate\Support\Facades\Log;
use \App\Http\Helpers\ApiHelper;

//-- UTILS

/**
 * Info log
 * @param  string $message
 * @param  string $file
 * @return void
 */
function _info($message, $file = 'app')
{
	Log::channel($file)->info($message);
}

/**
 * Warning log
 * @param  string $message
 * @param  string $file
 * @return void
 */
function _warning($message, $file = 'app')
{
	Log::channel($file)->warning($message);
}

/**
 * Error log
 * @param  string $message
 * @param  string $file
 * @return void
 */
function _error($message, $file = 'app')
{
	Log::channel($file)->error($message);
}

/**
 * Quick helper access
 * @param  {[type]} $type [description]
 * @return {[type]}       [description]
 * @throws Exception
 */
function _helper($type = null)
{

	$helper = null;

	switch($type){
		case 'api':
			$helper = new ApiHelper();
			break;
		default:
			throw new Exception('Give the helper type');
			break;
	}

	return $helper;
}


//-- HOURS / DAYS

function _getDayFromDate($date)
{
	return date('l', strtotime($date));
}


//-- NUMBERS

function _formatNumber($number)
{
	return number_format($number, 2, '.', ' ');
}
