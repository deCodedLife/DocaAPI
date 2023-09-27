<?php

/**
 * Фильтр документов при печати
 */

if ( $requestData->context->block == "print" ) {

    if ( $requestData->context->owner_id )
        $requestData->owner_id = $requestData->context->owner_id;

}
