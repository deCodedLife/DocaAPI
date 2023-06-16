<?php

/**
 * @file
 * Редактирование аватара сотрудника
 */



/**
 * Подключение модулей
 */

require_once( PATH_MODULES . "/db.php" );
require_once( PATH_MODULES . "/employees.php" );



/**
 * Проверка. Авторизован ли пользователь
 */
$userInfo = validateJWT( $JWT, $request->jwt, $jwt[ "key" ] );
if ( !$userInfo ) returnResponse( "Authorization required", 401 );

/**
 * Проверка. Есть ли у пользователя права на совершение данного действия
 */
if ( $Employees->validatePermission( "employees-change", $userInfo->role_id ) !== true ) returnResponse( "Access denied", 403 );



/**
 * Проверка. Переданны ли все обязательные параметры
 */
if ( !$request->data->employee_id ) returnResponse( "Bad request", 400 );
if ( !$request->data->file ) returnResponse( "Bad request", 400 );
if ( !$request->data->file_name ) returnResponse( "Bad request", 400 );
if ( strpos($request->data->file_name, "..") !== false ) returnResponse( "Bad file name", 400 );
if ( strpos($request->data->file_name, "/") !== false ) returnResponse( "Bad file name", 400 );

/**
 * Декодируем файл
 */

$image = base64_decode( $request->data->file, true );
$filename = PATH_ROOT . "/uploads/images/avatars/{$request->data->employee_id}.jpg";

if ( $image === false ) returnResponse("Cannot decode file", 400);

/**
 * Сохраняем файл
 */

$file = file_put_contents( $filename, $image );

if ( $file === false ) returnResponse( "Cannot create file", 500 );

/**
 * Меняем аватарку сотрудника
 */

if ( $Employees->changeAvatar( $request->data->employee_id, $filename ) === false ) returnResponse( "Something went wrong", 500 );

/**
 * Возвращаем ответ
 */

returnResponse( true, 200 );
