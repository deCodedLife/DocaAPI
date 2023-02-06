<?php
//
//
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
//        mb_substr( $details[ "last_name" ], 0, mb_strlen( $details[ "last_name" ], 'UTF-8' ), 'UTF-8'  ) . " " .
//        mb_substr( $details[ "first_name" ], 0, 1, 'UTF-8' ) . "." .
//        mb_substr( $details[ "patronymic" ], 0, 1, 'UTF-8' ) ?? ". ";
//
//} // function getShortName $details : string
//
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
//function getShortNames( $user_types, $users_id ) : string
//{
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
//    }  // foreach $users_id as $user_id
//
//    return $names;
//
//} // function geShortNames  $user_types, $users_id
//
//
//
///**
// * Получение короткого имени текущего пользователя
// */
//
//$currentEmployee = $API->DB->from( "users" )
//    ->where( "id", $API::$userDetail->id )
//    ->fetch();
//$currentEmployee = getShortName( $currentEmployee );
//
//
//
//$logDescription = "Сотрудник $currentEmployee записал: " .
//    getShortNames("clients", $logData->clients_id ) .
//    " к " .
//    getShortNames("users", $logData->users_id );
//
//$logData->is_ignore_current_user = true;
