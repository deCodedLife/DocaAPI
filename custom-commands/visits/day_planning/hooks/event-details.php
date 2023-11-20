<?php

/**
 * Формирование тела записи
 */

$visitServices = $API->DB->from( "visits_services" )
    ->where( "visit_id", $event[ "id"] );

foreach ($visitServices as $visitService) {

    $visitServiceDetail = $API->DB->from( "services" )
        ->where( "id", $visitService[ "service_id" ] )
        ->limit( 1 )
        ->fetch();


    $eventDetails[ "body" ] .= $visitServiceDetail[ "title" ] . ", ";

} // foreach. $visitServices

if ( $eventDetails[ "body" ] )
    $eventDetails[ "body" ] = substr( $eventDetails[ "body" ], 0, -2 );


/**
 * Формирование ссылок записи
 */

$visitClients = $API->DB->from( "visits_clients" )
    ->where( "visit_id", $event[ "id" ] );

foreach ( $visitClients as $visitClient ) {

    $visitClientDetail = $API->DB->from( "clients" )
        ->where("id", $visitClient[ "client_id"] )
        ->limit(1)
        ->fetch();

    $event[ "clients" ][] = $visitClientDetail;

    $eventDetails[ "links" ][] = [
        "title" => $visitClientDetail[ "last_name" ] . " " . $visitClientDetail[ "first_name" ] . " " . $visitClientDetail[ "patronymic" ],
        "link" => "clients/update/" . $visitClientDetail[ "id" ]
    ];

} // foreach. $visitClients

/**
 * Определение цвета
 */

switch ( $event[ "status" ] ) {

    case "planning":
        $eventDetails[ "color" ] = "blue";
        $eventDetails[ "description" ] = "Запланировано";
        break;

    case "ended":
        $eventDetails[ "color" ] = "red";
        $eventDetails[ "description" ] = "Завершено";
        break;

    case "process":
        $eventDetails[ "color" ] = "pink";
        $eventDetails[ "description" ] = "На приеме";
        break;

    case "online":
        $eventDetails[ "color" ] = "light_blue";
        $eventDetails[ "description" ] = "Онлайн запись";
        break;

    case "repeated":
        $eventDetails[ "color" ] = "yellow";
        $eventDetails[ "description" ] = "Повторная";
        break;

    case "moved":
        $eventDetails[ "color" ] = "orange";
        $eventDetails[ "description" ] = "Перемещена";
        break;

    case "waited":
        $eventDetails[ "color" ] = "green";
        $eventDetails[ "description" ] = "Ожидание";
        break;

} // switch. $event[ "status" ]


/**
 * Добавление кнопок
 */

if ( $event[ "status" ] == "planning" ) $eventDetails[ "buttons" ][] = [
    "type" => "script",
    "settings" => [
        "title" => "Принять пациента",
        "background" => "dark",
        "icon"=>"megaphone",
        "object" => "visits",
        "command" => "accept-patient",
        "data" => [
            "id" => $event[ "id" ]
        ]
    ]
];


if ( $event[ "status" ] == "process" ) $eventDetails[ "buttons" ][] = [
    "type" => "script",
    "settings" => [
        "title" => "Принять повторно",
        "background" => "dark",
        "icon"=>"megaphone",
        "object" => "visits",
        "command" => "accept-again",
        "data" => [
            "id" => $event[ "id" ]
        ]
    ]
];

if ( $event[ "status" ] == "process" ) $eventDetails[ "buttons" ][] = [
    "type"=>"print",
    "settings"=> [
        "title"=>"Печатать",
        "background"=>"dark",
        "icon"=>"print",
        "data" => [
            "save_to" => [
                "object"=> "visitReports",
                "properties"=> [
                    "client_id"=> $event[ "clients" ][ 0 ][ "id" ],
                    "user_id"=> $event[ "user_id" ]
                ]
            ],
            "is_edit"=> true,
            "scheme_name"=> "visits",
            "row_id"=> $event[ "id" ]
        ]
    ]
];

if ( $event[ "status" ] == "process" ) $eventDetails[ "buttons" ][] = [
    "type" => "script",
    "required_permissions"=> [
        "manager_schedule"
    ],
    "settings" => [
        "title" => "Завершить",
        "background" => "dark",
        "icon"=>"door",
        "object" => "visits",
        "command" => "check-success",
        "data" => [
            "id" => $event[ "id" ]
        ]
    ]
];
