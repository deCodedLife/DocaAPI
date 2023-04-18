<?php


/**
 * Фильтр Расписания по врачу
 */
if ( $requestData->context === "list" ) {

    $requestData->start_at = date( "Y-m-d" );
    $requestData->users_id = $API::$userDetail->id;

} // if. $requestData->context === "list"