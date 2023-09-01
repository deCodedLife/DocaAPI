<?php

global $API;

class LocalScope
{
    public $publicAppPath;
    public $libPath;
    public $libName;
    public $messages;

    public function __construct()
    {
        $this->publicAppPath = $API::$configs[ "paths" ][ "public_app" ];
        $this->libPath = __DIR__;
        $this->libName = basename( $this->libPath );
        $this->messages = [];

        $this->configureDatabase();
        $this->copyInternals();

    } // public function __construct()


    /**
     * Копирование общих файлов
     */
    public function copyInternals() {

        $internalContents = array_diff( scandir( "$this->libPath/internal" ), [ "..", "." ] );

        /**
         * Копирование файлов библиотеки
         */
        foreach ( $internalContents as $content ) {

            $folderPath = "$this->libPath/internal/$content";
            $this->moveFiles( $folderPath, "$this->publicAppPath/$content" );

        } // foreach ( $internalContents as $content )

        $this->messages[] = "[ ] $this->libName internals copied successfully";

    } // public function copyInternals() {



    /**
     * Создание таблиц в бд
     */
    public function configureDatabase(): void {

        /**
         * Проверка наличия скриптов конфигурации базы данных
         */
        if ( !is_dir( "$this->libPath/database" ) )  {

            $this->messages[] = "[ ] $this->libName: No database actions";
            return;

        }

        /**
         * Получение путей скриптов
         */
        $scripts = array_diff( scandir( $this->libPath . "/database" ), [ "..", "." ] );

        /**
         * Подключение скриптов
         */
        foreach ( $scripts as $script ) {

            /**
             * Пропускаем директории
             */
            if ( is_dir( $script ) ) continue;
            $scriptPath = "$this->libPath/database/$script";

            try {

                require_once( $scriptPath );

            } catch ( Throwable $e ) {

                /**
                 * Обработка ошибок в runtime)
                 */
                $this->messages[] = "[-] $this->libName: Error occurred when trying to create database ($script): $e";
                return;

            } // try

            $this->messages[] = "[ ] $this->libName ($script) installed";

        } // foreach ( $scripts as $script )

    } // public function configureDatabase


    /**
     * Копирование схем объектов
     */
    function moveFiles( $from, $to ): void {

        /**
         * Получение списка файлов в папке
         */
        $files = array_diff( scandir( $from ), [ "..", "." ] );

        foreach ( $files as $file ) {

            $filePath = $from . DIRECTORY_SEPARATOR . $file;

            /**
             * Рекурсивный вызов функции, если это папка
             */
            if ( is_dir( $filePath ) ) {

                $this->moveFiles( $filePath, "$to/$file" );
                continue;

            }

            /**
             * Копирование файла
             */
            $pathInfo = pathinfo( $to . DIRECTORY_SEPARATOR . $file );

            if ( !file_exists( $pathInfo[ 'dirname' ] ) )
                mkdir( $pathInfo[ 'dirname' ], 0777, true );

            copy( $filePath, "$to/$file" );

        }

    } // function moveFiles( $root, $from, $to ): array {

}

$Scope = new LocalScope;
$API->returnResponse( $Scope->messages );