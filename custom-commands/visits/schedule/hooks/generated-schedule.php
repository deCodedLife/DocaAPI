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
         * Обновленное расписание
         */
        $updatedSchedule = [];


        /**
         * Учет графика работ Исполнителя
         */

        foreach ( $performerSchedule as $performerEventKey => $performerEvent ) {

            /**
             * Игнорирование занятого времени
             */
            if ( $performerEvent[ "status" ] !== "available" ) {

                $updatedSchedule[ $performerEvent[ "steps" ][ 0 ] ] = [
                    "steps" => [ $performerEvent[ "steps" ][ 0 ], $performerEvent[ "steps" ][ 1 ] ],
                    "event" => $performerEvent[ "event" ],
                    "status" => $performerEvent[ "status" ]
                ];

                continue;

            } // if. $performerEvent[ "status" ] !== "available"

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
             * Удаление текущего блока.
             * Далее вместо него будут созданы блоки со статусами "available" и "empty" с учетом
             * графика работ Сотрудника
             */
            unset( $resultSchedule[ $scheduleDateKey ][ $schedulePerformerKey ][ "schedule" ][ $performerEventKey ] );


            /**
             * Последний добавленный шаг.
             * Используется для добавления блоков empty
             */
            $lastAddedStep = $performerEvent[ "steps" ][ 0 ];

            /**
             * Текущий статус.
             * Используется для добавления блоков empty
             */
            $currentStatus = "empty";
            if ( in_array( $performerEvent[ "steps" ][ 0 ], $workedScheduleSteps ) ) $currentStatus = "available";
            

            /**
             * Обход шагов блока
             */

            for (
                $scheduleBlockStepKey = $performerEvent[ "steps" ][ 0 ];
                $scheduleBlockStepKey <= $performerEvent[ "steps" ][ 1 ];
                $scheduleBlockStepKey++
            ) {

                /**
                 * Проверка, входит ли шаг в график работы
                 */
                $scheduleBlockStepStatus = "empty";
                if ( in_array( $scheduleBlockStepKey, $workedScheduleSteps ) ) $scheduleBlockStepStatus = "available";


                if (
                    (
                        ( $scheduleBlockStepStatus !== $currentStatus ) &&
                        ( $lastAddedStep < $scheduleBlockStepKey )
                    ) ||
                    ( $scheduleBlockStepKey >= $performerEvent[ "steps" ][ 1 ] )
                ) {

                    /**
                     * Добавление блока
                     */
                    $updatedSchedule[ $lastAddedStep ] = [
                        "steps" => [ $lastAddedStep, $scheduleBlockStepKey ],
                        "status" => $currentStatus
                    ];

                    /**
                     * Обновление последнего добавленного шага
                     */
                    $lastAddedStep = $scheduleBlockStepKey + 1;

                    /**
                     * Обновление текущего статуса
                     */
                    $currentStatus = $scheduleBlockStepStatus;

                } // if. $scheduleBlockStepStatus !== $currentStatus

            } // foreach. $updatedSchedule

        } // foreach. $performerSchedule


        /**
         * Обновление расписания с учетом графика работ
         */
        $updatedSchedule = array_values( $updatedSchedule );
        if ( $updatedSchedule ) $resultSchedule[ $scheduleDateKey ][ $schedulePerformerKey ][ "schedule" ] = $updatedSchedule;

    } // foreach. $scheduleDateDetail

} // foreach. $resultSchedule