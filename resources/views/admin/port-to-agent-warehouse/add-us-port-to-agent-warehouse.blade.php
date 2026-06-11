@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-md-flex border-navabr align-items-center">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle"> {{ $pageTitle }}</h1>
	</div>

	<section class="inner-wrapper-common-sections main-listing-section">
		<div class="container-fluid">
			<div class="card document-card mb-3">
				<ul class="document-items">
					<li class="document-list"> <a href="#details" class="document-text">{{ trans("messages.details") }}</a></li>
					<li class="document-list"> <a href="#documents" class="document-text">{{ trans("messages.documents") }}</a></li>
					<li class="document-list"> <a href="#transporter-invoice" class="document-text">{{ trans("messages.transporter-invoice") }}</a></li>
					<li class="document-list"> <a href="#status" class="document-text">{{ trans("messages.status") }}</a></li>
					<?php if( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ) { ?>
					<li class="document-text ml-auto logistic-master-no"><?php echo (!empty($recordInfo->v_port_to_agent_record_no) ? $recordInfo->v_port_to_agent_record_no : '')?></li>
					<?php }?>
				</ul>
			</div>

			<div class="card mb-3 good-in-buyer-class" id="details">
				<h3 class="title-goods"><i class="fas fa-level-up-alt list-icon mr-2"></i>{{ trans("messages.details") }}</h3>
				{!! Form::open(array( 'id'=> 'add-us-port-agent-warehouse-form' , 'method' => 'post' , 'files' => true , 'url' => 'port-to-agent-warehouse/add')) !!}
				@csrf
					@include('admin/common-form-validation-error')
				<div class="card-body">
					<div class="row us-agent-waregouse-list dependent-field-div">
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="way_of_transport">{{ trans("messages.way-of-transport") }}<span class="text-danger">*</span></label>
								<select name="way_of_transport" class="form-control" <?php echo $disableForm ?>>
									<option value="">{{ trans("messages.select") }}</option>
									<?php
									if (!empty($wayOfTransportDetails)) {
										foreach ($wayOfTransportDetails as  $key => $wayOfTransportDetail) {
											$selected = '';
											if (old('way_of_transport', (isset($recordInfo->e_transport_way) ? $recordInfo->e_transport_way : '')) == $key) {
												$selected = "selected='selected'";
											}
									?>
											<option value="{{ $key}}" {{ $selected }}>{{ $wayOfTransportDetail }}</option>
									<?php
										}
									}
									?>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="from_port_airport">{{ trans("messages.from-port-airport") }}<span class="text-danger">*</span></label>
								<select name="from_port_airport" class="form-control" onchange="getContainerRecordDetails(this)">
    <option value="">{{ trans("messages.select") }}</option>
    @foreach ($fromPortInfo as $item)
        @php
            $encodeId = Wild_tiger::encode($item->i_id);
            $selected = '';

            if (isset($recordInfo->i_transport_from_id) &&
                $recordInfo->i_transport_from_id == $item->i_id) {
                $selected = "selected='selected'";
            }
        @endphp

        <option value="{{ $encodeId }}" {{ $selected }}>
            {{ $item->v_warehouse_name }}
        </option>
    @endforeach
</select>
							</div>
						</div>

						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="broker_custom_agent">{{ trans("messages.broker-custom-agent") }}</label>
								<input type="text" name="broker_custom_agent" <?php echo $disableForm ?> class="form-control" placeholder="{{ trans('messages.broker-custom-agent') }}" value="{{old('broker_custom_agent',  ( (isset($recordInfo) && (!empty($recordInfo->v_brocker))) ?  $recordInfo->v_brocker : '' ) )}}">
							</div>
						</div>

						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
						<label class="control-label" for="logistic_partner">Logistic Partner<span class="text-danger">*</span></label>

<select name="logistic_partner" class="form-control select2" {{ $disableForm }} required>
    <option value="">Select</option>

    @foreach ($logisticPartnerDetails as $logisticPartnerDetail)
        @php
            $encoded = Wild_tiger::encode($logisticPartnerDetail->i_id);
            $selected = '';

            if (isset($recordInfo->i_logistic_partner_detail_id)) {
                if ($recordInfo->i_logistic_partner_detail_id == $logisticPartnerDetail->i_id) {
                    $selected = 'selected="selected"';
                }
            }
        @endphp

        <option value="{{ $encoded }}" {!! $selected !!}>
            {{ $logisticPartnerDetail->logisticPartnerMaster->v_logistic_partner_name ?? '' }}
        </option>
    @endforeach
</select>



						</div>
					</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="warehouse_type">{{ trans("messages.warehouse-type") }}<span class="text-danger">*</span></label>
								<select name="warehouse_type" class="form-control" <?php echo $disableForm ?> <?php echo ( ( isset($recordInfo) && ( $recordInfo->e_process_status == config('constants.COMPLETED_STATUS') || $recordInfo->e_process_status == config('constants.PARTIAL_DELIVERY_TYPE') ) ) ? 'disabled' : '' ) ?> onclick="warehouseTypeWiseLocation(this)">
									<option value="">{{ trans("messages.select") }}</option>									
									@if (!empty($usaGoodOutWarehouseTypeDetails))
										@foreach ($usaGoodOutWarehouseTypeDetails as $usaGoodOutWarehouseTypeKey => $usaGoodOutWarehouseTypeValue)
											@php
												$selected = '';
												if( old('warehouse_type', (isset($recordInfo) && !empty($recordInfo) && isset($recordInfo->e_warehose_type) && !empty($recordInfo->e_warehose_type) ? $recordInfo->e_warehose_type : '')  ) == $usaGoodOutWarehouseTypeKey ) {
													$selected = "selected='selected'";
												}
											@endphp
											<option value="{{ $usaGoodOutWarehouseTypeKey }}" {{ $selected }}>{{ (!empty($usaGoodOutWarehouseTypeValue) ? $usaGoodOutWarehouseTypeValue : '' ) }}</option>
										@endforeach
									@endif
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6 own-warehouse-location" style="{{ (old('warehouse_type', (isset($recordInfo) && !empty($recordInfo) && isset($recordInfo->e_warehose_type) && !empty($recordInfo->e_warehose_type) ? $recordInfo->e_warehose_type : '')  ) == config('constants.OWN_WAREHOUSE_TYPE') ? '' : 'display:none'  ) }}">
							<div class="form-group">
								<label class="control-label" for="to_own_location">{{ trans("messages.to-own-location") }}<span class="text-danger">*</span></label>
								<select name="to_own_location" class="form-control select2" <?php echo $disableForm ?> <?php echo ( ( isset($recordInfo) && ( $recordInfo->e_process_status == config('constants.COMPLETED_STATUS') || $recordInfo->e_process_status == config('constants.PARTIAL_DELIVERY_TYPE') ) ) ? 'disabled' : '' ) ?>>
									<option value="">{{ trans("messages.select") }}</option>									
									@if (!empty($ownFromWarehouseDetails))
										@foreach ($ownFromWarehouseDetails as $ownFromWarehouseDetail)
											@php
												$encodeFromPortId  = Wild_tiger::encode($ownFromWarehouseDetail->i_id);
												$warehouseName = $ownFromWarehouseDetail->v_warehouse_name;
												$selected = '';
												if (isset($recordInfo->i_own_warehouse_location_id) && ($recordInfo->i_own_warehouse_location_id == $ownFromWarehouseDetail->i_id)) {
													$selected = "selected='selected'";
												}
											@endphp
											<option value="{{ $encodeFromPortId }}" {{ $selected }}>{{ $warehouseName }}</option>
										@endforeach
									@endif
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6 agent-warehouse-location" style="{{ (old('warehouse_type', (isset($recordInfo) && !empty($recordInfo) && isset($recordInfo->e_warehose_type) && !empty($recordInfo->e_warehose_type) ? $recordInfo->e_warehose_type : '')  ) == config('constants.AGENT_WAREHOUSE_TYPE') ? '' : 'display:none'  ) }}">
							<div class="form-group">
								<label class="control-label" for="to_agent_location">{{ trans("messages.to-agent-location") }}<span class="text-danger">*</span></label>
								<select name="to_agent_location" class="form-control select2" <?php echo $disableForm ?> <?php echo ( ( isset($recordInfo) && ( $recordInfo->e_process_status == config('constants.COMPLETED_STATUS') || $recordInfo->e_process_status == config('constants.PARTIAL_DELIVERY_TYPE') ) ) ? 'disabled' : '' ) ?>>
									<option value="">{{ trans("messages.select") }}</option>
									<?php
									if (!empty($logisticPartnerDetails)) {
										foreach ($logisticPartnerDetails as $logisticPartnerDetail) {
											$encodeId  = Wild_tiger::encode($logisticPartnerDetail->i_id);
											$selected = '';
											if (old('to_agent_location', (isset($recordInfo->i_agent_location_id) ? Wild_tiger::encode($recordInfo->i_agent_location_id) : '')) == $encodeId) {
												$selected = "selected='selected'";
											}
									?>
											<option value="{{ $encodeId }}" {{ $selected }}>{{ (!empty($logisticPartnerDetail->logisticPartnerMaster->v_logistic_partner_name) ? $logisticPartnerDetail->logisticPartnerMaster->v_logistic_partner_name : '' ).(!empty($logisticPartnerDetail->v_logistic_partner_code) ? ' - '.$logisticPartnerDetail->v_logistic_partner_code : '' ) }}</option>
									<?php
										}
									}
									?>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-sm-6">
							<div class="form-group">
								<label for="select_containers" class="control-label">{{ trans("messages.select-containers") }}<span class="text-danger">*</span></label>
								<select name="select_containers" class="form-control select2 container-list" onchange="containerWiseFromWarehouseCountryAndWarehouseDetails(this)">
    <option value="">{{ trans("messages.select") }}</option>
    @if (!empty($getCountryToPortGoodsOutDetails))
        @foreach ($getCountryToPortGoodsOutDetails as $item)
            @php
                $encodeId = Wild_tiger::encode($item->i_id);
                $selected = '';
                $containerIds = [];
                if (isset($recordInfo) && !empty($recordInfo->v_container_ids)) {
                    $containerIds = explode(',', $recordInfo->v_container_ids);
                }
                if (!empty($containerIds) && in_array($item->i_id, $containerIds)) {
                    $selected = "selected='selected'";
                }
            @endphp
            <option value="{{ $encodeId }}" {{ $selected }}>
                {{ $item->v_country_to_port_record_no }}
            </option>
        @endforeach
    @endif
