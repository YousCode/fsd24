<?php
/*
 * jQuery File Upload Plugin PHP Example
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * https://opensource.org/licenses/MIT
 */

error_reporting(E_ALL | E_STRICT);
require('UploadHandler.php');

$options = array(
    'image_versions' => array(),
    'upload_dir' => dirname($_SERVER['SCRIPT_FILENAME']) . '/../../storage/uploads/'. $_GET['folder'] .'/',
    'upload_url' => '/uploads/'. $_GET['folder'] .'/',
);

$upload_handler = new UploadHandler($options);