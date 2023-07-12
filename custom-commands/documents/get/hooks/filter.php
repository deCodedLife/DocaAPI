<?php

/**
 * Фильтр документов при печати
 */

if ( $requestData->context->block == "print" )
    $requestData->owner_id = $API::$userDetail->id;
