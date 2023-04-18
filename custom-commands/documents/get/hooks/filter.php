<?php

/**
 * Фильтр документов при печати
 */
if ( $requestData->context == "print" )
    $requestData->owner_id = $API::$userDetail->id;
