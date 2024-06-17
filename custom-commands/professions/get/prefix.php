<?php

if ( !$requestData->sort_by ) {

    $requestData->sort_by = "title";
    $requestData->sort_order = "asc";

}

$requestSettings[ "filter" ][ "is_active" ] = "Y";

$role = $API->DB->from( "roles" )
    ->where( "id", $API::$userDetail->role_id )
    ->fetch()[ "article" ];

if ( $role == "public" || $API::$userDetail->id == 3 ) $requestData->is_visibleOnSite = "Y";