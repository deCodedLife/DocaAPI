<?php

/**
 * Фильтр Расписания по врачу
 */
if ( $requestData->is_list ) {

    $requestData->start_at = date( "Y-m-d" );
    $requestData->users_id = $API::$userDetail->id;

} // if. $requestData->is_list