<?php
//
//$changes = "";
//
//$currentVisit = $logData->id;
//$defaultVisit = [];
//
///**
// * Возвращает имена в стиле Иванов И.И.
// * из массива sql ответа
// *
// * @param $details
// * @return string
// */
//
//mb_internal_encoding("UTF-8");
//
//function getShortName( $details ): string
//{
//
//    return
//        mb_substr( $details[ "last_name" ], 0, mb_strlen( $details[ "last_name" ] )  ) . " " .
//        mb_substr( $details[ "first_name" ], 0, 1 ) . "." .
//        mb_substr( $details[ "patronymic" ], 0, 1 ) . ". ";
//
//} // function getShortName $details : string
//
//
///**
// * Выборка данных из ответа mysql по названию поля
// *
// * @param $mysql_response
// * @param $fieldName
// * @return array
// */
//function dbFieldToArray( $mysql_response, $fieldName ) {
//
//    $output = [];
//
//    foreach ( $mysql_response as $item ) {
//
//        $output[] = $item[ $fieldName ];
//
//    } // foreach $mysql_response as $item
//
//    return $output;
//
//} // function dbFieldToArray  $mysql_response, $fieldName {
//
//
///**
// * Получение коротких имён из массива
// *
// * [1, 2, 3]
// * "Семен В.А. Иванов И.И. ..."
// *
// * @param $user_types
// * @param $users_id
// * @return string
// */
//function getShortNames($user_types, $users_id ) {
//
//    $names = "";
//
//    foreach ( $users_id as $user_id ) {
//
//        $userDetails = $API->DB->from( $user_types )
//            ->where( "id", $user_id )
//            ->fetch();
//        $names .= getShortName( $userDetails );
//
//    } // foreach $users_id as $user_id
//
//    return $names;
//
//} // function geShortNames  $user_types, $users_id
//
//
///**
// * Получение короткого имени текущего пользователя
// */
//
//$currentEmployee = $API->DB->from( "users" )
//    ->where( "id", $API::$userData->id )
//    ->fetch();
//$currentEmployee = getShortName( $currentEmployee );
//
//
///**
// * Получение данных текущего посещения
// */
//
//$defaultVisit = $API->DB->from( "visits" )
//    ->where( "id", $currentVisit )
//    ->fetch();
//
//
//
///**
// * Получение клиентов неизмененного посещения
// */
//
//$defaultVisitClients = $API->DB->from( "visit_clients" )
//    ->where( "visit_id", $currentVisit );
//$defaultVisit[ "clients_id" ] = dbFieldToArray( $defaultVisitClients, "client_id" );
//
//
//
///**
// * Получение сотрудников неизмененного посещения
// */
//$defaultVisitEmployees = $API->DB->from( "visit_users" )
//    ->where( "visit_id", $currentVisit );
//$defaultVisit[ "users_id" ] = dbFieldToArray( $defaultVisitEmployees, "user_id" );
//
//
//
///**
// * Получение услуг неизмененного посещения
// */
//$defaultVisitServices = $API->DB->from( "visit_services" )
//    ->where( "visit_id", $currentVisit );
//$defaultVisit[ "services_id" ] = dbFieldToArray( $defaultVisitServices, "service_id" );
//
//
//
///**
// * Обработка изменений
// */
//
//if ( $logData->clients_id != $defaultVisit[ "clients_id" ] ) {
//
//    $changes .=
//        " клиентов с " . getShortNames("clients", $defaultVisit[ "clients_id" ]) .
//        " на " . getShortNames("clients", $logData->clients_id );
//
//} // if $logData->clients_id != $defaultVisit[ "clients_id" ]
//
//
//
//if ( $logData->users_id != $defaultVisit[ "users_id" ] ) {
//
//    $changes .=
//        " сотрудников с " . getShortNames("users", $defaultVisit[ "users_id" ]) .
//        " на " . getShortNames("users", $logData->users_id );
//
//} // if $logData->users_id != $defaultVisit[ "users_id" ]
//
//
//
//if ( $logData->services_id != $defaultVisit[ "services_id" ] ) {
//
//    $changes .=
//        " услуги с " . implode( ', ', $logData->services_id ) .
//        " на " . implode( ', ', $logData->services_id  );
//
//} // if $logData->services_id != $defaultVisit[ "services_id" ]
//
//
//
//if ( $logData->start_at != $defaultVisit[ "start_at" ] )
//    $changes .= "  начало с " . $defaultVisit[ "start_at" ] . " на " . $logData->start_at;
//
//if ( $logData->end_at != $defaultVisit[ "end_at" ] )
//    $changes .= " окончание  с " . $defaultVisit[ "end_at" ] . " на " . $logData->end_at;
//
//
//
//$logDescription = "Сотрудник $currentEmployee изменил $changes";
//$logData->is_ignore_current_user = true;