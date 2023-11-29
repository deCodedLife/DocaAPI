<?php

global $API, $requestData;


$sales_summary = 0;
$sales_count = 0;


/**
 * Получаем информацию о сотруднике
 */
$userDetail = $API->DB->from( "users" )
    ->where( "id", $requestData->user_id )
    ->fetch();


/**
 * Для менеджера и врачей kpi считается по разному
 */
if ( $userDetail[ "is_visible_in_schedule" ] == 'N' ) {

    /**
     * Берём продажи непосредственно из таблицы
     */
    $user_sales = mysqli_query(
        $API->DB_connection,
        "SELECT id
        FROM   salesList
        WHERE  employee_id = $requestData->user_id
               AND status = 'done'
               AND ( action = 'sell'
                      OR action = 'sellReturn' ) "
    );

} else {

    /**
     * Достаём id продаж из посещений
     */
    $user_sales = mysqli_query(
        $API->DB_connection,
        "SELECT saleVisits.sale_id AS id
        FROM   visits
               INNER JOIN saleVisits
                       ON visits.id = saleVisits.visit_id
        WHERE  visits.user_id = $requestData->user_id
               AND is_payed = 'Y'
               AND is_active = 'Y'"
    );

} // if ( $userDetail[ "is_visible_in_schedule" ] == 'N' )


/**
 * Получаем количество и стоимость услуг
 */
foreach ( $user_sales as $sale ) {
    
    $sale = $API->DB->from( "salesList" )
        ->where( "id", $sale[ "id" ] )
        ->fetch();

    if ( !$sale || !$sale[ "action" ] ) continue;


    $summary = mysqli_fetch_array(
        mysqli_query(
            $API->DB_connection,
            "SELECT Sum(( amount * cost ))
            FROM   salesProductsList
            WHERE  sale_id = {$sale[ "id" ]}
                   AND type = 'service'"
        )
    )[0];

    $count = mysqli_fetch_array(
        mysqli_query(
            $API->DB_connection,
            "SELECT Sum(amount)
            FROM   salesProductsList
            WHERE  sale_id = {$sale[ "id" ]}
                   AND type = 'service'; "
        )
    )[0];


    if ( $sale[ "action" ] == "sellReturn" ) {

        $summary = -$summary;
        $count = -$count;

    } // if ( $sale[ "action" ] == "sellReturn" )

    $sales_summary += $summary;
    $sales_count += $count;


} // foreach ( $user_sales as $sale )