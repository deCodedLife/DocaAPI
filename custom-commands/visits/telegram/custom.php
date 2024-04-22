<?php

$visitsList = $API->DB->from( "visits" )
    ->where([
        "start_at >= ?" => date("Y-m-d 00:00:00", strtotime("+1 day")),
        "end_at <= ?" => date("Y-m-d 23:59:59", strtotime("+1 day")),
        "is_active" => "Y"
    ])
    ->fetchAll( "client_id[]" );

foreach ( $visitsList as $client_id => $visits ) {

    $visitTexts = [];
    $visit_ids = [];

    foreach ( $visits as $visit ) {

        $employeeDetail = $API->DB->from( "users" )
            ->where( "id", $visit[ "user_id" ] )
            ->fetch();

        $employee_fio = trim( $employeeDetail[ "first_name" ] );
        if ( !empty( $employeeDetail[ "last_name" ] ) ) $employee_fio .= " " . trim( $employeeDetail[ "last_name" ] );
        if ( !empty( $employeeDetail[ "patronymic" ] ) ) $employee_fio .= " " . trim( $employeeDetail[ "patronymic" ] );

        $visitTime = date( "H:i", strtotime( $visit[ "start_at" ] ) );
        $visitTexts[] = "$visitTime к $employee_fio";
        $visit_ids[] = $visit[ "id" ];

    }
    $visitTexts = join( ", ", $visitTexts );

    $message = "Добрый день, Вы записаны на завтра в $visitTexts. Чтобы подтвердить запись ответьте 'да' или поставьте реакцию 👍. Чтобы отменить приём - 'нет' или 👎";
    telegram\sendMessage(
        $message,
        telegram\getClient( $client_id ),
        telegram\getDefaultVisitHandlers( $visit_ids )
    );

}