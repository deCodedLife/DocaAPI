<?php

/**
 * Формирование талона
 */
$serviceDetail = $API->DB->from( "services" )
    ->where( "id", $requestData->services_id[ 0 ] )
    ->limit( 1 )
    ->fetch();


/**
 * Заполнение недостающих полей для записи не из црм
 */

if ( $API->isPublicAccount() ) {

    $requestData->status = "online";
    $requestData->price = 0;


    /**
     * Определение времени приёма
     */
    $customTime = $API->DB->from( "workingTime" )
        ->where( [
            "user" => $requestData->user_id,
            "row_id" => $requestData->services_id[ 0 ] ?? 0
        ] )
        ->fetch();

    if ( $customTime ) $customTime = $customTime[ "time" ];
    else $customTime = $serviceDetail[ "take_minutes" ] ?? 60;

    $requestData->end_at = date(
        "Y-m-d H:i:s",
        strtotime( "$requestData->start_at + $customTime minute" )
    );


    //Получение кабинета в котором принимает Врач
    $event = $API->DB->from( "scheduleEvents" )
        ->where( [
            "event_from >= ?" => date( "Y-m-d 00:00:00", strtotime( $requestData->start_at ) ),
            "event_to <= ?" => date( "Y-m-d 23:59:59", strtotime( $requestData->end_at ) ),
            "user_id" => $requestData->user_id
        ] )
        ->fetch();

    if ( $event ) $requestData->cabinet_id = $event[ "cabinet_id" ];


    // Получение стоимости приёма
    foreach ( $requestData->services_id as $service ) {

        $serviceInfo = visits\getFullService( $service, $requestData->user_id );
        $requestData->price += $serviceInfo[ "price" ];

    } // foreach ( $requestData->services_id as $service ) {

} // if ( $API->isPublicAccount() )


/**
 * Определение рекламного источника
 */

$clientDetail = $API->DB->from( "clients" )
    ->where( "id", $requestData->client_id )
    ->limit( 1 )
    ->fetch();

$requestData->advert_id = $clientDetail[ "advertise_id" ];

function translit ( $value ) {

    $converter = array(
        'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',    'д' => 'd',
        'е' => 'e',    'ё' => 'e',    'ж' => 'zh',   'з' => 'z',    'и' => 'i',
        'й' => 'y',    'к' => 'k',    'л' => 'l',    'м' => 'm',    'н' => 'n',
        'о' => 'o',    'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
        'у' => 'u',    'ф' => 'f',    'х' => 'h',    'ц' => 'c',    'ч' => 'ch',
        'ш' => 'sh',   'щ' => 'sch',  'ь' => '',     'ы' => 'y',    'ъ' => '',
        'э' => 'e',    'ю' => 'yu',   'я' => 'ya',

        'А' => 'A',    'Б' => 'B',    'В' => 'V',    'Г' => 'G',    'Д' => 'D',
        'Е' => 'E',    'Ё' => 'E',    'Ж' => 'Zh',   'З' => 'Z',    'И' => 'I',
        'Й' => 'Y',    'К' => 'K',    'Л' => 'L',    'М' => 'M',    'Н' => 'N',
        'О' => 'O',    'П' => 'P',    'Р' => 'R',    'С' => 'S',    'Т' => 'T',
        'У' => 'U',    'Ф' => 'F',    'Х' => 'H',    'Ц' => 'C',    'Ч' => 'Ch',
        'Ш' => 'Sh',   'Щ' => 'Sch',  'Ь' => '',     'Ы' => 'Y',    'Ъ' => '',
        'Э' => 'E',    'Ю' => 'Yu',   'Я' => 'Ya',
    );

    return strtr( $value, $converter );

} // function. translit


/**
 * Валидация посещения
 */
$publicAppPath = $API::$configs[ "paths" ][ "public_app" ];
require_once ( $publicAppPath . "/custom-libs/visits/validate.php" );


/**
 * Статус "Повторное" у Посещения и Клиентов
 */

foreach ( $requestData->clients_id as $clientId ) {

    /**
     * Получение посещений Клиента
     */

    $clientVisits = $API->DB->from( "visits" )
        ->innerJoin( "visits_clients ON visits_clients.visit_id = visits.id" )
        ->where( [
            "visits_clients.client_id" => intval( $clientId ),
            "visits.status" => "ended",
            "visits.is_active" => "Y"
        ] );

    if ( count( $clientVisits ) > 0 ) {

        $requestData->status = "repeated";

        $API->DB->update( "clients" )
            ->set( "is_repeat", "Y" )
            ->where( "id", $clientId )
            ->execute();

    }

} // foreach. $requestData->clients_id

$requestData->talon = mb_strtoupper(
        mb_substr( $serviceDetail[ "title" ], 0, 1 )
    ) . " ";

$lastVisitDetail = $API->DB->from( "visits" )
    ->select( null )->select( "id" )
    ->orderBy( "id desc" )
    ->limit( 1 )
    ->fetch();

$talonNumber = $lastVisitDetail[ "id" ];
if ( $talonNumber < 100 ) $talonNumber += 100;

$talonNumber = (string) $talonNumber;
$talonNumber = substr( $talonNumber, -3 );

$requestData->talon .= $talonNumber;