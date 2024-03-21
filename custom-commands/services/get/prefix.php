<?php

if ( !$requestData->sort_by ) {

    $requestData->sort_by = "title";

}

if ( $API->isPublicAccount() ) $requestData->is_visibleOnSite = 'Y';