<?php 
	if(isset($allDates) && (!empty($allDates))){
		$palletLimit = '';
		foreach ($allDates as $key => $dates){
			if(isset($recordDetails) && (!empty($recordDetails))){
				$date = DateTime::createFromFormat('d-m-Y', $dates);
				$formattedDate = (!empty($date) ? $date->format('Y-m-d') : '');
				$record = $recordDetails->firstWhere('dt_pallet_date', $formattedDate);
				$palletLimit = (isset($record) ? $record->i_pallet_limit : null);
			}
			// Forecasted pallets (locked), default '-'
			$forecastValue = '-';
			if(isset($palletForecastMap) && is_array($palletForecastMap)){
				$forecastValue = (isset($palletForecastMap[$dates]) && $palletForecastMap[$dates] !== null && $palletForecastMap[$dates] !== '' ? $palletForecastMap[$dates] : '-');
			}
			?>
			<tr>
				<td class="text-center">{{++$key}}</td>
				<td style="max-width:100px;min-width:100px;">{{$dates}}</td>
				<td style="max-width:100px;min-width:100px;">{{ checkNotEmptyString($palletLimit) ? $palletLimit : '-' }}</td>
				<td style="max-width:120px;min-width:120px;" class="text-left">{{ $forecastValue }}</td>
				<?php 
					// Pallet Received: show value only up to yesterday
					$receivedValue = '-';
					$tz = config('app.timezone');
					$yesterday = \Carbon\Carbon::now($tz)->subDay()->startOfDay();
					$current = \Carbon\Carbon::createFromFormat('d-m-Y', $dates, $tz)->startOfDay();
					if($current && $current->lessThanOrEqualTo($yesterday)){
						if(isset($palletReceivedMap) && is_array($palletReceivedMap)){
							$receivedValue = (isset($palletReceivedMap[$dates]) && $palletReceivedMap[$dates] !== null && $palletReceivedMap[$dates] !== '' ? $palletReceivedMap[$dates] : '-');
						}
					}
				?>
				<td style="max-width:120px;min-width:120px;" class="text-left">{{ $receivedValue }}</td>
			</tr>
			<?php 
		}
	}
?>