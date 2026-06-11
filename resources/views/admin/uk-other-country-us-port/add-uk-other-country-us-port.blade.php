@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-md-flex border-navabr align-items-center">
        <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{$pageTitle }}</h1>
    </div>

    <section class="inner-wrapper-common-sections main-listing-section">
        <div class="container-fluid">
            <div class="card document-card mb-3">
                <ul class="document-items">
                    <li class="document-list"><a href="#details"   class="document-text">{{ trans("messages.details") }}</a></li>
                    <li class="document-list"><a href="#documents" class="document-text">{{ trans("messages.documents") }}</a></li>
                    <li class="document-list"><a href="#transporter" class="document-text">{{ trans("messages.transporter-invoice") }}</a></li>
                    <li class="document-list"><a href="#status" class="document-text">{{ trans("messages.status") }}</a></li>
                    <?php if( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ) { ?>
					<li class="document-text ml-auto logistic-master-no"><?php echo (!empty($recordInfo->v_country_to_port_record_no) ? $recordInfo->v_country_to_port_record_no : '')?></li>
					<?php }?>
                </ul>
            </div>
    <div class="card mb-3 good-in-buyer-class" id="details">
                <h3 class="title-goods"><i class="fas fa-level-up-alt list-icon mr-2"></i>{{ trans("messages.details") }}</h3>
                @include('admin/common-form-validation-error')
                {!! Form::open(array( 'id '=> 'add-good-out-country-port-form' , 'method' => 'post' , 'files' => true , 'url' => 'uk-other-country-us-port/add')) !!}
                    
                    
                    <div class="card-body">
                        <div class="row dependent-field-div">
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="way_of_transport">{{ trans("messages.way-of-transport") }}<span class="text-danger">*</span></label>
                                    <select name="way_of_transport" class="form-control" <?php echo $disableForm ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
                                        if(!empty($wayOfTransportDetails)){
                                        	foreach ($wayOfTransportDetails as  $key => $wayOfTransportDetail){
                                        		$selected = '';
                                        		if( isset($recordInfo->e_transport_way) && ( $recordInfo->e_transport_way == $key) ){
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
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="from_port_airport">{{ trans("messages.from-port-airport") }}<span class="text-danger">*</span></label>
                                    <select name="from_port_airport" class="form-control" <?php echo $disableForm ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
                                        if(!empty($fromPortInfo)){
                                        	foreach ($fromPortInfo as $fromPort){
                                        		$encodeFromPortId  = Wild_tiger::encode($fromPort->i_id);
                                        		$warehouseName = $fromPort->v_warehouse_name;
                                        		$selected = '';
                                        		if( isset($recordInfo->i_transport_from_id) && ( $recordInfo->i_transport_from_id == $fromPort->i_id ) ){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{ $encodeFromPortId }}" {{ $selected }}>{{ $warehouseName }}</option>
                                        		<?php 
                                        	}
                                        }
                                        
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="to_port_airport">{{ trans("messages.to-port-airport") }}<span class="text-danger">*</span></label>
                                    <select name="to_port_airport" class="form-control" <?php echo $disableForm ?> <?php echo ( ( isset($recordInfo) && ( $recordInfo->e_process_status == config('constants.COMPLETED_STATUS') ) ) ? 'disabled' : '' ) ?> >
                                        <option value="">{{ trans("messages.select") }}</option>
                                         <?php 
                                        if(!empty($toPortInfo)){
                                        	foreach ($toPortInfo as $toPort){
                                        		$encodeToPortId  = Wild_tiger::encode($toPort->i_id);
                                        		$warehouseName = $toPort->v_warehouse_name;
                                        		$selected = '';
                                        		if( isset($recordInfo->i_transport_to_id) && ( $recordInfo->i_transport_to_id == $toPort->i_id ) ){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{ $encodeToPortId }}" {{ $selected }}>{{ $warehouseName }}</option>
                                        		<?php 
                                        	}
                                        }
                                        
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="book_by" class="control-label">{{ trans("messages.book-by") }}<span class="text-danger">*</span></label>
                                    <select name="book_by" class="form-control select2" <?php echo $disableForm ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
                                        if(!empty($userRecordDetails)){
                                        	foreach ($userRecordDetails as $userRecordDetail){
                                        		$encodevUserId  = Wild_tiger::encode($userRecordDetail->i_id);
                                        		$selected = '';
                                        		if( isset($recordInfo->i_book_by_employee_id) && ( $recordInfo->i_book_by_employee_id == $userRecordDetail->i_id ) ){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{ $encodevUserId }}" {{ $selected }}><?php echo  (!empty($userRecordDetail->v_name) ? $userRecordDetail->v_name . (!empty($userRecordDetail->v_department) ?  ' ('. $userRecordDetail->v_department . ')' : '' ) : '' ) ?></option>
                                        		<?php 
                                        	}
                                        } 
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="logistic_partner_uk">{{ trans("messages.logistic-partner-uk") }}<span class="text-danger">*</span></label>
                                    <select name="logistic_partner_uk" class="form-control select2" <?php echo $disableForm ?> <?php //echo ( ( isset($recordInfo) && ( $recordInfo->e_process_status == config('constants.COMPLETED_STATUS') ) ) ? 'disabled' : '' ) ?> >
                                        <option value="">{{ trans("messages.select") }}</option>
                                       <?php 
                                        if(!empty($logisticPartnerDetails)){
                                        	foreach ($logisticPartnerDetails as $logisticPartnerDetail){
                                        		$encodeId  = Wild_tiger::encode($logisticPartnerDetail->i_id);
                                        		$selected = '';
                                        		if( isset($recordInfo->i_logistic_partner_detail_id) && ( $recordInfo->i_logistic_partner_detail_id == $logisticPartnerDetail->i_id ) ){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{ $encodeId }}" {{ $selected }}>{{ (!empty($logisticPartnerDetail->v_logistic_partner_name) ? $logisticPartnerDetail->v_logistic_partner_name : '' )}}</option>
                                        		<?php 
                                        	}
                                        } 
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="container_no_air_waybill_no">{{ trans("messages.container-no-air-waybill-no") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="container_no_air_waybill_no" class="form-control" placeholder="{{ trans('messages.container-no-air-waybill-no') }}" value="{{ old('container_no_air_waybill_no',  ( (isset($recordInfo) && (!empty($recordInfo->v_container_air_waybill_no))) ?  $recordInfo->v_container_air_waybill_no : '' ) )}}" <?php echo $disableForm ?>>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="seal_no_house_waybill_no">{{ trans("messages.seal-no-house-waybill-no") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="seal_no_house_waybill_no" class="form-control" placeholder="{{ trans('messages.seal-no-house-waybill-no') }}" value="{{ old('seal_no_house_waybill_no',  ( (isset($recordInfo) && (!empty($recordInfo->v_seal_house_waybill_no))) ?  $recordInfo->v_seal_house_waybill_no : '' ) )}}" <?php echo $disableForm ?>>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="etd_dispatch_date">{{ trans("messages.etd-dispatch-date") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="etd_dispatch_date" class="form-control date-format" placeholder="{{ trans('messages.etd-dispatch-date') }}" value="{{ old('etd_dispatch_date',  ( (isset($recordInfo) && (!empty($recordInfo->dt_est_dispatch_date))) ?  clientDate($recordInfo->dt_est_dispatch_date) : '' ) )}}" <?php echo $disableForm ?>>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="eta_arrival_date">{{ trans("messages.eta-arrival-date") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="eta_arrival_date" class="form-control date-format" placeholder="{{ trans('messages.eta-arrival-date') }}" value="{{ old('eta_arrival_date',  ( (isset($recordInfo) && (!empty($recordInfo->dt_est_port_arrival_date))) ?  clientDate($recordInfo->dt_est_port_arrival_date) : '' ) )}}" <?php echo $disableForm ?>>
                                </div>
                            </div>
                            <?php /* ?>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="total_value">{{ trans("messages.total-value") }}<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <select class="form-control mr-2" name="total_curruncy" <?php echo $disableForm ?>>
                                                <option selected value="">Currency</option>
                                                <?php 
		                                        if(!empty($currencyRecordDetails)){
		                                        	foreach ($currencyRecordDetails as $currencyRecordDetail){
		                                        		$encodeCurrencyrId  = Wild_tiger::encode($currencyRecordDetail->i_id);
		                                        		$selected = '';
		                                        		if( isset($recordInfo->i_goods_out_currency_id) && ( $recordInfo->i_goods_out_currency_id == $currencyRecordDetail->i_id ) ){
		                                        			$selected = "selected='selected'";
		                                        		}
		                                        		?>
		                                        		<option value="{{ $encodeCurrencyrId }}" {{ $selected }} >{{ (!empty($currencyRecordDetail->v_currency_code) ? $currencyRecordDetail->v_currency_code : '' ) }}</option>
		                                        		<?php 
		                                        	}
		                                        } 
		                                        ?>
                                            </select>
                                        </div>
                                        <input type="text" class="form-control" aria-label="Text input with dropdown button" onkeyup="onlyDecimal(this)"  name="total_amount" placeholder="{{ trans('messages.amount') }}" value="{{ old('total_amount',  ( (isset($recordInfo) && (!empty($recordInfo->d_payment_value))) ?  $recordInfo->d_payment_value : '' ) )}}" <?php echo $disableForm ?>>
                                     </div>
                                     <label id="total_curruncy-error" style='display: none;' class="invalid-input" for="total_curruncy"></label>
                                     <label id="total_amount-error" style='display: none;' class="invalid-input" for="total_amount"></label>
                                 </div>
                            </div>
                            <?php */ ?>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="total_pallets">{{ trans("messages.total-pallets") }}<span class="text-danger">*</span></label>
                                    <input type="number" onkeyup="onlyNumber(this);" min="1" name="total_pallets" class="form-control" placeholder="{{ trans('messages.total-pallets') }}" value="{{ old('total_pallets',  ( (isset($recordInfo) && (!empty($recordInfo->i_total_pallets))) ?  $recordInfo->i_total_pallets : '' ) )}}" <?php echo $disableForm ?>>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="dangerous_goods" class="control-label">{{ trans("messages.dangerous-goods") }}<span class="star">*</span></label>
                                    <select name="dangerous_goods" class="form-control" <?php echo $disableForm ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
                                        if(!empty($dangerousGoodsInfo)){
                                        	foreach ($dangerousGoodsInfo as  $key => $dangerousGoods){
                                        		$selected = '';
                                        		if( isset($recordInfo->e_dangerous_goods) && ( $recordInfo->e_dangerous_goods == $key ) ){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{ $key }}" {{ $selected }}>{{ $dangerousGoods }}</option>
                                        		<?php 
                                        	}
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="goods_remarks">{{ trans("messages.goods-remarks") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="goods_remarks" class="form-control" placeholder="{{ trans('messages.goods-remarks') }}" value="{{ old('goods_remarks',  ( (isset($recordInfo) && (!empty($recordInfo->v_goods_remark))) ?  $recordInfo->v_goods_remark : '' ) )}}" <?php echo $disableForm ?>>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="tracking_no" class="control-label">{{ trans("messages.tracking-no") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="tracking_no" class="form-control" placeholder="{{ trans('messages.tracking-no') }}" value="{{ old('tracking_no',  ( (isset($recordInfo) && (!empty($recordInfo->v_tracking_no))) ?  $recordInfo->v_tracking_no : '' ) )}}" <?php echo $disableForm ?>>
                                </div>
                            </div>
						
								

                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="tracking_link" class="control-label">{{ trans("messages.tracking-link") }}</label>
                                    <input type="text" name="tracking_link" class="form-control" placeholder="{{ trans('messages.tracking-link') }}" value="{{ old('tracking_link',  ( (isset($recordInfo) && (!empty($recordInfo->v_tracking_link))) ?  $recordInfo->v_tracking_link : '' ) )}}" <?php echo $disableForm ?>>
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="insurance_status" class="control-label">{{ trans("messages.insurance-status") }}<span class="text-danger">*</span></label>
                                    <select name="insurance_status" class="form-control" <?php echo $disableForm ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
                                        if(!empty($insuranceStatusDetails)){
                                        	foreach ($insuranceStatusDetails as  $key => $insuranceStatusDetail){
                                        		$selected = '';
                                        		if( isset($recordInfo->e_insurance_status) && ( $recordInfo->e_insurance_status == $key) ){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{ $key}}" {{ $selected }}>{{ $insuranceStatusDetail }}</option>
                                        		<?php 
                                        	}
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="delivery_date">{{ trans("messages.delivery-date") }} </label>
                                    <input type="text" {{ $disableForm }} name="delivery_date" class="form-control date-format" placeholder="{{ trans('messages.delivery-date') }}" value="{{ old('delivery_date', ( (isset($recordInfo) && (!empty($recordInfo->dt_delivery_date))) ? clientDate( $recordInfo->dt_delivery_date) : '' ))}}">
                                </div>
                            </div>
														<div class="col-xl-2  col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="personal_ref" class="control-label">{{ trans("messages.personal-ref") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="personal_ref" {{ $disableForm }} class="form-control" placeholder="{{ trans('messages.personal-ref') }}" value="{{ old('personal_ref', (isset($recordInfo) && !empty($recordInfo->v_personal_ref) ? $recordInfo->v_personal_ref : '')) }}">
                                </div>
                            </div>
							<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="from_warehouse_country" class="control-label">{{ trans("messages.from-warehouse-country") }}<span class="text-danger ">*</span></label>
                                    <select name="from_warehouse_country" class="form-control select2" {{ $disableForm }} onchange="relatedWarehouseByWarehouseCountry(this)">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        @if (!empty($countryDetails) && count($countryDetails) > 0)
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
							<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="warehouse" class="control-label">{{ trans("messages.warehouse") }}<span class="text-danger">*</span></label>
                                    <select name="warehouse" class="form-control warehouse-name-list select2" {{ $disableForm }}>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        @if (!empty($warehouseDetails) && count($warehouseDetails) > 0)
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
							<div class="col-xl-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="pick_up_date_from_warehouse">{{ trans("messages.pick-up-date-from-warehouse") }}<span class="text-danger">*</span> </label>
                                    <input type="text" name="pick_up_date_from_warehouse" {{ $disableForm }} class="form-control date-format" placeholder="{{ trans('messages.pick-up-date-from-warehouse') }}" value="{{ old('pick_up_date_from_warehouse', (isset($recordInfo) && !empty($recordInfo->dt_pick_up_date_from_warehouse) ? clientDate($recordInfo->dt_pick_up_date_from_warehouse) : '')) }}">
                                </div>
                            </div>
							<div class="col-xl-2  col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="comments" class="control-label">{{ trans("messages.comments") }}</label>
                                    <input type="text" name="comments" {{ $disableForm }} class="form-control" placeholder="{{ trans('messages.comments') }}" value="{{ old('comments', (isset($recordInfo) && !empty($recordInfo->v_comments) ? $recordInfo->v_comments : '')) }}">
                                </div>
                            </div>
							<div class="col-xl-2  col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="booking_reference" class="control-label">{{ trans("messages.booking-reference") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="booking_reference" {{ $disableForm }} class="form-control" placeholder="{{ trans('messages.booking-reference') }}" value="{{ old('booking_reference', (isset($recordInfo) && !empty($recordInfo->v_booking_ref) ? $recordInfo->v_booking_ref : '')) }}">
                                </div>
                            </div>
							<div class="col-xl-2  col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="total_units" class="control-label">{{ trans("messages.total-units") }}</label>
                                    <input type="text" name="total_units" class="form-control" placeholder="{{ trans('messages.total-units') }}" disabled readonly value="{{ old('total_units', (isset($totalUnits) && $totalUnits > 0 ? $totalUnits : '')) }}">
                                </div>
                            </div>
							<div class="col-xl-2  col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="total_value_of_container_invoice_no" class="control-label">{{ trans("messages.total-value-of-container")}} {{ trans("messages.usd") }} ({{ trans("messages.invoice-no") }})<span class="text-danger">*</span></label>
                                    <input type="text" name="total_value_of_container_invoice_no" class="form-control" placeholder="{{ trans('messages.total-value-of-container')}} {{ trans('messages.usd') }} ({{ trans('messages.invoice-no') }})" disabled readonly value="{{ old('total_value_of_container_invoice_no', (isset($recordInfo) && !empty($recordInfo->d_total_value_of_container) ? $recordInfo->d_total_value_of_container : '')) }}" {{ $disableForm }}>
                                </div>
                            </div>
							<div class="col-xl-2  col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="arrival_date_at_usa_port" class="control-label">{{ trans("messages.arrival-date-at-usa-port") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="arrival_date_at_usa_port" {{ $disableForm }} class="form-control" placeholder="{{ trans('messages.arrival-date-at-usa-port') }}" value="{{ old('arrival_date_at_usa_port', (isset($recordInfo) && !empty($recordInfo->dt_arrival_date_at_usa_port) ? clientDate($recordInfo->dt_arrival_date_at_usa_port) : '')) }}">
                                </div>
                            </div>
							<div class="col-md-12">
							    <div class="form-group pb-3 pt-3">
							        <div class="card shadow-none border">
							            <div class="card-header">
							                <span class="partner-tilte">
							                {{ trans("messages.shipment-values") }}
							                </span>
							            </div>
							            <div class="card-body logistic-partner">
							                <div class="table-responsive">
							                    <table class="table table-hover table-bordered table-sm pb-4">
							                        <thead>
							                            <tr class="text-center">
							                                <th style="max-width:70px;min-width:70px;">{{ trans("messages.sr-no") }}</th>
							                                <th style="max-width:250px;min-width:200px;">{{ trans("messages.invoice-no") }}<span class="text-danger">*</span></th>
							                                <th style="max-width:180px;min-width:180px;">{{ trans("messages.amount") }}<span class="text-danger">*</span></th>
							                                <th class="text-left" style="width:180px;min-width:180px;">{{ trans("messages.total") }}<span class="text-danger">*</span></th>
							                                <th class="text-left" style="width:110px;min-width:110px;">{{ trans("messages.cov-rate") }}<span class="text-danger">*</span></th>
							                                <th class="text-left" style="width:180px;min-width:180px;">{{ trans("messages.total-value-of-container") }} {{ trans("messages.usd") }}</th>
							                                <th style="max-width:250px;min-width:200px;">{{ trans("messages.attachment") }} </th>
							                                <th style="width:70px;min-width:70px;">{{ trans("messages.action") }}</th>
							                            </tr>
							                        </thead>
							                        <tbody class="good-out-country-port-shipment-value-tbody">
							                        <?php 
							                        $defaultShipmentValueRowCount = 2;
							                        if (isset($recordInfo->shipmentInfo) && count($recordInfo->shipmentInfo) > 0){
							                        	foreach ($recordInfo->shipmentInfo as $key => $shipmentDetail){
							                        		$shipmentInfoId = (!empty($shipmentDetail->i_id) ? $shipmentDetail->i_id : '');
							                        		?>
							                        		<tr>
								                                <td class="table-index text-center" style="width:70px;min-width:70px;">{{ $key + 1 }}</td>
								                                <td class="text-left">
								                                    <input type="text" class="form-control shipment-invoices-status" name="edit_shipment_invoices_{{ $shipmentInfoId }}" {{ $documentForm }} value="{{ (!empty($shipmentDetail->v_invoice) ? $shipmentDetail->v_invoice : '') }}">
								                                </td>
								                                <td class="text-left">
								                                    <input type="text" class="form-control shipment-value-amount shipment-amount-status" name="edit_shipment_amount_{{ $shipmentInfoId }}" {{ $documentForm }} onkeyup="onlyDecimal(this);getTotalValueOfContainer(this)" value="{{ (!empty($shipmentDetail->d_amount) ? $shipmentDetail->d_amount : '') }}">
								                                </td>
								                                <td class="text-left">
								                                    <select name="edit_shipment_currency_{{ $shipmentInfoId }}" class="form-control shipment-currency-status" {{ $documentForm }}>
								                                    <option value="">{{ trans("messages.currency") }}</option>
							                                        @if(!empty($currencyRecordDetails))
							                                        	@foreach ($currencyRecordDetails as $currencyRecordDetail)
							                                        		@php 
								                                        		$selected = (!empty($shipmentDetail->i_currency_id) && $shipmentDetail->i_currency_id == $currencyRecordDetail->i_id ? 'selected' : ''); 
								                                        	@endphp
							                                        		<option value="{{ Wild_tiger::encode($currencyRecordDetail->i_id) }}" {{ $selected }}>{{ (!empty($currencyRecordDetail->v_currency_code) ? $currencyRecordDetail->v_currency_code : '' ) }}</option>
							                                        	@endforeach
							                                        @endif
								                                    </select>
								                                </td>
								                                <td class="text-left">
								                                    <input type="text" class="form-control shipment-value-cov-rate shipment-cov-rate-status" name="edit_shipment_cov_rate_{{ $shipmentInfoId }}" {{ $documentForm }} onkeyup="onlyDecimal(this);getTotalValueOfContainer(this)" value="{{ (!empty($shipmentDetail->d_cov_rate) ? $shipmentDetail->d_cov_rate : '') }}">
								                                </td>
								                                <td class="text-left shipment-value-total-value-of-container"><?php echo (!empty($shipmentDetail->d_total_value_of_container) ? $shipmentDetail->d_total_value_of_container : '')?></td>
								                                <td class="text-left">
								                                    <div class="custom-file">
								                                        <input type="file" class="custom-file-input good-out-country-port-file" id="shipment_attachment_{{ $shipmentInfoId }}" name="edit_shipment_attachment_{{ $shipmentInfoId }}" onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')" {{ $documentForm }}>
								                                        <label class="custom-file-label" for="shipment_attachment_{{ $shipmentInfoId }}">{{ (uploadedFileExists($shipmentDetail->v_attachment_path) ? basename($shipmentDetail->v_attachment_path) : trans("messages.choose-file")) }}</label>
								                                    </div>
								                                </td>
								                                <td style="width:70px;min-width:70px;">
								                                @if ($key > 0)
								                                <button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button>
								                                @endif
								                                </td>
							                            	</tr>
							                        		<?php 
							                        	}
							                        } else {
							                        	if(!empty($documentForm)) {
							                        		?>
															<tr>
																<td colspan="8" class="text-center">{{ trans('messages.no-record-found')}}</td>
															</tr>
															<?php 
															} else {
																for ($i = 1; $i <= $defaultShipmentValueRowCount; $i++) {
																?>
																<tr>
																	<td class="table-index text-center" style="width:70px;min-width:70px;">{{ $i }}</td>
																	<td class="text-left">
																		<input type="text" class="form-control shipment-invoices-status" name="shipment_invoices_{{ $i }}">
																	</td>
																	<td class="text-left">
																		<input type="text" class="form-control shipment-value-amount shipment-amount-status" name="shipment_amount_{{ $i }}" onkeyup="onlyDecimal(this);getTotalValueOfContainer(this)">
																	</td>
																	<td class="text-left">
																		<select name="shipment_currency_{{ $i }}" class="form-control shipment-currency-status">
																		<option value="">{{ trans("messages.currency") }}</option>
																		@if(!empty($currencyRecordDetails))
																			@foreach ($currencyRecordDetails as $currencyRecordDetail)
																				<option value="{{ Wild_tiger::encode($currencyRecordDetail->i_id) }}">{{ (!empty($currencyRecordDetail->v_currency_code) ? $currencyRecordDetail->v_currency_code : '' ) }}</option>
																			@endforeach
																		@endif
																		</select>
																	</td>
																	<td class="text-left">
																		<input type="text" class="form-control shipment-value-cov-rate shipment-cov-rate-status" name="shipment_cov_rate_{{ $i }}" onkeyup="onlyDecimal(this);getTotalValueOfContainer(this)">
																	</td>
																	<td class="text-left shipment-value-total-value-of-container">
																	</td>
																	<td class="text-left">
																		<div class="custom-file">
																			<input type="file" class="custom-file-input good-out-country-port-file" id="shipment_attachment_{{ $i }}" name="shipment_attachment_{{ $i }}" onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
																			<label class="custom-file-label" for="shipment_attachment_{{ $i }}">{{ trans("messages.choose-file") }}</label>
																		</div>
																	</td>
																	<td style="width:70px;min-width:70px;">
																	@if ($i > 1)
																	<button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button>
																	@endif
																	</td>
																</tr>
																<?php 
															}
														}
							                        }
							                        ?>
							                        </tbody>
							                    </table>
							                    <?php if(empty($documentForm)) { ?>
							                    <button type="button" class="btn btn-sm bg-theme text-white add-new-row" title="{{ trans('messages.add-new') }}" onclick="addShipmentValueRow()"><i class="fa fa-plus fa-fw"></i>{{ trans("messages.add-new") }}</button>
							                    <?php } ?>
							                </div>
							            </div>
							        </div>
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
                                                        <tbody class="good-out-country-port-tbody">
                                                            <?php
                                                        	if( isset($recordInfo->documentInfo) && (!empty($recordInfo->documentInfo)) && (count($recordInfo->documentInfo) > 0 ) ) {
                                                        		foreach ($recordInfo->documentInfo as $countKey => $goodOutCountryPortDocumentDetail){
                                                        			$columIndex  = ( $countKey +  1 );
                                                        			?>
                                                        			<?php $documentFiles = (json_decode($goodOutCountryPortDocumentDetail->v_document_file_path)); ?>
	                                                        		<tr>
		                                                                <td class="table-index text-center" style="width:70px;min-width:70px;">{{$columIndex}}</td>
		                                                                <td class="text-left">
		                                                                    <select name="edit_type_<?php echo $goodOutCountryPortDocumentDetail->i_id ?>" class="form-control good-out-country-port-type" <?php echo $documentForm ?>>
		                                                                    	<option value="">{{ trans("messages.select") }}</option>
		                                                                        <?php 
		                                                                        if(!empty($documentTypeRecordDetails)){
		                                                                        	foreach ($documentTypeRecordDetails as $documentTypeRecordDetail){
		                                                                        		$encodevDocumentTypeId  = Wild_tiger::encode($documentTypeRecordDetail->i_id);
		                                                                        		$selected = '';
		                                                                        		if( isset($goodOutCountryPortDocumentDetail->i_document_type_id) && ( $goodOutCountryPortDocumentDetail->i_document_type_id == $documentTypeRecordDetail->i_id ) ){
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
		                                                                <td class="text-left">
		                                                                    <div class="custom-file">
		                                                                        <input type="file" class="custom-file-input good-out-country-port-file" id="edit_document_<?php echo $goodOutCountryPortDocumentDetail->i_id ?>" multiple name="edit_file_<?php echo $goodOutCountryPortDocumentDetail->i_id ?>[]" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')" <?php echo $documentForm ?>>
		                                                                        <label class="custom-file-label" for="edit_document_<?php echo $goodOutCountryPortDocumentDetail->i_id ?>"><?php echo (!empty($documentFiles) ? ( count($documentFiles) > 1 ? trans('messages.multiple-files') : ( isset($documentFiles[0]) ? basename($documentFiles[0]) : '' ) )  : trans('messages.choose-file') ) ?></label>
		                                                                    </div>
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" class="form-control" name="edit_remarks_<?php echo $goodOutCountryPortDocumentDetail->i_id ?>" value="<?php echo (isset($goodOutCountryPortDocumentDetail->v_document_remark) ? $goodOutCountryPortDocumentDetail->v_document_remark : '' ); ?>" <?php echo $documentForm ?>>
		                                                                </td>
																	 	<?php 
																	 	$documentFiles = (json_decode($goodOutCountryPortDocumentDetail->v_document_file_path)); ?>
		                                                              	<td class="actions-col">
																		<?php 
		                                                                	if(!empty($documentFiles)){
			                                                                	foreach ($documentFiles as $documentFile){
			                                                                		$documentFilePath = (config('constants.FILE_STORAGE_URL_PATH').$documentFile);
			                                                                	?>
			                                                                	<div class="download-link-items">
	                                                                  				<a title="{{trans('messages.remove')}}" href="javascript:void(0);" data-file-name="{{ basename($documentFilePath) }}" onclick="removeUploadedFile(this);" data-record-id="{{ $goodOutCountryPortDocumentDetail->i_id }}" data-field-name="document" class="close-icon"><i class="fa fa-times "></i></a>
		                                                                  			<a title="{{ basename($documentFilePath) }}" href="{{ $documentFilePath }}" target="_blank" class="btn btn-sm btn-danger mb-1 download-icon-items"><i class="fa fa-download"></i></a>
	                                                                			</div>
		                                                                    	<?php 
			                                                                	}
		                                                                	}
		                                                                ?>
		                                                              	</td>
		                                                            	<td style="width:70px;min-width:70px;">
		                                                            	 <?php if(empty($documentForm)) { ?>
		                                                            		<button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button>
		                                                            	<?php }?>
		                                                            	</td>
	                                                            	</tr>
                                                        		<?php 
                                                        		}
                                                        	} else {
                                                        		if(!empty($documentForm)) {
                                                        			?>
                                                        			<tr>
																		<td colspan="6" class="text-center">{{ trans('messages.no-record-found')}}</td>
																	</tr>
                                                        			<?php 
                                                        		} else {
	                                                        	?>
	                                                            <tr>
	                                                                <td class="table-index text-center" style="width:70px;min-width:70px;">1</td>
	                                                                <td class="text-left">
	                                                                    <select name="type_1" class="form-control good-out-country-port-type">
	                                                                    	<option value="">{{ trans("messages.select") }}</option>
	                                                                        <?php 
	                                                                        if(!empty($documentTypeRecordDetails)){
	                                                                        	foreach ($documentTypeRecordDetails as $documentTypeRecordDetail){
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
	                                                                        <input type="file" class="custom-file-input good-out-country-port-file" id="document_1" name="file_1[]" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
	                                                                        <label class="custom-file-label" for="document_1">{{ trans("messages.choose-file") }}</label>
	                                                                    </div>
	                                                                </td>
	                                                                <td class="text-left">
	                                                                    <input type="text" class="form-control" name="remarks_1">
	                                                                </td>
	                                                                <td class="actions-col">
	                                                                </td>
	
	                                                                <td style="width:70px;min-width:70px;"></td>
	                                                            </tr>
	                                                            <tr>
	                                                                <td class="table-index text-center" style="width:70px;min-width:70px;">2</td>
	                                                                <td class="text-left">
	                                                                    <select name="type_2" class="form-control good-out-country-port-type">
	                                                                    	<option value="">{{ trans("messages.select") }}</option>
	                                                                        <?php 
	                                                                        if(!empty($documentTypeRecordDetails)){
	                                                                        	foreach ($documentTypeRecordDetails as $documentTypeRecordDetail){
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
	                                                                        <input type="file" class="custom-file-input good-out-country-port-file" id="document_2" name="file_2[]" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
	                                                                        <label class="custom-file-label" for="document_2">{{ trans("messages.choose-file") }}</label>
	                                                                    </div>
	                                                                </td>
	                                                                <td class="text-left">
	                                                                    <input type="text" class="form-control" name="remarks_2">
	                                                                </td>
	                                                                <td class="actions-col">
	                                                                </td>
	
	                                                                <td style="width:70px;min-width:70px;"><button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button></td>
	                                                            </tr>
                                                           <?php } }?>
                                                        </tbody>
                                                    </table>
                                                    <?php if(empty($documentForm)) { ?>
                                                    	<button type="button" class="btn btn-sm bg-theme text-white add-new-row" title="{{ trans('messages.add-new') }}" onclick="addNewRow(this)"><i class="fa fa-plus fa-fw"></i>{{ trans("messages.add-new") }}</button>
                                                    <?php }?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="transporter">
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
                                                        <tbody class="good-out-country-port-transporter-tbody">
                                                            <?php 
                                                        	if( isset($recordInfo->invoiceInfo) && (!empty($recordInfo->invoiceInfo)) && (count($recordInfo->invoiceInfo) > 0 ) ){
                                                        		foreach ($recordInfo->invoiceInfo as $countKey => $goodOutCountryPortTransportDetail){
                                                        			$countIndex = ($countKey + 1 );
                                                        			?>
                                                        			<?php $invoiceFiles = (json_decode($goodOutCountryPortTransportDetail->v_invoice_file_path)); ?>
                                                        			<tr>
                                                                <td class="table-index">{{ $countIndex }}</td>
                                                                <td class="text-left">
                                                                	<select name="edit_name_<?php echo $goodOutCountryPortTransportDetail->i_id ?>" class="form-control good-out-country-port-transporter-name select2" <?php echo $documentForm ?>>
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
                                                                        		<option value="{{ $encodeId }}" {{ $selected }}>{{ (!empty($logisticPartnerRecordDetail->v_logistic_partner_name) ? $logisticPartnerRecordDetail->v_logistic_partner_name : '' ) }}</option>
                                                                        		<?php 
                                                                        	}
                                                                        }	
                                                                        ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control good-out-country-port-transporter-inv-no" name="edit_inv_no_<?php echo $goodOutCountryPortTransportDetail->i_id ?>" placeholder="{{ trans('messages.inv-no') }}" value="<?php echo (isset($goodOutCountryPortTransportDetail->v_invoice_no) ? $goodOutCountryPortTransportDetail->v_invoice_no : '' ); ?>" <?php echo $documentForm ?>>
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control agent-to-warehouse-freight" name="edit_freight_<?php echo $goodOutCountryPortTransportDetail->i_id ?>" placeholder="{{ trans('messages.freight') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($goodOutCountryPortTransportDetail->d_freight_charge) ? $goodOutCountryPortTransportDetail->d_freight_charge : '' ); ?>" <?php echo $documentForm ?>>
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control agent-to-warehouse-custom" name="edit_custom_<?php echo $goodOutCountryPortTransportDetail->i_id ?>" placeholder="{{ trans('messages.custom') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($goodOutCountryPortTransportDetail->d_custom_charge) ? $goodOutCountryPortTransportDetail->d_custom_charge : '' ); ?>" <?php echo $documentForm ?>>
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control agent-to-warehouse-duty" name="edit_duty_<?php echo $goodOutCountryPortTransportDetail->i_id ?>" placeholder="{{ trans('messages.duty') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($goodOutCountryPortTransportDetail->d_duty_charge) ? $goodOutCountryPortTransportDetail->d_duty_charge : '' ); ?>" <?php echo $documentForm ?>>
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control agent-to-warehouse-other" name="edit_other_<?php echo $goodOutCountryPortTransportDetail->i_id ?>" placeholder="{{ trans('messages.other') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($goodOutCountryPortTransportDetail->d_other_charge) ? $goodOutCountryPortTransportDetail->d_other_charge : '' ); ?>" <?php echo $documentForm ?>>
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control agent-to-warehouse-vat" name="edit_vat_<?php echo $goodOutCountryPortTransportDetail->i_id ?>" placeholder="{{ trans('messages.vat') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($goodOutCountryPortTransportDetail->d_vat_charge) ? $goodOutCountryPortTransportDetail->d_vat_charge : '' ); ?>" <?php echo $documentForm ?>>
                                                                </td>
                                                                <td class="text-left">
                                                                    <div class="input-group align-items-center flex-nowrap">
                                                                        <label class="mb-0" for=""><span class="agent-warehouse-total-value"><?php echo (isset($goodOutCountryPortTransportDetail->d_total_charge) ? $goodOutCountryPortTransportDetail->d_total_charge :'')?></span></label>
                                                                        <div class="input-group-prepend">
                                                                            <select class="form-control ml-2" name="edit_amount_<?php echo $goodOutCountryPortTransportDetail->i_id ?>" <?php echo $documentForm ?>>
                                                                             <option value="">{{ trans('messages.currency') }}</option>
                                                                                <?php 
										                                        if(!empty($currencyRecordDetails)){
										                                        	foreach ($currencyRecordDetails as $currencyRecordDetail){
										                                        		$encodeCurrencyrId  = Wild_tiger::encode($currencyRecordDetail->i_id);
										                                        		$selected = '';
										                                        		if( isset($goodOutCountryPortTransportDetail->i_invoice_currency_id) && ( $goodOutCountryPortTransportDetail->i_invoice_currency_id == $currencyRecordDetail->i_id ) ){
										                                        			$selected = "selected='selected'";
										                                        		}
										                                        		?>
										                                        		<option value="{{ $encodeCurrencyrId }}" {{ $selected }} >{{ (!empty($currencyRecordDetail->v_currency_code) ? $currencyRecordDetail->v_currency_code : '' ) }}</option>
										                                        		<?php 
										                                        	}
										                                        } 
										                                        ?>
                                                                            </select>
                                                                        </div>

                                                                    </div>
                                                                </td>

                                                                <td class="text-left">
                                                                    <input type="text" class="form-control agent-to-warehouse-con-rate" name="edit_cov_rate_<?php echo $goodOutCountryPortTransportDetail->i_id ?>" placeholder="{{ trans('messages.cov-rate') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($goodOutCountryPortTransportDetail->d_conversion_rate) ? $goodOutCountryPortTransportDetail->d_conversion_rate : '' ); ?>" <?php echo $documentForm ?>>
                                                                </td>
                                                                <td class="text-left"><span class="agent-warehouse-final-rate"><?php echo (isset($goodOutCountryPortTransportDetail->d_final_charge) ? $goodOutCountryPortTransportDetail->d_final_charge : '' ); ?></span></td>
                                                                <?php $invoiceFiles = (json_decode($goodOutCountryPortTransportDetail->v_invoice_file_path)); ?>
                                                                <td class="text-left">
                                                                    <div class="custom-file">
                                                                        <input type="file" class="custom-file-input" id="invoice_document_<?php echo $goodOutCountryPortTransportDetail->i_id ?>" name="edit_invoice_file_<?php echo $goodOutCountryPortTransportDetail->i_id ?>[]" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')" <?php echo $documentForm ?>>
                                                                        <label class="custom-file-label" for="invoice_document_<?php echo $goodOutCountryPortTransportDetail->i_id ?>"><?php echo (!empty($invoiceFiles) ? ( count($invoiceFiles) > 1 ? trans('messages.multiple-files') : ( isset($invoiceFiles[0]) ? basename($invoiceFiles[0]) : '' ) )  : trans('messages.choose-file') ) ?></label>
                                                                    </div>
                                                                </td>
                                                                
		                                                                
                                                                <td class="actions-col">
																<?php 
                                                                	if(!empty($invoiceFiles)){
	                                                                	foreach ($invoiceFiles as $invoiceFile){
	                                                                		$invoiceFilePath = (config('constants.FILE_STORAGE_URL_PATH').$invoiceFile);
	                                                                	?>
	                                                                	<div class="download-link-items">
	                                                                  		<a title="{{trans('messages.remove')}}" href="javascript:void(0);" data-file-name="{{ basename($invoiceFilePath) }}" onclick="removeUploadedFile(this);" data-record-id="{{ $goodOutCountryPortTransportDetail->i_id }}" data-field-name="invoice" class="close-icon"><i class="fa fa-times "></i></a>
		                                                                  	<a title="{{trans('messages.download-button')}}" target="_blank" href="{{ $invoiceFilePath }}" download class="btn btn-sm btn-danger mb-1 download-icon-items"><i class="fa fa-download"></i></a>
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
                                                        		if(!empty($documentForm)) {
                                                        			?>
                                                        			<tr>
																		<td colspan="13" class="text-center">{{ trans('messages.no-record-found')}}</td>
																	</tr>
                                                        			<?php 
                                                        		} else {
                                                        			?>
		                                                            <tr>
		                                                                <td class="table-index">1</td>
		                                                                <td class="text-left">
			                                                                <select name="name_1" class="form-control good-out-country-port-transporter-name select2">
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
		                                                                    <input type="text" class="form-control good-out-country-port-transporter-inv-no" name="inv_no_1" placeholder="{{ trans('messages.inv-no') }}">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" class="form-control agent-to-warehouse-freight" name="freight_1" placeholder="{{ trans('messages.freight') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" class="form-control agent-to-warehouse-custom" name="custom_1" placeholder="{{ trans('messages.custom') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" class="form-control agent-to-warehouse-duty" name="duty_1" placeholder="{{ trans('messages.duty') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" class="form-control agent-to-warehouse-other" name="other_1" placeholder="{{ trans('messages.other') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" class="form-control agent-to-warehouse-vat" name="vat_1" placeholder="{{ trans('messages.vat') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <div class="input-group align-items-center flex-nowrap">
		                                                                        <label class="mb-0" for=""><span class="agent-warehouse-total-value"></span></label>
		                                                                        <div class="input-group-prepend">
		                                                                            <select class="form-control ml-2" name="amount_1">
		                                                                             <option  value="">{{ trans('messages.currency') }}</option>
		                                                                                <?php 
												                                        if(!empty($currencyRecordDetails)){
												                                        	foreach ($currencyRecordDetails as $currencyRecordDetail){
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
		                                                                    <input type="text" class="form-control agent-to-warehouse-con-rate" name="cov_rate_1" placeholder="{{ trans('messages.cov-rate') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
		                                                                </td>
		                                                                <td class="text-left"><span class="agent-warehouse-final-rate"></span></td>
		                                                                <td class="text-left">
		                                                                    <div class="custom-file">
		                                                                        <input type="file" class="custom-file-input" id="invoice_document_1" name="invoice_file_1[]" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
		                                                                        <label class="custom-file-label" for="invoice_document_1">{{ trans("messages.choose-file") }}</label>
		                                                                    </div>
		                                                                </td>
		                                                                 <td class="actions-col">
		                                                                    
		                                                                </td>
		                                                            </tr>
		
		                                                            <tr>
		                                                                <td class="table-index">2</td>
		                                                                <td class="text-left">
		                                                                	<select name="name_2" class="form-control good-out-country-port-transporter-name select2">
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
		                                                                    <input type="text" class="form-control good-out-country-port-transporter-inv-no" name="inv_no_2" placeholder="{{ trans('messages.inv-no') }}">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" class="form-control agent-to-warehouse-freight" name="freight_2" placeholder="{{ trans('messages.freight') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" class="form-control agent-to-warehouse-custom" name="custom_2" placeholder="{{ trans('messages.custom') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" class="form-control agent-to-warehouse-duty" name="duty_2" placeholder="{{ trans('messages.duty') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" class="form-control agent-to-warehouse-other" name="other_2" placeholder="{{ trans('messages.other') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" class="form-control agent-to-warehouse-vat" name="vat_2" placeholder="{{ trans('messages.vat') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <div class="input-group align-items-center flex-nowrap">
		                                                                        <label class="mb-0" for=""><span class="agent-warehouse-total-value"></span></label>
		                                                                        <div class="input-group-prepend">
		                                                                            <select class="form-control ml-2" name="amount_2">
		                                                                                <option  value="">{{ trans('messages.currency') }}</option>
		                                                                                <?php 
												                                        if(!empty($currencyRecordDetails)){
												                                        	foreach ($currencyRecordDetails as $currencyRecordDetail){
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
		                                                                    <input type="text" class="form-control agent-to-warehouse-con-rate" name="cov_rate_2" placeholder="{{ trans('messages.cov-rate') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
		                                                                </td>
		                                                                <td class="text-left"><span class="agent-warehouse-final-rate"></span></td>
		                                                                <td class="text-left">
		                                                                    <div class="custom-file">
		                                                                        <input type="file" class="custom-file-input" id="invoice_document_2" name="invoice_file_2[]"   multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
		                                                                        <label class="custom-file-label" for="invoice_document_2">{{ trans("messages.choose-file") }}</label>
		                                                                    </div>
		                                                                </td>
		                                                                 <td class="actions-col">
		                                                                </td>
		                                                            </tr>
                                                            <?php }
                                                        		}?>
                                                        </tbody>
                                                    </table>
                                                    <?php if(empty($documentForm)) { ?> 
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
										 <?php ## view time a disabled aavu joi and role admin hoi to disabled na avu joi ana mate ni conditon .?>
                                        <select name="status" class="form-control" {{ $statusDisableForm }}>
                                            <option value="">{{ trans("messages.select") }}</option>
                                            <?php 
									        if(!empty($statusMasterRecordDetails)){
									        	foreach ($statusMasterRecordDetails as $statusMasterRecordDetail){
									           		$encoderId  = Wild_tiger::encode($statusMasterRecordDetail->i_id);
									           		$selected = '';
									           		if( isset($recordInfo->i_status_id) && ( $recordInfo->i_status_id == $statusMasterRecordDetail->i_id ) ){
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
                                        <input type="text" name="status_comments" class="form-control" placeholder="{{ trans('messages.status-comments') }}" value="{{ old('status_comments',  ( (isset($recordInfo) && (!empty($recordInfo->v_status_comment))) ?  $recordInfo->v_status_comment : '' ) )}}" <?php echo $documentForm ?>>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 submit-sticky">
                    <?php //if(empty($documentForm)) { ?> 
	                    <?php if( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ) { ?>
	                    	<input type="hidden" name="record_id" value="{{ Wild_tiger::encode($recordInfo->i_id) }}">
	                       <?php ## view time a button na aavu joi and role admin hoi to button avu joi ana mate ni conditon .?>
	                    		<?php if( empty($statusDisableForm) ){?>
	                        		<button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.update') }}">{{ trans("messages.update") }}</button>
	                        	<?php } ?>
	                    <?php } else {?>
	                    	<button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.submit') }}">{{ trans("messages.submit") }}</button>
	                    <?php }
					//}?>
                        <a href="{{ config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_URL') }}" class="btn btn-outline-secondary shadow-sm btn-wide" title="{{ trans('messages.cancel') }}">{{ trans("messages.cancel") }}</a>
                    </div>
                    <input type="hidden" name="good_out_country_port_document_type_count" value="">
                    <input type="hidden" name="good_out_country_port_transporter_count" value="">
                    <input type="hidden" name="good_out_country_port_shipment_value_count" value="">
            </div>
            {!! Form::close() !!}
        </div>
        </div>
        </div>
    </section>
</main>
<script>
    $("#add-good-out-country-port-form").validate({
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
            to_port_airport: {
                required: true,
                noSpace:true
            },
            book_by: {
                required: true,
                noSpace:true
            },
            logistic_partner_uk: {
                required: true,
                noSpace:true
            },
            container_no_air_waybill_no: {
                required: true,
                noSpace:true
            },
            seal_no_house_waybill_no: {
                required: true,
                noSpace:true
            },
            etd_dispatch_date: {
                required: true,
                noSpace:true
            },
            eta_arrival_date: {
                required: true,
                noSpace:true
            },
            total_amount: {
                required: false,
                noSpace:true
            },
            total_curruncy: {
                required: false,
                noSpace:true
            },
            total_pallets: {
                required: true,
                noSpace:true
            },
            tracking_no: {
                required: true,
                noSpace:true
            },
            insurance_status: {
                required: true,
                noSpace:true
            },
            dangerous_goods: {
                required: true,
                noSpace:true
            },
            goods_remarks: {
                required: true,
                noSpace:true
            },
            status: {
                required: true,
                noSpace:true
            },
            delivery_date: {
	   	   		required: function(element){
	   	   			return ( ($.trim($("[name='status']").find('option:selected').attr('data-status-id')) == '{{ config("constants.DELIVERED_STATUS_ID") }}') || ($.trim($("[name='status']").find('option:selected').attr('data-status-id')) == '{{ config("constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID") }}') ? true : false ) 
	   			},
	   		    noSpace: true
	   	   },
			personal_ref: {
				required: true,
				noSpace:true,
				validateUniquePersonalRefNumber: true,
			},
			from_warehouse_country: {
				required: true,
			},
			warehouse: {
				required: true,
			},
			pick_up_date_from_warehouse: {
				required: true,
				noSpace:true
			},
			booking_reference: {
				required: true,
				noSpace:true
			},
			arrival_date_at_usa_port: {
				required: true,
				noSpace:true
			}
        },
        messages: {
            way_of_transport: {
                required: "{{ trans('messages.require-way-of-transport') }}"
            },
            from_port_airport: {
                required: "{{ trans('messages.require-from-port-airport') }}"
            },  
            to_port_airport: {
                required: "{{ trans('messages.require-to-port-airport') }}"
            },
            book_by: {
                required: "{{ trans('messages.require-book-by') }}"
            },
            logistic_partner_uk: {
                required: "{{ trans('messages.require-logistic-partner-uk') }}"
            },
            container_no_air_waybill_no: {
                required: "{{ trans('messages.require-container-no-air-waybill-no') }}"
            },
            seal_no_house_waybill_no: {
                required: "{{ trans('messages.require-seal-no-house-waybill-no') }}"
            },
            etd_dispatch_date: {
                required: "{{ trans('messages.require-etd-dispatch-date') }}"
            },
            eta_arrival_date: {
                required: "{{ trans('messages.require-eta-arrival-date') }}"
            },
            total_amount: {
                required: "{{ trans('messages.require-amount') }}"
            },
            total_curruncy: {
                required: "{{ trans('messages.require-currency') }}"
            },
            total_pallets: {
                required: "{{ trans('messages.require-total-pallets') }}"
            },
            tracking_no: {
                required: "{{ trans('messages.require-tracking-no') }}"
            },
            insurance_status: {
                required: "{{ trans('messages.require-insurance-status') }}"
            },
            dangerous_goods: {
                required: "{{ trans('messages.require-dangerous-goods') }}"
            },
            goods_remarks: {
                required: "{{ trans('messages.require-goods-remarks') }}"
            },
            status: {
                required: "{{ trans('messages.require-status') }}"
            },
            delivery_date: {
            	required: "{{ trans('messages.require-delivery-date') }}"
            },
            personal_ref:  {
                required: "{{ trans('messages.require-personal-ref') }}"
            },
            from_warehouse_country:  {
                required: "{{ trans('messages.require-from-warehouse-country') }}"
            },
            warehouse:  {
                required: "{{ trans('messages.require-warehouse') }}"
            },
            pick_up_date_from_warehouse:  {
                required: "{{ trans('messages.require-pick-up-date-from-warehouse') }}"
            },
            booking_reference:  {
                required: "{{ trans('messages.require-booking-reference') }}"
            },
            arrival_date_at_usa_port:  {
                required: "{{ trans('messages.require-arrival-date-at-usa-port') }}"
            }
        },
        submitHandler: function(form) {
        	var agent_warehouse_type_status = false;
       	 	var agent_warehouse_file_status = false;
       		$('.good-out-country-port-tbody tr').each(function(){
       			var agent_warehouse_type = $.trim($(this).find('.good-out-country-port-type').val());
       			var agent_warehouse_file = $.trim($(this).find('.good-out-country-port-file').val());
        		var agent_warehouse_file_valid = $.trim($(this).find('.good-out-country-port-file').attr('data-valid-file'));
				
				if(agent_warehouse_file_valid != "" && agent_warehouse_file_valid != null && agent_warehouse_file_valid == "{{ strtolower(config('constants.SELECTION_YES'))}}"){
					
        			if( ( agent_warehouse_type == "" || agent_warehouse_type == null ) && (agent_warehouse_type_status != true) ){
						$.trim($(this).find('.good-out-country-port-type').focus());
						agent_warehouse_file_status = true;
                	}
        		}
       		});
       		
       		if( agent_warehouse_file_status != false ){
        		alertifyMessage("error","{{ trans('messages.require-document-type') }} ");
           		return false;
            }
       		var good_out_country_port_transporter_name_status = false;
            var good_out_country_port_inv_no_status = false;
            $('.good-out-country-port-transporter-tbody tr').each(function(){
            	var good_out_transport_port_transporter_name = $.trim($(this).find('.good-out-country-port-transporter-name').val());
         		var good_out_country_port_transporter_inv_no = $.trim($(this).find('.good-out-country-port-transporter-inv-no').val());

				if(good_out_transport_port_transporter_name != "" && good_out_transport_port_transporter_name != null){
					good_out_country_port_transporter_name_status = true;
					if( ( good_out_country_port_transporter_inv_no == "" || good_out_country_port_transporter_inv_no == null ) && (good_out_country_port_inv_no_status != true) ){
						$.trim($(this).find('.good-out-country-port-transporter-inv-no').focus());
						good_out_country_port_inv_no_status = true;
                	}
         		} 
         	});
           
            if( good_out_country_port_inv_no_status != false ){
         		alertifyMessage("error","{{ trans('messages.require-inv-no') }} ");
            	return false;
            }

            var shipment_invoices_status = false;
			var shipment_amount_status = false;
			var shipment_currency_status = false;
			var shipment_cov_rate_status = false;

			$('.good-out-country-port-shipment-value-tbody tr').each(function(){
				var shipment_invoices = $.trim($(this).find('.shipment-invoices-status').val());
				var shipment_amount = $.trim($(this).find('.shipment-amount-status').val());
				var shipment_currency = $.trim($(this).find('.shipment-currency-status').val());
				var shipment_cov_rate = $.trim($(this).find('.shipment-cov-rate-status').val());

				if(shipment_invoices != "" && shipment_invoices != null){
					shipment_invoices_status = true;
					
					if( ( shipment_amount == "" || shipment_amount == null ) && (shipment_amount_status != true) ){
						$(this).find('.shipment-amount-status').focus();
						shipment_amount_status = true;
					}
					if( ( shipment_currency == "" || shipment_currency == null ) && (shipment_amount_status != true && shipment_currency_status != true) ){
						$(this).find('.shipment-currency-status').focus();
						shipment_currency_status = true;
					}
					if( ( shipment_cov_rate == "" || shipment_cov_rate == null ) && (shipment_amount_status != true && shipment_currency_status != true && shipment_cov_rate_status != true) ){
						$(this).find('.shipment-cov-rate-status').focus();
						shipment_cov_rate_status = true;
					}
				} else {
					shipment_invoices_status = false;
					$(this).find('.shipment-invoices-status').focus();
					return false;
				}
			});
			
			if( shipment_invoices_status != true ){
				alertifyMessage("error","{{ trans('messages.require-invoice-no') }} ");
				return false;
			}
			if( shipment_amount_status != false ){
				alertifyMessage("error","{{ trans('messages.require-amount') }} ");
				return false;
			}
			if( shipment_currency_status != false ){
				alertifyMessage("error","{{ trans('messages.require-total') }} ");
				return false;
			}
			if( shipment_cov_rate_status != false ){
				alertifyMessage("error","{{ trans('messages.require-cov-rate') }} ");
				return false;
			}
            
        	var confirm_box = "";
            var confirm_box_msg = "";
            <?php if( isset($recordInfo) && (  $recordInfo->i_id > 0 ) ) { ?>
  					confirm_box = "{{ trans('messages.update-good-out-country-to-port') }}";
					confirm_box_msg = "{{ trans ( 'messages.confirm-good-out-country-to-port-update-msg') }}";
   
			<?php }else{?>
					confirm_box = "{{ trans('messages.add-good-out-country-to-port') }}";
 					confirm_box_msg = "{{ trans ( 'messages.confirm-good-out-country-to-port-add-msg') }}";
			<?php }?>
           
        	alertify.confirm(confirm_box,confirm_box_msg,function() {
            	$("[name='good_out_country_port_document_type_count']").val(good_out_country_port_document_type_count);
            	$("[name='good_out_country_port_transporter_count']").val(good_out_country_port_transporter_count);
            	$("[name='good_out_country_port_shipment_value_count']").val(good_out_country_port_shipment_value_count);
            	$("[name='logistic_partner_uk']").prop('disabled' , false );
            	$('input:disabled').prop('disabled', false);
 				$('select:disabled').prop('disabled', false);
            	
 				showLoader()
                form.submit();
         	},function() {});
        }
    });
    
    var good_out_country_port_module_url = '{{config("constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_URL")}}' + '/';
    
    var unique_personal_ref_number_msg = '';
    $.validator.addMethod("validateUniquePersonalRefNumber", function (value, element) {
		var result = true;
		
		$.ajax({
			type: "POST",
			async: false,
			url: good_out_country_port_module_url + 'check-unique-personal-ref-number',
			dataType: "json",
			data: {
				"_token": "{{ csrf_token() }}",
				'personal_ref': $.trim($("[name='personal_ref']").val()),
				'record_id': $.trim($("[name='record_id']").val())
			},
			success: function (response) {
				unique_lookup_value_msg = response.message;
				if (response.status_code != 1) {
					result = false;
				}
			}
		});
		
		return result;
	}, function (params, element) {
		return unique_lookup_value_msg;
	});
</script>

<script>

$(document).ready(function() {
 	$("[name='etd_dispatch_date'],[name='eta_arrival_date'],[name='delivery_date'], [name='pick_up_date_from_warehouse'],[name='arrival_date_at_usa_port']").datetimepicker({
    	useCurrent: false,
        viewMode: 'days',
   		ignoreReadonly: true,
		widgetPositioning: {
			vertical: 'bottom'
		},
       	format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

   	});
 	
});
 $(function(){
	$("[name='etd_dispatch_date']").datetimepicker().on('dp.change', function(e) {
		var incrementDay = moment((e.date)).startOf('d');
	 	$("[name='eta_arrival_date']").data('DateTimePicker').minDate(incrementDay);
	    $(this).data("DateTimePicker").hide();
	});

    $("[name='eta_arrival_date']").datetimepicker().on('dp.change', function(e) {
        var decrementDay = moment((e.date)).endOf('d');
        $("[name='etd_dispatch_date']").data('DateTimePicker').maxDate(decrementDay);
        $(this).data("DateTimePicker").hide();
    });
		
});

var good_out_country_port_document_type_count = 1;

function addNewRow(thisitem){
	good_out_country_port_document_type_count++;
  	var html =""; 
  	html += '<tr>';
  	html += '<td class="table-index text-center" style="width:70px;min-width:70px;">'+good_out_country_port_document_type_count+'</td>';
  	html += '<td class="text-left">';
  	html += '<select name="type_'+good_out_country_port_document_type_count+'" class="form-control good-out-country-port-type">';
  	html += '<option value="">{{ trans("messages.select") }}</option>';
    <?php 
    if(!empty($documentTypeRecordDetails)){
    	foreach ($documentTypeRecordDetails as $documentTypeRecordDetail){
        	$encodevDocumentTypeId  = Wild_tiger::encode($documentTypeRecordDetail->i_id);
        	?>
        	html += '<option value="{{ $encodevDocumentTypeId }}">{{ (!empty($documentTypeRecordDetail->v_document_type_name) ? $documentTypeRecordDetail->v_document_type_name : '' ) }}</option>';
            <?php 
       }
  	}	
    ?>
    html += '</select>';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<div class="custom-file">';
    html += '<input type="file" class="custom-file-input good-out-country-port-file" id="document_'+good_out_country_port_document_type_count+'" name="file_'+good_out_country_port_document_type_count+'[]" multiple onchange="validFile(this,\'pdf_doc_jpg_png_jpeg_xls\')">';
    html += '<label class="custom-file-label" for="document_'+good_out_country_port_document_type_count+'">{{ trans("messages.choose-file") }}</label>';
    html += '</div>';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<input type="text" class="form-control" name="remarks_'+good_out_country_port_document_type_count+'">';
    html += '</td>';
    html += '<td class="actions-col">';
    html += '</td>';
	html += '<td style="width:70px;min-width:70px;"><button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button></td>';
    html += '</tr>'; 
    if( $('.good-out-country-port-tbody').find('tr').length > 0 ){
		$(html).insertAfter($('.good-out-country-port-tbody').find('tr:last'));	
	} else {
		$('.good-out-country-port-tbody').html(html);
	}
	reindexTable('good-out-country-port-tbody');
	}
	
var good_out_country_port_transporter_count = 2;
	function addNewTransporterInvoiceRow(thisitem){
		good_out_country_port_transporter_count++;
		var html = "";
		html += '<tr>';
		html += '<td class="table-index">'+good_out_country_port_transporter_count+'</td>';
		html += '<td class="text-left">';
		html += '<select name="name_'+good_out_country_port_transporter_count+'" class="form-control good-out-country-port-transporter-name select2">';
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
		html += '<input type="text" class="form-control good-out-country-port-transporter-inv-no" name="inv_no_'+good_out_country_port_transporter_count+'" placeholder="{{ trans('messages.inv-no') }}">';
		html += '</td>';
		html += '<td class="text-left">';
		html += '<input type="text" class="form-control agent-to-warehouse-freight" name="freight_'+good_out_country_port_transporter_count+'" placeholder="{{ trans('messages.freight') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
		html += '</td>';
		html += '<td class="text-left">';
		html += '<input type="text" class="form-control agent-to-warehouse-custom" name="custom_'+good_out_country_port_transporter_count+'" placeholder="{{ trans('messages.custom') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
		html += '</td>';
		html += '<td class="text-left">';
		html += '<input type="text" class="form-control agent-to-warehouse-duty" name="duty_'+good_out_country_port_transporter_count+'" placeholder="{{ trans('messages.duty') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
		html += '</td>';
		html += '<td class="text-left">';
		html += '<input type="text" class="form-control agent-to-warehouse-other" name="other_'+good_out_country_port_transporter_count+'" placeholder="{{ trans('messages.other') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
		html += '</td>';
	    html += '<td class="text-left">';
	    html += '<input type="text" class="form-control agent-to-warehouse-vat" name="vat_'+good_out_country_port_transporter_count+'" placeholder="{{ trans('messages.vat') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
	 	html += '</td>';
		html += '<td class="text-left">';
		html += '<div class="input-group align-items-center flex-nowrap">';
		html += '<label class="mb-0" for=""><span class="agent-warehouse-total-value"></span></label>';
		html += '<div class="input-group-prepend">';
		html += '<select class="form-control ml-2" name="amount_'+good_out_country_port_transporter_count+'">';
		html += '<option selected value="">Currency</option>';
   	<?php 
    if(!empty($currencyRecordDetails)){
    	foreach ($currencyRecordDetails as $currencyRecordDetail){
       		$encodeCurrencyrId  = Wild_tiger::encode($currencyRecordDetail->i_id);
       	
         	?>
         	html += '<option value="{{ $encodeCurrencyrId }}">{{ (!empty($currencyRecordDetail->v_currency_code) ? $currencyRecordDetail->v_currency_code : '' ) }}</option>';
          	<?php 
       	}
    } 
    ?>
    html += '</select>';
    html += '</div>';
    html += '</div>';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<input type="text" class="form-control agent-to-warehouse-con-rate" name="cov_rate_'+good_out_country_port_transporter_count+'" placeholder="{{ trans('messages.cov-rate') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
    html += '</td>';
    html += '<td class="text-left"><span class="agent-warehouse-final-rate"></span></td>';
    html += '<td class="text-left">';
    html += '<div class="custom-file">';
    html += '<input type="file" class="custom-file-input" id="invoice_document_'+good_out_country_port_transporter_count+'" name="invoice_file_'+good_out_country_port_transporter_count+'[]" multiple onchange="validFile(this,\'pdf_doc_jpg_png_jpeg_xls\')">';
    html += '<label class="custom-file-label" for="invoice_document_'+good_out_country_port_transporter_count+'">{{ trans("messages.choose-file") }}</label>';
    html += '</div>';
    html += '</td>';
    html += '<td class="actions-col">';
    html += '</td>';
    html += '</tr>';
  
    if( $('.good-out-country-port-transporter-tbody').find('tr').length > 0 ){
		$(html).insertAfter($('.good-out-country-port-transporter-tbody').find('tr:last'));	
	} else {
		$('.good-out-country-port-transporter-tbody').html(html);
	}
	reindexTable('good-out-country-port-transporter-tbody');
	$(function(){
			$('.select2').select2();
		})
	}
	
	var good_out_country_port_shipment_value_count = {{ $defaultShipmentValueRowCount }};
	function addShipmentValueRow(){
		good_out_country_port_shipment_value_count++;
		var html = '';
		html += '<tr>';
		html += '<td class="table-index text-center" style="width:70px;min-width:70px;">'+good_out_country_port_shipment_value_count+'</td>';
		html += '<td class="text-left">';
		html += '<input type="text" class="form-control shipment-invoices-status" name="shipment_invoices_'+good_out_country_port_shipment_value_count+'">';
		html += '</td>';
		html += '<td class="text-left">';
		html += '<input type="text" class="form-control shipment-value-amount shipment-amount-status" name="shipment_amount_'+good_out_country_port_shipment_value_count+'" onkeyup="onlyDecimal(this);getTotalValueOfContainer(this)">';
		html += '</td>';
		html += '<td class="text-left">';
		html += '<select name="shipment_currency_'+good_out_country_port_shipment_value_count+'" class="form-control shipment-currency-status">';
		html += '<option value="">{{ trans("messages.currency") }}</option>';
		@if(!empty($currencyRecordDetails))
			@foreach ($currencyRecordDetails as $currencyRecordDetail)
				html += '<option value="{{ Wild_tiger::encode($currencyRecordDetail->i_id) }}">{{ (!empty($currencyRecordDetail->v_currency_code) ? $currencyRecordDetail->v_currency_code : '' ) }}</option>';
			@endforeach
		@endif
		html += '</select>';
		html += '</td>';
		html += '<td class="text-left">';
		html += '<input type="text" class="form-control shipment-value-cov-rate shipment-cov-rate-status" name="shipment_cov_rate_'+good_out_country_port_shipment_value_count+'" onkeyup="onlyDecimal(this);getTotalValueOfContainer(this)">';
		html += '</td>';
		html += '<td class="text-left shipment-value-total-value-of-container">';
		html += '</td>';
		html += '<td class="text-left">';
		html += '<div class="custom-file">';
		html += '<input type="file" class="custom-file-input good-out-country-port-file" id="attachment" name="shipment_attachment_'+good_out_country_port_shipment_value_count+'" onchange="validFile(this,\'pdf_doc_jpg_png_jpeg_xls\')">';
		html += '<label class="custom-file-label" for="shipment_attachment_'+good_out_country_port_shipment_value_count+'">{{ trans("messages.choose-file") }}</label>';
		html += '</div>';
		html += '</td>';
		html += '<td style="width:70px;min-width:70px;">';
		html += '<button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button>';
		html += '</td>';
		html += '</tr>';

		$(html).insertAfter($('.good-out-country-port-shipment-value-tbody').find('tr:last'));
		reindexTable('good-out-country-port-shipment-value-tbody');
	}

	function getTotalValueOfContainer(thisitem){
		var shipment_amount =  $.trim($(thisitem).parents('tr').find('.shipment-value-amount').val());
	   	var shipment_cov_rate =  $.trim($(thisitem).parents('tr').find('.shipment-value-cov-rate').val());

	   	$(thisitem).parents('tr').find('.shipment-value-total-value-of-container').html('');
	   	
	   	if(shipment_amount != '' && shipment_amount != null && shipment_cov_rate != '' && shipment_cov_rate != null){
	   		shipment_amount = ( parseFloat(shipment_amount) > 0.00 ? parseFloat(shipment_amount).toFixed(2) : 0.00 );
		   	shipment_cov_rate = ( parseFloat(shipment_cov_rate) > 0.00 ? parseFloat(shipment_cov_rate).toFixed(2) : 0.00 );
			   
		   	var total_value_of_container = (parseFloat(shipment_amount) * parseFloat(shipment_cov_rate));
		   	total_value_of_container = (parseFloat(total_value_of_container) > 0.00 ? total_value_of_container.toFixed(2) :0.00);
		   	
			$(thisitem).parents('tr').find('.shipment-value-total-value-of-container').html(total_value_of_container);
		}

	   	var total_value_of_container_invoice_no = 0;
		$('.shipment-value-total-value-of-container').each(function(){
			var current_total_value_of_container = $.trim($(this).html());
			current_total_value_of_container = ( parseFloat(current_total_value_of_container) > 0.00 ? parseFloat(current_total_value_of_container).toFixed(2) : 0.00 );
			total_value_of_container_invoice_no += parseFloat(current_total_value_of_container);

			$('[name="total_value_of_container_invoice_no"]').val(total_value_of_container_invoice_no);
		});
	}
</script>
@endsection