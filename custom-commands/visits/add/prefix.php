<?php

/**
 * Определение рекламного источника
 */

$clientDetail = $API->DB->from( "clients" )
    ->where( "id", $requestData->clients_id[ 0 ] )
    ->limit( 1 )
    ->fetch();

$requestData->advert_id = $clientDetail[ "advertise_id" ];


/**
 * Регистрация клиента при записи с сайта
 */
if ( $requestData->phone || $requestData->last_name || $requestData->first_name || $requestData->patronymic ) {

    $clientDetail = $API->DB->from( "clients" )
        ->where( "phone", $requestData->phone )
        ->limit( 1 )
        ->fetch();

    if ( !$clientDetail ) {

        $API->DB->insertInto( "clients" )
            ->values( [
                "last_name" => $requestData->last_name,
                "first_name" => $requestData->first_name,
                "patronymic" => $requestData->patronymic,
                "phone" => $requestData->phone
            ] )
            ->execute();

    }

}


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
 * Формирование талона
 */
$serviceDetail = $API->DB->from( "services" )
    ->where( "id", $requestData->services_id[ 0 ] )
    ->limit( 1 )
    ->fetch();

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
