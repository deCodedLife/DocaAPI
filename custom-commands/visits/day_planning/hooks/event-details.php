<?php

/**
 * Фильтр по врачу
 */

// $isContinue = true;

$visitUsers = $API->DB->from("visits_users")
    ->where("visit_id", $event["id"]);

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
 * Добавление кнопок
 */

$eventDetails[ "buttons" ][] = [
    "type" => "script",
    "settings" => [
        "title" => "Завершить",
        "background" => "dark",
        "object" => "visits",
        "command" => "check-success",
        "data" => [
            "id" => $event["id"]
        ]
    ]
];