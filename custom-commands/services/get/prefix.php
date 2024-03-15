<?php

if ( !$requestData->sort_by ) {

    $requestData->sort_by = "title";

}

if ( $API::$userDetail->id == 3 ) $requestData->is_visibleOnSite = 'Y';