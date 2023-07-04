<?php


/**
 * Получение детальной информации о клиенте
 */

$clientDetails = $API->DB->from( "clients" )
    ->where( "id", $requestData->client_id )
    ->fetch();

if ( $requestData->deposit_sum > $clientDetails[ "deposit" ] )
    $API->returnResponse( "Недостаточно средств на депозитном счёте клиента", 400 );

if ( $requestData->bonus_sum > $clientDetails[ "bonuses" ] )
    $API->returnResponse( "Недостаточно средств на бонусном счёте клиента", 400 );



/**
 * Проверка корректности суммы оплаты с суммой посещения
 */

$saleSummary = 0;
$paymentsSummary =
    $requestData->deposit_sum +
    $requestData->bonus_sum +
    $requestData->card_sum +
    $requestData->cash_sum;



/**
 * Валидация итоговой суммы
 */

if ( $paymentsSummary != $requestData->summary )
    $API->returnResponse( "Сумма всех способов оплаты не совпадает с итоговой суммой посещения", 400 );