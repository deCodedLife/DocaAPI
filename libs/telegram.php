<?php

namespace telegram;

function getDefaultVisitHandlers ( $visits ): array {

    return [
        "👍" => [
            "api_url" => "https://test.docacrm.com",
            "object" => "visits",
            "command" => "update",
            "data" => [
                "id" => $visits,
                "comment" => "Придёт"
            ]
        ],
        "да" => [
            "api_url" => "https://test.docacrm.com",
            "object" => "visits",
            "command" => "update",
            "data" => [
                "id" => $visits,
                "comment" => "Придёт"
            ]
        ],
        "👎" => [
            "api_url" => "https://test.docacrm.com",
            "object" => "visits",
            "command" => "update",
            "data" => [
                "id" => $visits,
                "comment" => "Не придёт"
            ]
        ],
        "нет" => [
            "api_url" => "https://test.docacrm.com",
            "object" => "visits",
            "command" => "update",
            "data" => [
                "id" => $visits,
                "comment" => "Не придёт"
            ]
        ]
    ];

}

function getClient ( $client_id ): array
{
    global $API;

    $clientDetails = $API->DB->from( "clients" )
        ->where( "id", $client_id )
        ->fetch();

    $client = [];
    $clientPhone = null;
    if ( !empty( $clientDetails[ "phone" ] ) ) $clientPhone = $clientDetails[ "phone" ];
    else if ( !empty( $clientDetails[ "second_phone" ] ) ) $clientPhone = $clientDetails[ "second_phone" ];

    $client[ "phone" ] = $clientPhone;

    if ( empty( $clientDetails[ "telegram_id" ] ) ) {

        $request = [];
        $request[ "messenger" ] = "telegram";
        $request[ "first_name" ] = $clientDetails[ "first_name" ];
        $request[ "last_name" ] = $clientDetails[ "last_name" ];
        $request[ "phone" ] = $clientPhone;

        $contacts = $API->curlRequest ( $request, "bot.docacrm.com/add_contact" );
        $API->DB->update( "clients" )
            ->set( "telegram_id", $contacts->telegram->id )
            ->where( "id", $client_id )
            ->execute();

        $client[ "telegram_id" ] = $contacts->telegram->id;

    }

    $client[ "messenger_id" ] = $clientDetails[ "telegram_id" ];
    return $client;

}

function sendMessage ( string $message, array $client, array $handlers = null )
{
    global $API;
    $request = [];
    $request[ "messenger" ] = "telegram";
    $request[ "user" ] = $client;
    $request[ "message" ] = $message;
    $request[ "handlers" ] = $handlers;
    $API->curlRequest ( $request, "bot.docacrm.com/send_message" );
}