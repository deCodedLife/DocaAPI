<?php

if ( $requestData->owners_id ) {

    $requestData->is_general = false;

} else {

    $requestData->is_general = true;

}
