<?php

/**
 * Отчет "Кол-во звонков у Сотрудника"
 */


/**
 * График звонков Сотрудника
 */
$userCallsGraph = [];


if ( !$requestData->user_id ) $API->returnResponse(

    [
        [
            "value" => "Не указан сотрудник",
            "description" => "",
            "icon" => "",
            "prefix" => "",
            "postfix" => [
                "icon" => "",
                "value" => "",
                "background" => ""
            ],
            "background" => "",
            "detail" => [
                "type" => "char",
                "settings" => [
                    "char" => []
                ]
            ]

        ]
    ]

);


/**
 * Получение посещений Сотрудника
 */

$filter = [ "user_id" => $requestData->user_id ];
if ( $requestData->start_at ) $filter[ "created_at >= ?" ] = $requestData->start_at . " 00:00:00";
if ( $requestData->end_at ) $filter[ "created_at <= ?" ] = $requestData->end_at . " 23:59:59";

$userCalls = $API->DB->from( "callHistory" )
    ->where( $filter )
    ->limit( 0 );


/**
 * Формирование графика посещений
 */

foreach ( $userCalls as $userCall ) {

    $callDate = date( "Y-m-d", strtotime( $userCall[ "created_at" ] ) );
    $userCallsGraph[ $callDate ]++;

} // foreach. $userCalls


function num_word ( $value, $words, $show = true ) { // function. num_word() for declension of nouns after the numeral

    $num = $value % 100;

    if ( $num > 19 ) {

        $num = $num % 10;

    }

    $out = ( $show ) ?  $value . ' ' : '';
    switch ( $num ) {

        case 1:  $out .= $words[0]; break;

        case 2:

        case 3:

        case 4:  $out .= $words[1]; break;

        default: $out .= $words[2]; break;

    }

    return $out;
}


$API->returnResponse(

    [
        [
            "value" => num_word( count( $userCalls ), [ 'звонок', 'звонка', 'звонков' ] ),
            "description" => "",
            "icon" => "",
            "prefix" => "",
            "postfix" => [
                "icon" => "",
                "value" => "",
                "background" => ""
            ],
            "background" => "",
            "detail" => [
                "type" => "char",
                "settings" => [
                    "char" => $userCallsGraph
                ]
            ]

        ]
    ]

);
