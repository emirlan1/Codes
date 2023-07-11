public static function getWorkTime(array $operatorIds): array
    {
        /** @var User $model */
        $query = User::find()
            ->andFilterWhere(['id' => $operatorIds])
            ->all();

        $workData = [];
        foreach ($query as $model) {
            $operatorTimezone = $model->operatorCC->timezone ? strtoupper($model->operatorCC->timezone) : "UTC";
            $currentTime = CarbonImmutable::now($operatorTimezone);
            $currentTimestamp = $currentTime->getTimestamp();
            $dayStart = $currentTime->startOfDay()->getTimestamp();

            //Берет статусы текущего дня и еще статус который начался в предыдущем дне, а закончился в текущем
            $sessions = OperatorSessions::find()
                ->andWhere(['>=', 'end_at', $dayStart])
                ->andWhere(['operator_id' => $model->id])
                ->orderBy(['id' => SORT_ASC])->all();

            //Так как в базе куча неправильных записей где end_at = null, делаем отдельный запрос где берем самый последний
            $currentSession = OperatorSessions::find()
                ->andWhere(['operator_id' => $model->id])
                ->andWhere(['end_at' => null])
                ->orderBy(['id' => SORT_DESC])->one();

            array_push($sessions, $currentSession);

            $readyTime = 0;
            $relaxTime = 0;
            $okTime = 0;
            $talkingTime = 0;
            foreach ($sessions as $row) {
                $time = 0;
                $start = $row->start_at;
                $end = !empty($row->end_at) ? $row->end_at : null;
                //Если начало сессии было вчера относительно текущего времени оператора
                if ($start < $dayStart) {
                    if ($end) {
                        //Отнимаю от даты конца сессии начало текущего дня
                        $time += $end - $dayStart;
                    } else {
                        //Сессия все еще идет, значит отнимаем от текущего времени
                        $time += $currentTimestamp - $dayStart;
                    }
                } else {
                    //Else если сессия была начата и закончена в рамках текущего дня
                    if ($end) {
                        //Отнимаю дату окончания сессии от начала сессии
                        $time += $end - $start;
                    } else {
                        //Сессия все еще идет, значит берем текущее время
                        $time += $currentTimestamp - $start;
                    }
                }

                switch ($row->session_status) {
                    case OperatorWorkingStatus::SESSION_STATUS_ACTIVE:
                        $readyTime += $time;
                        break;
                    case OperatorWorkingStatus::SESSION_STATUS_NOT_ACTIVE:
                        $okTime += $time;
                        break;
                    case OperatorWorkingStatus::SESSION_STATUS_PAUSE:
                        $relaxTime += $time;
                        break;
                    case OperatorWorkingStatus::SESSION_STATUS_IN_CONVERSATION:
                        $talkingTime += $time;
                        break;
                }
            }

            $workData[$model->id]['ready'] = gmdate("H:i:s", $readyTime);
            $workData[$model->id]['relax'] = gmdate("H:i:s", $relaxTime);
            $workData[$model->id]['ok'] = gmdate("H:i:s", $okTime);
            $workData[$model->id]['talking'] = gmdate("H:i:s", $talkingTime);
            $workData[$model->id]['approves'] = $model->todayApproveLeadsCount;
            $workData[$model->id]['out-calls'] = $model->todayOutCallsCount;
            $workData[$model->id]['success-calls'] = $model->todayAllSuccessCallsCount;
            $workData[$model->id]['approve-rate'] = $model->todayApproveRate . ' %';
            $workData[$model->id]['rejects'] = $model->todayRejectsCount;
        }

        return $workData;
    }