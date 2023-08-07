<?php

//ini_set( "display_errors", true );
global $API, $requestData;

$start_at    = "";
$end_at      = "";
$cabinet     = 0;
$clients     = [];
$services    = [];
$employee    = 0;
$assistant   = 0;
$store_id    = 0;
$consumables = [];


/**
 * Подтягиваем данные из существующего посещения
 */
if ( $requestData->id ) {

    $visitDetail = $API->DB->from( "visits" )
        ->where( "id", $requestData->id )
        ->fetch();

    $end_at    = $visitDetail[ "end_at" ];
    $start_at  = $visitDetail[ "start_at" ];
    $store_id  = $visitDetail[ "store_id" ];
    $cabinet   = $visitDetail[ "cabinet_id" ];
    $employee  = $visitDetail[ "user_id" ];
    $assistant = $visitDetail[ "assist_id" ];

    /**
     * Заполнение клиентов, сотрудников и услуг
     */
    foreach ( $API->DB->from( "visits_clients" )
                  ->where( "visit_id", $requestData->id ) as $visit_client )
        $clients[] = $visit_client[ "client_id" ];

    foreach ( $API->DB->from( "visits_services" )
                  ->where( "visit_id", $requestData->id ) as $visit_service )
        $services[] = $visit_service[ "service_id" ];

} // if ( $requestData->id )



$start_at    = $requestData->start_at ?? $start_at;
$end_at      = $requestData->end_at ?? $end_at;
$cabinet     = $requestData->cabinet_id ?? $cabinet;
$clients     = $requestData->clients_id ?? $clients;
$services    = $requestData->services_id ?? $services;
$employee    = $requestData->user_id ?? $employee;
$assistant   = $requestData->assist_id ?? $assistant;
$store_id    = $requestData->store_id ?? $store_id;



/**
 * Проверка времени филиала
 */
$storeData = $API->DB->from( "stores" )
    ->where( "id", $store_id )
    ->limit( 1 )
    ->fetch();

$isTimeCorrect = true;

if ( strtotime( DateTime::createFromFormat( 'Y-m-d H:i:s', $start_at )->format('H:i:s') ) < strtotime( $storeData[ "schedule_from" ] ) ) $isTimeCorrect = false;
if ( strtotime( DateTime::createFromFormat( 'Y-m-d H:i:s', $end_at )->format('H:i:s') ) > strtotime( $storeData[ "schedule_to" ] ) ) $isTimeCorrect = false;

if ( !$isTimeCorrect ) $API->returnResponse( "Время посещения выходит за рамки графика работы филиала", 400 );



/**
 * Получение расходников
 */
foreach ( $services as $service ) {

    $service_consumables = $API->DB->from( "services_consumables" )
        ->where( "row_id", $service );

    foreach ( $service_consumables as $consumable )
        $consumables[ $consumable[ "consumable_id" ] ][ "count" ] +=  $consumable[ "count" ];

} // foreach ( $services as $service ) {



/**
 * Проверка расходников
 */
foreach ( $consumables as $consumable_id => $consumable ) {

    $warehouse = $API->DB->from( "warehouses" )
        ->where( [
            "store_id" => $store_id,
            "consumable_id" => $consumable_id
        ] )
        ->limit( 1 )
        ->fetch();

    if ( $consumable[ "count" ] > $warehouse[ "count" ] )
        $API->returnResponse( "Недостаточно расходников", 400 );

} // foreach ( $consumables as $consumable_id => $consumable )



/**
 * Ищем все посещения за запрашиваемый период
 * Сорян, но периоды я таки скопировал(
 */
$getVisitsQuery = "SELECT * FROM visits WHERE (
    ( start_at >= '$start_at' and start_at < '$end_at' ) OR
    ( end_at > '$start_at' and end_at < '$end_at' ) OR
    ( start_at < '$start_at' and end_at > '$end_at' )
)";

/**
 * Отсекаем редактируемое посещение
 */
$getVisitsQuery .= $requestData->id ? " AND NOT id = {$requestData->id}" : "";


$existingVisits = mysqli_query(
    $API->DB_connection,
    $getVisitsQuery
);



/**
 * Проверяем кабинет на занятость
 * @param $cabinetID
 * @param $visits
 * @return bool
 */
function isCabinetOccupied( $cabinetID, $visits ): bool {

    global $API;

    /**
     * Получение информации по кабинету
     */
    $cabinetDetails = $API->DB->from( "cabinets" )
        ->where( "id", $cabinetID )
        ->fetch();

    /**
     * Если не учитывается занятость
     */
    if ( $cabinetDetails[ "is_employment" ] == "Y" ) return false;

    /**
     * Проверка занятости
     */
    foreach ( $visits as $visit )
        if ( $visit[ "cabinet_id" ] == $cabinetID ) return true;

    return false;

} // function isCabinetOccupied( $cabinetID, $visits )

if ( isCabinetOccupied( $cabinet, $existingVisits ) ) $API->returnResponse( "Кабинет занят", 400 );



