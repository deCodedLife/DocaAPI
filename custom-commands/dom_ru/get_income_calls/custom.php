<?php

/**
 * @file
 * Получение входящих звонков
 */


/**
 * Маска телефонного звонка
 */
function phoneMask ( $phone ) {

    $phone = trim( $phone );

    $phone = preg_replace(
        array(
            '/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{3})[-|\s]?\)[-|\s]?(\d{3})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
            '/[\+]?([7|8])[-|\s]?(\d{3})[-|\s]?(\d{3})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
            '/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{4})[-|\s]?\)[-|\s]?(\d{2})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
            '/[\+]?([7|8])[-|\s]?(\d{4})[-|\s]?(\d{2})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
            '/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{4})[-|\s]?\)[-|\s]?(\d{3})[-|\s]?(\d{3})/',
            '/[\+]?([7|8])[-|\s]?(\d{4})[-|\s]?(\d{3})[-|\s]?(\d{3})/',
        ),
        array(
            '+7 ($2) $3-$4-$5',
            '+7 ($2) $3-$4-$5',
            '+7 ($2) $3-$4-$5',
            '+7 ($2) $3-$4-$5',
            '+7 ($2) $3-$4',
            '+7 ($2) $3-$4',
        ),
        $phone
    );

    return $phone;

} // function. phoneMask


/**
 * Получение входящего звонка
 */
$incomeCall = $API->DB->from( "callHistory" )
    ->where( [
        "user_id" => $requestData->user_id,
        "status" => "INCOMING"
    ] )
    ->orderBy( "created_at ASC" )
    ->limit( 1 )
    ->fetch();


/**
 * Получение клиента
 */

$clientDetail = $API->DB->from( "clients" )
    ->where( "phone", $incomeCall[ "client_phone" ] )
    ->orderBy( 1 )
    ->fetch();

if ( !$clientDetail ) $clientDetail[ "id" ] = null;


/**
 * Нет входящих звонков
 */
if ( !$incomeCall ) $API->returnResponse( false );

/**
 * Клиент есть в базе. Возврат ссылки
 */
if ( $clientDetail[ "id" ] ) {

    $API->returnResponse(
        [
            "type" => "page",
            "value" => "/clients/update/" . $clientDetail[ "id" ],
            "phone" => phoneMask( $clientDetail[ "phone" ] )
        ]
    );

} // if. $clientDetail[ "id" ]


/**
 * Клиента нет в базе. Возврат номера
 */

$incomeCall[ "client_phone" ] = phoneMask( $incomeCall[ "client_phone" ] );

$API->returnResponse(
    [
        "type" => "phone",
        "value" => $incomeCall[ "client_phone" ]
    ]
);