<?php
/**
 * Отфильтрованные Записи
 */
$filteredEvents = [];

/**
 * Фильтрация Записей
 */
$equipmentVisits = $API->DB->from( "equipmentVisits" )
    ->where( [

        "start_at >= ?" => $requestData->day . " 00:00:00",
        "start_at <= ?" => $requestData->day . " 23:59:59",
        "is_active" => "Y"

    ] );


foreach ( $equipmentVisits as $equipmentVisit ) {

    if ( $equipmentVisit["user_id"] == $API::$userDetail->id || $equipmentVisit["assist_id"] == $API::$userDetail->id ) {

        $equipmentVisit["time"] = date("H:i", strtotime($equipmentVisit["start_at"]));
        $equipmentVisit["time"] .= " - " . date("H:i", strtotime($equipmentVisit["end_at"]));

        /**
         * Определение цвета
         */

        switch ($equipmentVisit["status"]) {

            case "planning":
                $equipmentVisit["color"] = "blue";
                $equipmentVisit["description"] = "Запланировано";
                break;

            case "ended":
                $equipmentVisit["color"] = "red";
                $equipmentVisit["description"] = "Завершено";
                break;

            case "process":
                $equipmentVisit["color"] = "pink";
                $equipmentVisit["description"] = "На приеме";
                break;

            case "online":
                $equipmentVisit["color"] = "light_blue";
                $equipmentVisit["description"] = "Онлайн запись";
                break;

            case "repeated":
                $equipmentVisit["color"] = "yellow";
                $equipmentVisit["description"] = "Повторная";
                break;

            case "moved":
                $equipmentVisit["color"] = "orange";
                $equipmentVisit["description"] = "Перемещена";
                break;

            case "waited":
                $equipmentVisit["color"] = "green";
                $equipmentVisit["description"] = "Ожидание";
                break;

        } // switch. $event[ "status" ]


        $visitServiceDetail = $API->DB->from("services")
            ->where("id", $equipmentVisit["service_id"])
            ->limit(1)
            ->fetch();

        $visitClientDetail = $API->DB->from("clients")
            ->where("id", $equipmentVisit["client_id"])
            ->limit(1)
            ->fetch();

        $event["clients"][] = $visitClientDetail;

        $equipmentVisit["links"][] = [
            "title" => $visitClientDetail["last_name"] . " " . $visitClientDetail["first_name"] . " " . $visitClientDetail["patronymic"],
            "link" => "equipmentVisits/update/" . $equipmentVisit["id"]
        ];


        /**
         * Добавление кнопок
         */

        /**
         * "script": {
         * "object": "visitReports",
         * "command": "add",
         * "properties": {
         * "client_id": ":client_id",
         * "user_id": ":user_id"
         * }
         * },
         */

        if ($equipmentVisit["status"]) $equipmentVisit["buttons"][] = [
            "type" => "print",
            "settings" => [
                "title" => "Печатать",
                "background" => "dark",
                "icon" => "print",
                "data" => [
                    "script" => [
                        "object" => "visitReports",
                        "command" => "add",
                        "properties" => [
                            "client_id" => $equipmentVisit["client_id"],
                            "user_id" => $equipmentVisit["user_id"],
                            "visit_id" => $equipmentVisit["id"],
                        ]
                    ],
                    "save_to" => [
                        "object" => "visitReports",
                        "properties" => [
                            "client_id" => $equipmentVisit["client_id"],
                            "user_id" => $equipmentVisit["user_id"]
                        ]
                    ],
                    "is_edit" => true,
                    "scheme_name" => "equipmentVisits",
                    "row_id" => $equipmentVisit["id"]
                ]
            ]
        ];

        if ($equipmentVisit["status"] == "waited") $equipmentVisit["buttons"][] = [
            "type" => "script",
            "settings" => [
                "title" => "Ожидает вызова",
                "background" => "danger",
                "icon" => "megaphone",
                "object" => "equipmentVisits",
                "command" => "accept-patient",
                "data" => [
                    "id" => $equipmentVisit["id"]
                ]
            ]
        ];

        if ($equipmentVisit["status"] == "process") $equipmentVisit["buttons"][] = [
            "type" => "script",
            "settings" => [
                "title" => "Принять повторно",
                "background" => "danger",
                "icon" => "megaphone",
                "object" => "equipmentVisits",
                "command" => "accept-again",
                "data" => [
                    "id" => $equipmentVisit["id"]
                ]
            ]
        ];

        if ($equipmentVisit["status"] == "process") $equipmentVisit["buttons"][] = [
            "type" => "script",
            "required_permissions" => [
                "manager_schedule"
            ],
            "settings" => [
                "title" => "Завершить",
                "background" => "dark",
                "icon" => "door",
                "object" => "equipmentVisits",
                "command" => "check-success",
                "data" => [
                    "id" => $equipmentVisit["id"]
                ]
            ]
        ];

        $filteredEvents[] = [

            "id" => $equipmentVisit[ "id" ],
            "body" => $visitServiceDetail[ "title" ],
            "color" => $equipmentVisit[ "color" ],
            "links" => $equipmentVisit[ "links" ],
            "buttons" => $equipmentVisit[ "buttons" ],
            "time" => $equipmentVisit[ "time" ],
            "user_id" => $equipmentVisit[ "user_id" ],
            "assist_id" => $equipmentVisit[ "assist_id" ]

        ];

    }

} // foreach. $response[ "data" ]

foreach ( $response[ "data" ] as $visit ) {

    if ( $visit[ "user_id" ] == $API::$userDetail->id || $visit[ "assist_id" ] == $API::$userDetail->id ) $filteredEvents[] = $visit;

} // foreach. $response[ "data" ]


usort($response[ "data" ], function($a, $b) {

    $timeA = strtotime(str_replace(" - ", " ", $a['time']));
    $timeB = strtotime(str_replace(" - ", " ", $b['time']));

    return $timeA <=> $timeB;
});

$response[ "data" ] = $filteredEvents;