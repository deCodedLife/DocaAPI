<?php

/**
 * Расчет свободности Исполнителей, Клиентов и Кабинетов
 */

if ( !$requestData->is_alert )
{
    /**
     * Валидация посещения
     */
    $publicAppPath = $API::$configs[ "paths" ][ "public_app" ];
    require_once ( $publicAppPath . '/custom-libs/visits/validateEquipment.php' );

}
if ( $requestData->status == "waited" ) $requestData->dateIssueCoupon = date("Y-m-d H:i:s");
