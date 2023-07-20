<?php

/**
 * @file
 * Импорт справочников
 */

set_time_limit( 0 );


function handbookGroupsImport ( $page ) {

    global $API;


    $request = json_encode( [
        "service" => "handbooks",
        "command" => "group-get",
        "data" => [
            "page" => $page
        ]
    ] );

    $ch = curl_init('https://api.mewbas.com');

    curl_setopt( $ch, CURLOPT_POST, 1 );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $request );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch, CURLOPT_HEADER, false );

    $handbooks = json_decode( curl_exec( $ch ) );

    curl_close( $ch );


    foreach ( $handbooks->data as $handbook ) {

        /**
         * Проверка дублей
         */

        $duplicate = $API->DB->from( "handbookTypes" )
            ->where( "ya_id", $handbook->id )
            ->limit( 1 )
            ->fetch();

        if ( $duplicate ) continue;


        /**
         * Категория
         */

        $handbookCategoryParentId = $API->DB->from( "handbookTypes" )
            ->where( "ya_id", $handbook->parent_id )
            ->limit( 1 )
            ->fetch()[ "id" ];


        $API->DB->insertInto( "handbookTypes" )
            ->values( [
                "ya_id" => $handbook->id,
                "title" => $handbook->title,
                "code" => $handbook->code,
                "parent_id" => $handbookCategoryParentId
            ] )
            ->execute();

    } // foreach. $handbooks

} // function. handbookGroupsImport

function handbooksImport ( $page ) {

    global $API;


    $request = json_encode( [
        "service" => "handbooks",
        "command" => "get",
        "data" => [
            "page" => $page
        ]
    ] );

    $ch = curl_init('https://api.mewbas.com');

    curl_setopt( $ch, CURLOPT_POST, 1 );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $request );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch, CURLOPT_HEADER, false );

    $handbooks = json_decode( curl_exec( $ch ) );

    curl_close( $ch );


    foreach ( $handbooks->data as $handbook ) {

        /**
         * Проверка дублей
         */

        $duplicate = $API->DB->from( "handbook" )
            ->where( "ya_id", $handbook->id )
            ->limit( 1 )
            ->fetch();

        if ( $duplicate ) continue;


        /**
         * Категория
         */

        $handbookCategoryId = $API->DB->from( "handbookTypes" )
            ->where( "ya_id", $handbook->group_id )
            ->limit( 1 )
            ->fetch()[ "id" ];


        $API->DB->insertInto( "handbook" )
            ->values( [
                "ya_id" => $handbook->id,
                "title" => $handbook->title,
                "code" => $handbook->code,
                "description" => $handbook->body,
                "type_id" => $handbookCategoryId
            ] )
            ->execute();

    } // foreach. $handbooks

} // function. handbooksImport


// handbookGroupsImport( 0 );

for ( $i = 5500; $i <= 23492; $i++ ) handbooksImport( $i );