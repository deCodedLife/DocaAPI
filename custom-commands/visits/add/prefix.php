<?php

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
 * Получение Записей за указанный период
 */

$sqlTimeCondition = "(
    ( start_at >= '$requestData->start_at' and start_at < '$requestData->end_at' ) OR
    ( end_at > '$requestData->start_at' and end_at < '$requestData->end_at' ) OR
    ( start_at < '$requestData->start_at' and end_at > '$requestData->end_at' )
)";

$existingVisits = mysqli_query(
    $API->DB_connection,
    "SELECT id, cabinet_id, store_id FROM visits WHERE $sqlTimeCondition AND is_active = 'Y'"
);



/**
 * Проверка времени филиала
 */

$storeData = $API->DB->from( "stores" )
    ->where( "id", $requestData->store_id )
    ->limit( 1 )
    ->fetch();

$isTimeCorrect = true;

if ( strtotime( DateTime::createFromFormat( 'Y-m-d H:i:s', $requestData->start_at )->format('H:i:s') ) < strtotime( $storeData[ "schedule_from" ] ) ) $isTimeCorrect = false;
if ( strtotime( DateTime::createFromFormat( 'Y-m-d H:i:s', $requestData->end_at )->format('H:i:s') ) > strtotime( $storeData[ "schedule_to" ] ) ) $isTimeCorrect = false;

if ( !$isTimeCorrect ) $API->returnResponse( "Время посещения выходит за рамки графика работы филиала", 400 );


/**
 * Проверка указания второго Сотрудника
 */

/**
 * Расходники посещения
 */
$allConsumables = [];

/**
 * Обход услуг
 */
foreach ( $requestData->services_id as $serviceId ) {

    /**
     * Получение детальной информации об услуге
     */
    $serviceDetail = $API->DB->from( "services" )
        ->where( "id", $serviceId )
        ->limit( 1 )
        ->fetch();

    /**
     * Получение вторых исполнителей
     */
    $services_second_users = $API->DB->from("services_second_users")
        ->where("service_id", $serviceId);

    /**
     * Нет необходимости указывать второго исполнителя?
     */
    $specifySecondEmployee = true;

    foreach ($services_second_users as $secondUser) {


        if ($secondUser) {

            $specifySecondEmployee = false;

            foreach ($requestData->users_id as $userId) {

                if ($userId == $secondUser["user_id"]) {

                    $specifySecondEmployee = true;

                }

            }

        } else {

            $specifySecondEmployee = true;

        }

    }

    if ($specifySecondEmployee == false) {

        $API->returnResponse("Укажите второго сотрудника для услуги " . $serviceDetail[ "title" ], 400);

    }

    /**
     * Получение расходников для услуги
     */
    $services_consumables = $API->DB->from( "services_consumables" )
        ->where( "row_id", $serviceId );


    foreach ( $services_consumables as $service_consumable ) {

        $allConsumables[ $service_consumable[ "consumable_id" ] ][ "count" ] +=  $service_consumable[ "count" ];

    }

}



foreach ( $allConsumables as $consumable_id => $consumable ) {

    $warehouse = $API->DB->from( "warehouses" )
            ->where( [
                "store_id" => $requestData->store_id,
                "consumable_id" => $consumable_id
            ] )
            ->limit( 1 )
            ->fetch();

    if ( $consumable[ "count" ] > $warehouse[ "count" ] ) {

        $API->returnResponse("Недостаточно расходников", 400);

    }

}





/**
 * Расчет свободности Исполнителей, Клиентов и Кабинетов
 */

foreach ( $existingVisits as $existingVisit ) {

    /**
     * Проверка свободности Кабинета
     */
    if ( $existingVisit[ "cabinet_id" ] == $requestData->cabinet_id )
        $API->returnResponse( "Кабинет занят", 400 );


    /**
     * Проверка свободности Сотрудника
     */

    $visitUsers = $API->DB->from( "visits_users" )
        ->where( "visit_id", $existingVisit[ "id" ] );

    foreach ( $visitUsers as $visitUser )

        if ( in_array( $visitUser[ "user_id" ], $requestData->users_id ) ) {

            /**
             * Получение детальной информации о Сотруднике
             */
            $userDetail = $API->DB->from( "users" )
                ->where( "id", $visitUser[ "user_id" ] )
                ->limit( 1 )
                ->fetch();

            $storeDetail = $API->DB->from( "stores" )
                ->where( "id", $existingVisit[ "store_id" ] )
                ->limit( 1 )
                ->fetch();


            $API->returnResponse( "Сотрудник " . userDetail[ "last_name" ] . " занят" . "(Филиал " . $storeDetail[ "title" ] . ")" , 400 );

        } // if. in_array( $visitUser[ "user_id" ], $requestData->users_id


    /**
     * Проверка свободности Клиента
     */

    $visitClients = $API->DB->from( "visits_clients" )
        ->where( "visit_id", $existingVisit[ "id" ] );

    foreach ( $visitClients as $visitClient )
        if ( in_array( $visitClient[ "client_id" ], $requestData->client_id ) ) {

            /**
             * Получение детальной информации о Клиенте
             */
            $clientDetail = $API->DB->from( "clients" )
                ->where( "id", $visitClient[ "client_id" ] )
                ->limit( 1 )
                ->fetch();

            $API->returnResponse( "Клиент ${clientDetail[ "last_name" ]} занят", 400 );

        } // if. in_array( $visitClient[ "client_id" ], $requestData->client_id

} // foreach. $existingVisits


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
