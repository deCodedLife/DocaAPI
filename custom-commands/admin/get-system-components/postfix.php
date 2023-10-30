<?php

/**
 * Текущий пользователь
 */
$currentUser = $API->getCurrentUser();


/**
 * Виджет зарплаты
 */

if ( $currentUser ) {

    $userSalaryType = $API->DB->from( "users" )
        ->where( "id", $currentUser->id )
        ->limit( 1 )
        ->fetch()[ "salary_type" ];
    
    if (
        ( $userSalaryType == "rate_percent" ) ||
        ( $userSalaryType == "rate_kpi" )
    ) $response[ "data" ][ "salary_widget" ] = true;

} // if. $currentUser