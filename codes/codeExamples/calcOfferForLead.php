public static function calcOfferForLead($lead)
	{
		$leadgen = User::find()->where(['id' => $lead->user_id])->one();
		if ($offer = Offer::find()->where(['id' => $lead->offer_id])->andWhere(['>', 'is_active', 0])->one()) {
			$offerState = new \stdClass();

			$leadData = $offer->getCurrentUserOfferData($lead, $leadgen, true);

			$offerState->offer = $leadData;

			//Set lead payout
            $lead->pay_type = $leadgen->user_type;
            $lead->pay_price = $leadData->defaultPrice;
            $lead->is_test = 0;

            //Change lead payout if there are other settings
			if (!empty($leadData->payouts)) {
				foreach ($leadData->payouts as $payout) {
					if ($payout->country_id == $lead->country || $payout->country_id == 'all') {

                        $lead->pay_type = $payout->pay_type;
						$lead->pay_price = $payout->price;
						$lead->is_test = $payout->is_test;

						$offerState->payout = $payout;
					}
				}
			}

            $now = CarbonImmutable::now();
            switch ($lead->country) {
                case 'ng':
                case 'nigeria':
                    $now = $now->setTimezone(Timezone::AFRICA_LAGOS_TIMEZONE);
                    break;
                case 'co':
                case 'columbia':
                    $now = $now->setTimezone(Timezone::AMERICA_BOGOTA_TIMEZONE);
                    break;
            }

            $deliveryTime = CarbonImmutable::createFromTimestamp($lead->deal->delivery_time_from)
                ->setTimezone($now->getTimezone())->startOfDay();
			$dayDifference = $deliveryTime->diffInDays($now->startOfDay());
			$objectPropertyDeliverySchedule = 'delivery_schedule_day_' . $dayDifference;

            if (isset($offer->$objectPropertyDeliverySchedule) && !$leadData->is_ignore_delivery_schedule) {
                $price = $lead->pay_price;
                $percent = $offer->$objectPropertyDeliverySchedule;
                $lead->pay_price = ceil($price * ($percent / 100));
            }

			$lead->offer_state = json_encode($offerState);
			$lead->has_error = (int)$lead->offerError['has_error'];

			$lead->save();
		}
    }


    public function getCurrentUserOfferData(Lead $lead, User $user, bool $stateOnly = false)
    {
        $offerUser = $this->offerUser;
        $userType = is_null($user) ? Yii::$app->user->identity->user_type : $user->user_type;
        $payouts = [];
        $isLeadDiscount = $lead->is_discounted;
        $payPrice = $this->detectPayoutPrice($userType, $this, $isLeadDiscount);
        $isIgnoreDeliverySchedule = false;

        // Main offer settings countries
        $geoList = $this->offerGeoList;

        // Access offer settings countries for current user
        $payoutCountriesIDs = [];

        $myPayouts = $this->getofferPayoutsMy($user->id, $lead->country);
        $paymentStatus = false;

        if ($myPayouts) {
            $payoutCountriesIDs[] = $myPayouts->country_id;
            $payout = new \stdClass();
            $payout->country_id = $myPayouts->country_id;
            $payout->pay_type = $myPayouts->pay_type;
            if ($myPayouts->pay_type == User::USER_TYPE_COSTS) {
                $payout->price = null;
            } else {
                $payout->price = $isLeadDiscount ? $myPayouts->discount_pay_price : $myPayouts->pay_price;
                if ($offerProductPayoutPrice = $myPayouts->getOfferProductPayoutPrice($myPayouts->offer->product_id)->all()) {
                    foreach ($offerProductPayoutPrice as $row) {
                        if ($row->offerProductPrice->price == $lead->price) {
                            $payout->price = $isLeadDiscount ? $row->discount_pay_price : $row->pay_price;
                        }
                    }
                }
            }
            $payout->is_test = $myPayouts->is_test;
            $payout->is_active = (int)($myPayouts->is_active == 1);
            $isIgnoreDeliverySchedule = $myPayouts->is_ignore_delivery_schedule;
            $payouts[] = $payout;
            $paymentStatus = (bool)($myPayouts->is_active == 1) || $paymentStatus;
        } elseif ($this->product_id) {
            $offerProductPrice = OfferProductPrice::find()
                ->where(['offer_id' => $this->id, 'product_id' => $this->product_id])->all();
            foreach ($offerProductPrice as $row) {
                if ($row->price == $lead->price) {
                    $payPrice = $this->detectPayoutPrice($userType, $row, $isLeadDiscount);
                }
            }
        }

        if ($geoList && $this->public_access) {
            foreach ($geoList as $key => $geoItem) {
                if (!in_array($geoItem->country_id, $payoutCountriesIDs)) {
                    $payout = new \stdClass();
                    $payout->country_id = $geoItem->country_id;
                    $payout->country = $geoItem->country;
					$payout->city_id = $geoItem->city_id ?? null;
                    $payout->price = $this->detectPayoutPrice($userType, $this, $isLeadDiscount);
                    $payout->is_test = 0;
                    $payout->is_active = (int)($this->is_active == 1);
                    $payouts[] = $payout;
                }
            }
        }

        $data = new \stdClass();
        $data->id = $this->id;
        $data->userType = $userType;
        $data->public_access = $this->public_access;
        $data->status = true;
        $data->is_active = $this->is_active;
        $data->message = '';
        $data->paymentStatus = $paymentStatus;
        $data->payouts = $payouts;
        $data->defaultPrice = $payPrice;
		$data->defaultIsCost = 0;
        $data->mainPrice = $this->mainPrice;
        $data->product_id = $this->product_id;
        $data->is_ignore_delivery_schedule = $isIgnoreDeliverySchedule;

        if (!$stateOnly) {
            $data->title = $this->title;
            $data->description = $this->description;
            $data->image = $this->image;
            $data->cr = $this->cr;
            $data->epc = $this->epc;
            $data->approvePercentText = $this->approvePercentText;
            $data->geo = $geoList;
            $data->landings = $this->offerLandings;
            $data->offerUser = $offerUser;
        }

        if ($offerUser && $offerUser->is_active == 0 && Yii::$app->controller->action->id == 'my') {
            $data->status = false;
            $data->message = 'Offer is removed';
        }

        if ($this->public_access == 0 && !$paymentStatus) {
            $data->status = false;
            $data->message = 'Access to the offer is stopped';
        }

        if ($this->is_active != 1) {
            $data->status = false;
            $data->message = 'Offer is stopped';
        }
        

        return $data;
    }
