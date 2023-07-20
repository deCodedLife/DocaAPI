<?php

/**
 * @file
 * Вызов из админки
 */

require_once( $API::$configs[ "paths" ][ "public_modules" ] . "/dom_ru.php" );

$IPCallsDomRu->makeCall( $requestData->phone, $requestData->user );


/**
 * Получение сотрудника
 */

$employeeDetail = $API->DB->from( "users" )
    ->where( "domru_login", $requestData->user )
    ->limit( 1 )
    ->fetch();

if ( !$employeeDetail ) $API->returnResponse( false );


/**
 * Получение клиента
 */

$clientDetail = $API->DB->from( "clients" )
    ->where( "phone", $requestData->phone )
    ->limit( 1 )
    ->fetch();

if ( !$clientDetail ) $clientDetail[ "id" ] = null;