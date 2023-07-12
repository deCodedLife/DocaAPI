<?php

/**
 * Фильтр Расписания по врачу
 */
if ( $requestData->context->block === "day_planning" ) {

    $requestData->start_at = date( "Y-m-d" );
    $requestData->users_id = $API::$userDetail->id;

} // if. $requestData->context->block === "day_planning"
