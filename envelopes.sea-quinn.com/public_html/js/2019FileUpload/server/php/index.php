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
require('/Volumes/Server/cmsphp/Includes/Libraries/JS/2019FileUpload/server/php/UploadHandler.php');
$upload_handler = new UploadHandler();
$upload_handler->set_upload_path($_REQUEST['app'], $_REQUEST['id']);