</select>

							</div>
						</div>

						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="book_by">{{ trans("messages.book-by") }}<span class="text-danger">*</span></label>
								<select name="book_by" class="form-control select2">
    <option value="">{{ trans("messages.select") }}</option>
    @foreach ($userRecordDetails as $user)
        @php
            $encodeId = Wild_tiger::encode($user->i_id);
            $selected = '';

            if (isset($recordInfo->i_book_by_employee_id) &&
                $recordInfo->i_book_by_employee_id == $user->i_id) {
                $selected = "selected='selected'";
            }
        @endphp

        <option value="{{ $encodeId }}" {{ $selected }}>
            {{ $user->v_name }} {{ $user->v_department ? '(' . $user->v_department . ')' : '' }}
        </option>
    @endforeach
</select>

							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="container_discharged_date">{{ trans("messages.container-discharged-date") }}<span class="text-danger">*</span></label>
								<input type="text" name="container_discharged_date" <?php echo $disableForm ?> class="form-control date-format" placeholder="{{ trans('messages.container-discharged-date') }}" value="{{old('container_discharged_date',  ( (isset($recordInfo) && (!empty($recordInfo->dt_contanier_discharge_date))) ?  clientDate($recordInfo->dt_contanier_discharge_date) : '' ) )}}">
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="container_theft_missing">{{ trans("messages.container-theft-missing") }}</label>
								<input type="text" name="container_theft_missing" <?php echo $disableForm ?> class="form-control" placeholder="{{ trans('messages.container-theft-missing') }}" value="{{old('container_theft_missing',  ( (isset($recordInfo) && (!empty($recordInfo->v_container_theft_missing))) ?  $recordInfo->v_container_theft_missing : '' ) )}}">
							</div>
						</div>
						<!-- <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="container_discharged_date">{{ trans("messages.container-discharged-date") }}<span class="text-danger">*</span></label>
                                <input type="text" name="container_discharged_date" class="form-control" placeholder="{{ trans('messages.container-discharged-date') }}" value="{{old('container_discharged_date',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->dt_container_discharged_date))) ?  clientDate($recordInfo->goodInBuyerMaster->dt_container_discharged_date) : '' ) )}}">
                            </div>
                        </div> -->
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="booking_date">{{ trans("messages.booking-date") }}<span class="text-danger">*</span></label>
								<input type="text" name="booking_date" <?php echo $disableForm ?> class="form-control date-format" placeholder="{{ trans('messages.booking-date') }}" value="{{old('booking_date',  ( (isset($recordInfo) && (!empty($recordInfo->dt_booking_date))) ?  clientDate($recordInfo->dt_booking_date) : '' ) )}}">
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="ref_no">{{ trans("messages.ref-no") }}<span class="text-danger">*</span></label>
								<input type="text" name="ref_no" <?php echo $disableForm ?> class="form-control" placeholder="{{ trans('messages.ref-no') }}" value="{{old('ref_no',  ( (isset($recordInfo) && (!empty($recordInfo->v_ref_no))) ?  $recordInfo->v_ref_no : '' ) )}}">
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="no_of_pallets">{{ trans("messages.no-of-pallets") }}</label>
								<input type="text" name="no_of_pallets" <?php echo $disableForm ?> onkeyup="onlyNumber(this)" class="form-control" placeholder="{{ trans('messages.no-of-pallets') }}" value="{{old('no_of_pallets',  ( (isset($recordInfo) && (!empty($recordInfo->i_total_pallets))) ?  $recordInfo->i_total_pallets : '' ) )}}">
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="tracking_no">{{ trans("messages.tracking-no") }}<span class="text-danger">*</span></label>
								<input type="text" name="tracking_no" <?php echo $disableForm ?> class="form-control" placeholder="{{ trans('messages.tracking-no') }}" value="{{old('tracking_no',  ( (isset($recordInfo) && (!empty($recordInfo->v_tracking_no))) ?  $recordInfo->v_tracking_no : '' ) )}}">
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="tracking_link">{{ trans("messages.tracking-link") }}</label>
								<input type="text" name="tracking_link" <?php echo $disableForm ?> class="form-control" placeholder="{{ trans('messages.tracking-link') }}" value="{{old('tracking_link',  ( (isset($recordInfo) && (!empty($recordInfo->v_tracking_link))) ?  $recordInfo->v_tracking_link : '' ) )}}">
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="collection_date">{{ trans("messages.collection-date") }} </label>
								<input type="text" name="collection_date" <?php echo $disableForm ?> class="form-control date-format" placeholder="{{ trans('messages.collection-date') }}" value="{{old('collection_date',  ( (isset($recordInfo) && (!empty($recordInfo->dt_collection_date))) ?  clientDate($recordInfo->dt_collection_date) : '' ) )}}">
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="delivery_date">{{ trans("messages.delivery-date") }} </label>
								<input type="text" name="delivery_date" <?php echo $disableForm ?> class="form-control date-format" placeholder="{{ trans('messages.delivery-date') }}" value="{{old('delivery_date',  ( (isset($recordInfo) && (!empty($recordInfo->dt_delivery_date))) ?  clientDate($recordInfo->dt_delivery_date) : '' ) )}}">
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label for="from_warehouse_country" class="control-label">{{ trans("messages.from-warehouse-country") }}</label>
								<select name="from_warehouse_country" class="form-control from-warehouse-country-class" disabled>
									<option value="">{{ trans("messages.select") }}</option>
									@if (isset($countryDetails) && count($countryDetails) > 0)
										@foreach ($countryDetails as $countryDetail)
											@php 
												$selected = (isset($recordInfo) && !empty($recordInfo->i_from_warehouse_country_id) && $recordInfo->i_from_warehouse_country_id == $countryDetail->i_id ? 'selected' : ''); 
											@endphp
											<option value="{{ Wild_tiger::encode($countryDetail->i_id) }}" {{ $selected }}>{{ (!empty($countryDetail->v_country_name) ? $countryDetail->v_country_name : '') }}</option>
										@endforeach
									@endif
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label for="warehouse" class="control-label">{{ trans("messages.warehouse") }}</label>
								<select name="warehouse" class="form-control warehouse-select-class" disabled>
									<option value="">{{ trans("messages.select") }}</option>
									@if (isset($warehouseDetails) && count($warehouseDetails) > 0)
										@foreach ($warehouseDetails as $warehouseDetail)
											@php 
												$selected = (isset($recordInfo) && !empty($recordInfo->i_warehouse_id) && $recordInfo->i_warehouse_id == $warehouseDetail->i_id ? 'selected' : ''); 
											@endphp
											<option value="{{ Wild_tiger::encode($warehouseDetail->i_id) }}" {{ $selected }}>{{ (!empty($warehouseDetail->v_warehouse_name) ? $warehouseDetail->v_warehouse_name : '') }}</option>
										@endforeach
									@endif
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label for="personal_ref" class="control-label">{{ trans("messages.personal-ref") }}</label>
								<input type="text" name="personal_ref" readonly disabled class="form-control" placeholder="{{ trans('messages.personal-ref') }}" value="{{old('personal_ref',  ( (isset($recordInfo) && (!empty($recordInfo->v_personal_ref))) ?  $recordInfo->v_personal_ref : '' ) )}}">
							</div>
						</div>
					</div>
				</div>
				<div id="documents">
					<h4 class="title-goods"><i class="fa fa-file list-icon mr-2"></i> {{ trans("messages.documents") }}</h4>
					<div class="card-body pb-0">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group pb-3 pt-3">
									<div class="card shadow-none border">
										<div class="card-header">
											<span class="partner-tilte">
												{{ trans("messages.attach-documents") }}
											</span>
										</div>
										<div class="card-body logistic-partner">
											<div class="table-responsive">
												<table class="table table-hover table-bordered table-sm pb-4">
													<thead>
														<tr class="text-center">
															<th style="max-width:70px;min-width:70px;">{{ trans("messages.sr-no") }}</th>
															<th style="max-width:250px;min-width:200px;">{{ trans("messages.type") }} <span class="text-danger">*</span></th>
															<th style="max-width:250px;min-width:250px;">{{ trans("messages.documents") }} </th>
															<th style="max-width:250px;min-width:200px;">{{ trans("messages.remarks") }} </th>
															<th style="width:70px;min-width:70px;">{{ trans("messages.view") }}</th>
															<th style="width:70px;min-width:70px;">{{ trans("messages.action") }}</th>
														</tr>
													</thead>
													<tbody class="good-out-port-agent-tbody">
														<?php
														if (isset($recordInfo->documentInfo) && (!empty($recordInfo->documentInfo)) && (count($recordInfo->documentInfo) > 0 ) ) {
															foreach ($recordInfo->documentInfo as $countKey => $goodOutCountryPortDocumentDetail) {
																$columIndex  = ($countKey +  1);
																?>
																<?php $documentFiles = (json_decode($goodOutCountryPortDocumentDetail->v_document_file_path)); ?>
																<tr>
																	<td class="table-index text-center" style="width:70px;min-width:70px;">{{$columIndex}}</td>
																	<td class="text-left">
																		<select name="edit_type_<?php echo $goodOutCountryPortDocumentDetail->i_id ?>" class="form-control good-out-port-agent-type" <?php echo $documentForm ?>>
																			<option value="">{{ trans("messages.select") }}</option>
																			<?php
																			if (!empty($documentTypeRecordDetails)) {
																				foreach ($documentTypeRecordDetails as $documentTypeRecordDetail) {
																					$encodevDocumentTypeId  = Wild_tiger::encode($documentTypeRecordDetail->i_id);
																					$selected = '';
																					if (isset($goodOutCountryPortDocumentDetail->i_document_type_id) && ($goodOutCountryPortDocumentDetail->i_document_type_id == $documentTypeRecordDetail->i_id)) {
																						$selected = "selected='selected'";
																					}
																			?>
																					<option value="{{ $encodevDocumentTypeId }}" {{ $selected }}>{{ (!empty($documentTypeRecordDetail->v_document_type_name) ? $documentTypeRecordDetail->v_document_type_name : '' ) }}</option>
																			<?php
																				}
																			}
																			?>
																		</select>
																	</td>
																	<?php $documentFiles = (json_decode($goodOutCountryPortDocumentDetail->v_document_file_path)); ?>
																	<td class="text-left">
																		<div class="custom-file">
																			<input type="file" name="edit_file_<?php echo $goodOutCountryPortDocumentDetail->i_id ?>[]" class="custom-file-input good-out-port-agent-file" id="document_<?php echo $goodOutCountryPortDocumentDetail->i_id ?>" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')" <?php echo $documentForm ?>>
																			<label class="custom-file-label" for="document_<?php echo $goodOutCountryPortDocumentDetail->i_id ?>"><?php echo (!empty($documentFiles) ? ( count($documentFiles) > 1 ? trans('messages.multiple-files') : ( isset($documentFiles[0]) ? basename($documentFiles[0]) : '' ) )  : trans('messages.choose-file') ) ?></label>
																		</div>
																	</td>
																	<td class="text-left">
																		<input type="text" class="form-control" name="edit_remarks_<?php echo $goodOutCountryPortDocumentDetail->i_id ?>" value="<?php echo (isset($goodOutCountryPortDocumentDetail->v_document_remark) ? $goodOutCountryPortDocumentDetail->v_document_remark : ''); ?>" <?php echo $documentForm ?>>
																	</td>
																	
																	<td class="actions-col">
																		<?php
																		if (!empty($documentFiles)) {
																			foreach ($documentFiles as $documentFile) {
																				$imagePath = (config('constants.FILE_STORAGE_URL_PATH') . $documentFile);
																		?>
																				<div class="download-link-items">
																					<a title="{{trans('messages.remove')}}" href="javascript:void(0);" data-file-name="{{ basename($imagePath) }}" onclick="removeUploadedFile(this);" data-record-id="{{ $goodOutCountryPortDocumentDetail->i_id }}" data-field-name="document" class="close-icon"><i class="fa fa-times "></i></a>
																					<a title="{{trans('messages.download-button')}}" href="{{ $imagePath }}" target="_blank" class="btn btn-sm btn-danger mb-1 download-icon-items"><i class="fa fa-download"></i></a>
																				</div>
																		<?php
																			}
																		}
																		?>
																	</td>

																	<td style="width:70px;min-width:70px;"><button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button></td>
																</tr>
															<?php
															}
														} else {
															?>
															<tr>
																<td class="table-index text-center" style="width:70px;min-width:70px;">1</td>
																<td class="text-left">
																	<select name="type_1" class="form-control good-out-port-agent-type" <?php echo $documentForm ?>>
																		<option value="">{{ trans("messages.select") }}</option>
																		<?php
																		if (!empty($documentTypeRecordDetails)) {
																			foreach ($documentTypeRecordDetails as $documentTypeRecordDetail) {
																				$encodevDocumentTypeId  = Wild_tiger::encode($documentTypeRecordDetail->i_id);
																		?>
																				<option value="{{ $encodevDocumentTypeId }}">{{ (!empty($documentTypeRecordDetail->v_document_type_name) ? $documentTypeRecordDetail->v_document_type_name : '' ) }}</option>
																		<?php
																			}
																		}
																		?>
																	</select>
																</td>
																<td class="text-left">
																	<div class="custom-file">
																		<input type="file" name="file_1[]" <?php echo $documentForm ?> class="custom-file-input good-out-port-agent-file" id="document_1" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
																		<label class="custom-file-label" for="document_1">{{ trans("messages.choose-file") }}</label>
																	</div>
																</td>
																<td class="text-left">
																	<input type="text" class="form-control" name="remarks_1" <?php echo $documentForm ?>>
																</td>
																<td class="actions-col">
																</td>

																<td style="width:70px;min-width:70px;"></td>
															</tr>
															<tr>
																<td class="table-index text-center" style="width:70px;min-width:70px;">2</td>
																<td class="text-left">
																	<select name="type_2" class="form-control good-out-port-agent-type" <?php echo $documentForm ?>>
																		<option value="">{{ trans("messages.select") }}</option>
																		<?php
																		if (!empty($documentTypeRecordDetails)) {
																			foreach ($documentTypeRecordDetails as $documentTypeRecordDetail) {
																				$encodevDocumentTypeId  = Wild_tiger::encode($documentTypeRecordDetail->i_id);
																		?>
																				<option value="{{ $encodevDocumentTypeId }}">{{ (!empty($documentTypeRecordDetail->v_document_type_name) ? $documentTypeRecordDetail->v_document_type_name : '' ) }}</option>
																		<?php
																			}
																		}
																		?>
																	</select>
																</td>
																<td class="text-left">
																	<div class="custom-file">
																		<input type="file" name="file_2[]" <?php echo $documentForm ?> class="custom-file-input good-out-port-agent-file" id="document_2" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
																		<label class="custom-file-label" for="document_2">{{ trans("messages.choose-file") }}</label>
																	</div>
																</td>
																<td class="text-left">
																	<input type="text" class="form-control" name="remarks_2" <?php echo $documentForm ?>>
																</td>
																<td class="actions-col">
																</td>

																<td style="width:70px;min-width:70px;"><button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button></td>
															</tr>
														<?php } ?>
													</tbody>
												</table>
												<?php if(empty($documentForm)) { ?>
													<button type="button" class="btn btn-sm bg-theme text-white add-new-row" title="{{ trans('messages.add-new') }}" onclick="addNewRow(this)"><i class="fa fa-plus fa-fw"></i>{{ trans("messages.add-new") }}</button>
												<?php } ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="transporter-invoice">
					<h4 class="title-goods"><i class="fa fa-file-invoice mr-2"></i> {{ trans("messages.transporter-invoice") }}</h4>
					<div class="card-body pb-0">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group pb-3 pt-3">
									<div class="card shadow-none border">
										<div class="card-body logistic-partner">
											<div class="table-responsive">
												<table class="table table-hover table-bordered table-sm pb-4">
													<thead>
														<tr class="text-center">
															<th style="width:70px;min-width:70px;">{{ trans("messages.sr-no") }}</th>
															<th class="text-left" style="width:240px;min-width:240px;">{{ trans("messages.name") }} <span class="text-danger">*</span></th>
															<th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.inv-no") }} <span class="text-danger">*</span></th>
															<th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.freight") }}</th>
															<th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.custom") }}</th>
															<th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.duty") }}</th>
															<th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.other") }}</th>
															<th class="text-left" style="width:120px;min-width:120px;">{{ trans("messages.vat") }}</th>
															<th class="text-left" style="width:180px;min-width:180px;">{{ trans("messages.total") }}</th>
															<th class="text-left" style="width:110px;min-width:110px;">{{ trans("messages.cov-rate") }}</th>
															<th class="text-left" style="width:100px;min-width:100px;">{{ trans("messages.total-logistic-cost-usd") }}</th>
															<th class="text-left" style="width:250px;min-width:250px;">{{ trans("messages.attach-documents") }}</th>
															<th class="text-center" style="width:80px;min-width:80px;">{{ trans("messages.documents") }}</th>
														</tr>
													</thead>
													<tbody class="good-out-port-agent-transporter-tbody">
														<?php
														if (isset($recordInfo->invoiceInfo) && (!empty($recordInfo->invoiceInfo)) && (count($recordInfo->invoiceInfo) > 0 ) ) {
															foreach ($recordInfo->invoiceInfo as $countKey => $goodOutCountryPortTransportDetail) {
																$countIndex = ($countKey + 1);
														?>
														<?php $invoiceFiles = (json_decode($goodOutCountryPortTransportDetail->v_invoice_file_path)); ?>
																<tr>
																	<td class="table-index">{{ $countIndex }}</td>
																	<td class="text-left">
																		<select name="edit_name_<?php echo $goodOutCountryPortTransportDetail->i_id ?>" class="form-control good-out-port-agent-transporter-name select2" <?php echo $documentForm ?>>
	                                                                    	<option value="">{{ trans("messages.select") }}</option>
	                                                                        <?php 
	                                                                        if(!empty($logisticPartnerRecordDetails)){
	                                                                        	foreach ($logisticPartnerRecordDetails as $logisticPartnerRecordDetail){
	                                                                        		$encodeId  = Wild_tiger::encode($logisticPartnerRecordDetail->i_id);
	                                                                        		$selected = '';
	                                                                        		if( isset($goodOutCountryPortTransportDetail->i_logistic_partner_master_id) && ( $goodOutCountryPortTransportDetail->i_logistic_partner_master_id == $logisticPartnerRecordDetail->i_id ) ){
	                                                                        			$selected = "selected='selected'";
	                                                                        		}
	                                                                        		?>
	                                                                        		<option value="{{ $encodeId }}" {{$selected}}>{{ (!empty($logisticPartnerRecordDetail->v_logistic_partner_name) ? $logisticPartnerRecordDetail->v_logistic_partner_name : '' ) }}</option>
	                                                                        		<?php 
	                                                                        	}
	                                                                        }	
	                                                                        ?>
	                                                                    </select>
																	</td>
																	<td class="text-left">
																		<input type="text" <?php echo $documentForm ?> class="form-control good-out-port-agent-transporter-inv-no" name="edit_inv_no_<?php echo $goodOutCountryPortTransportDetail->i_id ?>" placeholder="{{ trans('messages.inv-no') }}" value="<?php echo (isset($goodOutCountryPortTransportDetail->v_invoice_no) ? $goodOutCountryPortTransportDetail->v_invoice_no : ''); ?>">
																	</td>
																	<td class="text-left">
																		<input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-freight" name="edit_freight_<?php echo $goodOutCountryPortTransportDetail->i_id ?>" placeholder="{{ trans('messages.freight') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($goodOutCountryPortTransportDetail->d_freight_charge) ? $goodOutCountryPortTransportDetail->d_freight_charge : ''); ?>">
																	</td>
																	<td class="text-left">
																		<input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-custom" name="edit_custom_<?php echo $goodOutCountryPortTransportDetail->i_id ?>" placeholder="{{ trans('messages.custom') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($goodOutCountryPortTransportDetail->d_custom_charge) ? $goodOutCountryPortTransportDetail->d_custom_charge : ''); ?>">
																	</td>
																	<td class="text-left">
																		<input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-duty" name="edit_duty_<?php echo $goodOutCountryPortTransportDetail->i_id ?>" placeholder="{{ trans('messages.duty') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($goodOutCountryPortTransportDetail->d_duty_charge) ? $goodOutCountryPortTransportDetail->d_duty_charge : ''); ?>">
																	</td>
																	<td class="text-left">
																		<input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-other" name="edit_other_<?php echo $goodOutCountryPortTransportDetail->i_id ?>" placeholder="{{ trans('messages.other') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($goodOutCountryPortTransportDetail->d_other_charge) ? $goodOutCountryPortTransportDetail->d_other_charge : ''); ?>">
																	</td>
																	<td class="text-left">
																		<input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-vat" name="edit_vat_<?php echo $goodOutCountryPortTransportDetail->i_id ?>" placeholder="{{ trans('messages.vat') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($goodOutCountryPortTransportDetail->d_vat_charge) ? $goodOutCountryPortTransportDetail->d_vat_charge : ''); ?>">
																	</td>
																	<td class="text-left">
																		<div class="input-group align-items-center flex-nowrap">
																			<label class="mb-0" for=""><span class="agent-warehouse-total-value"><?php echo (isset($goodOutCountryPortTransportDetail->d_total_charge) ? $goodOutCountryPortTransportDetail->d_total_charge : '') ?></span></label>
																			<div class="input-group-prepend">
																				<select <?php echo $documentForm ?> class="form-control ml-2" name="edit_amount_<?php echo $goodOutCountryPortTransportDetail->i_id ?>">
																					<option  value="">{{trans('messages.currency')}}</option>
																					<?php
																					if (!empty($currencyRecordDetails)) {
																						foreach ($currencyRecordDetails as $currencyRecordDetail) {
																							$encodeCurrencyrId  = Wild_tiger::encode($currencyRecordDetail->i_id);
																							$selected = '';
																							if (isset($goodOutCountryPortTransportDetail->i_invoice_currency_id) && ($goodOutCountryPortTransportDetail->i_invoice_currency_id == $currencyRecordDetail->i_id)) {
																								$selected = "selected='selected'";
																							}
																					?>
																							<option value="{{ $encodeCurrencyrId }}" {{ $selected }}>{{ (!empty($currencyRecordDetail->v_currency_code) ? $currencyRecordDetail->v_currency_code : '' ) }}</option>
																					<?php
																						}
																					}
																					?>
																				</select>
																			</div>

																		</div>
																	</td>
																	<?php $invoiceFiles = (json_decode($goodOutCountryPortTransportDetail->v_invoice_file_path)); ?>
																	<td class="text-left">
																		<input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-con-rate" name="edit_cov_rate_<?php echo $goodOutCountryPortTransportDetail->i_id ?>" placeholder="{{ trans('messages.cov-rate') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($goodOutCountryPortTransportDetail->d_conversion_rate) ? $goodOutCountryPortTransportDetail->d_conversion_rate : ''); ?>">
																	</td>
																	<td class="text-left"><span class="agent-warehouse-final-rate"><?php echo (isset($goodOutCountryPortTransportDetail->d_final_charge) ? $goodOutCountryPortTransportDetail->d_final_charge : ''); ?></span></td>
																	<td class="text-left">
																		<div class="custom-file">
																			<input type="file" <?php echo $documentForm ?> class="custom-file-input" id="invoice_document_<?php echo $goodOutCountryPortTransportDetail->i_id ?>" multiple name="edit_invoice_file_<?php echo $goodOutCountryPortTransportDetail->i_id ?>[]" onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
																			<label class="custom-file-label" for="invoice_document_<?php echo $goodOutCountryPortTransportDetail->i_id ?>"><?php echo (!empty($invoiceFiles) ? ( count($invoiceFiles) > 1 ? trans('messages.multiple-files') : ( isset($invoiceFiles[0]) ? basename($invoiceFiles[0]) : '' ) )  : trans('messages.choose-file') ) ?></label>
																		</div>
																	</td>
																	

																	<td class="actions-col">
																		<?php
																		if (!empty($invoiceFiles)) {
																			foreach ($invoiceFiles as $invoiceFile) {
																				$invoicePath = (config('constants.FILE_STORAGE_URL_PATH') . $invoiceFile);
																		?>
																				<div class="download-link-items">
																					<a title="{{trans('messages.remove')}}" href="javascript:void(0);" data-file-name="{{ basename($invoicePath) }}" onclick="removeUploadedFile(this);" data-record-id="{{ $goodOutCountryPortTransportDetail->i_id }}" data-field-name="invoice" class="close-icon"><i class="fa fa-times "></i></a>
																					<a title="{{trans('messages.download-button')}}" href="{{ $invoicePath }}" target="_blank" class="btn btn-sm btn-danger mb-1 download-icon-items"><i class="fa fa-download"></i></a>
																				</div>
																		<?php
																			}
																		}
																		?>
																	</td>
																</tr>
															<?php
															}
														} else {
															?>
															<tr>
																<td class="table-index">1</td>
																<td class="text-left">
																	<select name="name_1" class="form-control good-out-port-agent-transporter-name select2" <?php echo $documentForm ?>>
                                                                    	<option value="">{{ trans("messages.select") }}</option>
                                                                        <?php 
                                                                        if(!empty($logisticPartnerRecordDetails)){
                                                                        	foreach ($logisticPartnerRecordDetails as $logisticPartnerRecordDetail){
                                                                        		$encodeId  = Wild_tiger::encode($logisticPartnerRecordDetail->i_id);
                                                                        		?>
                                                                        		<option value="{{ $encodeId }}">{{ (!empty($logisticPartnerRecordDetail->v_logistic_partner_name) ? $logisticPartnerRecordDetail->v_logistic_partner_name : '' ) }}</option>
                                                                        		<?php 
                                                                        	}
                                                                        }	
                                                                        ?>
                                                                    </select>
																</td>
																<td class="text-left">
																	<input type="text" <?php echo $documentForm ?> class="form-control good-out-port-agent-transporter-inv-no" name="inv_no_1" placeholder="{{ trans('messages.inv-no') }}">
																</td>
																<td class="text-left">
																	<input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-freight" name="freight_1" placeholder="{{ trans('messages.freight') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
																</td>
																<td class="text-left">
																	<input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-custom" name="custom_1" placeholder="{{ trans('messages.custom') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
																</td>
																<td class="text-left">
																	<input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-duty" name="duty_1" placeholder="{{ trans('messages.duty') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
																</td>
																<td class="text-left">
																	<input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-other" name="other_1" placeholder="{{ trans('messages.other') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
																</td>
																<td class="text-left">
																	<input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-vat" name="vat_1" placeholder="{{ trans('messages.vat') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
																</td>
																<td class="text-left">
																	<div class="input-group align-items-center flex-nowrap">
																		<label class="mb-0" for=""><span class="agent-warehouse-total-value"></span></label>
																		<div class="input-group-prepend">
																			<select class="form-control ml-2" name="amount_1" <?php echo $documentForm ?>>
																				<option  value="">{{trans('messages.currency')}}</option>
																				<?php
																				if (!empty($currencyRecordDetails)) {
																					foreach ($currencyRecordDetails as $currencyRecordDetail) {
																						$encodeCurrencyrId  = Wild_tiger::encode($currencyRecordDetail->i_id);

																				?>
																						<option value="{{ $encodeCurrencyrId }}">{{ (!empty($currencyRecordDetail->v_currency_code) ? $currencyRecordDetail->v_currency_code : '' ) }}</option>
																				<?php
																					}
																				}
																				?>
																			</select>
																		</div>

																	</div>
																</td>

																<td class="text-left">
																	<input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-con-rate" name="cov_rate_1" placeholder="{{ trans('messages.cov-rate') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
																</td>
																<td class="text-left"><span class="agent-warehouse-final-rate"></span></td>
																<td class="text-left">
																	<div class="custom-file">
																		<input type="file" <?php echo $documentForm ?> class="custom-file-input" id="invoice_document_1" multiple name="invoice_file_1[]" onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
																		<label class="custom-file-label" for="invoice_document_1">{{ trans("messages.choose-file") }}</label>
																	</div>
																</td>
																<td class="actions-col">
																</td>
															</tr>

															<tr>
																<td class="table-index">2</td>
																<td class="text-left">
																	<select name="name_2" class="form-control good-out-port-agent-transporter-name select2" <?php echo $documentForm ?> >
                                                                    	<option value="">{{ trans("messages.select") }}</option>
                                                                        <?php 
                                                                        if(!empty($logisticPartnerRecordDetails)){
                                                                        	foreach ($logisticPartnerRecordDetails as $logisticPartnerRecordDetail){
                                                                        		$encodeId  = Wild_tiger::encode($logisticPartnerRecordDetail->i_id);
                                                                        		?>
                                                                        		<option value="{{ $encodeId }}">{{ (!empty($logisticPartnerRecordDetail->v_logistic_partner_name) ? $logisticPartnerRecordDetail->v_logistic_partner_name : '' ) }}</option>
                                                                        		<?php 
                                                                        	}
                                                                        }	
                                                                        ?>
                                                                    </select>
																</td>
																<td class="text-left">
																	<input type="text" <?php echo $documentForm ?> class="form-control good-out-port-agent-transporter-inv-no" name="inv_no_2" placeholder="{{ trans('messages.inv-no') }}">
																</td>
																<td class="text-left">
																	<input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-freight" name="freight_2" placeholder="{{ trans('messages.freight') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
																</td>
																<td class="text-left">
																	<input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-custom" name="custom_2" placeholder="{{ trans('messages.custom') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
																</td>
																<td class="text-left">
																	<input type="text" <?php echo $documentForm ?>  class="form-control agent-to-warehouse-duty" name="duty_2" placeholder="{{ trans('messages.duty') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
																</td>
																<td class="text-left">
																	<input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-other" name="other_2" placeholder="{{ trans('messages.other') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
																</td>
																<td class="text-left">
																	<input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-vat" name="vat_2" placeholder="{{ trans('messages.vat') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
																</td>
																<td class="text-left">
																	<div class="input-group align-items-center flex-nowrap">
																		<label class="mb-0" for=""><span class="agent-warehouse-total-value"></span></label>
																		<div class="input-group-prepend">
																			<select class="form-control ml-2" name="amount_2" <?php echo $documentForm ?>>
																				<option  value="">{{trans('messages.currency')}}</option>
																				<?php
																				if (!empty($currencyRecordDetails)) {
																					foreach ($currencyRecordDetails as $currencyRecordDetail) {
																						$encodeCurrencyrId  = Wild_tiger::encode($currencyRecordDetail->i_id);

																				?>
																						<option value="{{ $encodeCurrencyrId }}">{{ (!empty($currencyRecordDetail->v_currency_code) ? $currencyRecordDetail->v_currency_code : '' ) }}</option>
																				<?php
																					}
																				}
																				?>
																			</select>
																		</div>

																	</div>
																</td>
																<td class="text-left">
																	<input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-con-rate" name="cov_rate_2" placeholder="{{ trans('messages.cov-rate') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
																</td>
																<td class="text-left"><span class="agent-warehouse-final-rate"></span></td>
																<td class="text-left">
																	<div class="custom-file">
																		<input type="file" <?php echo $documentForm ?> class="custom-file-input" id="invoice_document_2" multiple name="invoice_file_2[]" onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
																		<label class="custom-file-label" for="invoice_document_2">{{ trans("messages.choose-file") }}</label>
																	</div>
																</td>
																<td class="actions-col">
																</td>
															</tr>
														<?php } ?>
													</tbody>
												</table>
												<?php if(empty($documentForm)){?>
													<button type="button" class="btn btn-sm bg-theme text-white add-new-row mb-3" title="{{ trans('messages.add-new') }}" onclick="addNewTransporterInvoiceRow(this)"><i class="fa fa-plus fa-fw"></i>{{ trans("messages.add-new") }}</button>
												<?php }?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="status">
					<h4 class="title-goods"><i class="fab fa-stack-overflow mr-2"></i> {{ trans("messages.status") }}</h4>
					<div class="card-body">
						<div class="row">
							<div class="col-xl-3 col-lg-4 col-sm-6">
								<div class="form-group">
									<label class="control-label" for="status">{{ trans("messages.status") }}<span class="text-danger">*</span></label>
									<?php ## view time a disbled aavu joi and role admin hoi to disbaled na avu joi ana mate ni conditon .?>				
									<select name="status" class="form-control" {{ $statusDisableForm }} >
										<option value="">{{ trans("messages.select") }}</option>
										<?php
										if (!empty($statusMasterRecordDetails)) {
											foreach ($statusMasterRecordDetails as $statusMasterRecordDetail) {
												$encoderId  = Wild_tiger::encode($statusMasterRecordDetail->i_id);
												$selected = '';
												if (isset($recordInfo->i_status_id) && ($recordInfo->i_status_id == $statusMasterRecordDetail->i_id)) {
													$selected = "selected='selected'";
												}
										?>
												<option value="{{ $encoderId }}" {{ $selected }} data-status-id="{{ $statusMasterRecordDetail->i_id }}">{{ (!empty($statusMasterRecordDetail->v_status) ? $statusMasterRecordDetail->v_status : '' ) }}</option>
										<?php
											}
										}
										?>
									</select>
								</div>
							</div>

							<div class="col-lg-4 col-sm-6">
								<div class="form-group">
									<label for="status_comments" class="control-label">{{ trans("messages.status-comments") }}</label>
									<input type="text" name="status_comments" <?php echo $documentForm ?> class="form-control" placeholder="{{ trans('messages.status-comments') }}" value="{{ old('status_comments',  ( (isset($recordInfo) && (!empty($recordInfo->v_status_comment))) ?  $recordInfo->v_status_comment : '' ) )}}">
								</div>
							</div>

						</div>
					</div>
				</div>
				<div class="col-md-12 submit-sticky">
				<?php //if(empty($documentForm)) { ?> 
						<?php if (isset($recordInfo) && ($recordInfo->i_id > 0)) { ?>
							<input type="hidden" name="record_id" value="{{ Wild_tiger::encode($recordInfo->i_id) }}">
							<?php ## view time a button na aavu joi and role admin hoi to button avu joi ana mate ni conditon .?>
							<?php if( empty($statusDisableForm) ){?>
								<button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.update') }}">{{ trans("messages.update") }}</button>
							<?php } ?>
	
						<?php } else { ?>
							<button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.submit') }}">{{ trans("messages.submit") }}</button>
						<?php } ?>
					<?php //} ?>
					<a href="{{ url('port-to-agent-warehouse') }}" class="btn btn-outline-secondary shadow-sm btn-wide" title="{{ trans('messages.cancel') }}">{{ trans("messages.cancel") }}</a>
				</div>
				<input type="hidden" name="good_out_port_agent_document_type_count" value="">
				<input type="hidden" name="good_out_port_agent_transporter_count" value="">
				{!! Form::close() !!}
			</div>

	</section>
</main>
<script>
	$("#add-us-port-agent-warehouse-form").validate({
		errorClass: "invalid-input",
		rules: {
			way_of_transport: {
				required: true,
                noSpace:true
			},
			from_port_airport: {
				required: true,
                noSpace:true
			},
			book_by: {
				required: true,
                noSpace:true
			},
			logistic_partner: {
                required: true,
                noSpace:true
            },
			to_agent_location: {
				required: function(element){
	   		    	return ( ($.trim($("[name='warehouse_type']").val() == '{{ config("constants.AGENT_WAREHOUSE_TYPE") }}')) ? true : false ) 
	   			},
                noSpace:true
			},
			select_containers: {
				required: true,
                noSpace:true
			},
			container_discharged_date: {
				required: true,
                noSpace:true
			},
			ref_no: {
				required: true,
                noSpace:true
			},

			tracking_no: {
				required: true,
                noSpace:true
			},

			booking_date: {
				required: true,
                noSpace:true
			},
			status: {
				required: true,
                noSpace:true
			},
			collection_date: {
	   		    required: function(element){
	   		    	return ( ($.trim($("[name='status']").find('option:selected').attr('data-status-id')) == '{{ config("constants.DELIVERED_STATUS_ID") }}') || ($.trim($("[name='status']").find('option:selected').attr('data-status-id')) == '{{ config("constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID") }}') ? true : false ) 
	   			},
	   		    noSpace: true
   	   	   },
   	   		delivery_date: {
	   		    required: function(element){
	   		    	return ( ($.trim($("[name='status']").find('option:selected').attr('data-status-id')) == '{{ config("constants.DELIVERED_STATUS_ID") }}') || ($.trim($("[name='status']").find('option:selected').attr('data-status-id')) == '{{ config("constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID") }}') ? true : false ) 
	   			},
	   		    noSpace: true
	   	   },
	   		warehouse_type:{required: true, noSpace: true},
	   		to_own_location: {
	   		    required: function(element){
	   		    	return ( ($.trim($("[name='warehouse_type']").val() == '{{ config("constants.OWN_WAREHOUSE_TYPE") }}')) ? true : false ) 
	   			},
	   		    noSpace: true
	   	   },
		},
		messages: {
			way_of_transport: {
				required: "{{ trans('messages.require-way-of-transport') }}"
			},
			from_port_airport: {
				required: "{{ trans('messages.require-from-port-airport') }}"
			},

			book_by: {
				required: "{{ trans('messages.require-book-by') }}"
			},
			logistic_partner: {
				required: "{{ trans('messages.require-logistic-partner') }}"
			},
			to_agent_location: {
				required: "{{ trans('messages.require-to-agent-location') }}"
			},
			select_containers: {
				required: "{{ trans('messages.require-select-containers') }}"
			},
			container_discharged_date: {
				required: "{{ trans('messages.require-container-discharged-date') }}"
			},
			ref_no: {
				required: "{{ trans('messages.require-ref-no') }}"
			},

			tracking_no: {
				required: "{{ trans('messages.require-tracking-no') }}"
			},
			booking_date: {
				required: "{{ trans('messages.require-booking-date') }}"
			},
			status: {
				required: "{{ trans('messages.require-status') }}"
			},
			 collection_date:  {
                required: "{{ trans('messages.require-collection-date') }}"
            },
            delivery_date:  {
                required: "{{ trans('messages.require-delivery-date') }}"
            },
            warehouse_type: { required: "{{ trans('messages.required-select-field-validation', ['fieldName' => trans('messages.warehouse-type') ] ) }}" },
            to_own_location: { required: "{{ trans('messages.required-select-field-validation', ['fieldName' => trans('messages.to-own-location') ] ) }}" }
		},
		submitHandler: function(form) {
			// Debug hook: set window.DEBUG_PORT_TO_AGENT = true in browser console to enable breakpoint and logging
			if (window.DEBUG_PORT_TO_AGENT) {
				try {
					console.groupCollapsed('DEBUG: port-to-agent-warehouse submit');
					console.log('Form serialized:', $($(form)).serialize());
					console.log('Key fields:', {
						way_of_transport: $("[name='way_of_transport']").val(),
						from_port_airport: $("[name='from_port_airport']").val(),
						select_containers: $("[name='select_containers']").val(),
						tracking_no: $("[name='tracking_no']").val(),
						record_id: $("[name='record_id']").val()
					});
					console.groupEnd();
					// pause execution for debugger if dev wants to inspect
					debugger;
				} catch (e) {
					// ignore
				}
			}
			var port_us_agent_type_status = false;
       	 	var port_us_agent_file_status = false;
       		$('.good-out-port-agent-tbody tr').each(function(){
       			var goods_document_type = $.trim($(this).find('.good-out-port-agent-type').val());
        		var goods_document_file_valid = $.trim($(this).find('.good-out-port-agent-file').attr('data-valid-file'));

				if(goods_document_file_valid != "" && goods_document_file_valid != null && goods_document_file_valid == "{{ strtolower(config('constants.SELECTION_YES'))}}"){
					
        			if( ( goods_document_type == "" || goods_document_type == null ) && (port_us_agent_type_status != true) ){
						$.trim($(this).find('.good-out-port-agent-type').focus());
						port_us_agent_file_status = true;
                	}
        		}
       		});
       		
       		if( port_us_agent_file_status != false ){
        		alertifyMessage("error","{{ trans('messages.require-document-type') }} ");
           		return false;
            }

       		
           var good_out_port_agent_transporter_name_status = false;
			var good_out_port_agent_inv_no_status = false;
            $('.good-out-port-agent-transporter-tbody tr').each(function(){
            	var good_out_port_agent_transporter_name = $.trim($(this).find('.good-out-port-agent-transporter-name').val());
				var good_out_port_agent_transporter_inv_no = $.trim($(this).find('.good-out-port-agent-transporter-inv-no').val());

				if(good_out_port_agent_transporter_name != "" && good_out_port_agent_transporter_name != null){
					good_out_port_agent_transporter_name_status = true;
					if( ( good_out_port_agent_transporter_inv_no == "" || good_out_port_agent_transporter_inv_no == null ) && (good_out_port_agent_inv_no_status != true) ){
						$.trim($(this).find('.good-out-port-agent-transporter-inv-no').focus());
						good_out_port_agent_inv_no_status = true;
                	}
         		} 
         	});
           
            if( good_out_port_agent_inv_no_status != false ){
         		alertifyMessage("error","{{ trans('messages.require-inv-no') }} ");
            	return false;
            }

			var confirm_box = "";
			var confirm_box_msg = "";
			<?php if (isset($recordInfo) && ($recordInfo->i_id > 0)) { ?>
				confirm_box = "{{ trans('messages.update-good-out-port-to-agent') }}";
				confirm_box_msg = "{{ trans ( 'messages.confirm-good-out-port-to-agent-update-msg') }}";

			<?php } else { ?>
				confirm_box = "{{ trans('messages.add-good-out-port-to-agent') }}";
				confirm_box_msg = "{{ trans ( 'messages.confirm-good-out-port-to-agent-add-msg') }}";
			<?php } ?>

			alertify.confirm(confirm_box, confirm_box_msg, function() {
				$("[name='good_out_port_agent_document_type_count']").val(good_out_port_agent_document_type_count);
				$("[name='good_out_port_agent_transporter_count']").val(good_out_port_agent_transporter_count);
				$("[name='to_agent_location']").prop('disabled', false);
        		$("[name='from_port_airport']").prop('disabled', false);
        		$('input:disabled').prop('disabled', false);
 				$('select:disabled').prop('disabled', false);
				showLoader()
				form.submit();
			}, function() {});
		}
	});
</script>

<script>
	$(document).ready(function() {
		$("[name='container_discharged_date'],[name='booking_date'],[name='collection_date'],[name='delivery_date']").datetimepicker({
			useCurrent: false,
			viewMode: 'days',
			ignoreReadonly: true,
			widgetPositioning: {
				vertical: 'bottom'
			},
			format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

		});

		$("[name='warehouse_type']").on('change', function(){
			warehouseTypeWiseLocation(this);
		});

		// initialize on load
		if ($("[name='warehouse_type']").length) {
			warehouseTypeWiseLocation($("[name='warehouse_type']")[0]);
		}

		// Ensure logistic partner is preselected even with Select2
		var lpSel = $("[name='logistic_partner']");
		if (lpSel.length) {
			var current = $.trim(lpSel.attr('data-selected'));
			if (current && !lpSel.find('option:selected').length) {
				lpSel.val(current);
				lpSel.trigger('change.select2');
			}
			// Fallback: select by parent master id for legacy rows
			if (!lpSel.find('option:selected').length) {
				var currentMaster = $.trim(lpSel.attr('data-current-master'));
				if (currentMaster) {
					// find an option whose data-parent-master matches decoded currentMaster (server passed encoded, but we can compare via encoded values on a temp map not available). As a simple heuristic, match by raw data attribute if any option matches integer part.
					var matched = lpSel.find('option').filter(function(){
						return $.trim($(this).attr('data-parent-master')) && $.trim($(this).attr('data-parent-master')) == '{{ isset($recordInfo->i_logistic_partner_id) ? $recordInfo->i_logistic_partner_id : '' }}';
					}).first();
					if (matched.length) {
						lpSel.val(matched.val());
						lpSel.trigger('change.select2');
					}
				}
			}
		}
	});

	var good_out_port_agent_document_type_count = 2;

	function addNewRow(thisitem) {
		good_out_port_agent_document_type_count++;
		var html = "";
		html += '<tr>';
		html += '<td class="table-index text-center" style="width:70px;min-width:70px;">' + good_out_port_agent_document_type_count + '</td>';
		html += '<td class="text-left">';
		html += '<select name="type_' + good_out_port_agent_document_type_count + '" class="form-control good-out-port-agent-type">';
		html += '<option value="">{{ trans("messages.select") }}</option>';
		<?php
		if (!empty($documentTypeRecordDetails)) {
			foreach ($documentTypeRecordDetails as $documentTypeRecordDetail) {
				$encodevDocumentTypeId  = Wild_tiger::encode($documentTypeRecordDetail->i_id);
		?>
				html += '<option value="{{ $encodevDocumentTypeId }}">{{ (!empty($documentTypeRecordDetail->v_document_type_name) ? $documentTypeRecordDetail->v_document_type_name : '
				' ) }}</option>';
		<?php
			}
		}
		?>
		html += '</select>';
		html += '</td>';
		html += '<td class="text-left">';
		html += '<div class="custom-file">';
		html += '<input type="file" class="custom-file-input good-out-port-agent-file" id="document_' + good_out_port_agent_document_type_count + '" multiple name="file_' + good_out_port_agent_document_type_count + '[]" onchange="validFile(this,\'pdf_doc_jpg_png_jpeg_xls\')">';
		html += '<label class="custom-file-label" for="document_' + good_out_port_agent_document_type_count + '">{{ trans("messages.choose-file") }}</label>';
		html += '</div>';
		html += '</td>';
		html += '<td class="text-left">';
		html += '<input type="text" class="form-control" name="remarks_' + good_out_port_agent_document_type_count + '">';
		html += '</td>';
		html += '<td class="actions-col">';
		html += '</td>';
		html += '<td style="width:70px;min-width:70px;"><button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button></td>';
		html += '</tr>';
		if ($('.good-out-port-agent-tbody').find('tr').length > 0) {
			$(html).insertAfter($('.good-out-port-agent-tbody').find('tr:last'));
		} else {
			$('.good-out-port-agent-tbody').html(html);
		}
		reindexTable('good-out-port-agent-tbody');
	}
	
	var port_to_agent_goods_out_url = '{{config("constants.PORT_TO_AGENT_GOODS_OUT_MASTER_URL")}}' + '/';


	var good_out_port_agent_transporter_count = 2;

	function warehouseTypeWiseLocation(thisitem){
		var warehouse_type_val = $.trim($(thisitem).val());
		var ownDiv = $('.own-warehouse-location');
		var agentDiv = $('.agent-warehouse-location');
		if(warehouse_type_val == '{{ config("constants.OWN_WAREHOUSE_TYPE") }}'){
			ownDiv.show();
			agentDiv.hide();
			ownDiv.find("[name='to_own_location']").prop('disabled', false);
			agentDiv.find("[name='to_agent_location']").prop('disabled', true).val('');
		} else if(warehouse_type_val == '{{ config("constants.AGENT_WAREHOUSE_TYPE") }}'){
			agentDiv.show();
			ownDiv.hide();
			agentDiv.find("[name='to_agent_location']").prop('disabled', false);
			ownDiv.find("[name='to_own_location']").prop('disabled', true).val('');
		} else {
			agentDiv.hide();
			ownDiv.hide();
			agentDiv.find("[name='to_agent_location']").prop('disabled', true).val('');
			ownDiv.find("[name='to_own_location']").prop('disabled', true).val('');
		}
	}

	function addNewTransporterInvoiceRow(thisitem) {
		good_out_port_agent_transporter_count++;
		var html = "";
		html += '<tr>';
		html += '<td class="table-index">'+good_out_port_agent_transporter_count+'</td>';
		html += '<td class="text-left">';
		html += '<select name="name_'+good_out_port_agent_transporter_count+'" class="form-control good-out-port-agent-transporter-name select2">';
		html += '<option value="">{{ trans("messages.select") }}</option>';
        <?php 
        if(!empty($logisticPartnerRecordDetails)){
        	foreach ($logisticPartnerRecordDetails as $logisticPartnerRecordDetail){
        		$encodeId  = Wild_tiger::encode($logisticPartnerRecordDetail->i_id);
        		?>
        		html += '<option value="{{ $encodeId }}">{{ (!empty($logisticPartnerRecordDetail->v_logistic_partner_name) ? $logisticPartnerRecordDetail->v_logistic_partner_name : '' ) }}</option>';
        		<?php 
        	}
        }	
        ?>
        html += '</select>';
		html += '</td>';
		html += '<td class="text-left">';
		html += '<input type="text" class="form-control good-out-port-agent-transporter-inv-no" name="inv_no_' + good_out_port_agent_transporter_count + '" placeholder="{{ trans('messages.inv-no') }}">';
		html += '</td>';
		html += '<td class="text-left">';
		html += '<input type="text" class="form-control agent-to-warehouse-freight" name="freight_' + good_out_port_agent_transporter_count + '" placeholder="{{ trans('messages.freight') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
		html += '</td>';
		html += '<td class="text-left">';
		html += '<input type="text" class="form-control agent-to-warehouse-custom" name="custom_' + good_out_port_agent_transporter_count + '" placeholder="{{ trans('messages.custom') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
		html += '</td>';
		html += '<td class="text-left">';
		html += '<input type="text" class="form-control agent-to-warehouse-duty" name="duty_' + good_out_port_agent_transporter_count + '" placeholder="{{ trans('messages.duty') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
		html += '</td>';
		html += '<td class="text-left">';
		html += '<input type="text" class="form-control agent-to-warehouse-other" name="other_' + good_out_port_agent_transporter_count + '" placeholder="{{ trans('messages.other') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
		html += '</td>';
		html += '<td class="text-left">';
		html += '<input type="text" class="form-control agent-to-warehouse-vat" name="vat_' + good_out_port_agent_transporter_count + '" placeholder="{{ trans('messages.vat') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
		html += '</td>';
		html += '<td class="text-left">';
		html += '<div class="input-group align-items-center flex-nowrap">';
		html += '<label class="mb-0" for=""><span class="agent-warehouse-total-value"></span></label>';
		html += '<div class="input-group-prepend">';
		html += '<select class="form-control ml-2" name="amount_' + good_out_port_agent_transporter_count + '">';
		html += '<option selected value="">Currency</option>';
		<?php
		if (!empty($currencyRecordDetails)) {
			foreach ($currencyRecordDetails as $currencyRecordDetail) {
				$encodeCurrencyrId  = Wild_tiger::encode($currencyRecordDetail->i_id);

		?>
				html += '<option value="{{ $encodeCurrencyrId }}">{{ (!empty($currencyRecordDetail->v_currency_code) ? $currencyRecordDetail->v_currency_code : '
				' ) }}</option>';
		<?php
			}
		}
		?>
		html += '</select>';
		html += '</div>';
		html += '</div>';
		html += '</td>';
		html += '<td class="text-left">';
		html += '<input type="text" class="form-control agent-to-warehouse-con-rate" name="cov_rate_' + good_out_port_agent_transporter_count + '" placeholder="{{ trans('messages.cov-rate') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
		html += '</td>';
		html += '<td class="text-left"><span class="agent-warehouse-final-rate"></span></td>';
		html += '<td class="text-left">';
		html += '<div class="custom-file">';
		html += '<input type="file" class="custom-file-input" id="invoice_document_' + good_out_port_agent_transporter_count + '" multiple name="invoice_file_' + good_out_port_agent_transporter_count + '[]" onchange="validFile(this,\'pdf_doc_jpg_png_jpeg_xls\')">';
		html += '<label class="custom-file-label" for="invoice_document_' + good_out_port_agent_transporter_count + '">{{ trans("messages.choose-file") }}</label>';
		html += '</div>';
		html += '</td>';
		html += '<td class="actions-col">';
		html += '</td>';
		html += '</tr>';

		if ($('.good-out-port-agent-transporter-tbody').find('tr').length > 0) {
			$(html).insertAfter($('.good-out-port-agent-transporter-tbody').find('tr:last'));
		} else {
			$('.good-out-port-agent-transporter-tbody').html(html);
		}
		reindexTable('good-out-port-agent-transporter-tbody');
		$(function(){
			$('.select2').select2();
		})
	}

	function getContainerRecordDetails(thisitem){
    // Only require from_port_airport for container loading
    var from_port_airport = $.trim($("[name='from_port_airport']").val());
    if(from_port_airport != "" && from_port_airport != null){
        var logistic_parner_detail_id = $.trim($("[name='logistic_partner']").val());
        var record_id = $.trim($("[name='record_id']").val());
        $.ajax({
            type: "POST",
            url: port_to_agent_goods_out_url + 'getContainerRecordDetails',
            data: {
                "_token": "{{ csrf_token() }}",
                'logistic_parner_detail_id': logistic_parner_detail_id,
                'from_port_airport': from_port_airport,
                'record_id': record_id,
            },
            beforeSend: function() {
                //block ui
                showLoader();
            },
            success: function(response) {
                hideLoader();
                $(thisitem).parents('.us-agent-waregouse-list').find('.container-list').html('');
                if(response !="" && response != null){
                    $(thisitem).parents('.us-agent-waregouse-list').find('.container-list').html(response);
                } 
            },
            error: function() {
                hideLoader();
            }
        });
    } else {
        // Clear container dropdown if no port is selected
        $(thisitem).parents('.us-agent-waregouse-list').find('.container-list').html('<option value="">{{ trans("messages.select") }}</option>');
    }
    
}

$("[name='from_port_airport']").on('change', function(){
    getContainerRecordDetails(this);
});

	function containerWiseFromWarehouseCountryAndWarehouseDetails(thisitem){
		var container_id = $.trim($(thisitem).val());

		$.ajax({
			type: "POST",
			url: port_to_agent_goods_out_url + 'container-wise-from-warehouse-country-and-warehouse',
			dataType: 'json',
			data: {
				"_token": "{{ csrf_token() }}",
				'container_id': container_id,
			},
			beforeSend: function() {
				showLoader();
			},
			success: function(response) {
				hideLoader();
				if(response.data.from_warehouse_country_option != "" && response.data.from_warehouse_country_option != null){
					$(thisitem).parents('.dependent-field-div').find('.from-warehouse-country-class').html(response.data.from_warehouse_country_option);
				}
				if(response.data.warehouse_option != "" && response.data.warehouse_option != null){
					$(thisitem).parents('.dependent-field-div').find('.warehouse-select-class').html(response.data.warehouse_option);
				}
				if(response.data.personal_ref_value != "" && response.data.personal_ref_value != null){
					$(thisitem).parents('.dependent-field-div').find('[name="personal_ref"]').val(response.data.personal_ref_value);
				}
			},
			error: function() {
				hideLoader();
			}
		});
	}
</script>
@endsection