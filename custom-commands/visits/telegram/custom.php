<?php

$visitsList = $API->DB->from( "visits" )
    ->where([
        "start_at >= ?" => date("Y-m-d 00:00:00", strtotime("+1 day")),
        "end_at <= ?" => date("Y-m-d 23:59:59", strtotime("+1 day")),
        "is_active" => "Y"
    ])
    ->fetchAll( "client_id[]" );

foreach ( $visitsList as $client_id => $visits ) {

    $employees = [];
    $visit_ids = [];
    $times = [];
    $store_id = null;

    foreach ( $visits as $visit ) {

        $employeeDetail = $API->DB->from( "users" )
            ->where( "id", $visit[ "user_id" ] )
            ->fetch();

        if ( $visit[ "store_id" ] ) $store_id = $visit[ "store_id" ];

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

    $app_name = $API->DB->from( "stores" )->where( "id", $store_id ?? 0 )->fetch()[ "name" ];


    telegram\sendMessage(
        "Здравствуйте!\n\n$clientFio, Вы записаны в $app_name на $tomorrow в $times.\n\nВрач: $employees\n\nДля подтверждения записи ответьте '1'\n\nДля переноса или отмены посещения напишите об этом в чате.",
        $userDetails,
        telegram\getDefaultVisitHandlers( $visit_ids, $userDetails[ "phone" ] )
    );

}