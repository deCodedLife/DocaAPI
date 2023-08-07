<?php

/**
 * @file
 * Получение кол-ва непрочитанных сообщений
 */


/**
 * Сформированный счетчик сообщений
 */
$resultCounter = [
    "total" => 0,
    "groups" => [],
    "chats" => []
];

/**
 * Группы чатов
 */
$chatGroups = [];


/**
 * Получение непрочитанных сообщений
 */
$unreadMessages = $API->DB->from( "personMessages" )
    ->where( [
        "is_readed" => "N",
        "author_id != ?" => $API::$userDetail->id
    ] );


/**
 * Подсчет непрочитанных сообщений
 */

foreach ( $unreadMessages as $unreadMessage ) {

    if ( !$chatGroups[ $unreadMessage[ "chat_id" ] ] ) {

        /**
         * Получение ID группы чатов
         */

        $chatGroup = $API->DB->from( "personChats" )
            ->where( "id", $unreadMessage[ "chat_id" ] )
            ->limit( 1 )
            ->fetch();

        $chatKey = explode( "_", $chatGroup[ "chat_key" ] );

        foreach ( $chatKey as $chatUserId )
            if ( $chatUserId != $API::$userDetail->id )
                $chatGroup = $API->DB->from( "users" )
                    ->where( "id", $chatUserId )
                    ->limit( 1 )
                    ->fetch();


        $chatGroups[ $unreadMessage[ "chat_id" ] ][ "chat" ] = $chatGroup[ "id" ];
        $chatGroups[ $unreadMessage[ "chat_id" ] ][ "group" ] = $chatGroup[ "role_id" ];

    } // if. !$chatGroups[ $unreadMessage[ "chat_id" ] ]


    $resultCounter[ "total" ]++;
    $resultCounter[ "chats" ][ $chatGroups[ $unreadMessage[ "chat_id" ] ][ "chat" ] ]++;
    $resultCounter[ "groups" ][ $chatGroups[ $unreadMessage[ "chat_id" ] ][ "group" ] ]++;

} // foreach. $unreadMessages


$API->returnResponse( $resultCounter );