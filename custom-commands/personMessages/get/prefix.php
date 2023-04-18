<?php

/**
 * Получение ключа чата
 */

$usersId = [ $API::$userDetail->id, $requestData->chat_key ];
asort( $usersId );

$chatKey = implode( "_", $usersId );


/**
 * Получение чата
 */

$personChat = $API->DB->from( "personChats" )
    ->where( "chat_key", $chatKey )
    ->limit( 1 )
    ->fetch();

if ( !$personChat ) {

    $personChatId = $API->DB->insertInto( "personChats" )
        ->values( [ "chat_key" => $chatKey ] )
        ->execute();

} else {

    $personChatId = $personChat[ "id" ];

} // if. !!$personChat


$requestData->chat_id = (int) $personChatId;