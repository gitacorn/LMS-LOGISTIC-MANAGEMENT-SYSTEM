@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color"> 
<div class="breadcrumb-wrapper d-flex align-items-center border-navabr">  
    <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle"> {{ $pageTitle }} (<span class="total-record-count"></span>) </h1>
    <div class=" ml-auto pt-sm-0 d-flex align-items-center">
    <?php if((checkPermission(config('permission_constants.ADD_GOODS_OUT_US_PORT_TO_AGENT_WAREHOUSE')) != false)){?>
        <a href="{{ config('constants.PORT_TO_AGENT_GOODS_OUT_MASTER_URL') . '/create' }}" class="btn btn btn-theme text-white button-actions-top-bar  border btn-sm mr-2 d-flex align-items-center" title="{{ trans('messages.add-us-port-to-agent-warehouse') }}"><i class=" fas fa-plus mr-md-2"></i> <span class="d-md-block d-none">{{ trans("messages.add") }}</span></a>
       <?php }?>
        <button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center mr-2" data-toggle="collapse" data-target="#filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none">{{ trans("messages.filter") }}</span></button>
        @if((checkPermission(config('permission_constants.EXCEL_EXPORT_GOODS_OUT_US_PORT_TO_AGENT_WAREHOUSE')) != false))
            <button type="button" title="{{ trans('messages.export-excel') }}"  class="btn btn btn-theme text-white button-actions-top-bar  border btn-sm mr-2 d-flex align-items-center" onclick="exportData();"><i class="fas fa-upload mr-md-1 fa-fw"></i><span class="d-md-block d-none">{{ trans("messages.export-excel") }}</span></button>
        @endif
    </div>
</div>


