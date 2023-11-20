<?php

/**
 * Фильтр по периоду
 */
if ( $requestData->sort_by === "period" ) $requestData->sort_by = "start_at";

/**
 * Фильтр по дате (до)
 */
if ( $requestData->end_at ) $requestData->end_at .= " 23:59:59";