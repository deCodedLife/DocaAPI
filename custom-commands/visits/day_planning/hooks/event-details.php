<?php

/**
 * Фильтр по врачу
 */

$isContinue = true;

$visitUsers = [ $API->DB->from("visits")
    ->where("id", $event["id"])
    ->fetch()[ "user_id" ] ];

foreach ($visitUsers as $visitUser)
    if ($visitUser["user_id"] == $API::$userDetail->id) $isContinue = false;


/**
 * Формирование тела записи
 */

$visitServices = $API->DB->from("visits_services")
    ->where("visit_id", $event["id"]);

foreach ($visitServices as $visitService) {

    $visitServiceDetail = $API->DB->from("services")
        ->where("id", $visitService["service_id"])
        ->limit(1)
        ->fetch();


    $eventDetails["body"] .= $visitServiceDetail["title"] . ", ";

} // foreach. $visitServices

if ($eventDetails["body"])
    $eventDetails["body"] = substr($eventDetails["body"], 0, -2);


/**
 * Формирование ссылок записи
 */

$visitClients = $API->DB->from("visits_clients")
    ->where("visit_id", $event["id"]);

foreach ($visitClients as $visitClient) {

    $visitClientDetail = $API->DB->from("clients")
        ->where("id", $visitClient["client_id"])
        ->limit(1)
        ->fetch();


    $eventDetails["links"][] = [
        "title" => $visitClientDetail["last_name"] . " " . $visitClientDetail[ "first_name" ] . " " . $visitClientDetail[ "patronymic" ],
        "link" => "clients/update/" . $visitClientDetail[ "id" ]
    ];

} // foreach. $visitClients

/**
 * Определение цвета
 */

switch ( $event[ "status" ] ) {

    case "planning":
        $eventDetails[ "color" ] = "primary";
        $eventDetails[ "description" ] = "Запланировано";
        break;

    case "process":
        $eventDetails[ "color" ] = "warning";
        $eventDetails[ "description" ] = "На приеме";
        break;

    case "ended":
        $eventDetails[ "color" ] = "success";
        $eventDetails[ "description" ] = "Завершено";
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
        "icon"=>"stethoscope",
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
        "icon"=>"stethoscope",
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
            "document_article" => "services_contract",
            "is_edit" => true,
            "row_id" => $event[ "id" ]
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
