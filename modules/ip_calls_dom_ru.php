<?php

/**
 * @file
 * Интеграция с IP телефонией Дом.ру
 */

class IPCallsDomRu {

    /**
     * Модуль базы данных
     */
    private $DB = null;

    /**
     * Настройки IP телефонии
     */
    private $settings = null;



    function __construct ( $ipCallsSettings ) {

        /**
         * Подключение базы данных
         */
        $this->DB = new DB;

        /**
         * Подключение настроек IP телефонии
         */
        $this->settings = $ipCallsSettings;

    } // function __construct



    /**
     * Список аккаунтов
     *
     * return bool
     */
    public function getAccounts () {

        $queryUrl = $this->settings[ "crm_url" ] . "?token=" . $this->settings[ "token" ] . "&cmd=accounts";

        return file_get_contents( $queryUrl );

    } // function getAccounts

    /**
     * История
     *
     * return bool
     */
    public function getHistory ( $is_month = false ) {

        if ( !$is_month ) $queryUrl = $this->settings[ "crm_url" ] . "?token=" . $this->settings[ "token" ] . "&cmd=history";
        else $queryUrl = $this->settings[ "crm_url" ] . "?token=" . $this->settings[ "token" ] . "&cmd=history&period=last_month";


        return file_get_contents( $queryUrl );

    } // function getHistory

    /**
     * Инициализация звонка
     *
     * return bool
     */
    public function makeCall ( $clientPhone, $employee ) {

        $queryUrl = $this->settings[ "crm_url" ] . "?token=" . $this->settings[ "token" ] . "&cmd=makeCall&phone=$clientPhone&user=$employee";

        $response = file_get_contents(
            $queryUrl,
            false,
            stream_context_create(
                array(
                    'http' => array(
                        'method' => 'POST'
                    )
                )
            )
        );

        return $queryUrl;

    } // function makeCall

} // class Services

$IPCallsDomRu = new IPCallsDomRu( $ipCallsSettings );