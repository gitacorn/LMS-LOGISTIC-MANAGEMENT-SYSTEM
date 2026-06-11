<?php
if(isset($allDates) && (!empty($allDates))){?>
	
			<table class="table table-hover table-bordered table-sm pb-4">
				<thead>
					<tr>
						<th class="sr-col">{{ trans("messages.sr-no") }}</th>
						<th style="max-width:100px;min-width:100px;">{{ trans("messages.date") }}</th>
						<th style="max-width:100px;min-width:100px;">{{ trans("messages.pallet-limit") }}</th>
						<th style="max-width:120px;min-width:120px;">Pallet Forecasted</th>
						<th style="max-width:120px;min-width:120px;">Pallet Received</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$recordId = $palletLimit = '';
						foreach($allDates as $key => $dates){
							if(isset($recordDetails) && (!empty($recordDetails))){
								$date = DateTime::createFromFormat('d-m-Y', $dates);
								$formattedDate = (!empty($date) ? $date->format('Y-m-d') : '');
								$record = $recordDetails->firstWhere('dt_pallet_date', $formattedDate);
								$palletLimit = (isset($record) ? $record->i_pallet_limit : null);
								$recordId = (isset($record) ? trim(Wild_tiger::encode($record->i_id)) : '');
							}
							?>
							<tr>
								<td class="text-center">{{++$key}}</td>
								<td style="max-width:100px;min-width:100px;">{{$dates}}</td>
								@if(!empty($recordId))
									<td style="max-width:100px;min-width:100px;"><input type="text" onchange="onlyNumber(this)" onkeyup="onlyNumber(this)" class="form-control" name="edit_pallet_limit[{{$recordId}}]" value="{{ checkNotEmptyString($palletLimit) ? $palletLimit : null }}"></td>
								@else
									<td style="max-width:100px;min-width:100px;"><input type="text" onchange="onlyNumber(this)" onkeyup="onlyNumber(this)" class="form-control" name="pallet_limit[{{$dates}}]"></td>
								@endif	
								<?php 
									$forecastValue = '-';
									if(isset($palletForecastMap) && is_array($palletForecastMap)){
										$forecastValue = (isset($palletForecastMap[$dates]) && $palletForecastMap[$dates] !== null && $palletForecastMap[$dates] !== '' ? $palletForecastMap[$dates] : '-');
									}
								?>
								<td style="max-width:120px;min-width:120px;" class="text-left">{{ $forecastValue }}</td>
								<?php 
									// Pallet Received: show value only up to yesterday (app timezone)
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
						?>
					 </tbody>
				</table>                           
	
	
<?php 
		
	}
?>