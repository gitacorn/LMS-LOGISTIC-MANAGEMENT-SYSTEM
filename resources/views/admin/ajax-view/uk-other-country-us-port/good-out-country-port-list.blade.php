<div class="card card-body card-pagination-items-class">
	{{ Wild_tiger::readMessage() }}
		<div class="table-responsive fixed-tabel">
			<table class="table table-sm table-bordered table-hover" id="user-table">
				<thead>
					<tr>
						<th style="max-width:100px;min-width:40px;">{{ trans("messages.sr-no") }}</th>
						<th class="text-left">{{ trans("messages.entry-no") }}</th>
						<th class="text-left" style="max-width:180px;min-width:180px;">{{ trans("messages.way-of-transport") }} <br> {{ trans("messages.from-port-airport") }} <br> {{ trans("messages.to-port-airport") }} </th>
						<th class="text-left" style="max-width:80px;min-width:80px;">{{ trans("messages.book-by") }}</th>
						<th class="text-left" style="max-width:80px;min-width:80px;">{{ trans("messages.logistic-partner-uk") }}</th>
						<th class="text-left">{{ trans("messages.container-no-air-waybill-no") }}</th>
						<th class="text-left" style="max-width:140px;min-width:140px;">{{ trans("messages.etd-dispatch-date") }} <br> {{ trans("messages.eta-arrival-date") }} <br> {{ trans("messages.delivery-date") }}</th>
						<th class="text-left" style="max-width:100px;min-width:100px;">{{ trans("messages.total-logistic-cost-usd") }} <br> {{ trans("messages.total-pallets") }}</th>
						<th class="text-left">{{ trans("messages.dangerous-goods") }}</th>
						<th class="text-left" style="max-width:120px;min-width:120px;">{{ trans("messages.tracking-no") }} <br> {{ trans("messages.tracking-link") }}</th>
						<th class="text-left">{{ trans("messages.status") }}</th>
						<th class="text-left">{{ trans("messages.upload-view-sheet-status") }}</th>
						<th class="text-left" style="max-width:110px;min-width:110px;">{{ trans("messages.fba-sheet-status") }}</th>	
						<th class="text-left" style="max-width:110px;min-width:110px;">{{ trans("messages.personal-ref") }}</th>
						<th class="text-left" style="max-width:150px;min-width:150px;">{{ trans("messages.from-warehouse-country") }} - {{ trans("messages.warehouse") }}</th>
						<th class="text-left" style="max-width:110px;min-width:110px;">{{ trans("messages.pick-up-date-from-warehouse") }}</th>
						<th class="text-center" style="max-width:100px;min-width:100px;">{{ trans("messages.actions") }}</th>
					</tr>
				</thead>

				<tbody class="">
					<?php 
					if(count($recordDetails) > 0){
						$index= ($page_no - 1) * $perPageRecord;
						foreach ($recordDetails as $recordDetail){
						?>
							<tr>
								<?php 
								$recordInfo = [];
								$recordInfo['rowIndex'] = ++$index;
								$recordInfo['recordDetail'] = $recordDetail;
								$html = view (config('constants.AJAX_VIEW_FOLDER') . 'uk-other-country-us-port/single-good-out-country-port-list')->with ( $recordInfo )->render();
								echo $html;
								?>
							</tr>
						<?php 
						}
					} else {
					?>
					<tr>
						<td colspan="17" class="text-center">{{ trans('messages.no-record-found')}}</td>
					</tr>
					<?php 
					}?>
					@include('admin/common-display-count')
					
				</tbody>
			</table>
		</div>
		{{ $recordDetails->onEachSide(1)->links() }}
	</div>
