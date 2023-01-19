<?php

/**
 * Обход дат расписания
 */
foreach ( $resultSchedule as $scheduleDateKey => $scheduleDateDetail ) {

    /**
     * Обход Исполнителей в расписании за текущую дату
     */
    foreach ( $scheduleDateDetail as $schedulePerformerKey => $schedulePerformerDetail ) {

        /**
         * Расписание Исполнителя на текущую дату
         */
        $performerSchedule = $schedulePerformerDetail[ "schedule" ];


        /**
         * Учет графика работ Исполнителя
         */

        foreach ( $performerSchedule as $performerEventKey => $performerEvent ) {

            /**
             * Игнорирование занятого времени
             */
            if ( $performerEvent[ "status" ] !== "available" ) continue;


            /**
             * Шаги в рабочем графике
             */
            $workedScheduleSteps = [];


            /**
             * Определение шагов в рамках рабочего времени Сотрудника
             */

            foreach ( $performersWorkSchedule[ $schedulePerformerKey ][ $scheduleDateKey ] as $performerWorkSchedule ) {

                $workScheduleStepFromKey = getStepKey( $performerWorkSchedule[ "from" ] );
                $workScheduleStepToKey = getStepKey( $performerWorkSchedule[ "to" ] );


                foreach ( $stepsList as $stepKey => $step )
                    if (
                        ( $stepKey >= $workScheduleStepFromKey ) && ( $stepKey <= $workScheduleStepToKey )
                    ) $workedScheduleSteps[] = $stepKey;

            } // foreach. $performersWorkSchedule[ $schedulePerformerKey ][ $scheduleDateKey ]


            /**
             * Игнорирование Сотрудников без графика работ
             */

            if ( count( $workedScheduleSteps ) < 1 ) {

                unset( $resultSchedule[ $scheduleDateKey ][ $schedulePerformerKey ] );


                /**
                 * Игнорирование пустых дней
                 */
                if ( !$resultSchedule[ $scheduleDateKey ] ) unset( $resultSchedule[ $scheduleDateKey ] );

                continue;

            } // if. count( $workedScheduleSteps ) < 1


            /**
             * Сжатие шагов в Расписании
             */
            $workedScheduleSteps = [
                $workedScheduleSteps[ 0 ],
                $workedScheduleSteps[ count( $workedScheduleSteps ) - 1 ]
            ];


            /**
             * Обновление блока "available" с учетом графика работы Сотрудника
             */
            $resultSchedule[ $scheduleDateKey ][ $schedulePerformerKey ][ "schedule" ][ $performerEventKey ][ "steps" ] = $workedScheduleSteps;


            /**
             * Обновленное расписание с блоков типа "empty"
             */

            $updatedSchedule = [];

            foreach ( $resultSchedule[ $scheduleDateKey ][ $schedulePerformerKey ][ "schedule" ] as $scheduleBlock )
                $updatedSchedule[ $scheduleBlock[ "steps" ][ 0 ] ] = $scheduleBlock;


            /**
             * Добавление блоков типа "empty"
             */

            if ( $performerEvent[ "steps" ][ 0 ] < $workedScheduleSteps[ 0 ] ) {

                $updatedSchedule[ $performerEvent[ "steps" ][ 0 ] ] = [
                    "steps" => [
                        $performerEvent[ "steps" ][ 0 ], $workedScheduleSteps[ 0 ] - 1
                    ],
                    "status" => "empty"
                ];

            } // if. $performerEvent[ "steps" ][ 0 ] < $workedScheduleSteps[ 0 ]

            if ( $performerEvent[ "steps" ][ 1 ] > $workedScheduleSteps[ 1 ] ) {

                $updatedSchedule[ $workedScheduleSteps[ 1 ] + 1 ] = [
                    "steps" => [
                        $workedScheduleSteps[ 1 ] + 1, $performerEvent[ "steps" ][ 1 ]
                    ],
                    "status" => "empty"
                ];

            } // if. $performerEvent[ "steps" ][ 1 ] > $workedScheduleSteps[ 1 ]

            sort( $updatedSchedule );


            /**
             * Обновление расписания
             */
            $resultSchedule[ $scheduleDateKey ][ $schedulePerformerKey ][ "schedule" ] = $updatedSchedule;

        } // foreach. $performerSchedule

    } // foreach. $scheduleDateDetail

} // foreach. $resultSchedule