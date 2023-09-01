<?php

namespace KPI;

define( "KPI_TABLE", "employees_kpi" );
define( "KPI_RELATED", "kpi_queries" );

class KPI
{

    private function getQueryResult( $kpi, $kpi_queries ) {

        global $API;

        $sorted_list = [];
        $target_priority = 0;

        while ( $target_priority == count( $kpi_queries ) - 1 ) {

            foreach ( $kpi_queries as $query ) {

                if ( $query[ "priority" ] == $target_priority )
                    $sorted_list[] = $query;

            }

        }


        $last_result = $sorted_list[ count( $sorted_list ) - 1 ][ "value" ];


        /**
         * Обход запросов
         */
        foreach ( $kpi_queries as $query ) {

            /**
             * Определение объектов запроса
             */
            $table = explode( ".", $query[ "property" ] )[ 0 ];
            $column = explode( ".", $query[ "property" ] )[ 1 ];

            $where = $query[ "value" ];
            $expression = $query[ "related" ] == "Y" ? "IN" : "=";

            if ( $query[ "related" ] == "Y" ) {

                foreach ( $last_result as $row ) {

                    $selector = explode( '.', $query[ "value" ] )[ 1 ];
                    $where[] = $row[ $selector ];

                }

                $where = "(" . join( ',', $where ) . ")";

            }

            /**
             * Запрос
             */
            $query = "SELECT * from $table WHERE $column $expression $where";
            $last_result = mysqli_fetch_array(
                mysqli_query(
                    $API->DB_connection,
                    $query
                )
            );

        }

        $summary = 0;

        if ( $kpi[ "type" ] == "summary" ) {

            foreach ( $last_result as $result )
                $summary += $result[ $kpi[ "article" ] ];

        }
        if ( $kpi[ "type" ] == "count" ) {
            
            $summary = count( $last_result[ $kpi[ "article" ] ] );

        }

        return $summary;

    } // function getQueryResult( $kpi_queries )


    /**
     * @param $user_id: int
     */
    public function __construct( $user_id ) {

        global $API;

        $employee_kpi_list = $API->DB->from( "employees_kpi" )
            ->where( "user_id", $user_id )
            ->fetch();

        foreach ( $employee_kpi_list as $kpi ) {

            $is_valid = false;
            $kpi_queries = $API->DB->from( "kpi_queries" )
                ->where( "kpi_id", $kpi[ "id" ] );

            $result_array = $this->getQueryResult( $kpi_queries );


        }

    }
};

global $KPI;