<?php

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
        'a' => 'а',    'b' => 'б',    'v' => 'в',    'g' => 'г',    'd' => 'д',
        'e' => 'е',    'zh' => 'ж',   'z' => 'з',    'i' => 'и',    'yu' => 'ю',
        'y' => 'й',    'k' => 'к',    'l' => 'л',    'm' => 'м',    'n' => 'н',
        'o' => 'о',    'p' => 'п',    'r' => 'р',    's' => 'с',    't' => 'т',
        'u' => 'у',    'f' => 'ф',    'h' => 'х',    'c' => 'ц',    'ch' => 'ч',
        'sh' => 'ш',   'sch' => 'щ', 'ya' => 'я',

        'A' => 'А',    'B' => 'Б',    'V' => 'В',    'G' => 'Г',    'D' => 'Д',
        'E' => 'Е',    'Zh' => 'Ж',   'Z' => 'З',    'I' => 'И',    'Ya' => 'Я',
        'Y' => 'Й',    'K' => 'К',    'L' => 'Л',    'M' => 'М',    'N' => 'Н',
        'O' => 'О',    'P' => 'П',    'R' => 'Р',    'S' => 'С',    'T' => 'Т',
        'U' => 'У',    'F' => 'Ф',    'H' => 'Х',    'C' => 'Ц',    'Ch' => 'Ч',
        'Sh' => 'Ш',   'Sch' => 'Щ',  'Ы' => 'Y',    'Yu' => 'Ю',
    );

    if ( !$converter[ $value ] ) return $value;
    return strtr( $value, $converter );

} // function. translit


/**
 * Валидация посещения
 */
$publicAppPath = $API::$configs[ "paths" ][ "public_app" ];
require_once ( $publicAppPath . "/custom-libs/visits/validateEquipment.php" );


/**
 * Формирование талона
 */
$serviceDetail = $API->DB->from( "services" )
    ->where( "id", $requestData->service_id )
    ->limit( 1 )
    ->fetch();

$requestData->talon = mb_strtoupper(
        translit( mb_substr( $serviceDetail[ "title" ], 0, 1 ) )
    ) . " ";


$lastVisitDetail = $API->DB->from( "equipmentVisits" )
    ->select( null )->select( "id" )
    ->orderBy( "id desc" )
    ->limit( 1 )
    ->fetch();

$talonNumber = $lastVisitDetail[ "id" ];
if ( $talonNumber < 100 ) $talonNumber += 100;

$talonNumber = (string) $talonNumber;
$talonNumber = substr( $talonNumber, -3 );

$requestData->talon .= $talonNumber;