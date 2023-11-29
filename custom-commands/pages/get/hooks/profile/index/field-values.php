<?php
$requestData = $API->sendRequest( "users", "get-current", $requestData );

$formFieldValues[ "first_name" ] = $requestData->first_name;
$formFieldValues[ "last_name" ] = $requestData->last_name;
$formFieldValues[ "patronymic" ] = $requestData->patronymic;
$formFieldValues[ "email" ] = $requestData->email;
$formFieldValues[ "domru_login" ] = $requestData->domru_login;
$formFieldValues[ "passport_series" ] = $requestData->passport_series;
$formFieldValues[ "passport_number" ] = $requestData->passport_number;
$formFieldValues[ "passport_issued" ] = $requestData->passport_issued;
$formFieldValues[ "snils" ] = $requestData->snils;
$formFieldValues[ "inn" ] = $requestData->inn;
$formFieldValues[ "address" ] = $requestData->address;
$formFieldValues[ "phone" ] = $requestData->phone;
$formFieldValues[ "second_phone" ] = $requestData->second_phone;
$formFieldValues[ "gender" ] = $requestData->gender;
$formFieldValues[ "birthday" ] = $requestData->birthday;
$formFieldValues[ "is_visible_in_schedule" ] = $requestData->is_visible_in_schedule;
$formFieldValues[ "salary" ] = $requestData->salary;
$formFieldValues[ "is_percent" ] = $requestData->is_percent;
$formFieldValues[ "salary_type" ] = $requestData->salary_type;
$formFieldValues[ "avatar" ] = $requestData->avatar;
$formFieldValues[ "created_at" ] = $requestData->created_at;
$formFieldValues[ "comment" ] = $requestData->comment;
$formFieldValues[ "role_id" ] = $requestData->role_id;
$formFieldValues[ "store_id" ] = $requestData->store_id;



$users_professions = $API->DB->from( "users_professions" )
    ->where( "user_id", $requestData->id );

$professionIds = [];

foreach ( $users_professions as $users_profession ) {

    $professionIds[] = (int)$users_profession[ "profession_id"] ;

}

$formFieldValues[ "professions_id" ][ "value" ] = $professionIds;
