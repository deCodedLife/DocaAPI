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
        $visitTexts[] = "в $visitTime к $employee_fio";
        $visit_ids[] = $visit[ "id" ];

    }
    $userDetails = telegram\getClient( $client_id );
    $visitTexts = join( ", ", $visitTexts );
    $tomorrow = date( "d.m.Y", strtotime( "+1 day" ) );

    $API->returnResponse( telegram\getDefaultVisitHandlers( $visit_ids, $userDetails[ "phone" ] ) );
    telegram\sendMessage(
        "Здравствуйте!\n\nВы записаны на $tomorrow $visitTexts. Для подтверждения записи ответьте '1'\n\nДля отмены запись ответьте '2'\n\nДля переноса записи ответьте '3'",
        $userDetails,
        telegram\getDefaultVisitHandlers( $visit_ids, $userDetails[ "phone" ] )
    );

}