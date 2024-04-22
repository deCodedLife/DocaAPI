<?php

$visitsList = $API->DB->from( "visits" )
    ->where([
        "start_at >= ?" => date("Y-m-d 00:00:00", strtotime("+1 day")),
        "end_at <= ?" => date("Y-m-d 23:59:59", strtotime("+1 day")),
        "is_active" => "Y",
        "id" => 534811
    ])
    ->fetchAll( "client_id[]" );

foreach ( $visitsList as $client_id => $visits ) {

    $employees = [];
    $visit_ids = [];
    $times = [];

    foreach ( $visits as $visit ) {

        if ( $visit[ "id" ] != 534811 ) $API->returnResponse( "nene" );

        $employeeDetail = $API->DB->from( "users" )
            ->where( "id", $visit[ "user_id" ] )
            ->fetch();

        $employee_fio = trim( $employeeDetail[ "last_name" ] );
        if ( !empty( $employeeDetail[ "first_name" ] ) ) $employee_fio .= " " . trim( $employeeDetail[ "first_name" ] );
        if ( !empty( $employeeDetail[ "patronymic" ] ) ) $employee_fio .= " " . trim( $employeeDetail[ "patronymic" ] );

        $visitTime = date( "H:i", strtotime( $visit[ "start_at" ] ) );

        $times[] = $visitTime;
        $employees[] = $employee_fio;
        $visit_ids[] = $visit[ "id" ];

    }

    $clientDetail = $API->DB->from( "clients" )
        ->where( "id", $client_id )
        ->fetch();

    $clientFio = $clientDetail[ "first_name" ];
    if ( empty( $clientDetail[ "patronymic" ] ) ) $clientFio .= " " . trim( $clientDetail[ "last_name" ] );
    else $clientFio .= " " . trim( $clientDetail[ "patronymic" ] );

    $userDetails = telegram\getClient( $client_id );
    $times = join( ", ", $times );
    $employees = join( ", ", $employees );
    $tomorrow = date( "d.m.Y", strtotime( "+1 day" ) );


    telegram\sendMessage(
        "Здравствуйте!\n\n$clientFio, Вы записаны в COMMUNITY CLINIC на $tomorrow в $times.\n\nВрач: $employees\n\nДля подтверждения записи ответьте '1'\nДля отмены запись ответьте '2'\nДля переноса записи ответьте '3'",
        $userDetails,
        telegram\getDefaultVisitHandlers( $visit_ids, $userDetails[ "phone" ] )
    );

}