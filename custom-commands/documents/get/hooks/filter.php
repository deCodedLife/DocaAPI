<?php

/**
 * Фильтр документов при печати
 */
if ( $requestData->context->block == "print" ) {

    if ( $requestData->context->owners_id )
        $requestData->owners_id = $requestData->context->owners_id;

}
