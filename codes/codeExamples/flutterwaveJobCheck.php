public function actionFlutterwaveLeadCheck()
    {
        try {
            $flutterwaveCheckUrl = Yii::$app->params['flutterwave']['api'];
            $secretKey = Yii::$app->params['flutterwave']['secret_key'];

            $prepaymentAwaitLeads = Lead::find()->whereStatuses(Status::DEFAULT_FLUTTERWAVE_PREPAYMENT_AWAIT)->all();

            if (!$prepaymentAwaitLeads) {
                return 'No lead found';
            }

            $from = $prepaymentAwaitLeads[0]->created_at;
            $to = $prepaymentAwaitLeads[count($prepaymentAwaitLeads) - 1]->created_at + 86400;

            $headers = ['Content-Type' => 'application/json', 'Authorization' => "Bearer " . $secretKey];
            $client = new Client(['headers' => $headers]);

            $response = $client->request(
                'GET',
                $flutterwaveCheckUrl,
                [
                    'query' => [
                        'from' => date('Y-m-d', $from),
                        'to' => date('Y-m-d', $to)
                    ]
                ]
            );
            $content = json_decode($response->getBody()->getContents(), true);
            $pageCount = $content['meta']['page_info']['total_pages'];
            $transactions = [];
            for ($i = 1; $i <= $pageCount; $i++) {
                if ($i > 1) {
                    $response = $client->request(
                        'GET',
                        $flutterwaveCheckUrl,
                        [
                            'query' => [
                                'from' => date('Y-m-d', $from),
                                'to' => date('Y-m-d', $to),
                                'page' => $i
                            ]
                        ]
                    );
                    $content = json_decode($response->getBody()->getContents(), true);
                }
                foreach ($content['data'] as $row) {
                    $transactions[] = $row;
                }
            }

            foreach ($prepaymentAwaitLeads as $lead) {
                $leadTransactions = array_filter($content['data'], function ($item) use ($lead) {
                    return $lead->tx_ref == $item['tx_ref'];
                });

                ArrayHelper::multisort($leadTransactions, ['id'], [SORT_DESC]);
                $transaction = $leadTransactions[0];

                if (count($leadTransactions) == 0) {
                    $lead->linkStatus(Status::DEFAULT_FLUTTERWAVE_PREPAYMENT_FAILED);
                    $lead->flutterwave_prepayment = 1;
                    $lead->flutterwave_transaction_id = 0;
                    $lead->save();
                    continue;
                }

                if ($transaction['status'] == 'failed') {
                    $lead->linkStatus(Status::DEFAULT_FLUTTERWAVE_PREPAYMENT_FAILED);
                    $lead->flutterwave_prepayment = 1;
                    $lead->flutterwave_transaction_id = $transaction["id"];
                    $lead->save();
                    continue;
                }

                if ($transaction['status'] == 'successful'
                    && $transaction['amount'] == $lead->price
                    && $transaction['currency'] == 'NGN'
                ) {
                    try {
                        $dealService = new DealService($lead);
                        $dealService->createDeal();
                        $lead->flutterwave_prepayment = 1;
                        $lead->flutterwave_transaction_id = $transaction["id"];
                        $lead->save();
                    } catch (NotFoundHttpException $e) {
                        $message = 'flutterwave create deal error, lead_id=' . $lead->id . ', : ' . $e->getMessage();
                        Yii::warning($message);
                    }
                    continue;
                }
            }
        } catch (\Exception $e) {
            $message = 'flutterwave cron error: ' . $e->getMessage();
            Yii::warning($message);
        }
    }