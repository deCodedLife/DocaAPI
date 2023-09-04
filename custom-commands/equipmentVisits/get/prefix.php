<?php

/**
 * Фильтр Расписания по врачу
 */
if ( $requestData->context->block === "day_planning" ) {

    $requestData->start_at = date( "Y-m-d" );
    $requestData->user_id = $API::$userDetail->id;

} // if. $requestData->context->block === "day_planning"


/**
 * Фильтр по периоду
 */
if ( $requestData->sort_by === "period" ) $requestData->sort_by = "start_at";

/**
 * Фильтр по дате (до)
 */
if ( $requestData->end_at ) $requestData->end_at .= " 23:59:59";