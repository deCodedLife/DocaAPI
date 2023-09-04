<?php


/**
 * Получение детальной информации о клиенте
 */

$clientDetails = $API->DB->from( "clients" )
    ->where( "id", $requestData->client_id )
    ->fetch();

if ( $requestData->sum_deposit > $clientDetails[ "deposit" ] )
    $API->returnResponse( "Недостаточно средств на депозитном счёте клиента", 400 );

if ( $requestData->sum_bonus > $clientDetails[ "bonuses" ] )
    $API->returnResponse( "Недостаточно средств на бонусном счёте клиента", 400 );



/**
 * Проверка корректности суммы оплаты с суммой посещения
 */

$saleSummary = 0;
$paymentsSummary =
    $requestData->sum_deposit +
    $requestData->sum_bonus +
    $requestData->sum_card +
    $requestData->sum_cash;



/**
 * Валидация итоговой суммы
 */

if ( $paymentsSummary != $requestData->summary )
    $API->returnResponse( "Сумма всех способов оплаты не совпадает с итоговой суммой посещения", 400 );