<section class="inner-wrapper-common-section main-listing-section">
    <div class="container-fluid">
        <div class="collapse" id="filter">
                <div class="card card-body mb-3">
                    <div class="row align-items-center dependent-field-div">
                        <div class="col-xl-6 col-md-12">
                            <div class="form-group">
                                <label for="search_by_us_port_to_agent_warehouse" class="control-label">{{ trans("messages.search-by") }}</label>
                                <input type="text" class="form-control twt-enter-search custom-input" name="search_by_us_port_to_agent_warehouse" id="search_by_us_port_to_agent_warehouse" placeholder="{{ trans('messages.search-by') }} {{ trans('messages.personal-ref') }}, {{ trans('messages.fba') }}, {{ trans('messages.entry-no') }}, {{ trans('messages.tracking-no') }} , {{ trans('messages.ref-no') }}">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="way_of_transport">{{ trans("messages.way-of-transport") }}</label>
                                        <select name="way_of_transport" class="form-control" onchange="filterData()">
                                            <option value="">{{ trans("messages.select") }}</option>
                                            <?php 
		                                        if(!empty($wayOfTransportDetails)){
		                                        	foreach ($wayOfTransportDetails as  $key => $wayOfTransportDetail){
		                                        		?>
		                                        		<option value="{{ $key }}">{{ $wayOfTransportDetail }}</option>
		                                        		<?php 
		                                        	}
		                                        }
		                                        ?>                                          
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="from_port_airport">{{ trans("messages.from-port-airport") }}</label>
                                        <select name="from_port_airport" class="form-control" onchange="filterData()">
                                            <option value="">{{ trans("messages.select") }}</option>
                                            <?php 
		                                        if(!empty($fromPortInfo)){
		                                        	foreach ($fromPortInfo as $fromPort){
		                                        		$encodeFromPortId  = Wild_tiger::encode($fromPort->i_id);
		                                        		$warehouseName = $fromPort->v_warehouse_name;
		                                        		?>
		                                        		<option value="{{ $encodeFromPortId }}">{{ $warehouseName }}</option>
		                                        		<?php 
		                                        	}
		                                        }
		                                        ?>                                                                        
                                        </select>
                                    </div>
                                </div>

                                <!-- <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="form-group">
                                            <label class="control-label" for="broker_custom_agent">{{ trans("messages.broker-custom-agent") }}</label>
                                            <input type="text" name="broker_custom_agent" class="form-control" placeholder="{{ trans('messages.broker-custom-agent') }}">
                                        </div>
                                    </div> -->

                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="logistic_partner">{{ trans("messages.logistic-partner") }}</label>
                                        <select name="logistic_partner" class="form-control select2" onchange="filterData()">
                                            <option value="">{{ trans("messages.select") }}</option>
                                            <?php 
		                                        if(!empty($logisticPartners)){
		                                        	foreach ($logisticPartners as $logisticPartner){
		                                        		$encodeId  = Wild_tiger::encode($logisticPartner->i_id);
		                                        		?>
		                                        		<option value="{{ $encodeId }}">{{ (!empty($logisticPartner->v_logistic_partner_name) ? $logisticPartner->v_logistic_partner_name : '' ) }}</option>
		                                        		<?php 
		                                        	}
		                                        } 
		                                        ?>                                                                         
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="search_warehouse_type">{{ trans("messages.warehouse-type") }}</label>
                                        <select name="search_warehouse_type" class="form-control" onchange="warehouseTypeWiseLocation(this, true);">
                                            <option value="">{{ trans("messages.select") }}</option>
                                            @if (!empty($usaGoodOutWarehouseTypeDetails))
											@foreach ($usaGoodOutWarehouseTypeDetails as $usaGoodOutWarehouseTypeKey => $usaGoodOutWarehouseTypeValue)
												<option value="{{ $usaGoodOutWarehouseTypeKey }}">{{ (!empty($usaGoodOutWarehouseTypeValue) ? $usaGoodOutWarehouseTypeValue : '' ) }}</option>
											@endforeach
										@endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label for="search_from_warehouse_country" class="control-label">{{ trans("messages.from-warehouse-country") }}</label>
                                        <select name="search_from_warehouse_country" class="form-control select2" onchange="relatedWarehouseByWarehouseCountry(this, true)">
                                            <option value="">{{ trans("messages.select") }}</option>
                                            @if (!empty($countryDetails) && count($countryDetails) > 0)
												@foreach ($countryDetails as $countryDetail)
													<option value="{{ Wild_tiger::encode($countryDetail->i_id) }}">{{ (!empty($countryDetail->v_country_name) ? $countryDetail->v_country_name : '') }}</option>
												@endforeach
											@endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label for="search_warehouse" class="control-label">{{ trans("messages.warehouse") }}</label>
                                        <select name="search_warehouse" class="form-control warehouse-name-list select2" onchange="filterData()">
                                            <option value="">{{ trans("messages.select") }}</option>
                                            @if (!empty($warehouseDetails) && count($warehouseDetails) > 0)
												@foreach ($warehouseDetails as $warehouseDetail)
													<option value="{{ Wild_tiger::encode($warehouseDetail->i_id) }}">{{ (!empty($warehouseDetail->v_warehouse_name) ? $warehouseDetail->v_warehouse_name : '') }}</option>
												@endforeach
											@endif
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-lg-3 col-md-4 col-sm-6 own-warehouse-location" style="display: none">
                                    <div class="form-group">
                                        <label class="control-label" for="search_to_own_location">{{ trans("messages.to-own-location") }}</label>
                                        <select name="search_to_own_location" class="form-control select2" onchange="filterData()">
                                            <option value="">{{ trans("messages.select") }}</option>
	                                        @if (!empty($ownFromWarehouseDetails))
												@foreach ($ownFromWarehouseDetails as $ownFromWarehouseDetail)
													@php
														$encodeFromPortId  = Wild_tiger::encode($ownFromWarehouseDetail->i_id);
													@endphp
													<option value="{{ $encodeFromPortId }}">{{ (!empty($ownFromWarehouseDetail->v_warehouse_name) ? $ownFromWarehouseDetail->v_warehouse_name : '') }}</option>
												@endforeach
											@endif
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-lg-3 col-md-4 col-sm-6 agent-warehouse-location" style="display: none">
                                    <div class="form-group">
                                        <label class="control-label" for="to_agent_location">{{ trans("messages.to-agent-location") }}</label>
                                        <select name="to_agent_location" class="form-control select2" onchange="filterData()">
                                            <option value="">{{ trans("messages.select") }}</option>
                                            <?php 
		                                        if(!empty($logisticPartnerDetails)){
		                                        	foreach ($logisticPartnerDetails as $logisticPartnerDetail){
		                                        		$encodeId  = Wild_tiger::encode($logisticPartnerDetail->i_id);
		                                        		?>
		                                        		<option value="{{ $encodeId }}">{{ (!empty($logisticPartnerDetail->logisticPartnerMaster->v_logistic_partner_name) ? $logisticPartnerDetail->logisticPartnerMaster->v_logistic_partner_name : '' ).(!empty($logisticPartnerDetail->v_logistic_partner_code) ? ' - '.$logisticPartnerDetail->v_logistic_partner_code : '' ) }}</option>
		                                        		<?php 
		                                        	}
		                                        } 
		                                        ?>                                                                            
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-sm-6">
                                        <div class="form-group">
                                            <label for="select_containers" class="control-label">{{ trans("messages.select-containers") }}</label>
                                            <select name="select_containers" class="form-control select2" onchange="filterData()">
                                            	<option value="">{{ trans("messages.select") }}</option>
                                                <?php 
		                                        if(!empty($getCountryToPortGoodsOutDetails)){
		                                        	foreach ($getCountryToPortGoodsOutDetails as $getCountryToPortGoodsOutDetail){
		                                        		$encodeId = Wild_tiger::encode($getCountryToPortGoodsOutDetail->i_id);
		                                        		
		                                        		?>
		                                        		<option value="{{ $encodeId }}">{{ (!empty($getCountryToPortGoodsOutDetail->v_country_to_port_record_no) ? $getCountryToPortGoodsOutDetail->v_country_to_port_record_no .(!empty($getCountryToPortGoodsOutDetail->e_transport_way) ? ' (' .$getCountryToPortGoodsOutDetail->e_transport_way.')' : ''): '' ) }}</option>
		                                        		<?php 
		                                        	}
		                                        } 
		                                        ?> 
                                            </select>
                                        </div>
                                    </div>

                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="book_by">{{ trans("messages.book-by") }}</label>
                                        <select name="book_by" class="form-control select2" onchange="filterData()">
                                            <option value="">{{ trans("messages.select") }}</option>
                                            <?php 
	                                        if(!empty($userRecordDetails)){
	                                        	foreach ($userRecordDetails as $userRecordDetail){
	                                        		$encodevUserId  = Wild_tiger::encode($userRecordDetail->i_id);
	                                        		?>
	                                        		<option value="{{ $encodevUserId }}"><?php echo  (!empty($userRecordDetail->v_name) ? $userRecordDetail->v_name . (!empty($userRecordDetail->v_department) ?  ' ('. $userRecordDetail->v_department . ')' : '' ) : '' ) ?></option>
	                                        		<?php 
	                                        	}
	                                        } 
	                                        ?>                                                                         
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="container_discharged_from_date">{{ trans("messages.container-discharged-from-date") }}</label>
                                        <input type="text" name="container_discharged_from_date" class="form-control date-format" placeholder="{{ trans('messages.container-discharged-from-date') }}">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="container_discharged_to_date">{{ trans("messages.container-discharged-to-date") }}</label>
                                        <input type="text" name="container_discharged_to_date" class="form-control date-format" placeholder="{{ trans('messages.container-discharged-to-date') }}">
                                    </div>
                                </div>
                                                    
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="booking_from_date">{{ trans("messages.booking-from-date") }}</label>
                                        <input type="text" name="booking_from_date" class="form-control date-format" placeholder="{{ trans('messages.booking-from-date') }}">
                                    </div>
                                </div>
                                                        
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="booking_to_date">{{ trans("messages.booking-to-date") }}</label>
                                        <input type="text" name="booking_to_date" class="form-control date-format" placeholder="{{ trans('messages.booking-to-date') }}">
                                    </div>
                                </div>
                            
                            
                                
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="collection_from_date">{{ trans("messages.collection-from-date") }}</label>
                                        <input type="text" name="collection_from_date" class="form-control date-format" placeholder="{{ trans('messages.collection-from-date') }}">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="collection_to_date">{{ trans("messages.collection-to-date") }}</label>
                                        <input type="text" name="collection_to_date" class="form-control date-format" placeholder="{{ trans('messages.collection-to-date') }}">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="delivery_from_date">{{ trans("messages.delivery-from-date") }}</label>
                                        <input type="text" name="delivery_from_date" class="form-control date-format" placeholder="{{ trans('messages.delivery-from-date') }}">
                                    </div>
                                </div> 
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="delivery_to_date">{{ trans("messages.delivery-to-date") }}</label>
                                        <input type="text" name="delivery_to_date" class="form-control date-format" placeholder="{{ trans('messages.delivery-to-date') }}">
                                    </div>
                                </div> 
                                <div class="col-xl-4 col-lg-3 col-md-4 col-sm-6">
	                                <div class="form-group">
	                                    <label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
                                        <a href="javascript:void(0)" class="float-right remove-all-btn">{{ trans("messages.remove-all")}}</a>
	                                    <select class="form-control select2" multiple name="search_status" onchange="filterData()">
	                                        <?php 
										        if(!empty($statusMasterRecordDetails)){
										        	foreach ($statusMasterRecordDetails as $statusMasterRecordDetail){
										           		$encoderId  = Wild_tiger::encode($statusMasterRecordDetail->i_id);
										           		$selected = '';
										           		if((isset($statusInfo)) && (!in_array($statusMasterRecordDetail->i_id, $statusInfo))){
										           			$selected ="selected='selected'";
										           		}
										             	?>
										             	<option value="{{ $encoderId }}" {{ $selected }}>{{ (!empty($statusMasterRecordDetail->v_status) ? $statusMasterRecordDetail->v_status : '' ) }}</option>
										              	<?php 
										           	}
										        } 
										        ?>
	                                    </select>
	                                </div>
								</div>
                    
                                <div class="col-xl-2 col-md-4 d-flex align-items-end gap pt-md-3">
                                    <button type="button" title="{{ trans('messages.search') }}" class="btn btn-theme text-white" onclick="filterData();" >{{ trans("messages.search") }}</button>
									<button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers">{{ trans("messages.reset") }}</button>
                                </div>

                    </div>
                </div>    
        </div>

        <div class="filter-result-wrapper ajax-view">
				@include( config('constants.AJAX_VIEW_FOLDER') . 'port-to-agent-warehouse/good-out-port-agent-list')
		</div>

		</div>
    </div>
