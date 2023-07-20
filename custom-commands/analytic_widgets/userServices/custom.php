<?php

/**
 * @file
 * Отчет "продажа услуг по сотрудникам
 */


/**
 * Детальная информация об отчете
 */
$reportStatistic = [

    /**
     * Сумма продаж
     */
    "services_sum" => 0,

];

/**
 * Получение списка услуг
 */
$userServices = $API->sendRequest( "userServices", "get", $requestData );

/**
 * Обрабботка списка
 */
foreach ( $userServices as $userService ) {

    $reportStatistic[ "services_sum" ] += $userService->sum;

} // foreach .$userServices


$API->returnResponse(

    [

        [
            "value" => $reportStatistic[ "services_sum" ],
            "description" => "Прибыль",
            "icon" => "",
            "prefix" => "₽",
            "postfix" => [
                "icon" => "",
                "value" => "",
                "background" => ""
            ],
            "type" => "char",
            "background" => "",
            "detail" => []
        ]
    ]

);























//
///**
// * Фильтр услуг в посещениях
// */
//$visitServicesFilter = [];
//
///**
// * Фильтр для услуг
// */
//$servicesFilter = [];
//
//
///**
// * Формирование фильтров
// */
//
//if ( $requestData->start_at ) $visitServicesFilter[ "date >= ?" ] = $requestData->start_at . " 00:00:00";
//if ( $requestData->end_at ) $visitServicesFilter[ "date <= ?" ] = $requestData->end_at . " 23:59:59";
//if ( $requestData->store_id ) $visitServicesFilter[ "store_id" ] = $requestData->store_id;
//
//if ( $requestData->category_id ) $servicesFilter[ "category_id" ] = $requestData->category_id;
//if ( $requestData->id ) $servicesFilter[ "id" ] = $requestData->id;
//
//$servicesFilter[ "is_active" ] = "Y";
//
//
///**
// * Получение услуг
// */
//$services = $API->DB->from( "services" )
//    ->where( $servicesFilter );
//
///**
// * Получение услуг в посещениях
// */
//$visitsServices = $API->DB->from( "visits_services" )
//    ->where( $visitServicesFilter );
//
//
///**
// * Фильтр по сотрудникам
// */
//if ( $requestData->user_id ) {
//
//    /**
//     * Получение посещений сотрудника
//     */
//    $visitsUsers = $API->DB->from( "visits_users" )
//        ->where( "user_id", $requestData->user_id );
//
//
//    /**
//     * Фильтр услуг в посещениях по сотруднику
//     */
//
//    $filteredVisitsServices = [];
//
//    foreach ( $visitsServices as $visitsService ) {
//
//        $isContinue = true;
//
//        foreach ( $visitsUsers as $visitsUser )
//            if ( $visitsUser[ "visit_id" ] == $visitsService[ "visit_id" ] ) $isContinue = false;
//
//        if ( $isContinue ) continue;
//
//
//        $filteredVisitsServices[] = $visitsService;
//
//    } // foreach. $visitsServices
//
//
//    /**
//     * Обновление услуг в посещениях
//     */
//    $visitsServices = $filteredVisitsServices;
//
//} // if. $requestData->user_id
//
//
///**
// * Обработка услуг
// */
//
//foreach ( $services as $service ) {
//
//    /**
//     * Кол-во оказанных услуг
//     */
//    $count = 0;
//
//
//    /**
//     * Подсчет оказанных услуг
//     */
//    foreach ( $visitsServices as $visitsService )
//        if ( $visitsService[ "service_id" ] == $service[ "id" ] )
//            $count++;
//
//
//    $reportStatistic[ "visits_sum" ] += $service[ "price" ] * $count;
//
//} // foreach. $services

