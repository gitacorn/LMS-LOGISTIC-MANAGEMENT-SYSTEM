@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
@include('admin/csv-import-model')	
<main class="page-height bg-light-color">
  <div class="breadcrumb-wrapper d-md-flex border-navabr align-items-center">
      <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }}</h1>
  </div>
  
  <section class="inner-wrapper-common-sections main-listing-section">
      <div class="container-fluid">
          <div class="card document-card mb-3">
              <ul class="document-items">
                  <li class="document-list"><a href="#details" class="document-text">{{ trans("messages.details") }}</a></li>
                  <li class="document-list"><a href="#documents" class="document-text">{{ trans("messages.documents") }}</a></li>
                  <li class="document-list"><a href="#transporter" class="document-text">{{ trans("messages.transporter-invoice") }}</a></li>
                  <li class="document-list"><a href="#status" class="document-text">{{ trans("messages.status") }}</a></li>
                  <?php if( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ) { ?>
  				<li class="document-text ml-auto logistic-master-no"><?php echo (!empty($recordInfo->v_country_to_port_europe_record_no) ? $recordInfo->v_country_to_port_europe_record_no : '')?></li>
  				<?php }?>
              </ul>
          </div>
          
          
          <div class="card mb-3 good-in-buyer-class" id="details">
              <h3 class="title-goods"><i class="fas fa-level-up-alt list-icon mr-2"></i>{{ trans("messages.details") }}</h3>
              {!! Form::open(array( 'id '=> 'add-to-amazon-form' , 'method' => 'post' , 'files' => true , 'url' => 'europe-to-amazon/add')) !!}
              	@if (count($errors) > 0)
  				    <div class="error">
  				        <ul>
  				            @foreach ($errors->all() as $error)
  				                <li>{{ $error }}</li>
  				            @endforeach
  				        </ul>
  				    </div>
  				@endif
                  <div class="card-body">
                      <div class="row">
                     
                          <div class="col-xl-2 col-md-4 col-sm-6">
                              <div class="form-group">
                                  <label class="control-label" for="way_of_transport">{{ trans("messages.way-of-transport") }}<span class="text-danger">*</span></label>
                                  <select name="way_of_transport" class="form-control" <?php echo $disableForm ?>> 
                                      <option value="">{{ trans("messages.select") }}</option>
                                      <?php 
                                     	if(!empty($wayOfTransportDetails)){
                                     		foreach ($wayOfTransportDetails as $key => $wayOfTransportDetail) {
                                     			$selected = '';
                                     			if( isset($recordInfo->e_transport_way) && ( $recordInfo->e_transport_way == $key) ){
                                     				$selected = "selected='selected'";
                                     			}
                                     			?>
                                     			<option value="{{ $key  }}" {{ $selected }}>{{ $wayOfTransportDetail }}</option>
                                     			<?php 
                                     		}
                                     	}
                                     	?>
                                  </select>
                              </div>
                          </div>
                          <div class="col-xl-3 col-md-4 col-sm-6">
                              <div class="form-group">
                                  <label for="book_by" class="control-label">{{ trans("messages.book-by") }}<span class="text-danger">*</span></label>
                                  <select name="book_by" class="form-control select2" <?php echo $disableForm ?>>
                                      <option value="">{{ trans("messages.select") }}</option>
                                      <?php 
                                      if(!empty($userRecordDetails)){
                                      	foreach ($userRecordDetails as $userRecordDetail){
                                      		$encodevUserId  = Wild_tiger::encode($userRecordDetail->i_id);
                                      		$selected = '';
                                      		if( isset($recordInfo->i_book_by_employee_id) && ( $recordInfo->i_book_by_employee_id == $userRecordDetail->i_id) ){
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
                          <div class="col-xl-3 col-md-4 col-sm-6">
                              <div class="form-group">
                                  <label class="control-label" for="logistic_partner">{{ trans("messages.logistic-partner") }}<span class="text-danger">*</span></label>
                                  <select name="logistic_partner" class="form-control select2" <?php echo $disableForm ?>>
                                      <option value="">{{ trans("messages.select") }}</option>
                                     <?php 
                                      if(!empty($logisticPartnerRecordDetails)){
                                      	foreach ($logisticPartnerRecordDetails as $logisticPartnerRecordDetail){
                                      		$encodeId  = Wild_tiger::encode($logisticPartnerRecordDetail->i_id);
                                      		$selected = '';
                                      		if( isset($recordInfo->i_logistic_partner_detail_id) && ( $recordInfo->i_logistic_partner_detail_id == $logisticPartnerRecordDetail->i_id) ){
                                      			$selected = "selected='selected'";
                                      		}
                                      		?>
                                      		<option value="{{ $encodeId }}" {{ $selected }}><?php echo (!empty($logisticPartnerRecordDetail->logisticPartnerMaster->v_logistic_partner_name) ? $logisticPartnerRecordDetail->logisticPartnerMaster->v_logistic_partner_name .(!empty($logisticPartnerRecordDetail->v_logistic_partner_code) ? ' (' .$logisticPartnerRecordDetail->v_logistic_partner_code .')' : '') :'') ?></option>
                                      		<?php 
                                      	}
                                      } 
                                      ?>
                                  </select>
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group pb-3 pt-3">
                                  <div class="card shadow-none border">
                                      <div class="card-header">
                                          <span class="partner-tilte">
                                              {{ trans("messages.shipment-details") }}
                                          </span>
                                          <?php if(!isset($recordInfo)) { ?>
  											<a href="javascript:void(0)"  class="btn btn-success border-0 float-right" data-shipment-type='{{ config("constants.AMAZON_FBA_SHEET") }}' onclick="openCSVImportModel(this)">{{ trans('messages.import') }}</a>
  										<?php } ?>
                                      </div>
                                      <div class="card-body logistic-partner">
                                          <div class="table-responsive">
                                              <table class="table table-hover table-bordered table-sm pb-4 logistic-europe-amazon">
                                                  <thead>
                                                      <tr class="text-center">
                                                          <th style="max-width:70px;min-width:70px;">{{ trans("messages.sr-no") }}</th>
                                                          <th style="max-width:160px;min-width:160px;">{{ trans("messages.workflow-id") }} <span class="text-danger">*</span></th>
                                                          <th style="max-width:160px;min-width:160px;">{{ trans("messages.shipment-id") }} <span class="text-danger">*</span></th>
                                                          <th style="max-width:160px;min-width:160px;">{{ trans("messages.ref-id") }} <span class="text-danger">*</span></th>
                                                          <th style="max-width:250px;min-width:200px;">{{ trans("messages.account") }} <span class="text-danger">*</span></th>
                                                          <th style="max-width:250px;min-width:200px;">{{ trans("messages.packing-warehouse") }} <span class="text-danger">*</span></th>
                                                          <th style="max-width:250px;min-width:200px;">{{ trans("messages.from-warehouse") }} <span class="text-danger">*</span></th>
                                                          <th style="max-width:250px;min-width:200px;">{{ trans("messages.to-amazon-location") }} <span class="text-danger">*</span></th>
                                                          <th style="max-width:250px;min-width:200px;">{{ trans("messages.to-country-delivery") }} <span class="text-danger">*</span></th>
                                                          <th style="width:110px;min-width:110px;">{{ trans("messages.sku") }} <span class="text-danger">*</span></th>
                                                          <th style="width:110px;min-width:110px;">{{ trans("messages.unit") }} <span class="text-danger">*</span></th>
                                                          <th style="width:230px;min-width:230px;">{{ trans("messages.shipment-value") }} <span class="text-danger">*</span></th>
                                                          <th style="width:110px;min-width:110px;">{{ trans("messages.weight") }} <span class="text-danger">*</span></th>
                                                          <th style="width:110px;min-width:110px;">{{ trans("messages.pallets-boxes") }} <span class="text-danger">*</span></th>
                                                          <th style="width:110px;min-width:110px;">{{ trans("messages.no-of-pallets-boxes") }}<span class="text-danger">*</span></th>
                                                          <th style="max-width:160px;min-width:160px;">{{ trans("messages.booking-date") }}</th>
                                                          <th style="max-width:160px;min-width:160px;">{{ trans("messages.collection-date") }}</th>
                                                          <th style="max-width:160px;min-width:160px;">{{ trans("messages.delivery-date") }}</th>
                                                          <th style="max-width:160px;min-width:160px;">{{ trans("messages.tracking-no") }}<span class="text-danger">*</span></th>
                                                          <th style="max-width:160px;min-width:160px;">{{ trans("messages.tracking-link") }}</th>
                                                          <th style="max-width:160px;min-width:160px;">{{ trans("messages.amazon-appointment-date") }}</th>
                                                          <th style="max-width:160px;min-width:160px;">{{ trans("messages.amazon-appointment-id") }}</th>
                                                          <th style="width:70px;min-width:70px;">{{ trans("messages.action") }}</th>
                                                      </tr>
                                                  </thead>
                                                  <tbody class="europe-to-amazon-tbody europe-amazon-tbody">
                                                  	<?php 
                                                  	if( isset($recordInfo->detailInfo) && (!empty($recordInfo->detailInfo)) && (count($recordInfo->detailInfo) > 0 ) ){
                                                          	foreach ($recordInfo->detailInfo as $countKey => $toAmazonDetail){
                                                          		$countIndex = ($countKey + 1 );
                                                  			?>
                                                  			<tr>
  	                                                            <td class="table-index text-center" style="width:70px;min-width:70px;">{{ $countIndex }}</td>
  	                                                            <td class="text-left"><input type="text" <?php echo $disableForm ?> class="form-control amazon-workflow-id-status" name="edit_workflow_id_<?php echo $toAmazonDetail->i_id ?>" value="<?php echo (isset($toAmazonDetail->v_workflow_id) ? $toAmazonDetail->v_workflow_id : '' ); ?>"></td>
  	                                                            <td class="text-left"><input type="text" <?php echo $disableForm ?> class="form-control amazon-shipment-id-status" name="edit_shipment_id_<?php echo $toAmazonDetail->i_id ?>" value="<?php echo (isset($toAmazonDetail->v_shipment_id) ? $toAmazonDetail->v_shipment_id : '' ); ?>" onchange="checkUniqueShipmentId(this)"></td>
  	                                                            <td class="text-left"><input type="text" <?php echo $disableForm ?> class="form-control amazon-ref-id-status" name="edit_ref_id_<?php echo $toAmazonDetail->i_id ?>" value="<?php echo (isset($toAmazonDetail->v_ref_id) ? $toAmazonDetail->v_ref_id : '' ); ?>"></td>
  	                                                            <td class="text-left">
  	                                                                <select name="edit_account_<?php echo $toAmazonDetail->i_id ?>" <?php echo $disableForm ?> class="form-control amazon-account-status">
  	                                                                    <option value="">{{ trans("messages.select") }}</option>
  	                                                                    <?php 
  	                                                                    if(!empty($comapnyMasterDetails)){
  	                                                                    	foreach ($comapnyMasterDetails as $comapnyMasterDetail){
  	                                                                    		$encodeId  = Wild_tiger::encode($comapnyMasterDetail->i_id);
  	                                                                    		$selected = '';
  	                                                                    		if( isset($toAmazonDetail->i_account_company_id) && ( $toAmazonDetail->i_account_company_id == $comapnyMasterDetail->i_id ) ){
  	                                                                    			$selected = "selected='selected'";
  	                                        									}
  	                                        									?>
  	                                        									<option value="{{ $encodeId }}" {{$selected }}>{{ (!empty($comapnyMasterDetail->v_company_name) ? $comapnyMasterDetail->v_company_name : '' ) }}</option>
  	                                        									<?php  
  	                                                                    	}
  	                                                                    }
  	                                                                    ?>
  	                                                                </select>
  	                                                            </td>
  																<td class="text-left">
  	                                                                <select name="edit_packing_warehouse_<?php echo $toAmazonDetail->i_id ?>" <?php echo $disableForm ?> class="form-control amazon-packing-warehouse-status">
  	                                                                    <option value="">{{ trans("messages.select") }}</option>
  	                                                                    <?php 
  	                                                                    if(!empty($warehouseMasterDetails)){
  	                                                                    	foreach ($warehouseMasterDetails as $warehouseMasterDetail){
  	                                                                    		$encodeId  = Wild_tiger::encode($warehouseMasterDetail->i_id);
  	                                                                    		$selected = '';
  	                                                                    		if( isset($toAmazonDetail->i_packing_warehouse_id) && ( $toAmazonDetail->i_packing_warehouse_id == $warehouseMasterDetail->i_id ) ){
  	                                                                    			$selected = "selected='selected'";
  	                                                                    		}
  	                                                                    		?>
  																				<option value="{{ $encodeId }}" {{ $selected }}>{{ (!empty($warehouseMasterDetail->v_warehouse_name) ? $warehouseMasterDetail->v_warehouse_name .(!empty($warehouseMasterDetail->v_warehouse_code) ? ' (' .$warehouseMasterDetail->v_warehouse_code .')' : '' ): '' ) }}</option>
                                                                  		        <?php  
                                                                  			}
                                                                  		}
  	                                                                    ?>
  																	</select>
  																</td>
  	                                                            <td class="text-left">
  	                                                                <select name="edit_from_warehouse_<?php echo $toAmazonDetail->i_id ?>" <?php echo $disableForm ?> class="form-control amazon-from-status">
  	                                                                    <option value="">{{ trans("messages.select") }}</option>
  	                                                                     <?php 
  	                                                                    if(!empty($warehouseMasterDetails)){
  	                                                                    	foreach ($warehouseMasterDetails as $warehouseMasterDetail){
  	                                                                    		$encodeId  = Wild_tiger::encode($warehouseMasterDetail->i_id);
  	                                                                    		$selected = '';
  	                                                                    		if( isset($toAmazonDetail->i_warehouse_id) && ( $toAmazonDetail->i_warehouse_id == $warehouseMasterDetail->i_id ) ){
  	                                                                    			$selected = "selected='selected'";
  	                                        									}
  	                                        									?>
  	                                        									<option value="{{ $encodeId }}" {{ $selected }}>{{ (!empty($warehouseMasterDetail->v_warehouse_name) ? $warehouseMasterDetail->v_warehouse_name .(!empty($warehouseMasterDetail->v_warehouse_code) ? ' (' .$warehouseMasterDetail->v_warehouse_code .')' : '' ) : '' ) }}</option>
  	                                        									<?php  
  	                                                                    	}
  	                                                                    }
  	                                                                    ?>
  	                                                                </select>
  	                                                            </td>
  	                                                            <td class="text-left">
  	                                                                <select name="edit_to_amazon_location_<?php echo $toAmazonDetail->i_id ?>" <?php echo $disableForm ?> class="form-control amazon-to-status select2">
  	                                                                    <option value="">{{ trans("messages.select") }}</option>
  	                                                                     <?php 
  	                                                                    if(!empty($locationMasterDetails)){
  	                                                                    	foreach ($locationMasterDetails as $locationMasterDetail){
  	                                                                    		$encodeId  = Wild_tiger::encode($locationMasterDetail->i_id);
  	                                                                    		$selected = '';
  	                                                                    		if( isset($toAmazonDetail->i_location_id) && ( $toAmazonDetail->i_location_id == $locationMasterDetail->i_id ) ){
  	                                                                    			$selected = "selected='selected'";
  	                                        									}
  	                                        									?>
  	                                        									<option value="{{ $encodeId }}" {{ $selected }}>{{ (!empty($locationMasterDetail->v_warehouse_name) ? $locationMasterDetail->v_warehouse_name .(!empty($locationMasterDetail->v_warehouse_code) ? ' (' .$locationMasterDetail->v_warehouse_code .')' : '' ): '' ) }}</option>
  	                                        									<?php  
  	                                                                    	}
  	                                                                    }
  	                                                                    ?>
  	                                                                </select>
  	                                                            </td>
  	                                                            
  	                                                            <td class="text-left">
  	                                                                <select name="edit_to_country_delivery_<?php echo $toAmazonDetail->i_id ?>" <?php echo $disableForm ?> class="form-control amazon-to-country-cell-status select2">
  	                                                                    <option value="">{{ trans("messages.select") }}</option>
  	                                                                     <?php 
  		                                                                    if(!empty($countryMasterDetails)){
  		                                                                    	foreach ($countryMasterDetails as $countryMasterDetail){
  		                                                                    		$encodeId = Wild_tiger::encode($countryMasterDetail->i_id);
  		                                                                    		$selected = '';
  		                                                                    		if( isset($toAmazonDetail->i_to_country_id) && ( $toAmazonDetail->i_to_country_id == $countryMasterDetail->i_id ) ){
  		                                                                    			$selected = "selected='selected'";
  		                                        									}
  		                                        									?>
  		                                        									<option value="{{ $encodeId }}" {{ $selected }}>{{ (!empty($countryMasterDetail->v_country_name) ? $countryMasterDetail->v_country_name : '') }}</option>
  		                                        									<?php
  		                                                                    	}
  		                                                                    }
  	                                                                    ?>
  	                                                                </select>
  	                                                            </td>
  	                                                            
  	                                                            <td class="text-left"><input type="text" <?php echo $disableForm ?> class="form-control amazon-sku-status" name="edit_sku_<?php echo $toAmazonDetail->i_id ?>" value="<?php echo (isset($toAmazonDetail->v_sku) && !empty($toAmazonDetail->v_sku) ? $toAmazonDetail->v_sku : '' ); ?>"></td>
  	                                                            <td class="text-left"><input type="text" <?php echo $disableForm ?> class="form-control amazon-unit-status" name="edit_unit_<?php echo $toAmazonDetail->i_id ?>" value="<?php echo (isset($toAmazonDetail->v_units) && !empty($toAmazonDetail->v_units) ? $toAmazonDetail->v_units : '' ); ?>"></td>
  	                                                            <td class="text-left">
  		                                                            <div class="d-flex align-items-center justify-content-center">
  		                                                            	<select name="edit_shipment_currency_<?php echo $toAmazonDetail->i_id ?>" class="form-control amazon-shipment-currency-status mr-2" <?php echo $disableForm ?> >
  		                                                                    <option value="">{{ trans("messages.select") }}</option>
  		                                                                    <?php 
  			                                                                    if(!empty($currencyRecordDetails)){
  			                                                                    	foreach ($currencyRecordDetails as $currencyRecordDetail){
  			                                                                    		$encodeId = Wild_tiger::encode($currencyRecordDetail->i_id);
  			                                                                    		$selected = '';
  			                                                                    		if( isset($toAmazonDetail->i_currency_id) && ( $toAmazonDetail->i_currency_id == $currencyRecordDetail->i_id ) ){
  			                                                                    			$selected = "selected='selected'";
  			                                        									}
  			                                        									?>
  			                                        									<option value="{{ $encodeId }}" {{ $selected }}>{{ (!empty($currencyRecordDetail->v_currency_code) ? $currencyRecordDetail->v_currency_code : '') }}</option>
  			                                        									<?php  
  			                                                                    	}
  			                                                                    }
  		                                                                    ?>
  		                                                                </select>
  		                                                            	<input type="text" <?php echo $disableForm ?> class="form-control amazon-shipment-value-status" name="edit_price_<?php echo $toAmazonDetail->i_id ?>" onkeyup="onlyDecimal(this)" onchange="onlyDecimal(this)" value="<?php echo (isset($toAmazonDetail->v_price) && !empty($toAmazonDetail->v_price) ? $toAmazonDetail->v_price : '' ); ?>">
  																	</div>
  	                                                            </td>
  	                                                            <td class="text-left"><input type="text" <?php echo $disableForm ?> class="form-control amazon-weight-status" name="edit_weight_<?php echo $toAmazonDetail->i_id ?>" value="<?php echo (isset($toAmazonDetail->d_weight) && !empty($toAmazonDetail->d_weight) && $toAmazonDetail->d_weight > 0 ? $toAmazonDetail->d_weight : '' ); ?>" onkeyup="onlyDecimal(this)"></td>
  	                                                            <td class="text-left">
  	                                                            	<select name="edit_pallets_boxes_type_<?php echo $toAmazonDetail->i_id ?>" <?php echo $disableForm ?> class="form-control amazon-pallet-box-type-status">
  	                                                                    <option value="">{{ trans("messages.select") }}</option>
                                              							<option value="{{ config('constants.BOX') }}" <?php echo ( !empty($toAmazonDetail->e_dimension) && $toAmazonDetail->e_dimension == config('constants.BOX') ? 'selected' : '' )?> >{{ trans("messages.box") }}</option>
  	                                                                    <option value="{{ config('constants.PALLET') }}" <?php echo ( !empty($toAmazonDetail->e_dimension) && $toAmazonDetail->e_dimension == config('constants.PALLET') ? 'selected' : '' )?> >{{ trans("messages.pallet") }}</option>
  	                                                                </select>
  	                                                            </td>
  	                                                            <td class="text-left"><input type="text" <?php echo $disableForm ?> class="form-control amazon-no-of-pallet-box-status" name="edit_no_of_pallets_boxes_<?php echo $toAmazonDetail->i_id ?>" value="<?php echo (isset($toAmazonDetail->i_no_of_pallet_box) && !empty($toAmazonDetail->i_no_of_pallet_box) ? $toAmazonDetail->i_no_of_pallet_box : '' ); ?>" onchange="naturalNumber(this)"></td>
  	                                                            <td class="text-left"><input type="text" name="edit_amazon_booking_date_{{ $toAmazonDetail->i_id }}" <?php echo $disableForm ?> class="form-control date-format" value="{{  (isset($toAmazonDetail->dt_booking_date) ? clientDate($toAmazonDetail->dt_booking_date) : '' ) }}"></td>
  							                                	<td class="text-left"><input type="text" <?php echo $disableForm ?> name="edit_amazon_collection_date_{{ $toAmazonDetail->i_id }}" class="form-control date-format collection-date" value="{{  (isset($toAmazonDetail->dt_collection_date) ? clientDate($toAmazonDetail->dt_collection_date) : '' ) }}"></td>
  							                               		<td class="text-left"><input type="text" <?php echo $disableForm ?> name="edit_amazon_delivery_date_{{ $toAmazonDetail->i_id }}" class="form-control date-format delivery-date" value="{{  (isset($toAmazonDetail->dt_delivery_date) ? clientDate($toAmazonDetail->dt_delivery_date) : '' ) }}"></td>
  							                               		<td class="text-left"><input type="text" name="edit_tracking_no_{{ $toAmazonDetail->i_id }}" {{ ( (isset($toAmazonDetail->i_id) && (!empty($toAmazonDetail->i_id)) ) ? 'disabled' : '' ) }} <?php echo $disableForm ?> class="form-control amazon-tracking-no-row" value="{{  (isset($recordInfo->v_tracking_no) ?  $recordInfo->v_tracking_no: '' ) }}"></td>
  								                                <td class="text-left"><input type="text" name="edit_tracking_link_{{ $toAmazonDetail->i_id }}" <?php echo $disableForm ?> class="form-control" value="{{  (isset($toAmazonDetail->v_tracking_link) ?  $toAmazonDetail->v_tracking_link: '' ) }}"></td>
  								                                <td class="text-left"><input type="text" name="edit_amazon_appointment_date_{{ $toAmazonDetail->i_id }}" <?php echo $disableForm ?> class="form-control date-format" value="{{  (isset($toAmazonDetail->dt_amazon_appointment_date) ? clientDate($toAmazonDetail->dt_amazon_appointment_date) : '' ) }}"></td>
  							                               		<td class="text-left"><input type="text" name="edit_amazon_appointment_id_{{ $toAmazonDetail->i_id }}" <?php echo $disableForm ?> class="form-control" value="{{  (isset($toAmazonDetail->v_amazon_appointment_id) ?  $toAmazonDetail->v_amazon_appointment_id: '' ) }}"></td>
  							                               		<td style="width:70px;min-width:70px;">
  	                                                            <?php if(empty($disableForm)){?>
  	                                                            <button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this, 'europe-to-amazon-tbody')"><i class="fa fa-trash fa-fw"></i></button></td>
  	                                                            <?php } ?>
  	                                                        </tr>
                                                  			<?php 
                                                  		}
                                                  	} else {
                                                  		?>
                                                          <tr>
                                                              <td class="table-index text-center" style="width:70px;min-width:70px;">1</td>
                                                              <td class="text-left"><input type="text" <?php echo $disableForm ?> class="form-control amazon-workflow-id-status" name="workflow_id_1"></td>
                                                              <td class="text-left"><input type="text" <?php echo $disableForm ?> class="form-control amazon-shipment-id-status" name="shipment_id_1" onchange="checkUniqueShipmentId(this)"></td>
                                                              <td class="text-left"><input type="text" <?php echo $disableForm ?> class="form-control amazon-ref-id-status" name="ref_id_1"></td>
                                                              <td class="text-left">
                                                                  <select name="account_1" class="form-control amazon-account-status" <?php echo $disableForm ?> >
                                                                      <option value="">{{ trans("messages.select") }}</option>
                                                                      <?php 
                                                                      if(!empty($comapnyMasterDetails)){
                                                                      	foreach ($comapnyMasterDetails as $comapnyMasterDetail){
                                                                      		$encodeId  = Wild_tiger::encode($comapnyMasterDetail->i_id);
                                          									?>
                                          									<option value="{{ $encodeId }}">{{ (!empty($comapnyMasterDetail->v_company_name) ? $comapnyMasterDetail->v_company_name : '' ) }}</option>
                                          									<?php  
                                                                      	}
                                                                      }
                                                                      ?>
                                                                  </select>
                                                              </td>
                                                              <td class="text-left">
                                                                  <select name="packing_warehouse_1" class="form-control amazon-packing-warehouse-status" <?php echo $disableForm ?> >
                                                                      <option value="">{{ trans("messages.select") }}</option>
                                                                      <?php 
                                                                      if(!empty($warehouseMasterDetails)){
                                                                      	foreach ($warehouseMasterDetails as $warehouseMasterDetail){
                                                                      		$encodeId  = Wild_tiger::encode($warehouseMasterDetail->i_id);
                                                                      		?>
  																			<option value="{{ $encodeId }}">{{ (!empty($warehouseMasterDetail->v_warehouse_name) ? $warehouseMasterDetail->v_warehouse_name .(!empty($warehouseMasterDetail->v_warehouse_code) ? ' (' .$warehouseMasterDetail->v_warehouse_code .')' : '' ): '' ) }}</option>
                                                                  			<?php  
                                                                  		}
                                                                  	}
  	                                                                ?>
  																</select>
  															</td>
                                                              <td class="text-left">
                                                                  <select name="from_warehouse_1" class="form-control amazon-from-status" <?php echo $disableForm ?> >
                                                                      <option value="">{{ trans("messages.select") }}</option>
                                                                      <?php 
                                                                      if(!empty($warehouseMasterDetails)){
                                                                      	foreach ($warehouseMasterDetails as $warehouseMasterDetail){
                                                                      		$encodeId  = Wild_tiger::encode($warehouseMasterDetail->i_id);
                                          									?>
                                          									<option value="{{ $encodeId }}">{{ (!empty($warehouseMasterDetail->v_warehouse_name) ? $warehouseMasterDetail->v_warehouse_name .(!empty($warehouseMasterDetail->v_warehouse_code) ? ' (' .$warehouseMasterDetail->v_warehouse_code .')' : '' ) : '' ) }}</option>
                                          									<?php  
                                                                      	}
                                                                      }
                                                                      ?>

                                                                  </select>
                                                              </td>
                                                              <td class="text-left">
                                                                  <select name="to_amazon_location_1" class="form-control amazon-to-status select2" <?php echo $disableForm ?> >
                                                                      <option value="">{{ trans("messages.select") }}</option>
                                                                      <?php 
                                                                      if(!empty($locationMasterDetails)){
                                                                      	foreach ($locationMasterDetails as $locationMasterDetail){
                                                                      		$encodeId  = Wild_tiger::encode($locationMasterDetail->i_id);
                                          									?>
                                          									<option value="{{ $encodeId }}">{{ (!empty($locationMasterDetail->v_warehouse_name) ? $locationMasterDetail->v_warehouse_name .(!empty($locationMasterDetail->v_warehouse_code) ? ' (' .$locationMasterDetail->v_warehouse_code .')' : '' ): '' ) }}</option>
                                          									<?php  
                                                                      	}
                                                                      }
                                                                      ?>
                                                                  </select>
                                                              </td>
                                                              
                                                              <td class="text-left">
                                                                  <select name="to_country_delivery_1" class="form-control amazon-to-country-cell-status select2" <?php echo $disableForm ?> >
                                                                      <option value="">{{ trans("messages.select") }}</option>
                                                                      <?php 
  	                                                                    if(!empty($countryMasterDetails)){
  	                                                                    	foreach ($countryMasterDetails as $countryMasterDetail){
  	                                                                    		$encodeId = Wild_tiger::encode($countryMasterDetail->i_id);
  	                                        									?>
  	                                        									<option value="{{ $encodeId }}">{{ (!empty($countryMasterDetail->v_country_name) ? $countryMasterDetail->v_country_name : '') }}</option>
  	                                        									<?php  
  	                                                                    	}
  	                                                                    }
                                                                      ?>
                                                                  </select>
                                                              </td>
                                                              
                                                              <td class="text-left"><input type="text" class="form-control amazon-sku-status" name="sku_1" <?php echo $disableForm ?>></td>
                                                              <td class="text-left"><input type="text" class="form-control amazon-unit-status" name="unit_1" <?php echo $disableForm ?>></td>
                                                              <td class="text-left">
                                                              	<div class="d-flex align-items-center justify-content-center">
  	                                                            	<select name="shipment_currency_1" class="form-control amazon-shipment-currency-status mr-2" <?php echo $disableForm ?> >
  	                                                                    <option value="">{{ trans("messages.select") }}</option>
  	                                                                    <?php 
  		                                                                    if(!empty($currencyRecordDetails)){
  		                                                                    	foreach ($currencyRecordDetails as $currencyRecordDetail){
  		                                                                    		$encodeId = Wild_tiger::encode($currencyRecordDetail->i_id);
  		                                        									?>
  		                                        									<option value="{{ $encodeId }}">{{ (!empty($currencyRecordDetail->v_currency_code) ? $currencyRecordDetail->v_currency_code : '') }}</option>
  		                                        									<?php  
  		                                                                    	}
  		                                                                    }
  	                                                                    ?>
  	                                                                </select>
  	                                                            	<input type="text" class="form-control amazon-shipment-value-status" name="price_1" onkeyup="onlyDecimal(this)" onchange="onlyDecimal(this)" <?php echo $disableForm ?>>
                                                              	</div>
                                                              </td>
                                                              <td class="text-left"><input type="text" class="form-control amazon-weight-status" name="weight_1" <?php echo $disableForm ?> onkeyup="onlyDecimal(this)"></td>
                                                              <td class="text-left">
                                                              	<select name="pallets_boxes_type_1" <?php echo $disableForm ?> class="form-control amazon-pallet-box-type-status">
                                                                      <option value="">{{ trans("messages.select") }}</option>
                                                                      <option value="{{ config('constants.BOX') }}" >{{ trans("messages.box") }}</option>
                                                                      <option value="{{ config('constants.PALLET') }}" >{{ trans("messages.pallet") }}</option>
                                                                  </select>
                                                              </td>
                                                              <td class="text-left"><input type="text" class="form-control amazon-no-of-pallet-box-status" name="no_of_pallets_boxes_1" <?php echo $disableForm ?> onchange="naturalNumber(this)"></td>
                                                              <td class="text-left"><input type="text" name="amazon_booking_date_1" <?php echo $disableForm ?> class="form-control date-format"></td>
  						                                	<td class="text-left"><input type="text" <?php echo $disableForm ?> name="amazon_collection_date_1" class="form-control date-format"></td>
  						                               		<td class="text-left"><input type="text" <?php echo $disableForm ?> name="amazon_delivery_date_1" class="form-control date-format"></td>
  						                               		<td class="text-left"><input type="text" name="amazon_tracking_no_1" <?php echo $disableForm ?> class="form-control amazon-tracking-no-row"></td>
  							                                <td class="text-left"><input type="text" name="amazon_tracking_link_1" <?php echo $disableForm ?> class="form-control"></td>
  							                                <td class="text-left"><input type="text" name="amazon_appointment_date_1" <?php echo $disableForm ?> class="form-control date-format"></td>
  						                               		<td class="text-left"><input type="text" name="amazon_appointment_id_1" <?php echo $disableForm ?> class="form-control"></td>
                                                              <td style="width:70px;min-width:70px;"></td>
                                                          </tr>
                                                          <tr>
                                                              <td class="table-index text-center" style="width:70px;min-width:70px;">2</td>
                                                              <td class="text-left"><input type="text" class="form-control amazon-workflow-id-status" <?php echo $disableForm ?> name="workflow_id_2"></td>
                                                              <td class="text-left"><input type="text" class="form-control amazon-shipment-id-status" <?php echo $disableForm ?> name="shipment_id_2" onchange="checkUniqueShipmentId(this)"></td>
                                                              <td class="text-left"><input type="text" class="form-control amazon-ref-id-status" <?php echo $disableForm ?> name="ref_id_2"></td>
                                                              <td class="text-left">
                                                                  <select name="account_2" class="form-control amazon-account-status" <?php echo $disableForm ?> >
                                                                      <option value="">{{ trans("messages.select") }}</option>
                                                                      <?php 
                                                                      if(!empty($comapnyMasterDetails)){
                                                                      	foreach ($comapnyMasterDetails as $comapnyMasterDetail){
                                                                      		$encodeId  = Wild_tiger::encode($comapnyMasterDetail->i_id);
                                          									?>
                                          									<option value="{{ $encodeId }}">{{ (!empty($comapnyMasterDetail->v_company_name) ? $comapnyMasterDetail->v_company_name : '' ) }}</option>
                                          									<?php  
                                                                      	}
                                                                      }
                                                                      ?>
                                                                  </select>
                                                              </td>
                                                              <td class="text-left">
                                                                  <select name="packing_warehouse_2" class="form-control amazon-packing-warehouse-status" <?php echo $disableForm ?> >
                                                                      <option value="">{{ trans("messages.select") }}</option>
                                                                      <?php 
                                                                      if(!empty($warehouseMasterDetails)){
                                                                      	foreach ($warehouseMasterDetails as $warehouseMasterDetail){
                                                                      		$encodeId  = Wild_tiger::encode($warehouseMasterDetail->i_id);
                                                                      		?>
  																			<option value="{{ $encodeId }}">{{ (!empty($warehouseMasterDetail->v_warehouse_name) ? $warehouseMasterDetail->v_warehouse_name .(!empty($warehouseMasterDetail->v_warehouse_code) ? ' (' .$warehouseMasterDetail->v_warehouse_code .')' : '' ): '' ) }}</option>
                                                                  			<?php  
                                                                  		}
                                                                  	}
  	                                                                ?>
  																</select>
  															</td>
                                                              <td class="text-left">
                                                                  <select name="from_warehouse_2" class="form-control amazon-from-status" <?php echo $disableForm ?> >
                                                                      <option value="">{{ trans("messages.select") }}</option>
                                                                       <?php 
                                                                      if(!empty($warehouseMasterDetails)){
                                                                      	foreach ($warehouseMasterDetails as $warehouseMasterDetail){
                                                                      		$encodeId  = Wild_tiger::encode($warehouseMasterDetail->i_id);
                                          									?>
                                          									<option value="{{ $encodeId }}">{{ (!empty($warehouseMasterDetail->v_warehouse_name) ? $warehouseMasterDetail->v_warehouse_name .(!empty($warehouseMasterDetail->v_warehouse_code) ? ' (' .$warehouseMasterDetail->v_warehouse_code .')' : '' ) : '' ) }}</option>
                                          									<?php  
                                                                      	}
                                                                      }
                                                                      ?>
                                                                  </select>
                                                              </td>
                                                              <td class="text-left">
                                                                  <select name="to_amazon_location_2" class="form-control amazon-to-status select2" <?php echo $disableForm ?> >
                                                                      <option value="">{{ trans("messages.select") }}</option>
                                                                       <?php 
                                                                      if(!empty($locationMasterDetails)){
                                                                      	foreach ($locationMasterDetails as $locationMasterDetail){
                                                                      		$encodeId  = Wild_tiger::encode($locationMasterDetail->i_id);
                                          									?>
                                          									<option value="{{ $encodeId }}">{{ (!empty($locationMasterDetail->v_warehouse_name) ? $locationMasterDetail->v_warehouse_name .(!empty($locationMasterDetail->v_warehouse_code) ? ' (' .$locationMasterDetail->v_warehouse_code .')' : '' ) : '' ) }}</option>
                                          									<?php  
                                                                      	}
                                                                      }
                                                                      ?>
                                                                  </select>
                                                              </td>
                                                              
                                                              <td class="text-left">
                                                                  <select name="to_country_delivery_2" class="form-control amazon-to-country-cell-status select2" <?php echo $disableForm ?> >
                                                                      <option value="">{{ trans("messages.select") }}</option>
                                                                      <?php 
  	                                                                    if(!empty($countryMasterDetails)){
  	                                                                    	foreach ($countryMasterDetails as $countryMasterDetail){
  	                                                                    		$encodeId = Wild_tiger::encode($countryMasterDetail->i_id);
  	                                        									?>
  	                                        									<option value="{{ $encodeId }}">{{ (!empty($countryMasterDetail->v_country_name) ? $countryMasterDetail->v_country_name : '') }}</option>
  	                                        									<?php  
  	                                                                    	}
  	                                                                    }
                                                                      ?>
                                                                  </select>
                                                              </td>
                                                              
                                                              <td class="text-left"><input type="text" <?php echo $disableForm ?> class="form-control amazon-sku-status" name="sku_2"></td>
                                                              <td class="text-left"><input type="text" <?php echo $disableForm ?> class="form-control amazon-unit-status" name="unit_2"></td>
                                                              <td class="text-left">
  	                                                            <div class="d-flex align-items-center justify-content-center">
  	                                                            	<select name="shipment_currency_2" class="form-control amazon-shipment-currency-status mr-2" <?php echo $disableForm ?> >
  	                                                                    <option value="">{{ trans("messages.select") }}</option>
  	                                                                    <?php 
  		                                                                    if(!empty($currencyRecordDetails)){
  		                                                                    	foreach ($currencyRecordDetails as $currencyRecordDetail){
  		                                                                    		$encodeId = Wild_tiger::encode($currencyRecordDetail->i_id);
  		                                        									?>
  		                                        									<option value="{{ $encodeId }}">{{ (!empty($currencyRecordDetail->v_currency_code) ? $currencyRecordDetail->v_currency_code : '') }}</option>
  		                                        									<?php  
  		                                                                    	}
  		                                                                    }
  	                                                                    ?>
  	                                                                </select>
  	                                                            	<input type="text" class="form-control amazon-shipment-value-status" name="price_2" onkeyup="onlyDecimal(this)" onchange="onlyDecimal(this)" <?php echo $disableForm ?>>
  																</div>
                                                              </td>
                                                              <td class="text-left"><input type="text" class="form-control amazon-weight-status" name="weight_2" <?php echo $disableForm ?> onkeyup="onlyDecimal(this)"></td>
                                                              <td class="text-left">
                                                              	<select name="pallets_boxes_type_2" <?php echo $disableForm ?> class="form-control amazon-pallet-box-type-status">
                                                                      <option value="">{{ trans("messages.select") }}</option>
                                                                      <option value="{{ config('constants.BOX') }}" >{{ trans("messages.box") }}</option>
                                                                      <option value="{{ config('constants.PALLET') }}" >{{ trans("messages.pallet") }}</option>
                                                                  </select>
                                                              </td>
                                                              <td class="text-left"><input type="text" class="form-control amazon-no-of-pallet-box-status" name="no_of_pallets_boxes_2" <?php echo $disableForm ?> onchange="naturalNumber(this)"></td>
                                                              <td class="text-left"><input type="text" name="amazon_booking_date_2" <?php echo $disableForm ?> class="form-control date-format"></td>
  						                                	<td class="text-left"><input type="text" <?php echo $disableForm ?> name="amazon_collection_date_2" class="form-control date-format"></td>
  						                               		<td class="text-left"><input type="text" <?php echo $disableForm ?> name="amazon_delivery_date_2" class="form-control date-format"></td>
  						                               		<td class="text-left"><input type="text" name="amazon_tracking_no_2" <?php echo $disableForm ?> class="form-control amazon-tracking-no-row"></td>
  							                                <td class="text-left"><input type="text" name="amazon_tracking_link_2" <?php echo $disableForm ?> class="form-control"></td>
  							                                <td class="text-left"><input type="text" name="amazon_appointment_date_2" <?php echo $disableForm ?> class="form-control date-format"></td>
  						                               		<td class="text-left"><input type="text" name="amazon_appointment_id_2" <?php echo $disableForm ?> class="form-control"></td>
  						                               		
                                                              <td style="width:70px;min-width:70px;">
                                                              <?php if(empty($disableForm)){?>
                                                              <button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this, 'europe-to-amazon-tbody')"><i class="fa fa-trash fa-fw"></i></button></td>
                                                              <?php } ?>
                                                          </tr>
                                                          <?php 
                                                      	}?>
                                                  </tbody>
                                              </table>
                                              <?php if(empty($disableForm)){?>
                                              	<button type="button" class="btn btn-sm bg-theme text-white add-new-row mb-3" title="{{ trans('messages.add-new') }}" onclick="addNewShipmentRow(this)"><i class="fa fa-plus fa-fw"></i>{{ trans("messages.add-new") }}</button>
                                             <?php }?>
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
                                                      <tbody class="agent-to-warehouse-document-tbody">
                                                      	<?php
                                                      	if( isset($recordInfo->documentInfo) && (!empty($recordInfo->documentInfo)) && (count($recordInfo->documentInfo) > 0 ) ){
                                                      		foreach ($recordInfo->documentInfo as $countKey => $documentDetail){
                                                      			$columIndex  = ( $countKey +  1 );
                                                      			?>
                                                          		<tr>
  	                                                                <td class="table-index text-center" style="width:70px;min-width:70px;">{{ $columIndex }}</td>
  	                                                                <td class="text-left">
  	                                                                    <select name="edit_type_<?php echo $documentDetail->i_id ?>" <?php echo $documentForm ?> class="form-control warehouse-document-type">
  	                                                                        <option value="">{{ trans("messages.select") }}</option>
  	                                                                        <?php 
  									                                        if(!empty($documentTypeRecordDetails)){
  									                                        	foreach ($documentTypeRecordDetails as $documentTypeRecordDetail){
  									                                        		$encodevDocumentTypeId  = Wild_tiger::encode($documentTypeRecordDetail->i_id);
  									                                        		$selected = '';
  									                                        		if( isset($documentDetail->i_document_type_id) && ( $documentDetail->i_document_type_id == $documentTypeRecordDetail->i_id ) ){
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
  	                                                                 <?php $documentFiles = (json_decode($documentDetail->v_document_file_path)); ?>
  	                                                                <td class="text-left">
  	                                                                    <div class="custom-file">
  	                                                                        <input type="file" <?php echo $documentForm ?> class="custom-file-input warehouse-document-file" id="document_<?php echo $documentDetail->i_id ?>" name="edit_file_<?php echo $documentDetail->i_id ?>[]" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
  	                                                                        <label class="custom-file-label" for="document_<?php echo $documentDetail->i_id ?>"><?php echo (!empty($documentFiles) ? ( count($documentFiles) > 1 ? trans('messages.multiple-files') : ( isset($documentFiles[0]) ? basename($documentFiles[0]) : '' ) )  : trans('messages.choose-file') ) ?></label>
  	                                                                    </div>
  	                                                                </td>
  	                                                                <td class="text-left">
  	                                                                    <input type="text" <?php echo $documentForm ?>  class="form-control" name="edit_remarks_<?php echo $documentDetail->i_id ?>" value="<?php echo (isset($documentDetail->v_document_remark) ? $documentDetail->v_document_remark : '' ); ?>">
  	                                                                </td>
  	                                                                <td class="actions-col">
  	                                                                   <?php 
  	                                                                	if(!empty($documentFiles)){
  		                                                                	foreach ($documentFiles as $documentFile){
  		                                                                		$imagePath = (config('constants.FILE_STORAGE_URL_PATH').$documentFile);
  		                                                                	?>
  		                                                                	<div class="download-link-items">
  			                                                                  <?php if(empty($documentForm)){?>
  			                                                                  <a title="{{trans('messages.remove')}}" href="javascript:void(0);" data-file-name="{{ basename($imagePath) }}" onclick="removeUploadedFile(this);" data-record-id="{{ $documentDetail->i_id }}" data-field-name="document" class="close-icon"><i class="fa fa-times "></i></a>
  			                                                                  <?php } ?>
  			                                                                  <a title="{{ basename($imagePath) }}" href="{{ $imagePath }}" target="_blank" class="btn btn-sm btn-danger mb-1 download-icon-items"><i class="fa fa-download"></i></a>
  			                                                                </div>
  	                                                                    	<?php 
  		                                                                	}
  	                                                                	}?> 
  	                                                                </td>
  	
  	                                                                <td style="width:70px;min-width:70px;">
  	                                                                 <?php if(empty($documentForm)){?>
  	                                                                <button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button>
  	                                                                <?php } ?>
  	                                                                </td>
  	                                                       		</tr>
                                                      		<?php 
                                                      		}
                                                      	} else {
                                                      	?>
                                                          	<tr>
                                                              	<td class="table-index text-center" style="width:70px;min-width:70px;">1</td>
                                                                  <td class="text-left">
                                                                      <select name="type_1" class="form-control warehouse-document-type" <?php echo $documentForm ?> >
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
                                                                          <input type="file" class="custom-file-input warehouse-document-file"  <?php echo $documentForm ?> id="document_1" name="file_1[]" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
                                                                          <label class="custom-file-label" for="document_1">{{ trans("messages.choose-file") }}</label>
                                                                      </div>
                                                                  </td>
                                                                  <td class="text-left">
                                                                      <input type="text" class="form-control" name="remarks_1" <?php echo $documentForm ?> >
                                                                  </td>
                                                                  <td class="actions-col">
                                                                      
                                                                  </td>
  															<td style="width:70px;min-width:70px;"></td>
                                                              </tr>
                                                              <tr>
                                                                  <td class="table-index text-center" style="width:70px;min-width:70px;">2</td>
                                                                  <td class="text-left">
                                                                      <select name="type_2" class="form-control warehouse-document-type" <?php echo $documentForm ?> >
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
                                                                          <input type="file" class="custom-file-input warehouse-document-file"  <?php echo $documentForm ?> id="document_2" name="file_2[]" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
                                                                          <label class="custom-file-label" for="document_2">{{ trans('messages.choose-file') }}</label>
                                                                      </div>
                                                                  </td>
                                                                  <td class="text-left">
                                                                      <input type="text" class="form-control" name="remarks_2" <?php echo $documentForm ?> >
                                                                  </td>
                                                                  <td class="actions-col">
                                                                     
                                                                  </td>
  
                                                                  <td style="width:70px;min-width:70px;">
                                                                  <?php if(empty($documentForm)){?>
                                                                  <button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button>
                                                                  <?php } ?>
                                                                  </td>
                                                                  
                                                              </tr>
                                                          <?php }?>
                                                      </tbody>
                                                  </table>

  												
                                                  <?php if(empty($documentForm)){?>
                                                   	<button type="button" class="btn btn-sm bg-theme text-white add-new-row" title="{{ trans('messages.add-new') }}" onclick="addNewDocumentRow(this)"><i class="fa fa-plus fa-fw"></i>{{ trans("messages.add-new") }}</button>
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
                                                              <th class="text-left" style="width:100px;min-width:100px;">{{ trans("messages.total-logistic-cost-gbp") }}</th>
                                                              <th class="text-left" style="width:250px;min-width:250px;">{{ trans("messages.attach-documents") }}</th>
                                                              <th class="text-center" style="width:80px;min-width:80px;">{{ trans("messages.documents") }}</th>
                                                          </tr>
                                                      </thead>
                                                      <tbody class="agent-to-warehouse-transport-tbody">
                                                      	<?php 
                                                      	if( isset($recordInfo->invoiceInfo) && (!empty($recordInfo->invoiceInfo)) && (count($recordInfo->invoiceInfo) > 0 ) ){
                                                          	foreach ($recordInfo->invoiceInfo as $countKey => $transportInvoiceDetail){
                                                          		$countIndex = ($countKey + 1 );
                                                      			?>
                                                      			<tr>
  	                                                                <td class="table-index">{{$countIndex}}</td>
  	                                                                <td class="text-left">
  	                                                                	<select name="edit_name_<?php echo $transportInvoiceDetail->i_id ?>" <?php echo $documentForm ?> class="form-control agent-warehouse-transporter-name select2">
  									                                        <option value="">{{ trans("messages.select") }}</option>
  									                                        <?php 
  									                                        if(!empty($logisticPartnerDetails)){
  									                                        	foreach ($logisticPartnerDetails as $logisticPartnerDetail){
  									                                        		$encodeLogisticPartnerId = Wild_tiger::encode($logisticPartnerDetail->i_id);
  									                                        		$selected = '';
  									                                        		if( isset($transportInvoiceDetail->i_logistic_partner_master_id) && ( $transportInvoiceDetail->i_logistic_partner_master_id == $logisticPartnerDetail->i_id ) ){
  									                                        			$selected = "selected='selected'";
  									                                        		}
  									                                        		?>
  									                                        		<option value="{{ $encodeLogisticPartnerId }}" {{ $selected }}><?php echo  (!empty($logisticPartnerDetail->v_logistic_partner_name) ? $logisticPartnerDetail->v_logistic_partner_name : '' ) ?></option>
  									                                        		<?php 
  									                                        	}
  									                                        }
  									                                        ?>
  									                                    </select>
  	                                                                </td>
  	                                                                <td class="text-left">
  	                                                                    <input type="text" <?php echo $documentForm ?> class="form-control agent-warehouse-transporter-inv-no" name="edit_inv_no_<?php echo $transportInvoiceDetail->i_id ?>" placeholder="{{ trans('messages.inv-no') }}" value="<?php echo (isset($transportInvoiceDetail->v_invoice_no) ? $transportInvoiceDetail->v_invoice_no : '' ); ?>">
  	                                                                </td>
  	                                                                <td class="text-left">
  	                                                                    <input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-freight" name="edit_freight_<?php echo $transportInvoiceDetail->i_id ?>" placeholder="{{ trans('messages.freight') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($transportInvoiceDetail->d_freight_charge) ? $transportInvoiceDetail->d_freight_charge : '' ); ?>">
  	                                                                </td>
  	                                                                <td class="text-left">
  	                                                                    <input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-custom" name="edit_custom_<?php echo $transportInvoiceDetail->i_id ?>" placeholder="{{ trans('messages.custom') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($transportInvoiceDetail->d_custom_charge) ? $transportInvoiceDetail->d_custom_charge : '' ); ?>">
  	                                                                </td>
  	                                                                <td class="text-left">
  	                                                                    <input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-duty" name="edit_duty_<?php echo $transportInvoiceDetail->i_id ?>" placeholder="{{ trans('messages.duty') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($transportInvoiceDetail->d_duty_charge) ? $transportInvoiceDetail->d_duty_charge : '' ); ?>">
  	                                                                </td>
  	                                                                <td class="text-left">
  	                                                                    <input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-other" name="edit_other_<?php echo $transportInvoiceDetail->i_id ?>" placeholder="{{ trans('messages.other') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($transportInvoiceDetail->d_other_charge) ? $transportInvoiceDetail->d_other_charge : '' ); ?>">
  	                                                                </td>
  	                                                                <td class="text-left">
  	                                                                    <input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-vat" name="edit_vat_<?php echo $transportInvoiceDetail->i_id ?>" placeholder="{{ trans('messages.vat') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($transportInvoiceDetail->d_vat_charge) ? $transportInvoiceDetail->d_vat_charge : '' ); ?>">
  	                                                                </td>
  	                                                                <td class="text-left">
  	                                                                    <div class="input-group align-items-center flex-nowrap">
  	                                                                        <label class="mb-0" for=""><span class="agent-warehouse-total-value"><?php echo (isset($transportInvoiceDetail->d_total_charge) ? $transportInvoiceDetail->d_total_charge :'')?></span></label>
  	                                                                        <div class="input-group-prepend">
  	                                                                            <select class="form-control ml-2" <?php echo $documentForm ?> name="edit_currency_id_<?php echo $transportInvoiceDetail->i_id ?>">
  	                                                                                <option selected value="">{{trans('messages.currency')}}</option>
  	                                                                               <?php 
  											                                        if(!empty($currencyRecordDetails)){
  											                                        	foreach ($currencyRecordDetails as $currencyRecordDetail){
  											                                        		$encodeCurrencyrId  = Wild_tiger::encode($currencyRecordDetail->i_id);
  											                                        		$selected = '';
  											                                        		if( isset($transportInvoiceDetail->i_invoice_currency_id) && ( $transportInvoiceDetail->i_invoice_currency_id == $currencyRecordDetail->i_id ) ){
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
  	                                                                <td class="text-left">
  	                                                                    <input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-con-rate" name="edit_cov_rate_<?php echo $transportInvoiceDetail->i_id ?>" placeholder="{{ trans('messages.cov-rate') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($transportInvoiceDetail->d_conversion_rate) ? $transportInvoiceDetail->d_conversion_rate : '' ); ?>">
  	                                                                </td>
  	                                                                <td class="text-left"><span class="agent-warehouse-final-rate"><?php echo (isset($transportInvoiceDetail->d_final_charge) ? $transportInvoiceDetail->d_final_charge : '' ); ?></span></td>
  	                                                                <?php $invoiceFiles = (json_decode($transportInvoiceDetail->v_invoice_file_path)); ?>	
  	                                                                <td class="text-left">
  	                                                                    <div class="custom-file">
  	                                                                        <input type="file" class="custom-file-input" <?php echo $documentForm ?> id="invoice_<?php echo $transportInvoiceDetail->i_id ?>" name="edit_invoice_file_<?php echo $transportInvoiceDetail->i_id ?>[]" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
  	                                                                        <label class="custom-file-label" for="invoice_<?php echo $transportInvoiceDetail->i_id ?>"><?php echo (!empty($invoiceFiles) ? ( count($invoiceFiles) > 1 ? trans('messages.multiple-files') : ( isset($invoiceFiles[0]) ? basename($invoiceFiles[0]) : '' ) )  : trans('messages.choose-file') ) ?></label>
  	                                                                    </div>
  	                                                                </td>
  	                                                                <td class="actions-col">
  	                                                                   <?php 
  	                                                                	if(!empty($invoiceFiles)){
  		                                                                	foreach ($invoiceFiles as $invoiceFile){
  		                                                                		$invoicePath = (config('constants.FILE_STORAGE_URL_PATH').$invoiceFile);
  		                                                                	?>
  		                                                                	<div class="download-link-items">
  	                                                                  			<?php if(empty($documentForm)){ ?>
  	                                                                  			<a title="{{trans('messages.remove')}}"  href="javascript:void(0);" data-file-name="{{ basename($invoicePath) }}" onclick="removeUploadedFile(this);" data-record-id="{{ $transportInvoiceDetail->i_id }}" data-field-name="invoice"  class="close-icon"><i class="fa fa-times "></i></a>
  	                                                                  			<?php } ?>
  	                                                                  			<a title="{{ basename($invoicePath) }}" href="{{ $invoicePath }}" target="_blank" class="btn btn-sm btn-danger mb-1 download-icon-items"><i class="fa fa-download"></i></a>
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
  	                                                             	<select name="name_1" class="form-control agent-warehouse-transporter-name select2" <?php echo $documentForm ?>>
  								                                        <option value="">{{ trans("messages.select") }}</option>
  								                                        <?php 
  								                                        if(!empty($logisticPartnerDetails)){
  								                                        	foreach ($logisticPartnerDetails as $logisticPartnerDetail){
  								                                        		$encodeLogisticPartnerId = Wild_tiger::encode($logisticPartnerDetail->i_id);
  								                                        		?>
  								                                        		<option value="{{ $encodeLogisticPartnerId }}"><?php echo  (!empty($logisticPartnerDetail->v_logistic_partner_name) ? $logisticPartnerDetail->v_logistic_partner_name : '' ) ?></option>
  								                                        		<?php 
  								                                        	}
  								                                        }
  								                                        ?>
  								                                    </select>
  	                                                             </td>
                                                                  <td class="text-left">
                                                                      <input type="text" <?php echo $documentForm ?> class="form-control agent-warehouse-transporter-inv-no" name="inv_no_1" placeholder="{{ trans('messages.inv-no') }}">
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
                                                                              <select class="form-control ml-2" name="currency_id_1" <?php echo $documentForm ?>>
                                                                                  <option selected value="">{{trans('messages.currency')}}</option>
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
                                                                      <input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-con-rate" name="cov_rate_1" placeholder="{{ trans('messages.cov-rate') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
                                                                  </td>
                                                                  <td class="text-left"><span class="agent-warehouse-final-rate"></span></td>
                                                                  <td class="text-left">
                                                                      <div class="custom-file">
                                                                          <input type="file" <?php echo $documentForm ?> class="custom-file-input" id="invoice_1" name="invoice_file_1[]" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
                                                                          <label class="custom-file-label" for="invoice_1">{{ trans('messages.choose-file') }}</label>
                                                                      </div>
                                                                  </td>
                                                                  <td class="actions-col">
                                                                      
                                                                  </td>
  	                                                         </tr>
  	
  	                                                         <tr>
                                                                  <td class="table-index">2</td>
                                                                  <td class="text-left">
                                                                  <select name="name_2" class="form-control agent-warehouse-transporter-name select2" <?php echo $documentForm ?>>
  								                                        <option value="">{{ trans("messages.select") }}</option>
  								                                        <?php 
  								                                        if(!empty($logisticPartnerDetails)){
  								                                        	foreach ($logisticPartnerDetails as $logisticPartnerDetail){
  								                                        		$encodeLogisticPartnerId = Wild_tiger::encode($logisticPartnerDetail->i_id);
  								                                        		?>
  								                                        		<option value="{{ $encodeLogisticPartnerId }}"><?php echo  (!empty($logisticPartnerDetail->v_logistic_partner_name) ? $logisticPartnerDetail->v_logistic_partner_name : '' ) ?></option>
  								                                        		<?php 
  								                                        	}
  								                                        }
  								                                        ?>
  								                                    </select>
                                                                  </td>
                                                                  <td class="text-left">
                                                                      <input type="text" <?php echo $documentForm ?> class="form-control agent-warehouse-transporter-inv-no" name="inv_no_2" placeholder="{{ trans('messages.inv-no') }}">
                                                                  </td>
                                                                  <td class="text-left">
                                                                      <input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-freight" name="freight_2" placeholder="{{ trans('messages.freight') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
                                                                  </td>
                                                                  <td class="text-left">
                                                                      <input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-custom" name="custom_2" placeholder="{{ trans('messages.custom') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
                                                                  </td>
                                                                  <td class="text-left">
                                                                      <input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-duty" name="duty_2" placeholder="{{ trans('messages.duty') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
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
                                                                              <select class="form-control ml-2" name="currency_id_2" <?php echo $documentForm ?>>
                                                                                  <option selected value="">{{trans('messages.currency')}}</option>
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
                                                                      <input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-con-rate" name="cov_rate_2" placeholder="{{ trans('messages.cov-rate') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
                                                                  </td>
                                                                  <td class="text-left"><span class="agent-warehouse-final-rate"></span></td>
                                                                  <td class="text-left">
                                                                      <div class="custom-file">
                                                                          <input type="file" <?php echo $documentForm ?> class="custom-file-input" id="invoice_2" name="invoice_file_2[]" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
                                                                          <label class="custom-file-label" for="invoice_2">{{ trans('messages.choose-file') }}</label>
                                                                      </div>
                                                                  </td>
                                                                  <td class="actions-col">
                                                                     
                                                                  </td>
                                                              </tr>
                                                          <?php 
                                                      	}?>
                                                      </tbody>
                                                  </table>
                                                  <?php if(empty($documentForm)){?>
                                                  	<button type="button" class="btn btn-sm bg-theme text-white add-new-row" title="{{ trans('messages.add-new') }}" onclick="addNewInvoiceRow(this)"><i class="fa fa-plus fa-fw"></i>{{ trans("messages.add-new") }}</button>
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
                                      <?php ## view time a disbled aavu joi and role admin hoi to disabled na avu joi ana mate ni conditon .?>
  									<select name="status" class="form-control" {{ $statusDisableForm }}>
                                          <option value="">{{ trans("messages.select") }}</option>
                                          <?php 
  								        if(!empty($statusMasterRecordDetails)){
  								        	foreach ($statusMasterRecordDetails as $statusMasterRecordDetail){
  								           		$encoderId  = Wild_tiger::encode($statusMasterRecordDetail->i_id);
  								           		$selected = '';
  								           		if( isset($recordInfo->i_status_id) && ( $recordInfo->i_status_id == $statusMasterRecordDetail->i_id) ){
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
                                      <input type="text" name="status_comments" <?php echo $documentForm ?> class="form-control" placeholder="{{ trans('messages.status-comments') }}" value="{{old('status_comments',  ( (isset($recordInfo) && (!empty($recordInfo->v_status_comment))) ?  $recordInfo->v_status_comment : '' ) )}}">
                                  </div>
                              </div>

                          </div>
                      </div>
                  </div>
                  <div class="col-md-12 submit-sticky">
                  <?php //if(empty($documentForm)){?>
                       <?php if( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ) { ?>
                      		<input type="hidden" name="record_id" value="{{ Wild_tiger::encode($recordInfo->i_id) }}">
                      		<?php ## view time a updatebutton na aavu joi and role admin hoi to avu joi ana mate ni conditon .?>
                      		<?php if(empty($statusDisableForm)){?>
  	                      			<button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.update') }}">{{ trans("messages.update") }}</button>
  	                      	<?php } ?>
                          
                       <?php } else {?>
                          		<button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.submit') }}">{{ trans("messages.submit") }}</button>
                       <?php }?>
                  <?php //}?>
                      <a href="{{config('constants.EUROPE_TO_AMAZON_MASTER_URL')}}" class="btn btn-outline-secondary shadow-sm btn-wide" title="{{ trans("messages.cancel") }}">{{ trans("messages.cancel") }}</a>
                  </div>
                  <input type="hidden" name="europe_to_amazon_shipment_count" value="">
                  <input type="hidden" name="europe_to_amazon_document_type_count" value="">
                  <input type="hidden" name="europe_to_amazon_transporter_count" value="">
               {!! Form::close() !!}
          </div>
      </div>
  </section>
  
</main>

<style>
  .ui-datepicker { z-index: 99999 !important; }        /* jQuery UI datepicker */
  .datepicker-dropdown { z-index: 99999 !important; }  /* Bootstrap datepicker */
</style>

<script>
// Initialize global variables
var unique_shipment_id = true;
var agent_warehouse_document_type_count = <?php echo isset($recordInfo->documentInfo) ? count($recordInfo->documentInfo) + 1 : 2; ?>;
var agent_warehouse_transporter_count = <?php echo isset($recordInfo->invoiceInfo) ? count($recordInfo->invoiceInfo) + 1 : 2; ?>;
var europe_to_amazon_shipment_count = <?php echo isset($recordInfo->detailInfo) ? count($recordInfo->detailInfo) + 1 : 2; ?>;

function initDatepickers(context) {
  var $scope = context ? $(context) : $(document);

  // If jQuery UI Datepicker is present
  if (window.jQuery && $.ui && $.ui.datepicker) {
    $scope.find('.date-format').each(function () {
      try { $(this).datepicker('destroy'); } catch (e) {}
      $(this).datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true
      });
    });
    return;
  }

  // If Bootstrap Datepicker is present
  if (window.jQuery && $.fn && $.fn.datepicker) {
    $scope.find('.date-format').each(function () {
      try { $(this).datepicker('destroy'); } catch (e) {}
      $(this).datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayHighlight: true
      });
    });
    return;
  }

  // No datepicker loaded
  console.warn('No datepicker library found. Please include jQuery UI Datepicker OR Bootstrap Datepicker (but not both).');
}

$(document).ready(function() {
  // Form validation
  $("#add-to-amazon-form").validate({
      errorClass: "invalid-input",
      rules: {
          way_of_transport: {
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
          status: {
              required: true,
              noSpace:true
          },
      },
      messages: {
          way_of_transport: {
              required: "{{ trans('messages.require-way-of-transport') }}"
          },
          book_by: {
              required: "{{ trans('messages.require-book-by') }}"
          },
          logistic_partner: {
              required: "{{ trans('messages.require-logistic-partner') }}"
          },
          status: {
              required: "{{ trans('messages.require-status') }}"
          },
      },
      submitHandler: function(form) {
          // Set hidden field values before submission
          $('input[name="europe_to_amazon_shipment_count"]').val(europe_to_amazon_shipment_count);
          $('input[name="europe_to_amazon_document_type_count"]').val(agent_warehouse_document_type_count);
          $('input[name="europe_to_amazon_transporter_count"]').val(agent_warehouse_transporter_count);
          
          if (unique_shipment_id) {
              // Show confirmation dialog before submitting
              var confirm_box = "";
              var confirm_box_msg = "";
              <?php if( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ) { ?>
                  confirm_box = "{{ trans('messages.update-to-amazon') }}";
                  confirm_box_msg = "{{ trans ( 'messages.confirm-to-amazon-update-msg') }}";
              <?php }else{?>
                  confirm_box = "{{ trans('messages.add-to-amazon') }}";
                  confirm_box_msg = "{{ trans ( 'messages.confirm-to-amazon-add-msg') }}";
              <?php } ?>
              alertify.confirm(confirm_box, confirm_box_msg, function() {
                  $('input:disabled').prop('disabled', false);
                  $('select:disabled').prop('disabled', false);
                  // Assuming showLoader() is defined elsewhere to show a loading indicator
                  // showLoader(); 
                  form.submit();
              }, function() {
                  // User cancelled, do nothing
              });
          } else {
              alertifyMessage("error", "{{ trans('messages.shipment-id-already-exists') }}");
              return false;
          }
      }
  });

  // IMPORTANT: Ensure that the Bootstrap Datepicker or jQuery UI Datepicker library
  // (and its dependencies like jQuery) is properly included in your project's layout file
  // (e.g., resources/views/includes/header.blade.php or resources/views/includes/footer.blade.php).
  // Without the library, the .datepicker() function will not be defined.
  
  initDatepickers();

  // Initialize select2
  $('.select2').select2();

  // --- Ensure "Type" dropdowns are populated everywhere ---
window.getDocumentTypeOptionsTemplate = function () {
  var $source = $('.warehouse-document-type').filter(function () {
    return $(this).find('option').length > 1; // has real options beyond "Select"
  }).first();
  return $source.length ? $source.html() : '';
};

window.hydrateEmptyDocumentTypeSelects = function (context) {
  var template = window.getDocumentTypeOptionsTemplate();
  if (!template) return; // nothing to copy from

  var $scope = context ? $(context) : $(document);
  $scope.find('.warehouse-document-type').each(function () {
    var $sel = $(this);
    if ($sel.find('option').length <= 1) {
      var current = $sel.val() || '';
      $sel.html(template);
      if (current) $sel.val(current); // preserve value if any
    }
  });
};

// Initial hydration on page load
window.hydrateEmptyDocumentTypeSelects();
});

// Function to add new shipment row
function addNewShipmentRow(element) {
  var tableBody = $('.europe-to-amazon-tbody');
  var newRowIndex = europe_to_amazon_shipment_count + 1; // Increment before using for new row
  
  var newRow = `
      <tr>
          <td class="table-index text-center" style="width:70px;min-width:70px;">${newRowIndex}</td>
          <td class="text-left"><input type="text" class="form-control amazon-workflow-id-status" name="workflow_id_${newRowIndex}"></td>
          <td class="text-left"><input type="text" class="form-control amazon-shipment-id-status" name="shipment_id_${newRowIndex}" onchange="checkUniqueShipmentId(this)"></td>
          <td class="text-left"><input type="text" class="form-control amazon-ref-id-status" name="ref_id_${newRowIndex}"></td>
          <td class="text-left">
              <select name="account_${newRowIndex}" class="form-control amazon-account-status">
                  <option value="">{{ trans("messages.select") }}</option>
                  <?php 
                  if(!empty($comapnyMasterDetails)){
                      foreach ($comapnyMasterDetails as $comapnyMasterDetail){
                          $encodeId  = Wild_tiger::encode($comapnyMasterDetail->i_id);
                          ?>
                          <option value="{{ $encodeId }}">{{ (!empty($comapnyMasterDetail->v_company_name) ? $comapnyMasterDetail->v_company_name : '' ) }}</option>
                          <?php  
                      }
                  }
                  ?>
              </select>
          </td>
          <td class="text-left">
              <select name="packing_warehouse_${newRowIndex}" class="form-control amazon-packing-warehouse-status">
                  <option value="">{{ trans("messages.select") }}</option>
                  <?php 
                  if(!empty($warehouseMasterDetails)){
                      foreach ($warehouseMasterDetails as $warehouseMasterDetail){
                          $encodeId  = Wild_tiger::encode($warehouseMasterDetail->i_id);
                          ?>
                          <option value="{{ $encodeId }}">{{ (!empty($warehouseMasterDetail->v_warehouse_name) ? $warehouseMasterDetail->v_warehouse_name .(!empty($warehouseMasterDetail->v_warehouse_code) ? ' (' .$warehouseMasterDetail->v_warehouse_code .')' : '' ): '' ) }}</option>
                          <?php  
                      }
                  }
                  ?>
              </select>
          </td>
          <td class="text-left">
              <select name="from_warehouse_${newRowIndex}" class="form-control amazon-from-status">
                  <option value="">{{ trans("messages.select") }}</option>
                  <?php 
                  if(!empty($warehouseMasterDetails)){
                      foreach ($warehouseMasterDetails as $warehouseMasterDetail){
                          $encodeId  = Wild_tiger::encode($warehouseMasterDetail->i_id);
                          ?>
                          <option value="{{ $encodeId }}">{{ (!empty($warehouseMasterDetail->v_warehouse_name) ? $warehouseMasterDetail->v_warehouse_name .(!empty($warehouseMasterDetail->v_warehouse_code) ? ' (' .$warehouseMasterDetail->v_warehouse_code .')' : '' ) : '' ) }}</option>
                          <?php  
                      }
                  }
                  ?>
              </select>
          </td>
          <td class="text-left">
              <select name="to_amazon_location_${newRowIndex}" class="form-control amazon-to-status select2">
                  <option value="">{{ trans("messages.select") }}</option>
                  <?php 
                  if(!empty($locationMasterDetails)){
                      foreach ($locationMasterDetails as $locationMasterDetail){
                          $encodeId  = Wild_tiger::encode($locationMasterDetail->i_id);
                          ?>
                          <option value="{{ $encodeId }}">{{ (!empty($locationMasterDetail->v_warehouse_name) ? $locationMasterDetail->v_warehouse_name .(!empty($locationMasterDetail->v_warehouse_code) ? ' (' .$warehouseMasterDetail->v_warehouse_code .')' : '' ): '' ) }}</option>
                          <?php  
                      }
                  }
                  ?>
              </select>
          </td>
          <td class="text-left">
              <select name="to_country_delivery_${newRowIndex}" class="form-control amazon-to-country-cell-status select2">
                  <option value="">{{ trans("messages.select") }}</option>
                  <?php 
                  if(!empty($countryMasterDetails)){
                      foreach ($countryMasterDetails as $countryMasterDetail){
                          $encodeId = Wild_tiger::encode($countryMasterDetail->i_id);
                          ?>
                          <option value="{{ $encodeId }}">{{ (!empty($countryMasterDetail->v_country_name) ? $countryMasterDetail->v_country_name : '') }}</option>
                          <?php  
                      }
                  }
                  ?>
              </select>
          </td>
          <td class="text-left"><input type="text" class="form-control amazon-sku-status" name="sku_${newRowIndex}"></td>
          <td class="text-left"><input type="text" class="form-control amazon-unit-status" name="unit_${newRowIndex}"></td>
          <td class="text-left">
              <div class="d-flex align-items-center justify-content-center">
                  <select name="shipment_currency_${newRowIndex}" class="form-control amazon-shipment-currency-status mr-2">
                      <option value="">{{ trans("messages.select") }}</option>
                      <?php 
                      if(!empty($currencyRecordDetails)){
                          foreach ($currencyRecordDetails as $currencyRecordDetail){
                              ?>
                              <option value="{{ $encodeId }}">{{ (!empty($currencyRecordDetail->v_currency_code) ? $currencyRecordDetail->v_currency_code : '') }}</option>
                              <?php  
                          }
                      }
                      ?>
                  </select>
                  <input type="text" class="form-control amazon-shipment-value-status" name="price_${newRowIndex}" onkeyup="onlyDecimal(this)" onchange="onlyDecimal(this)">
              </div>
          </td>
          <td class="text-left"><input type="text" class="form-control amazon-weight-status" name="weight_${newRowIndex}" onkeyup="onlyDecimal(this)"></td>
          <td class="text-left">
              <select name="pallets_boxes_type_${newRowIndex}" class="form-control amazon-pallet-box-type-status">
                  <option value="">{{ trans("messages.select") }}</option>
                  <option value="{{ config('constants.BOX') }}">{{ trans("messages.box") }}</option>
                  <option value="{{ config('constants.PALLET') }}">{{ trans("messages.pallet") }}</option>
              </select>
          </td>
          <td class="text-left"><input type="text" class="form-control amazon-no-of-pallet-box-status" name="no_of_pallets_boxes_${newRowIndex}" onchange="naturalNumber(this)"></td>
          <td class="text-left"><input type="text" name="amazon_booking_date_${newRowIndex}" class="form-control date-format"></td>
          <td class="text-left"><input type="text" name="amazon_collection_date_${newRowIndex}" class="form-control date-format"></td>
          <td class="text-left"><input type="text" name="amazon_delivery_date_${newRowIndex}" class="form-control date-format"></td>
          <td class="text-left"><input type="text" name="amazon_tracking_no_${newRowIndex}" class="form-control amazon-tracking-no-row"></td>
          <td class="text-left"><input type="text" name="amazon_tracking_link_${newRowIndex}" class="form-control"></td>
          <td class="text-left"><input type="text" name="amazon_appointment_date_${newRowIndex}" class="form-control date-format"></td>
          <td class="text-left"><input type="text" name="amazon_appointment_id_${newRowIndex}" class="form-control"></td>
          <td style="width:70px;min-width:70px;">
              <button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this, 'europe-to-amazon-tbody')"><i class="fa fa-trash fa-fw"></i></button>
          </td>
      </tr>
  `;
  
  tableBody.append(newRow);
  europe_to_amazon_shipment_count++;
  
  // Initialize datepicker(s) for the appended row (supports jQuery UI or Bootstrap)
  initDatepickers(tableBody);

  // Initialize select2 for new row
  tableBody.find('.select2').last().select2();
}

// Function to add new document row
function addNewDocumentRow(element) {
  var tableBody = $('.agent-to-warehouse-document-tbody');
  var newRowIndex = agent_warehouse_document_type_count + 1; // Increment before using for new row
  
  var newRow = `
      <tr>
          <td class="table-index text-center" style="width:70px;min-width:70px;">${newRowIndex}</td>
          <td class="text-left">
              <select name="type_${newRowIndex}" class="form-control warehouse-document-type">
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
                  <input type="file" class="custom-file-input warehouse-document-file" id="document_${newRowIndex}" name="file_${newRowIndex}[]" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
                  <label class="custom-file-label" for="document_${newRowIndex}">{{ trans("messages.choose-file") }}</label>
              </div>
          </td>
          <td class="text-left">
              <input type="text" class="form-control" name="remarks_${newRowIndex}">
          </td>
          <td class="actions-col"></td>
          <td style="width:70px;min-width:70px;">
              <button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button>
          </td>
      </tr>
  `;
  
  tableBody.append(newRow);
  hydrateEmptyDocumentTypeSelects(tableBody);
  agent_warehouse_document_type_count++;
}

// Function to add new invoice row
function addNewInvoiceRow(element) {
  var tableBody = $('.agent-to-warehouse-transport-tbody');
  var newRowIndex = agent_warehouse_transporter_count + 1; // Increment before using for new row
  
  var newRow = `
      <tr>
          <td class="table-index">${newRowIndex}</td>
          <td class="text-left">
              <select name="name_${newRowIndex}" class="form-control agent-warehouse-transporter-name select2">
                  <option value="">{{ trans("messages.select") }}</option>
                  <?php 
                  if(!empty($logisticPartnerDetails)){
                      foreach ($logisticPartnerDetails as $logisticPartnerDetail){
                          $encodeLogisticPartnerId = Wild_tiger::encode($logisticPartnerDetail->i_id);
                          ?>
                          <option value="{{ $encodeLogisticPartnerId }}"><?php echo  (!empty($logisticPartnerDetail->v_logistic_partner_name) ? $logisticPartnerDetail->v_logistic_partner_name : '' ) ?></option>
                          <?php 
                      }
                  }
                  ?>
              </select>
          </td>
          <td class="text-left">
              <input type="text" class="form-control agent-warehouse-transporter-inv-no" name="inv_no_${newRowIndex}" placeholder="{{ trans('messages.inv-no') }}">
          </td>
          <td class="text-left">
              <input type="text" class="form-control agent-to-warehouse-freight" name="freight_${newRowIndex}" placeholder="{{ trans('messages.freight') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
          </td>
          <td class="text-left">
              <input type="text" class="form-control agent-to-warehouse-custom" name="custom_${newRowIndex}" placeholder="{{ trans('messages.custom') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
          </td>
          <td class="text-left">
              <input type="text" class="form-control agent-to-warehouse-duty" name="duty_${newRowIndex}" placeholder="{{ trans('messages.duty') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
          </td>
          <td class="text-left">
              <input type="text" class="form-control agent-to-warehouse-other" name="other_${newRowIndex}" placeholder="{{ trans('messages.other') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
          </td>
          <td class="text-left">
              <input type="text" class="form-control agent-to-warehouse-vat" name="vat_${newRowIndex}" placeholder="{{ trans('messages.vat') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
          </td>
          <td class="text-left">
              <div class="input-group align-items-center flex-nowrap">
                  <label class="mb-0" for=""><span class="agent-warehouse-total-value"></span></label>
                  <div class="input-group-prepend">
                      <select class="form-control ml-2" name="currency_id_${newRowIndex}">
                          <option selected value="">{{trans('messages.currency')}}</option>
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
              <input type="text" class="form-control agent-to-warehouse-con-rate" name="cov_rate_${newRowIndex}" placeholder="{{ trans('messages.cov-rate') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
          </td>
          <td class="text-left"><span class="agent-warehouse-final-rate"></span></td>
          <td class="text-left">
              <div class="custom-file">
                  <input type="file" class="custom-file-input" id="invoice_${newRowIndex}" name="invoice_file_${newRowIndex}[]" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
                  <label class="custom-file-label" for="invoice_${newRowIndex}">{{ trans('messages.choose-file') }}</label>
              </div>
          </td>
          <td class="actions-col"></td>
      </tr>
  `;
  
  tableBody.append(newRow);
  agent_warehouse_transporter_count++;
  
  // Initialize select2 for new row
  tableBody.find('.select2').last().select2();
}

// Function to remove table record
function removeLogisticTableRrecord(element, tableBodyClass) {
  if (tableBodyClass) {
      var tableBody = $('.' + tableBodyClass);
      var rowCount = tableBody.find('tr').length;
      
      if (rowCount > 2) {
          $(element).closest('tr').remove();
          // Update row indices
          tableBody.find('tr').each(function(index) {
              $(this).find('.table-index').text(index + 1);
          });
      } else {
          alertifyMessage("error", "{{ trans('messages.minimum-two-rows-required') }}");
      }
  } else {
      var tableBody = $(element).closest('tbody');
      var rowCount = tableBody.find('tr').length;
      
      if (rowCount > 2) {
          $(element).closest('tr').remove();
          // Update row indices
          tableBody.find('tr').each(function(index) {
              $(this).find('.table-index').text(index + 1);
          });
      } else {
          alertifyMessage("error", "{{ trans('messages.minimum-two-rows-required') }}");
      }
  }
}

// Function to check unique shipment ID
function checkUniqueShipmentId(element) {
  var shipmentId = $(element).val();
  if (shipmentId) {
      $.ajax({
          url: "{{ url('europe-to-amazon/check-unique-shipment-id') }}",
          type: 'POST',
          data: {
              shipment_id: shipmentId,
              record_id: "{{ isset($recordInfo) ? Wild_tiger::encode($recordInfo->i_id) : '' }}",
              _token: "{{ csrf_token() }}"
          },
          success: function(response) {
              if (response.exists) {
                  unique_shipment_id = false;
                  $(element).addClass('is-invalid');
                  $(element).after('<div class="invalid-feedback">{{ trans("messages.shipment-id-already-exists") }}</div>');
              } else {
                  unique_shipment_id = true;
                  $(element).removeClass('is-invalid');
                  $(element).next('.invalid-feedback').remove();
              }
          }
      });
  }
}

// Function to validate file
function validFile(input, allowedTypes) {
  var files = input.files;
  var allowedExtensions = allowedTypes.split('_');
  var validFiles = [];
  var invalidFiles = [];
  
  for (var i = 0; i < files.length; i++) {
      var file = files[i];
      var fileName = file.name;
      var fileExtension = fileName.split('.').pop().toLowerCase();
      
      if (allowedExtensions.includes(fileExtension)) {
          validFiles.push(fileName);
      } else {
          invalidFiles.push(fileName);
      }
  }
  
  if (invalidFiles.length > 0) {
      alertifyMessage("error", "{{ trans('messages.invalid-file-format') }}: " + invalidFiles.join(', '));
      input.value = '';
      $(input).next('.custom-file-label').text("{{ trans('messages.choose-file') }}");
      return false;
  }
  
  // Update label with file names
  if (validFiles.length > 1) {
      $(input).next('.custom-file-label').text("{{ trans('messages.multiple-files') }}");
  } else if (validFiles.length === 1) {
      $(input).next('.custom-file-label').text(validFiles[0]);
  }
  
  return true;
}

// Function to calculate total values
function getTotalNumberOfValue(element) {
  var row = $(element).closest('tr');
  var freight = parseFloat(row.find('.agent-to-warehouse-freight').val()) || 0;
  var custom = parseFloat(row.find('.agent-to-warehouse-custom').val()) || 0;
  var duty = parseFloat(row.find('.agent-to-warehouse-duty').val()) || 0;
  var other = parseFloat(row.find('.agent-to-warehouse-other').val()) || 0;
  var vat = parseFloat(row.find('.agent-to-warehouse-vat').val()) || 0;
  var conversionRate = parseFloat(row.find('.agent-to-warehouse-con-rate').val()) || 0;
  
  var total = freight + custom + duty + other + vat;
  var finalTotal = total * conversionRate;
  
  row.find('.agent-warehouse-total-value').text(total.toFixed(2));
  row.find('.agent-warehouse-final-rate').text(finalTotal.toFixed(2));
}

// Function to allow only decimal numbers
function onlyDecimal(input) {
  var value = input.value;
  var regex = /^\d*\.?\d*$/;
  
  if (!regex.test(value)) {
      input.value = value.slice(0, -1);
  }
}

// Function to allow only natural numbers
function naturalNumber(input) {
  var value = input.value;
  var regex = /^\d+$/;
  
  if (!regex.test(value)) {
      input.value = value.replace(/[^\d]/g, '');
  }
}

// Function to remove uploaded file
function removeUploadedFile(element) {
  var fileName = $(element).data('file-name');
  var recordId = $(element).data('record-id');
  var fieldName = $(element).data('field-name');
  
  alertify.confirm("{{ trans('messages.confirm-delete') }}", "{{ trans('messages.are-you-sure-delete') }}", function() {
      $.ajax({
          url: "{{ url('europe-to-amazon/remove-file') }}",
          type: 'POST',
          data: {
              file_name: fileName,
              record_id: recordId,
              fieldName: fieldName,
              _token: "{{ csrf_token() }}"
          },
          success: function(response) {
              if (response.success) {
                  $(element).closest('.download-link-items').remove();
                  alertifyMessage("success", "{{ trans('messages.file-removed-successfully') }}");
              } else {
                  alertifyMessage("error", "{{ trans('messages.error-removing-file') }}");
              }
          },
          error: function() {
              alertifyMessage("error", "{{ trans('messages.error-removing-file') }}");
          }
      });
  }, function() {
      // User cancelled, do nothing
  });
}

// Function to open CSV import modal
function openCSVImportModel(element) {
  var shipmentType = $(element).data('shipment-type');
  $('#csv-import-modal').modal('show');
  $('#shipment_type').val(shipmentType);
}

// Function to reindex table rows (if needed, based on previous context)
function reindexTable(tableBodyClass) {
  $('.' + tableBodyClass + ' tr').each(function(index) {
      $(this).find('.table-index').text(index + 1);
  });
}

// Placeholder for alertifyMessage, assuming it's defined in common-update-status-delete-script.blade.php or another global script
function alertifyMessage(type, message) {
  // Implement your alertify.js message display here
  // Example: alertify.notify(message, type, 3);
  console.log(`Alertify ${type}: ${message}`);
  alert(message); // Fallback to native alert if alertify is not loaded
}

// Placeholder for showLoader, assuming it's defined elsewhere
function showLoader() {
  // Implement your loader display here
  console.log("Showing loader...");
}
</script>

<!-- jQuery UI Datepicker -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<!-- ✅ Add these two lines for jQuery UI -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<!-- Then your datepicker initialization -->

<script>
    $(document).ready(function () {
        $('.date-format').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        });
    });
</script>

@include('admin/common-update-status-delete-script')
@endsection