</section>


</main>
<script>
	$("form").validate({
		errorClass: "invalid-input",
		rules: {
			upload_file: {
				required: true
			}

		},
		messages: {
			upload_file: {
				required: "{{ trans('messages.require-upload-file') }}"
			}
		},
		submitHandler: function(form) {
			showLoader()
			form.submit();
		}
	});
</script>

<script>
$(document).ready(function() {
 	$("[name='container_discharged_from_date'],[name='container_discharged_to_date']").datetimepicker({
    	useCurrent: false,
        viewMode: 'days',
   		ignoreReadonly: true,
		widgetPositioning: {
			vertical: 'bottom'
		},
       	format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',
   	});
 	$("[name='booking_from_date'],[name='booking_to_date']").datetimepicker({
    	useCurrent: false,
        viewMode: 'days',
   		ignoreReadonly: true,
		widgetPositioning: {
			vertical: 'bottom'
		},
       	format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

   	});

 	$("[name='collection_from_date'],[name='collection_to_date']").datetimepicker({
    	useCurrent: false,
        viewMode: 'days',
   		ignoreReadonly: true,
		widgetPositioning: {
			vertical: 'bottom'
		},
       	format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

   	});

 	$("[name='delivery_from_date'],[name='delivery_to_date']").datetimepicker({
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
	$("[name='container_discharged_from_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
		var incrementDay = moment((e.date)).startOf('d');
	 	$("[name='container_discharged_to_date']").data('DateTimePicker').minDate(incrementDay);
        }else{
	 	$("[name='container_discharged_to_date']").data('DateTimePicker').minDate(false);
        }
	    $(this).data("DateTimePicker").hide();
	});

    $("[name='container_discharged_to_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
        var decrementDay = moment((e.date)).endOf('d');
        $("[name='container_discharged_from_date']").data('DateTimePicker').maxDate(decrementDay);
        }else{
        $("[name='container_discharged_from_date']").data('DateTimePicker').maxDate(false);
        }
        $(this).data("DateTimePicker").hide();
    });
    $("[name='booking_from_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
		var incrementDay = moment((e.date)).startOf('d');
	 	$("[name='booking_to_date']").data('DateTimePicker').minDate(incrementDay);
        }else{
	 	$("[name='booking_to_date']").data('DateTimePicker').minDate(false);
        }
	    $(this).data("DateTimePicker").hide();
	});

    $("[name='booking_to_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
        var decrementDay = moment((e.date)).endOf('d');
        $("[name='booking_from_date']").data('DateTimePicker').maxDate(decrementDay);
        }else{
        $("[name='booking_from_date']").data('DateTimePicker').maxDate(false);
        }
        $(this).data("DateTimePicker").hide();
    });	
    $("[name='collection_from_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
		var incrementDay = moment((e.date)).startOf('d');
	 	$("[name='collection_to_date']").data('DateTimePicker').minDate(incrementDay);
        }else{
	 	$("[name='collection_to_date']").data('DateTimePicker').minDate(false);
        }
	    $(this).data("DateTimePicker").hide();
	});

    $("[name='collection_to_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
        var decrementDay = moment((e.date)).endOf('d');
        $("[name='collection_from_date']").data('DateTimePicker').maxDate(decrementDay);
        }else{
        $("[name='collection_from_date']").data('DateTimePicker').maxDate(false);
        }
        $(this).data("DateTimePicker").hide();
    });

    $("[name='delivery_from_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
		var incrementDay = moment((e.date)).startOf('d');
	 	$("[name='delivery_to_date']").data('DateTimePicker').minDate(incrementDay);
        }else{
	 	$("[name='delivery_to_date']").data('DateTimePicker').minDate(false);
        }
	    $(this).data("DateTimePicker").hide();
	});

    $("[name='delivery_to_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
        var decrementDay = moment((e.date)).endOf('d');
        $("[name='delivery_from_date']").data('DateTimePicker').maxDate(decrementDay);
        }else{
        $("[name='delivery_from_date']").data('DateTimePicker').maxDate(false);
        }
        $(this).data("DateTimePicker").hide();
    });
});
		
 var good_out_port_to_agent_module_url = '{{config("constants.PORT_TO_AGENT_GOODS_OUT_MASTER_URL")}}' + '/';
 function searchField(){
 	var search_by_us_port_to_agent_warehouse = $.trim($('[name="search_by_us_port_to_agent_warehouse"]').val());
 	var search_way_of_transport = $.trim($('[name="way_of_transport"]').val());
 	var search_from_port_airport = $.trim($('[name="from_port_airport"]').val());
 	var search_logistic_partner = $.trim($('[name="logistic_partner"]').val());
 	var search_warehouse_type = $.trim($('[name="search_warehouse_type"]').val());
 	var search_from_warehouse_country = $.trim($('[name="search_from_warehouse_country"]').val());
 	var search_warehouse = $.trim($('[name="search_warehouse"]').val());
 	var search_to_own_location = $.trim($('[name="search_to_own_location"]').val());
 	var search_to_agent_location = $.trim($('[name="to_agent_location"]').val());
 	var search_select_containers = $.trim($('[name="select_containers"]').val());
 	var search_book_by = $.trim($('[name="book_by"]').val());
 	var container_discharged_from_date = $.trim($('[name="container_discharged_from_date"]').val());
 	var container_discharged_to_date = $.trim($('[name="container_discharged_to_date"]').val());
 	var booking_from_date = $.trim($('[name="booking_from_date"]').val());
 	var booking_to_date = $.trim($('[name="booking_to_date"]').val());
 	var collection_from_date = $.trim($('[name="collection_from_date"]').val());
 	var collection_to_date = $.trim($('[name="collection_to_date"]').val());
 	var delivery_from_date = $.trim($('[name="delivery_from_date"]').val());
 	var delivery_to_date = $.trim($('[name="delivery_to_date"]').val());
 	var search_status = $.trim($('[name="search_status"]').val());
 	
 	var searchData = {
 		 'search_by_us_port_to_agent_warehouse':search_by_us_port_to_agent_warehouse,
     	 'search_way_of_transport':search_way_of_transport,
         'search_from_port_airport': search_from_port_airport,
         'search_logistic_partner': search_logistic_partner,
         'search_warehouse_type': search_warehouse_type,
         'search_from_warehouse_country': search_from_warehouse_country,
	     'search_warehouse': search_warehouse,
         'search_to_own_location': search_to_own_location,
         'search_to_agent_location': search_to_agent_location,
         'search_select_containers': search_select_containers,
         'search_book_by': search_book_by,
         'container_discharged_from_date': container_discharged_from_date,
         'container_discharged_to_date': container_discharged_to_date,
         'booking_from_date': booking_from_date,
         'booking_to_date': booking_to_date,
         'collection_from_date': collection_from_date,
         'collection_to_date': collection_to_date,
         'delivery_from_date': delivery_from_date,
         'delivery_to_date': delivery_to_date,
         'search_status': search_status
        
 	}
     return searchData;
 }
 function filterData(){
 	var searchFieldName = searchField();
 	searchAjax(good_out_port_to_agent_module_url + 'filter' , searchFieldName);
 }
 
 var paginationUrl = good_out_port_to_agent_module_url + 'filter'
 
	function exportData(){
		var searchData = searchField();
		var export_info = {};
		export_info.url = good_out_port_to_agent_module_url + 'filter';
		export_info.searchData = searchData;
		dataExportIntoExcel(export_info);
	}
</script>
<script type="text/javascript" src="{{ asset ('js/twt_fixed_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>

@endsection