/**
 * Проверка на занятость клиента
 * @param $clients
 * @param $visits
 * @return bool
 */
function isClientBusy( $client, $visits ): int {

    global $API;

    /**
     * Проход по всем посещениях
     */
//    $a=[];foreach($visits as $v)$a[]=$v;$API->returnResponse( $a, 500 );
    foreach ( $visits as $visit ) {

        /**
         * Запрос на получение клиентов у существующих посещений
         */
        $visit_clients = mysqli_query(
            $API->DB_connection,
            "SELECT * FROM visits_clients WHERE visit_id = {$visit[ 'id' ]} AND client_id = $client"
        );

        /**
         * Если в выдаче есть клиенты, то они - заняты
         */
        if ( mysqli_num_rows( $visit_clients ) != 0 ) return $visit[ "id" ];

    }

    return 0;

} // function isClientBusy( $clients, $visits )



/**
 *  Обход всех клиентов
 */
foreach ( $clients as $client ) {

    /**
     * Проверка занят ли клиент
     */
    $busyVisitID = isClientBusy( $client, $existingVisits );
    if ( $busyVisitID == 0 ) continue;

    /**
     * Получение детальной информации о клиенте
     */
    $client_details = $API->DB->from( "clients" )
        ->where( "id", $client )
        ->fetch();

    /**
     * Получение имени сотрудника у которого клиент на приёме
     */
    $employee_details = mysqli_fetch_array(
        mysqli_query(
            $API->DB_connection,
            "SELECT users.last_name FROM users INNER JOIN visits_users ON users.id = visits_users.user_id WHERE visits_users.visit_id = $busyVisitID"
        )
    )[0] ?? "";

    $API->returnResponse( "Клиент {$client_details[ 'last_name' ]} на приёме у врача {$employee_details}", 400 );

} // foreach ( $clients as $client )



/**
 * Проверка дополнительного сотрудника
 * @param $serviceDetails
 * @param $employees
 * @return bool
 */
function employeesAccountedFor( $serviceDetails, $employee ): bool {

    global $API;

    /**
     * Получение списка вторых исполнителей
     */
    $service_second_employees = $API->DB->from( "services_second_users" )
        ->where( "service_id", $serviceDetails[ "id" ] );

    if ( count( $service_second_employees ) == 0 ) return true;
    if ( !$employee ) return false;

    /**
     * Если нет нужного сотрудника, то выкинуть ошибку
     */
    foreach ( $service_second_employees as $second_employee )
        if ( $second_employee[ "user_id" ] == $employee ) return true;

    return false;

} // function employeesAccountedFor( $services, $employees )



/**
 * Проверка второго исполнителя для каждой услуги
 */
foreach ( $services as $service ) {

    $serviceDetails = $API->DB->from( "services" )
        ->where( "id", $service )
        ->fetch();

    if ( !employeesAccountedFor( $serviceDetails, $assistant ) )
        $API->returnResponse( "Укажите второго исполнителя для услуги {$serviceDetails[ 'title' ]}", 500 );

    $service_second_employees = $API->DB->from( "services_second_users" )
        ->where( "service_id", $serviceDetails[ "id" ] );

    if ( count( $service_second_employees ) == 0 ) continue;
    if ( $serviceDetails[ "is_consider_second_performer_time" ] == "N" ) $employees = [ $employees[ 0 ] ];

} // foreach ( $services as $service )



/**
 * Проверка на занятость сотрудников
 * @param $emoployees
 * @param $services
 * @param $visits
 * @return bool
 * @throws \Envms\FluentPDO\Exception
 */
function isEmployeeBusy( $employee, $visits ): int {

    global $API;

    /**
     * Проход по всем посещениям
     */
    foreach ( $visits as $visit ) {

        /**
         * Получение всех сотрудников из посещения
         * PS: Запрос $API->DB->from... ->where( "user_id in (?)", join( ... ) ) не отрабатывает(
         */
        $visit_employees = mysqli_query(
            $API->DB_connection,
            "SELECT * FROM visits_users WHERE visit_id = {$visit[ "id" ]} AND user_id = $employee"
        );

        if ( mysqli_num_rows( $visit_employees ) != 0 ) return $visit[ "id" ];

    } // foreach ( $visits as $visit )

    return 0;

} // function isEmployeeBusy( $employees, $visits ): bool



/**
 * Обход сотрудников
 */
/**
 * Проверка сотрудника на занятость
 */
$busyVisitID = isEmployeeBusy( $employee, $existingVisits );
if ( $busyVisitID != 0 ) {

    $employee_details = $API->DB->from( "users" )
        ->where( "id", $employee )
        ->fetch();

    $store_details = $API->DB->from( "stores" )
        ->innerJoin( "visits ON stores.id = visits.store_id" )
        ->where( "visits.id", $busyVisitID )
        ->fetch();

    $API->returnResponse( "Сотрудник {$employee_details[ 'last_name' ]} занят. Филиал ({$store_details[ 'title' ]})", 500 );

}

