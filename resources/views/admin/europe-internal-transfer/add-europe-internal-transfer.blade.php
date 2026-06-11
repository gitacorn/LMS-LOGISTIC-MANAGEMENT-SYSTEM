@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-md-flex border-navabr align-items-center">
        <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }}</h1>
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
					<li class="document-text ml-auto logistic-master-no">{{ (!empty($recordInfo->v_europe_transfer_record_no) ? $recordInfo->v_europe_transfer_record_no : '') }}</li>
					<?php }?>
                </ul>
            </div>
    		<div class="card mb-3 good-in-buyer-class" id="details">
                <h3 class="title-goods"><i class="fas fa-level-up-alt list-icon mr-2"></i>{{ trans("messages.details") }}</h3>
              		{!! Form::open(array( 'id '=> 'add-internal-transfer-form' , 'method' => 'post' , 'files' => true , 'url' => 'europe-internal-transfer/add')) !!}
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
                            <div class="col-lg-3 col-md-4 col-sm-6">
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
                                       			<option value="{{ $key }}" {{ $selected }}>{{ $wayOfTransportDetail }}</option>
                                       			<?php 
                                       		}
                                       	}
                                       	?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="book_by" class="control-label">{{ trans("messages.book-by") }}<span class="text-danger">*</span></label>
                                    <select name="book_by" class="form-control select2" <?php echo $disableForm ?><?php echo (isset($recordInfo) && isset($warehouseId) ? ' disabled' : '')?> onchange="getFromWarehouseInfo(this);">
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
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="logistic_partner">{{ trans("messages.logistic-partner") }}<span class="text-danger">*</span></label>
                                    <select name="logistic_partner" class="form-control select2" <?php echo $disableForm ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
                                        if(!empty($logisticPartnerRecordDetails)){
                                        	foreach ($logisticPartnerRecordDetails as $logisticPartnerDetail){
                                        		$encodeId  = Wild_tiger::encode($logisticPartnerDetail->i_id);
                                        		$selected = '';
                                        		if( isset($recordInfo->i_logistic_partner_detail_id) && ( $recordInfo->i_logistic_partner_detail_id == $logisticPartnerDetail->i_id) ){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{ $encodeId }}" {{ $selected }}>{{ (!empty($logisticPartnerDetail->logisticPartnerMaster->v_logistic_partner_name) ? $logisticPartnerDetail->logisticPartnerMaster->v_logistic_partner_name : '' ) .' ('.$logisticPartnerDetail->v_logistic_partner_code.')' }}</option>
                                        		<?php 
                                        	}
                                        } 
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="booking_date">{{ trans("messages.booking-date") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="booking_date" <?php echo $disableForm ?> class="form-control" placeholder="{{ trans('messages.booking-date') }}" value="{{old('booking_date',  ( (isset($recordInfo) && (!empty($recordInfo->dt_booking_date))) ?  clientDate($recordInfo->dt_booking_date) : '' ) )}}">

                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="reference_no" class="control-label">{{ trans("messages.reference-no") }}</label>
                                    <input type="text" name="reference_no" <?php echo $disableForm ?> class="form-control" placeholder="{{ trans('messages.reference-no') }}" value="{{old('reference_no',  ( (isset($recordInfo) && (!empty($recordInfo->v_reference_no))) ?  $recordInfo->v_reference_no : '' ) )}}">

                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="no_of_pallets">{{ trans("messages.no-of-pallets") }}</label>
                                    <input type="number" name="no_of_pallets" min="1"  <?php echo $disableForm ?> class="form-control"  placeholder="{{ trans('messages.no-of-pallets') }}" value="{{old('no_of_pallets',  ( (isset($recordInfo) && (!empty($recordInfo->i_no_of_pallets))) ?  $recordInfo->i_no_of_pallets : '' ) )}}">

                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="weight">{{ trans("messages.weight") }}</label>
                                    <input type="text" name="weight" <?php echo $disableForm ?> class="form-control" onkeyup="onlyDecimal(this)" placeholder="{{ trans('messages.weight') }}" value="{{old('weight',  ( (isset($recordInfo) && (!empty($recordInfo->d_weight))) ?  $recordInfo->d_weight : '' ) )}}">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="from_warehouse">{{ trans("messages.from-warehouse") }}<span class="text-danger">*</span></label>
                                    <select name="from_warehouse" class="form-control select2 from-warehouse-html" <?php echo $disableForm ?><?php echo (isset($recordInfo) && isset($warehouseId) ? ' disabled' : '')?>>
                                    	<option value="">{{ trans("messages.select") }}</option>
                                    	<?php 
                                    	if (!empty($warehouseMasterDetails)){
                                    		foreach ($warehouseMasterDetails as $warehouseMasterDetail){
                                    			if (!isset($recordInfo) && isset($warehouseId) && !empty($warehouseMasterDetail->i_id) && $warehouseId != $warehouseMasterDetail->i_id){
                                    				continue;
                                    			}
                                    			$encodeId  = (!empty($warehouseMasterDetail->i_id) ? Wild_tiger::encode($warehouseMasterDetail->i_id) : 0);
                                    			$selected = '';
                                    			if (isset($recordInfo) && !empty($recordInfo->i_from_warehouse_id) && $recordInfo->i_from_warehouse_id == $warehouseMasterDetail->i_id){
                                    				$selected = 'selected';
                                    			}
                                    			?>
                                    			<option value="{{ $encodeId }}" {{ $selected }}>{{ (!empty($warehouseMasterDetail->v_warehouse_name) ? $warehouseMasterDetail->v_warehouse_name .(!empty($warehouseMasterDetail->v_warehouse_code) ? ' (' .$warehouseMasterDetail->v_warehouse_code .')' : '' ) : '' ) }}</option>
                                    			<?php 
                                    		}
                                    	}
                                    	?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="to_warehouse">{{ trans("messages.to-warehouse") }}<span class="text-danger">*</span></label>
                                    <select name="to_warehouse" class="form-control select2" <?php echo $disableForm ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
                                        if(!empty($warehouseMasterDetails)){
                                        	foreach ($warehouseMasterDetails as $warehouseMasterDetail){
                                        		$encodeId  = (!empty($warehouseMasterDetail->i_id) ? Wild_tiger::encode($warehouseMasterDetail->i_id) : 0);
                                        		$selected = '';
                                        		if (isset($recordInfo) && !empty($recordInfo->i_to_warehouse_id) && $recordInfo->i_to_warehouse_id == $warehouseMasterDetail->i_id){
                                        			$selected = 'selected';
                                        		}
                                        		?>
                                        		<option value="{{ $encodeId }}" {{ $selected }}>{{ (!empty($warehouseMasterDetail->v_warehouse_name) ? $warehouseMasterDetail->v_warehouse_name .(!empty($warehouseMasterDetail->v_warehouse_code) ? ' (' .$warehouseMasterDetail->v_warehouse_code .')' : '' ): '' ) }}</option>
                                        		<?php 
                                        	}
                                        } 
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="pb-0">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group pb-3 pt-3">
                                        <div class="card shadow-none border">
                                            <div class="card-header">
                                                <span class="partner-tilte">
                                                    {{ trans("messages.transfer-details") }}
                                                </span>
                                            </div>
                                            <div class="card-body logistic-partner">
                                                <div class="table-responsive">
                                                    <table class="table table-hover table-bordered table-sm pb-4">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th style="max-width:70px;min-width:70px;">{{ trans("messages.sr-no") }}</th>
                                                                <th style="max-width:200px;min-width:200px;">{{ trans("messages.invoice-no-ref-no") }} <span class="text-danger">*</span></th>
                                                                <th style="max-width:200px;min-width:200px;">{{ trans("messages.account") }}<span class="text-danger">*</span></th>
                                                                <?php /* 
                                                                <th style="max-width:220px;min-width:220px;">{{ trans("messages.from-warehouse") }} <span class="text-danger">*</span></th>
                                                                <th style="width:200px;min-width:200px;">{{ trans("messages.to-warehouse") }} <span class="text-danger">*</span></th>
                                                                 */?>
                                                                <th style="width:120px;min-width:120px;">{{ trans("messages.unit") }}<span class="text-danger">*</span></th>
                                                                <th style="width:120px;min-width:120px;">{{ trans("messages.price") }}</th>
                                                                <th style="width:75px;min-width:75pxpx;">{{ trans("messages.action") }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="europe-internal-transfer-tbody">
                                                        	<?php 
                                                        	if( isset($recordInfo->detailInfo) && (!empty($recordInfo->detailInfo)) && (count($recordInfo->detailInfo) > 0 ) ){
	                                                        	foreach ($recordInfo->detailInfo as $countKey => $interanalTransferDetail){
	                                                        		$countIndex = ($countKey + 1 );
                                                        			?>
                                                        			<tr>
		                                                                <td class="table-index text-center" >{{ $countIndex }}</td>
		                                                                <td class="text-left">
		                                                                <input type="text" class="form-control transfer-invoice-ref-status amazon-shipment-id-status" onchange="checkUniqueShipmentId(this)" data-record-type='{{ config("constants.INTERNAL_WAREHOUSE_TRANSFER") }}' data-shipment-id='{{ $interanalTransferDetail->i_id}}' <?php echo $documentForm?> name="edit_invoice_no_ref_no_<?php echo $interanalTransferDetail->i_id ?>" value="<?php echo (isset($interanalTransferDetail->v_invoice_ref_no) ? $interanalTransferDetail->v_invoice_ref_no : '' ); ?>">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <select name="edit_account_<?php echo $interanalTransferDetail->i_id ?>" <?php echo $disableForm ?> class="form-control transfer-account-status">
		                                                                        <option value="">{{ trans("messages.select") }}</option>
		                                                                        <?php 
		                                                                        if(!empty($comapnyMasterDetails)){
																		        	foreach ($comapnyMasterDetails as $comapnyMasterDetail){
																		           		$encodeId  = Wild_tiger::encode($comapnyMasterDetail->i_id);
																		           		$selected = '';
			                                                                    		if( isset($interanalTransferDetail->i_account_company_id) && ( $interanalTransferDetail->i_account_company_id == $comapnyMasterDetail->i_id ) ){
			                                                                    			$selected = "selected='selected'";
			                                                                    		}
																						?>
																						<option value="{{ $encodeId }}" {{ $selected }}>{{ (!empty($comapnyMasterDetail->v_company_name) ? $comapnyMasterDetail->v_company_name : '' ) }}</option>
																						<?php  
																		           	}
								       											}
				     														?>
		                                                                    </select>
		                                                                </td>
		                                                                <?php /* 
		                                                                <td class="text-left">
		                                                                    <select name="edit_from_warehouse_<?php echo $interanalTransferDetail->i_id ?>" <?php echo $disableForm ?> class="form-control transfer-from-status">
		                                                                        <option value="">{{ trans("messages.select") }}</option>
		                                                                        <?php 
			                                                                    if(!empty($warehouseMasterDetails)){
			                                                                    	foreach ($warehouseMasterDetails as $warehouseMasterDetail){
			                                                                    		$encodeId  = Wild_tiger::encode($warehouseMasterDetail->i_id);
			                                                                    		$selected = '';
			                                                                    		if( isset($interanalTransferDetail->i_warehouse_id) && ( $interanalTransferDetail->i_warehouse_id == $warehouseMasterDetail->i_id ) ){
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
		                                                                    <select name="edit_to_warehouse_<?php echo $interanalTransferDetail->i_id ?>" class="form-control transfer-to-status" <?php echo $disableForm?>>
		                                                                        <option value="">{{ trans("messages.select") }}</option>
		                                                                         <?php 
			                                                                    if(!empty($warehouseMasterDetails)){
			                                                                    	foreach ($warehouseMasterDetails as $warehouseMasterDetail){
			                                                                    		$encodeId  = Wild_tiger::encode($warehouseMasterDetail->i_id);
			                                                                    		$selected = '';
			                                                                    		if( isset($interanalTransferDetail->i_location_id) && ( $interanalTransferDetail->i_location_id == $warehouseMasterDetail->i_id ) ){
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
		                                                                 */?>
		                                                                <td class="text-left">
		                                                                	<input type="text" class="form-control transfer-unit-status" <?php echo $disableForm ?> name="edit_unit_<?php echo $interanalTransferDetail->i_id ?>" value="<?php echo (isset($interanalTransferDetail->v_units) ? $interanalTransferDetail->v_units : '' ); ?>">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                	<input type="text" class="form-control" <?php echo $disableForm ?> name="edit_price_<?php echo $interanalTransferDetail->i_id ?>" value="<?php echo (isset($interanalTransferDetail->v_price) ? $interanalTransferDetail->v_price : '' ); ?>">
		                                                                </td>
		                                                                <td class="actions-col" style="width:75px;min-width:75px;"><button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button></td>                                                          
		                                                            </tr>
                                                        			<?php 
	                                                        	}
                                                        	} else {
                                                        	?>
	                                                            <tr>
	                                                                <td class="table-index text-center">1</td>
	                                                                <td class="text-left">
	                                                                	<input type="text" class="form-control transfer-invoice-ref-status amazon-shipment-id-status" onchange="checkUniqueShipmentId(this)" name="invoice_no_ref_no_1" <?php echo $documentForm?>>
	                                                                </td>
	                                                                <td class="text-left">
	                                                                    <select name="account_1" class="form-control transfer-account-status" <?php echo $disableForm ?>>
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
	                                                                <?php /*
	                                                                <td class="text-left">
	                                                                    <select name="from_warehouse_1" class="form-control transfer-from-status" <?php echo $disableForm ?>>
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
	                                                                    <select name="to_warehouse_1" class="form-control transfer-to-status" <?php echo $disableForm ?>>
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
	                                                                  */?>
	                                                                <td class="text-left">
	                                                                <input type="text" class="form-control transfer-unit-status" name="unit_1" <?php echo $disableForm ?>>
	                                                                </td>
	                                                                <td class="text-left">
	                                                                <input type="text" class="form-control" name="price_1" <?php echo $disableForm ?>>
	                                                                </td>
	                                                                <td class="text-left">
	                                                                <a title="Download" href="javascript:void(0);" class="btn btn-sm btn-danger mb-1 d-none"><i class="fa fa-trash fa-fw"></i></a>
	                                                                
	                                                                </td>                                                     
	                                                            </tr>
	                                                            <tr>
	                                                                <td class="table-index text-center" >2</td>
	                                                                <td class="text-left">
	                                                                <input type="text" class="form-control transfer-invoice-ref-status amazon-shipment-id-status" onchange="checkUniqueShipmentId(this)" name="invoice_no_ref_no_2" <?php echo $documentForm?>>
	                                                                </td>
	                                                                <td class="text-left">
	                                                                    <select name="account_2" class="form-control transfer-account-status" <?php echo $disableForm ?>>
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
	                                                                <?php /* 
	                                                                <td class="text-left">
	                                                                    <select name="from_warehouse_2" class="form-control transfer-from-status" <?php echo $disableForm ?>>
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
	                                                                    <select name="to_warehouse_2" class="form-control transfer-to-status" <?php echo $disableForm ?>>
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
	                                                                 */?>
	                                                                <td class="text-left">
	                                                                	<input type="text" <?php echo $disableForm ?> class="form-control transfer-unit-status" name="unit_2">
	                                                                </td>
	                                                                 <td class="text-left">
	                                                                	<input type="text" <?php echo $disableForm ?> class="form-control" name="price_2">
	                                                                </td>
	                                                                <td class="actions-col" style="width:75px;min-width:75px;"><button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button></td>                                                          
	                                                            </tr>
	                                                    	<?php 
                                                        	}?>                                                            
                                                        </tbody>
                                                    </table>
                                                    <?php if(empty($disableForm)){?>
                                                    	<button type="button" class="btn btn-sm bg-theme text-white add-new-row mb-3" title="{{ trans('messages.add-new') }}" onclick="addNewTransferRow(this)"><i class="fa fa-plus fa-fw"></i>{{ trans("messages.add-new") }}</button>
                                                    <?php }?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="tracking_no">{{ trans("messages.tracking-no") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="tracking_no" <?php echo $disableForm ?> class="form-control" placeholder="{{ trans('messages.tracking-no') }}" value="{{old('tracking_no',  ( (isset($recordInfo) && (!empty($recordInfo->v_tracking_no))) ? ($recordInfo->v_tracking_no) : '' ) )}}">
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="tracking_link">{{ trans("messages.tracking-link") }}</label>
                                    <input type="text" name="tracking_link" <?php echo $disableForm ?> class="form-control" placeholder="{{ trans('messages.tracking-link') }}" value="{{old('tracking_link',  ( (isset($recordInfo) && (!empty($recordInfo->v_tracking_link))) ? ($recordInfo->v_tracking_link) : '' ) )}}">
                                </div>
                            </div>     
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="collection_date">{{ trans("messages.collection-date") }} </label>
                                    <input type="text" name="collection_date" <?php echo $disableForm ?> class="form-control" placeholder="{{ trans('messages.collection-date') }}" value="{{old('collection_date',  ( (isset($recordInfo) && (!empty($recordInfo->dt_collection_date))) ?  clientDate($recordInfo->dt_collection_date) : '' ) )}}">
                                </div>
                            </div>  
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="delivery_date">{{ trans("messages.delivery-date") }} </label>
                                    <input type="text" name="delivery_date" <?php echo $disableForm ?> class="form-control" placeholder="{{ trans('messages.delivery-date') }}" value="{{old('delivery_date',  ( (isset($recordInfo) && (!empty($recordInfo->dt_delivery_date))) ?  clientDate($recordInfo->dt_delivery_date) : '' ) )}}">
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
	                                                                    <select name="edit_type_<?php echo $documentDetail->i_id ?>" <?php echo $documentForm?> class="form-control warehouse-document-type">
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
	                                                                        <input type="file" <?php echo $documentForm?> class="custom-file-input warehouse-document-file" id="document_<?php echo $documentDetail->i_id ?>" name="edit_file_<?php echo $documentDetail->i_id ?>[]" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
	                                                                        <label class="custom-file-label" for="document_<?php echo $documentDetail->i_id ?>"><?php echo (!empty($documentFiles) ? ( count($documentFiles) > 1 ? trans('messages.multiple-files') : ( isset($documentFiles[0]) ? basename($documentFiles[0]) : '' ) )  : trans('messages.choose-file') ) ?></label>
	                                                                    </div>
	                                                                </td>
	                                                                <td class="text-left">
	                                                                    <input type="text" class="form-control" <?php echo $documentForm?> name="edit_remarks_<?php echo $documentDetail->i_id ?>" value="<?php echo (isset($documentDetail->v_document_remark) ? $documentDetail->v_document_remark : '' ); ?>">
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
			                                                                  <a title="{{ basename($imagePath) }}" href="{{ $imagePath }}" target="_blank"  class="btn btn-sm btn-danger mb-1 download-icon-items"><i class="fa fa-download"></i></a>
			                                                                </div>
	                                                                    	<?php 
		                                                                	}
	                                                                	}?> 
	                                                                </td>
				
	                                                                <td style="width:70px;min-width:70px;">
	                                                                 <?php if(empty($documentForm)){?>
	                                                                <button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button>
	                                                                <?php }?>
	                                                                </td>
	                                                       		</tr>
                                                        	<?php 
                                                        	}
                                                        } else {?>
                                                        	<tr>
                                                            	<td class="table-index text-center" style="width:70px;min-width:70px;">1</td>
                                                                <td class="text-left">
                                                                    <select name="type_1" class="form-control warehouse-document-type" <?php echo $documentForm?>>
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
                                                                        <input type="file" <?php echo $documentForm?> class="custom-file-input warehouse-document-file" id="document_1" name="file_1[]" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
                                                                        <label class="custom-file-label" for="document_1">{{ trans("messages.choose-file") }}</label>
                                                                    </div>
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control" name="remarks_1" <?php echo $documentForm?>>
                                                                </td>
                                                                <td class="actions-col">
                                                                    
                                                                </td>
															<td style="width:70px;min-width:70px;"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="table-index text-center" style="width:70px;min-width:70px;">2</td>
                                                                <td class="text-left">
                                                                    <select name="type_2" class="form-control warehouse-document-type" <?php echo $documentForm?>>
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
                                                                        <input type="file" <?php echo $documentForm?> class="custom-file-input warehouse-document-file" id="document_2" name="file_2[]" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
                                                                        <label class="custom-file-label" for="document_2">{{ trans('messages.choose-file') }}</label>
                                                                    </div>
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" class="form-control" name="remarks_2" <?php echo $documentForm?>>
                                                                </td>
                                                                <td class="actions-col">
                                                                   
                                                                </td>

                                                                <td style="width:70px;min-width:70px;">
                                                                <?php if(empty($documentForm)){?>
                                                                <button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button></td>
                                                                <?php } ?>
                                                            </tr>
                                                           	<?php 
                                                        	}?>
                                                    	</tbody>
                                                    	</table>
                                                    <?php if(empty($documentForm)){?>
                                                    	<button type="button" class="btn btn-sm bg-theme text-white add-new-row mb-3" title="{{ trans('messages.add-new') }}" onclick="addNewDocumentRow(this)"><i class="fa fa-plus fa-fw"></i>{{ trans("messages.add-new") }}</button>
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
		                                                                            <select class="form-control ml-2"<?php echo $documentForm ?>  name="edit_currency_id_<?php echo $transportInvoiceDetail->i_id ?>">
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
		                                                                        <input type="file" <?php echo $documentForm ?> class="custom-file-input" id="invoice_<?php echo $transportInvoiceDetail->i_id ?>" name="edit_invoice_file_<?php echo $transportInvoiceDetail->i_id ?>[]" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
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
		                                                                  			<?php if(empty($documentForm)){?>
		                                                                  			<a title="{{trans('messages.remove')}}"  href="javascript:void(0);" data-file-name="{{ basename($invoicePath) }}" onclick="removeUploadedFile(this);" data-record-id="{{ $transportInvoiceDetail->i_id }}" data-field-name="invoice"  class="close-icon"><i class="fa fa-times "></i></a>
		                                                                  			<?php } ?>
		                                                                  			<a title="{{ basename($invoicePath) }}" href="{{ $invoicePath }}" target="_blank"  class="btn btn-sm btn-danger mb-1 download-icon-items"><i class="fa fa-download"></i></a>
		                                                                		</div>
		                                                                    	
		                                                                    	<?php 
			                                                                	}
		                                                                	}
			                                                                ?>
		                                                                </td>
	                                                            	</tr>
                                                        		<?php 
	                                                        	}
	                                                        } else {?>
	                                                            <tr>
		                                                        	<td class="table-index">1</td>
		                                                             <td class="text-left">
		                                                             	<select name="name_1" class="form-control agent-warehouse-transporter-name select2" <?php echo $documentForm ?> >
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
	                                                                            <select class="form-control ml-2" name="currency_id_1" <?php echo $documentForm ?> >
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
	                                                                	<select name="name_2" class="form-control agent-warehouse-transporter-name select2" <?php echo $documentForm ?> >
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
	                                                                            <select class="form-control ml-2" name="currency_id_2" <?php echo $documentForm ?> >
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
	                                                                        <input type="file" class="custom-file-input" <?php echo $documentForm ?> id="invoice_2" name="invoice_file_2[]" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
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
                                                    	<button type="button" class="btn btn-sm bg-theme text-white add-new-row mb-3" title="{{ trans('messages.add-new') }}" onclick="addNewInvoiceRow(this)"><i class="fa fa-plus fa-fw"></i>{{ trans("messages.add-new") }}</button>
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
										<?php ## view time a disbaled aavu joi and role admin hoi to disbaled na avu joi ana mate ni conditon .?>
                                        <select name="status" class="form-control" {{ $statusDisableForm  }}>
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
                                        <input type="text" <?php echo $documentForm ?> name="status_comments" class="form-control" placeholder="{{ trans('messages.status-comments') }}" value="{{old('status_comments',  ( (isset($recordInfo) && (!empty($recordInfo->v_status_comment))) ?  $recordInfo->v_status_comment : '' ) )}}">
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
	                    		<?php if( empty($statusDisableForm) ){?>
			                    	  	<button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.update') }}">{{ trans("messages.update") }}</button>
			                    <?php } ?>
		                        
		                     <?php } else {?>
		                        	<button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.submit') }}">{{ trans("messages.submit") }}</button>
		                     <?php } ?>
	                    <?php //} ?>
                        <a href="{{config('constants.EUROPE_INTERNAL_TRANSFER_MASTER_URL')}}" class="btn btn-outline-secondary shadow-sm btn-wide" title="{{ trans('messages.cancel') }}">{{ trans("messages.cancel") }}</a>
                    </div>
                     <input type="hidden" name="europe_internal_transfer_count" value="">
                    <input type="hidden" name="europe_internal_document_type_count" value="">
                    <input type="hidden" name="europe_internal_transporter_count" value="">
            	 {!! Form::close() !!}
            </div>
        </div>
    </section>
