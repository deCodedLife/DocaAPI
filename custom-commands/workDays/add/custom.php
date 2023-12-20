<?php

require_once $API::$configs[ "paths" ][ "public_app" ] . "/custom-libs/workDays/validate.php";

if ( $requestData->work_days ) require_once "addEvents.php";
else require_once "addRule.php";


$API->returnResponse();