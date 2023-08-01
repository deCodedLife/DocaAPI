<?php

/**
 * Блок "Процент от продаж услуг"
 */
if ( $pageDetail[ "row_detail" ][ "is_percent" ] )
    $generatedTab[ "settings" ][ "areas" ][ 0 ][ "blocks" ][ 0 ][ "fields" ][ 2 ][ "is_visible" ] = true;


/**
 * Услуги врача
 */
$userServices = [];


/**
 * Текущие услуги
 */
foreach ( $generatedTab[ "settings" ][ "areas" ][ 0 ][ "blocks" ][ 0 ][ "fields" ][ 2 ][ "value" ] as $userService )
    $userServices[] = $userService;


/**
 * Связанные группы услуг
 */

$userServiceGroups = $API->DB->from( "serviceGroupEmployees" )
    ->where( "employeeID", $pageDetail[ "row_detail" ][ "id" ] );

foreach ( $userServiceGroups as $userServiceGroup ) {

    $userGroupServices = $API->DB->from( "services" )
        ->where( [
            "category_id" => $userServiceGroup[ "groupID" ],
            "is_active" => "Y"
        ] );


    foreach ( $userGroupServices as $userGroupService ) {

        /**
         * Проверка привязки услуги
         */

        $isContinueService = false;

        foreach ( $userServices as $userService )
            if ( $userService[ "service_id" ] == $userGroupService[ "id" ] ) $isContinueService = true;

        if ( $isContinueService ) continue;


        $userServices[] = [
            "service_id" => $userGroupService[ "id" ],
            "percent" => 0,
            "fix_sum" => 0
        ];

    } // foreach. $userGroupServices

} // foreach. $userServiceGroups


/**
 * Связанные услуги
 */

$joinUserServices = $API->DB->from( "services_users" )
    ->where( "user_id", $pageDetail[ "row_detail" ][ "id" ] );

foreach ( $joinUserServices as $joinUserService ) {

    /**
     * Проверка привязки услуги
     */

    $isContinueService = false;

    foreach ( $userServices as $userService )
        if ( $userService[ "service_id" ] == $joinUserService[ "service_id" ] ) $isContinueService = true;

    if ( $isContinueService ) continue;


    $userServices[] = [
        "service_id" => $joinUserService[ "service_id" ],
        "percent" => 0,
        "fix_sum" => 0
    ];

} // foreach. $joinUserServices

$generatedTab[ "settings" ][ "areas" ][ 0 ][ "blocks" ][ 0 ][ "fields" ][ 2 ][ "value" ] = $userServices;