</main>
<script>
    $("#add-internal-transfer-form").validate({
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
            booking_date: {
                required: true,
                noSpace:true
            },
            from_warehouse: {
                required: true
            },
            to_warehouse: {
                required: true
            },
            tracking_no: {
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
            booking_date: {
                required: "{{ trans('messages.require-booking-date') }}"
            },
            from_warehouse: {
                required: "{{ trans('messages.require-from-warehouse') }}"
            },
            to_warehouse: {
                required: "{{ trans('messages.require-to-warehouse') }}"
            },
            tracking_no: {
                required: "{{ trans('messages.require-tracking-no') }}"
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
        },
        submitHandler: function(form) {
        	var document_type_status = false;
       	 	var document_file_status = false;
       		$('.agent-to-warehouse-document-tbody tr').each(function(){
       			var document_type = $.trim($(this).find('.warehouse-document-type').val());
       			var document_file = $.trim($(this).find('.warehouse-document-file').val());
        		var document_file_valid = $.trim($(this).find('.warehouse-document-file').attr('data-valid-file'));
				if(document_file_valid != "" && document_file_valid != null && document_file_valid == "{{ strtolower(config('constants.SELECTION_YES'))}}"){
					
        			if( ( document_type == "" || document_type == null ) && (document_type_status != true) ){
						$.trim($(this).find('.warehouse-document-type').focus());
						document_file_status = true;
                	}
        		}
       		});
       		
       		if( document_file_status != false ){
        		alertifyMessage("error","{{ trans('messages.require-document-type') }} ");
           		return false;
            }

       		var transporter_name_status = false;
            var transporter_inv_no_status = false;
            $('.agent-to-warehouse-transport-tbody tr').each(function(){
         		var transporter_name = $.trim($(this).find('.agent-warehouse-transporter-name').val());
         		var transporter_inv_no = $.trim($(this).find('.agent-warehouse-transporter-inv-no').val());

				if(transporter_name != "" && transporter_name != null){
					transporter_name_status = true;
					if( ( transporter_inv_no == "" || transporter_inv_no == null ) && (transporter_inv_no_status != true) ){
						$.trim($(this).find('.agent-warehouse-transporter-inv-no').focus());
						transporter_inv_no_status = true;
                	}
         		} 
         	});
           
            if( transporter_inv_no_status != false ){
         		alertifyMessage("error","{{ trans('messages.require-inv-no') }} ");
            	return false;
            }
            var transfer_invoice_ref_status = true;
			var transfer_account_status = false;
			var transfer_from_status = false;
			var transfer_to_status = false;
			var transfer_unit_status = false;
			var unique_shipment_array = [];
	       	 $('.europe-internal-transfer-tbody tr').each(function(){
	       		var transfer_invoice_ref = $.trim($(this).find('.transfer-invoice-ref-status').val());
         		var transfer_account = $.trim($(this).find('.transfer-account-status').val());
         		//var transfer_from = $.trim($(this).find('.transfer-from-status').val());
         		//var transfer_to = $.trim($(this).find('.transfer-to-status').val());
         		var transfer_unit = $.trim($(this).find('.transfer-unit-status').val());
         		if(transfer_invoice_ref != "" && transfer_invoice_ref != null){
         			transfer_invoice_ref_status = false;
         			if( ( transfer_account == "" || transfer_account == null ) && (transfer_account_status != true) ){
						$.trim($(this).find('.transfer-account-status').focus());
						transfer_account_status = true;
                	}
         			/* if( ( transfer_from == "" || transfer_from == null ) && ( transfer_account_status != true && transfer_from_status != true ) ){
						$.trim($(this).find('.transfer-from-status').focus());
						transfer_from_status = true;
					} */
         			/* if( ( transfer_to == "" || transfer_to == null ) && ( transfer_account_status != true && transfer_from_status != true && transfer_to_status != true ) ){
						$.trim($(this).find('.transfer-to-status').focus());
						transfer_to_status = true;
					} */
         			if( ( transfer_unit == "" || transfer_unit == null ) && ( transfer_account_status != true && transfer_from_status != true && transfer_to_status != true && transfer_unit_status != true) ){
						$.trim($(this).find('.transfer-unit-status').focus());
						transfer_unit_status = true;
					}
         			if( unique_shipment_id != false ){
						if( $.inArray( transfer_invoice_ref ,unique_shipment_array  ) == -1 ){
							unique_shipment_array.push(transfer_invoice_ref);
						} else {
							unique_shipment_id = false;
							$(this).find('.transfer-invoice-ref-status').focus()
						}            		
	        		} 
             	}
		       	 
	      	});
	       	if( unique_shipment_id != true ){
        		alertifyMessage("error","{{ trans('messages.error-unique-shipment-id') }} ");
        		return false;
            }
	       	if( transfer_invoice_ref_status != false ){
	        	$.trim($('.transfer-invoice-ref-status:first').focus());
	        	alertifyMessage("error","{{ trans('messages.required-atleast-checkbox-selection') }} ");
	        	return false;
	        }
	       	if( transfer_account_status != false ){
	         	alertifyMessage("error","{{ trans('messages.require-account') }} ");
	            return false;
	         }
	       	if( transfer_from_status != false ){
	         	alertifyMessage("error","{{ trans('messages.require-from-warehouse') }} ");
	            return false;
	         }
	       	if( transfer_to_status != false ){
	         	alertifyMessage("error","{{ trans('messages.require-to-warehouse') }} ");
	            return false;
	         }
	       	if( transfer_unit_status != false ){
	         	alertifyMessage("error","{{ trans('messages.require-unit') }} ");
	            return false;
	         }
            var confirm_box = "";
            var confirm_box_msg = "";
            <?php if( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ) { ?>
					confirm_box = "{{ trans('messages.update-internal-transfer') }}";
					confirm_box_msg = "{{ trans('messages.confirm-update-internal-transfer') }}";

			<?php }else{?>
            		confirm_box = "{{ trans('messages.add-internal-transfer') }}";
					confirm_box_msg = "{{ trans('messages.confirm-add-internal-transfer') }}";
			<?php } ?>
        	alertify.confirm(confirm_box,confirm_box_msg,function() {
        		$("[name='europe_internal_transfer_count']").val(europe_internal_transfer_count);
        		$("[name='europe_internal_document_type_count']").val(agent_warehouse_document_type_count);
        		$("[name='europe_internal_transporter_count']").val(agent_warehouse_transporter_count);
        		$('input:disabled').prop('disabled', false);
 				$('select:disabled').prop('disabled', false);
            	showLoader()
            	form.submit();
			},function() {});
        }
    });
</script>

<script>
    $(document).ready(function() {

        //init date time picker
        $("[name='booking_date']").datetimepicker({
            useCurrent: false,
            viewMode: 'days',
            ignoreReadonly: true,
            widgetPositioning: {
            vertical: 'bottom'
            },
           format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

        });
        $("[name='collection_date']").datetimepicker({
            useCurrent: false,
            viewMode: 'days',
            ignoreReadonly: true,
            widgetPositioning: {
            vertical: 'bottom'
            },
           format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

        });
        $("[name='delivery_date']").datetimepicker({
            useCurrent: false,
            viewMode: 'days',
            ignoreReadonly: true,
            widgetPositioning: {
            vertical: 'bottom'
            },
            format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

        });
        <?php if( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ) { ?>
       			var booking_date = '{{ ( isset($recordInfo->dt_booking_date) ? $recordInfo->dt_booking_date : '') }}';
       			var collection_date = '{{ ( isset($recordInfo->dt_collection_date) ? $recordInfo->dt_collection_date : '') }}';
       			var delivery_date = '{{ ( isset($recordInfo->dt_delivery_date) ? $recordInfo->dt_delivery_date : '') }}';

            	if(booking_date != "" && booking_date != null){
        			//$("[name='booking_date']").data('DateTimePicker').minDate(moment(booking_date).startOf('d'));
        		}
            	if(collection_date != "" && collection_date != null){
            		//$("[name='collection_date']").data('DateTimePicker').minDate(moment(collection_date).startOf('d'));
        			
            	} else {
            		//$("[name='collection_date']").data('DateTimePicker').minDate(moment().startOf('d'));
                }
            	if(delivery_date != "" && delivery_date != null){
            		//$("[name='delivery_date']").data('DateTimePicker').minDate(moment(delivery_date).startOf('d'));	
            	} else {
            		//$("[name='delivery_date']").data('DateTimePicker').minDate(moment().startOf('d'));
                }
       	<?php } else { ?>
        		//$("[name='booking_date']").data('DateTimePicker').minDate(moment().startOf('d'));
        		//$("[name='collection_date']").data('DateTimePicker').minDate(moment().startOf('d'));
        		//$("[name='delivery_date']").data('DateTimePicker').minDate(moment().startOf('d'));
        <?php } ?>

    });
    var europe_internal_transfer_count = 2;
    function addNewTransferRow(thisitem){
    	europe_internal_transfer_count++;
    	var html =""; 
    	html += '<tr>';
    	html += '<td class="table-index text-center" >'+europe_internal_transfer_count+'</td>';
    	html += '<td class="text-left">';
    	html += '<input type="text" class="form-control transfer-invoice-ref-status amazon-shipment-id-status" onchange="checkUniqueShipmentId(this)" name="invoice_no_ref_no_'+europe_internal_transfer_count+'">';
    	html += '</td>';
    	html += '<td class="text-left">';
    	html += '<select name="account_'+europe_internal_transfer_count+'" class="form-control transfer-account-status">';
    	html += '<option value="">{{ trans("messages.select") }}</option>';
       	<?php 
        if(!empty($comapnyMasterDetails)){
			foreach ($comapnyMasterDetails as $comapnyMasterDetail){
		    	$encodeId  = Wild_tiger::encode($comapnyMasterDetail->i_id);
				?>
				html += '<option value="{{ $encodeId }}">{{ (!empty($comapnyMasterDetail->v_company_name) ? $comapnyMasterDetail->v_company_name : '' ) }}</option>';
				<?php  
           	}
		}
		?>
		html += '</select>';
		html += '</td>';
		<?php /*
		html += '<td class="text-left">';
		html += '<select name="from_warehouse_'+europe_internal_transfer_count+'" class="form-control transfer-from-status">';
		html += '<option value="">{{ trans("messages.select") }}</option>';
       	<?php 
    	if(!empty($warehouseMasterDetails)){
        	foreach ($warehouseMasterDetails as $warehouseMasterDetail){
           		$encodeId  = Wild_tiger::encode($warehouseMasterDetail->i_id);
				?>
				html += '<option value="{{ $encodeId }}">{{ (!empty($warehouseMasterDetail->v_warehouse_name) ? $warehouseMasterDetail->v_warehouse_name .(!empty($warehouseMasterDetail->v_warehouse_code) ? ' (' .$warehouseMasterDetail->v_warehouse_code .')' : '' ): '' ) }}</option>';
				<?php  
     		}
   		}
       	?>
       	html += '</select>';
       	html += '</td>';
       	html += '<td class="text-left">';
       	html += '<select name="to_warehouse_'+europe_internal_transfer_count+'" class="form-control transfer-to-status">';
       	html += '<option value="">{{ trans("messages.select") }}</option>';
    	<?php 
       	if(!empty($warehouseMasterDetails)){
        	foreach ($warehouseMasterDetails as $warehouseMasterDetail){
        		$encodeId  = Wild_tiger::encode($warehouseMasterDetail->i_id);
				?>
				html += '<option value="{{ $encodeId }}">{{ (!empty($warehouseMasterDetail->v_warehouse_name) ? $warehouseMasterDetail->v_warehouse_name .(!empty($warehouseMasterDetail->v_warehouse_code) ? ' (' .$warehouseMasterDetail->v_warehouse_code .')' : '' ): '' ) }}</option>';
				<?php  
           	}
       	}
      	?>
      	html += '</select>';
      	html += '</td>';
      	 */?>
      	html += '<td class="text-left">';
      	html += '<input type="text" class="form-control transfer-unit-status" name="unit_'+europe_internal_transfer_count+'">';
      	html += '</td>';
      	html += '<td class="text-left">';
      	html += '<input type="text" class="form-control" name="price_'+europe_internal_transfer_count+'">';
      	html += '</td>';
      	html += '<td class="actions-col" style="width:75px;min-width:75px;"><button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button></td>';
      	html += '</tr>';
      	if( $('.europe-internal-transfer-tbody').find('tr').length > 0 ){
      		$(html).insertAfter($('.europe-internal-transfer-tbody').find('tr:last'));	
    	} else {
    		$('.europe-internal-transfer-tbody').html(html);
    	}
    	reindexTable('europe-internal-transfer-tbody');
    }
    var module_url = '{{ config("constants.EUROPE_INTERNAL_TRANSFER_MASTER_URL") }}' + '/';
	function getFromWarehouseInfo(thisitem){
		var book_by_id = $.trim($(thisitem).val());
		$.ajax({
			type : 'post',
			data : { 'book_by_id' : book_by_id },
			url : module_url + 'getFromWarehouseDetails',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			beforeSend:function(){
				showLoader();
			},
			success:function(response){
				hideLoader();
				if(response != '' && response != null){
					$('.from-warehouse-html').html(response);
				}
			},
			error:function(){
				hideLoader();
			}
		});
	}
</script>
@endsection