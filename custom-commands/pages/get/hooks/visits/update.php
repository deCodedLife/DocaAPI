<?php


/**
 * Получение продажи, связанной с посещением
 */

$saleDetails = $API->DB->from( "salesVisits" )
    ->where( "visit_id", $pageDetail[ "row_detail" ][ "id" ] )
    ->limit( 1 )
    ->fetch();


/**
 * Отключение возможности оплатить посещения, после оплаты
 */

//$API->returnResponse( json_encode( $pageScheme ), 400 );
/**
 * {"required_modules":[],"required_permissions":[],"structure":[{"title":"\u0428\u0430\u043f\u043a\u0430","type":"header","size":4,"settings":{"description":"\u0417\u0430\u043f\u0438\u0441\u0438 \u043a \u0432\u0440\u0430\u0447\u0430\u043c - \u0420\u0435\u0434\u0430\u043a\u0442\u0438\u0440\u043e\u0432\u0430\u043d\u0438\u0435","title":["\u0417\u0430\u043f\u0438\u0441\u044c \u2116 ",":id"]},"components":[]},{"title":"\u0422\u0430\u0431\u044b","type":"tabs","size":4,"settings":[{"title":"\u041e\u0441\u043d\u043e\u0432\u043d\u0430\u044f \u0438\u043d\u0444\u043e\u0440\u043c\u0430\u0446\u0438\u044f","body":[{"title":"\u0424\u043e\u0440\u043c\u0430","type":"form","size":4,"settings":{"object":"visits","command":"update","areas":[{"size":2,"blocks":[{"title":"","fields":["start_at","end_at","status"]},{"title":"","fields":["users_id","clients_id","services_id"]}]},{"size":2,"blocks":[{"title":"","fields":["store_id","cabinet_id"]},{"title":"","fields":["price","discount_type","discount_value"]}]}]},"components":{"buttons":[{"type":"script","required_permissions":["manager_schedule"],"settings":{"title":"\u041e\u0442\u043c\u0435\u043d\u0438\u0442\u044c","background":"dark","object":"visits","command":"cancel","data":{"id":":id"}}},{"type":"script","required_permissions":["manager_schedule"],"settings":{"title":"\u0417\u0430\u0432\u0435\u0440\u0448\u0438\u0442\u044c","background":"dark","object":"visits","command":"check-success","data":{"id":":id"}}},{"type":"submit","required_permissions":["manager_schedule"],"settings":{"title":"\u0421\u043e\u0445\u0440\u0430\u043d\u0438\u0442\u044c","background":"dark"}}]}}]},{"title":"\u041e\u043f\u043b\u0430\u0442\u0430","body":[{"title":"\u0424\u043e\u0440\u043c\u0430 \u043e\u043f\u043b\u0430\u0442\u044b","type":"form","size":4,"settings":{"object":"sales","command":"add","areas":[{"size":2,"blocks":[{"title":"","fields":["visits_ids","pay_object","pay_method","cash_sum","card_sum","summary"]}]},{"size":2,"blocks":[{"title":"","fields":["store_id","bonus_sum","deposit_sum","online_receipt","is_combined","client_id"]}]}]},"components":{"buttons":[{"type":"submit","settings":{"title":"\u041e\u043f\u043b\u0430\u0442\u0438\u0442\u044c","background":"dark","href":"dashboard"}}]}}]},{"title":"\u0418\u0441\u0442\u043e\u0440\u0438\u044f \u043f\u043e\u0441\u0435\u0449\u0435\u043d\u0438\u0439","body":[{"title":"\u0418\u0441\u0442\u043e\u0440\u0438\u044f \u043f\u043e\u0441\u0435\u0449\u0435\u043d\u0438\u0439","type":"list","size":4,"settings":{"object":"visits","filters":[{"property":"clients_id","value":":clients_id"}]},"components":[]}]},{"title":"\u0421\u0442\u0430\u0442\u0438\u0441\u0442\u0438\u043a\u0430","body":[{"title":"\u0421\u0442\u0430\u0442\u0438\u0441\u0442\u0438\u043a\u0430 \u043a\u043b\u0438\u0435\u043d\u0442\u043e\u0432","type":"analytic_widgets","size":4,"settings":{"widgets_group":"client_statistic","filters":[{"property":"client_id","value":":clients_id"}]},"components":[]}]},{"title":"\u041b\u043e\u0433\u0438","body":[{"title":"\u041b\u043e\u0433\u0438","type":"logs","size":4,"settings":{"object":"logs","filters":[{"property":"clients_id","value":":clients_id"}]},"components":{"filters":[{"title":"\u0414\u0430\u0442\u0430","type":"date","required_permissions":[],"required_modules":[],"settings":{"recipient_property":"created_at"}}]}}]}]}]}
 */

/**
 * {"url":["visits","update","209"],"scheme_name":"update.json","row_id":"209","row_detail":{"id":209,"store_id":{"title":"\u043f\u0440. \u042f\u0448\u044c\u043b\u0435\u043a 15\u0411","value":1},"cabinet_id":null,"price":"60","start_at":"2023-03-11 10:00:00","end_at":"2023-03-11 10:21:00","status":{"title":"\u0417\u0430\u043f\u043b\u0430\u043d\u0438\u0440\u043e\u0432\u0430\u043d\u043e","value":"planning"},"discount_type":{"title":"\u0424\u0438\u043a\u0441\u0438\u0440\u043e\u0432\u0430\u043d\u043d\u0430\u044f","value":"fixed"},"discount_value":"5","is_repeat":"N","is_payed":false,"users_id":[{"title":"\u0413\u0438\u043b\u044c\u0432\u0430\u043d\u043e\u0432 \u041d. \u0410.","value":3}],"clients_id":[{"title":"\u0418\u0432\u0430\u043d\u043e\u0432 \u0410. \u0418.","value":12}],"services_id":[{"title":"\u041f\u0440\u0438\u0435\u043c (\u043e\u0441\u043c\u043e\u0442\u0440, \u043a\u043e\u043d\u0441\u0443\u043b\u044c\u0442\u0430\u0446\u0438\u044f) \u0432\u0440\u0430\u0447\u0430-\u0442\u0435\u0440\u0430\u043f\u0435\u0432\u0442\u0430 \u043f\u0435\u0440\u0432\u0438\u0447\u043d\u044b\u0439","value":2},{"title":"\u041a\u043b\u0438\u043d\u0438\u0447\u0435\u0441\u043a\u0438\u0439 \u0430\u043d\u0430\u043b\u0438\u0437 \u043a\u0440\u043e\u0432\u0438","value":24}],"step":0,"profession_id":null},"section":"visits","scheme_path":"visits\/update.json"}
 */

if ( $pageDetail[ "row_detail" ][ "status" ]->value === "ended" || $saleDetails )
    $pageScheme[ "structure" ][ 1 ][ "settings" ][ 0 ][ "body" ][ 0 ][ "components" ][ "buttons" ] = [];

if ( $pageDetail[ "row_detail" ][ "is_payed" ] || $saleDetails )
    $pageScheme[ "structure" ][ 1 ][ "settings" ][ 1 ][ "body" ][ 0 ][ "components" ][ "buttons" ] = [];