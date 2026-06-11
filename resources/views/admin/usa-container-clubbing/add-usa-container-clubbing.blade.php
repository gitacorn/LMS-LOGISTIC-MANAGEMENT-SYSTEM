@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-md-flex border-navabr align-items-center">
        <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ ( isset($pageTitle) && !empty($pageTitle) ? $pageTitle : trans("messages.add-usa-container-clubbing") ) }}</h1>
    </div>

    <section class="inner-wrapper-common-sections main-listing-section">
        <div class="container-fluid">
            <div class="card document-card mb-3">
                <ul class="document-items">
                    <li class="document-list"><a href="#details" class="document-text">{{ trans("messages.details") }}</a></li>
                    <li class="document-list"><a href="#documents" class="document-text">{{ trans("messages.documents") }}</a></li>
                    <li class="document-list"><a href="#transporter" class="document-text">{{ trans("messages.transporter-invoice") }}</a></li>
                    <li class="document-list"><a href="#status" class="document-text">{{ trans("messages.status") }}</a></li>
                </ul>
            </div>
            <div class="card mb-3 good-in-buyer-class" id="details">
                <h3 class="title-goods"><i class="fas fa-level-up-alt list-icon mr-2"></i>{{ trans("messages.details") }}</h3>
                @include('admin/common-form-validation-error')
                {!! Form::open(array( 'id '=> 'add-usa-container-form' , 'method' => 'post' , 'files' => true , 'url' => config('constants.USA_CONTAINER_CLUBBING_MASTER_URL') . '/add')) !!}
                	<input type="hidden" name="record_id" value="{{ (isset($recordInfo) && !empty($recordInfo) ? Wild_tiger::encode($recordInfo->i_id) : '' ) }}">
                	<input type="hidden" name="usa_container_clubbing_document_type_count" value="">
                    <input type="hidden" name="usa_container_clubbing_transporter_count" value="">	
                    <input type="hidden" name="usa_container_checked_row_number" value="">	
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="type">{{ trans("messages.type") }}<span class="text-danger">*</span></label>
                                    <select name="type" class="form-control" <?php echo $disableForm ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
	                                        if(isset($typeDetails) && !empty($typeDetails)){
	                                        	foreach ($typeDetails as $typeKey => $typeValue){
	                                        		$selected = '';
	                                        		if( isset($recordInfo->e_type) && ( $recordInfo->e_type == $typeKey) ){
	                                        			$selected = "selected='selected'";
	                                        		}
	                                        		?>
	                                        		<option value="{{ $typeKey }}" {{ $selected }}><?php echo  (!empty($typeValue) ? $typeValue : '' ) ?></option>
	                                        		<?php 
	                                        	}
	                                        }
	                                    ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="from_warehouse">{{ trans("messages.from-warehouse") }}<span class="text-danger">*</span></label>
                                    <select name="from_warehouse" class="form-control select2" onchange="getFbaRecordDetails(this)" <?php echo $disableForm ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
	                                        if(isset($fromWarehouseDetails) && !empty($fromWarehouseDetails)){
	                                        	foreach ($fromWarehouseDetails as $fromWarehouseDetail){
	                                        		$encodeId = Wild_tiger::encode($fromWarehouseDetail->i_id);
	                                        		$selected = '';
	                                        		if( isset($recordInfo->i_from_warehouse_id) && ( $recordInfo->i_from_warehouse_id == $fromWarehouseDetail->i_id ) ){
	                                        			$selected = "selected='selected'";
	                                        		}
	                                        		?>
	                                        		<option value="{{ $encodeId }}" {{ $selected }}><?php echo  (!empty($fromWarehouseDetail->v_warehouse_name) ? $fromWarehouseDetail->v_warehouse_name . (!empty($fromWarehouseDetail->v_warehouse_code) ? ' (' . $fromWarehouseDetail->v_warehouse_code . ')' : '' ) : '' ) ?></option>
	                                        		<?php 
	                                        	}
	                                        }
	                                    ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="to_location">{{ trans("messages.to-location") }}<span class="text-danger">*</span></label>
                                    <select name="to_location" class="form-control select2" onchange="getFbaRecordDetails(this)" <?php echo $disableForm ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
	                                        if(isset($locationMasterCodeDetails) && !empty($locationMasterCodeDetails)){
	                                        	foreach ($locationMasterCodeDetails as $locationMasterCodeDetail){
	                                        		$encodeId = Wild_tiger::encode($locationMasterCodeDetail->i_id);
	                                        		$selected = '';
	                                        		if( isset($recordInfo->i_to_location_id) && ( $recordInfo->i_to_location_id == $locationMasterCodeDetail->i_id ) ){
	                                        			$selected = "selected='selected'";
	                                        		}
	                                        		?>
	                                        		<option value="{{ $encodeId }}" {{ $selected }}><?php echo  (!empty($locationMasterCodeDetail->v_warehouse_name) ? $locationMasterCodeDetail->v_warehouse_name . (!empty($locationMasterCodeDetail->v_warehouse_code) ? ' (' . $locationMasterCodeDetail->v_warehouse_code . ')' : '' ) : '' ) ?></option>
	                                        		<?php 
	                                        	}
	                                        }
	                                    ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="box_pallet">{{ trans("messages.box-pallet") }}<span class="text-danger">*</span></label>
                                    <select name="box_pallet[]" class="form-control select2" multiple <?php echo $disableForm ?>>
                                        <?php 
	                                        if(isset($boxPalletDetails) && !empty($boxPalletDetails)){
	                                        	foreach ($boxPalletDetails as $boxPalletKey => $boxPalletValue){
	                                        		$selected = '';
	                                        		if( isset($recordInfo->v_box_pallet_type) && ( in_array($boxPalletKey, explode(',', $recordInfo->v_box_pallet_type)) ) ){
	                                        			$selected = "selected='selected'";
	                                        		}
	                                        		?>
	                                        		<option value="{{ $boxPalletKey }}" {{ $selected }}><?php echo  (!empty($boxPalletValue) ? $boxPalletValue : '' ) ?></option>
	                                        		<?php 
	                                        	}
	                                        }
	                                    ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="total_box">{{ trans("messages.total-box") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="total_box" class="form-control" placeholder="{{ trans('messages.total-box') }}" readonly value="{{old('total_box',  ( (isset($recordInfo) && (!empty($recordInfo->d_total_boxes))) ?  $recordInfo->d_total_boxes : (!isset($recordInfo) ? '' : 0) ) )}}" <?php echo $disableForm ?>>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="total_pallet">{{ trans("messages.total-pallet") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="total_pallet" class="form-control" placeholder="{{ trans('messages.total-pallet') }}" readonly value="{{old('total_pallet',  ( (isset($recordInfo) && (!empty($recordInfo->d_total_pallets))) ?  $recordInfo->d_total_pallets : (!isset($recordInfo) ? '' : 0) ) )}}" <?php echo $disableForm ?>>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 usa-container-clubbing-fba-record-details" <?php echo ( isset($recordInfo) && ($recordInfo->i_id) ? '' : 'style="display:none"' )  ?>>
                                <div class="form-group">
                                    <div class="card shadow-none border">
                                        <div class="card-header d-flex align-item-center justify-content-between flex-wrap">
                                            <span class="partner-tilte align-self-center pr-3">
                                                {{ trans("messages.select-container") }}
                                            </span>
                                            <div class="form-group my-2 align-items-center search-area" style="width:100%; max-width:300px;">
																							<label for="" class="mb-0 mr-3 font-weight-bold">{{ trans('messages.search') }} : </label>
																							<input type="text" class="form-control table-part-search" placeholder="{{ trans('messages.search-by') . ' ' . trans('messages.fba') }}">
																						</div>
                                        </div>
                                        <div class="logistic-partner card-body logistic-partner-collection table-responsive-date">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-bordered table-sm pb-4">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th style="width:50px;min-width:50px;">{{ trans("messages.select") }} </th>
                                                            <th style="width:50px;min-width:50px;">{{ trans("messages.sr-no") }}</th>
                                                            <th style="width:50px;min-width:50px;">{{ trans("messages.entry-no") }}</th>
                                                            <th class="text-left" style="width:70px;min-width:70px;">{{ trans("messages.account") }}</th>
                                                            <th class="text-left" style="width:100px;min-width:100px;">{{ trans("messages.fba") }} </th>
                                                            <th class="text-left" style="width:100px;min-width:120px;">{{ trans("messages.ref-id") }}</th>
                                                            <th class="text-left" style="width:120px;min-width:120px;">Personal Ref.</th>
                                                            <?php /* ?>
                                                            <th class="text-left" style="width:70px;min-width:70px;">{{ trans("messages.status") }} </th>
                                                            <?php */ ?>
                                                            <th class="text-left" style="width:70px;min-width:70px;">{{ trans("messages.from") }} </th>

                                                            <th class="text-left" style="width:150px;min-width:150px;">{{ trans("messages.to") }}</th>
                                                            <th class="text-left" style="width:80px;min-width:80px;">{{ trans("messages.sku") }}
                                                            <th class="text-left" style="width:80px;min-width:80px;">{{ trans("messages.unit") }}</th>
                                                            <th class="text-left" style="width:80px;min-width:80px;">{{ trans("messages.product") }}</th>
                                                            <th class="text-left" style="width:80px;min-width:80px;">{{ trans("messages.boxes-pallets") }}</th>
                                                            <th class="text-left" style="width:100px;min-width:100px;">{{ trans("messages.final-boxes-pallets") }}<span class="text-danger">*</span></th>
                                                            <th class="text-left" style="width:150px;min-width:150px;">{{ trans("messages.number-of-box-pallet") }}<span class="text-danger">*</span></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="usa-container-clubbing-fba-record">
                                                    	<?php 
	                                                        /* if(isset($recordInfo->i_id) && isset($getFbaRecordDetails) && !empty($getFbaRecordDetails)){
	                                                        	$getFbaRecordInfo = [];
	                                                        	$getFbaRecordInfo['getFbaRecordDetails'] = $getFbaRecordDetails;
	                                                        	$getFbaRecordInfo['recordInfo'] = $recordInfo;
	                                                        	$getFbaRecordInfo['disableForm'] = $disableForm;
	                                                        	$html = view (config('constants.AJAX_VIEW_FOLDER') . 'usa-container-clubbing/usa-container-clubbing-fba-goods' )->with ( $getFbaRecordInfo )->render();
	                                                       	 	echo $html;
	                                                        } */
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="etd_dispatch_date">{{ trans("messages.booking-date") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="booking_date" class="form-control" placeholder="{{ trans('messages.booking-date') }}" value="{{old('booking_date',  ( (isset($recordInfo) && (!empty($recordInfo->dt_booking_date))) ?  clientDate($recordInfo->dt_booking_date) : '' ) )}}" <?php echo $disableForm ?>>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="etd_dispatch_date">{{ trans("messages.booking-portal") }}</label>
                                    <select name="booking_portal" class="form-control select2" <?php echo $disableForm ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
	                                        if(isset($bookingPortalDetails) && !empty($bookingPortalDetails)){
	                                        	foreach ($bookingPortalDetails as $bookingPortalDetail){
	                                        		$encodeId = Wild_tiger::encode($bookingPortalDetail->i_id);
	                                        		$selected = '';
	                                        		if( isset($recordInfo->i_booking_portal_id) && ( $recordInfo->i_booking_portal_id == $bookingPortalDetail->i_id ) ){
	                                        			$selected = "selected='selected'";
	                                        		}
	                                        		?>
	                                        		<option value="{{ $encodeId }}" {{ $selected }}><?php echo  (!empty($bookingPortalDetail->v_value) ? $bookingPortalDetail->v_value : '' ) ?></option>
	                                        		<?php 
	                                        	}
	                                        }
	                                    ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="etd_dispatch_date">{{ trans("messages.carrier-company") }}<span class="text-danger">*</span></label>
                                    <select name="carrier_company" class="form-control select2" <?php echo $disableForm ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
	                                        if(isset($logisticPartnerDetails) && !empty($logisticPartnerDetails)){
	                                        	foreach ($logisticPartnerDetails as $logisticPartnerDetail){
	                                        		$encodeId = Wild_tiger::encode($logisticPartnerDetail->i_id);
	                                        		$selected = '';
	                                        		if( isset($recordInfo->i_carrier_company_id) && ( $recordInfo->i_carrier_company_id == $logisticPartnerDetail->i_id ) ){
	                                        			$selected = "selected='selected'";
	                                        		}
	                                        		?>
	                                        		<option value="{{ $encodeId }}" {{ $selected }}><?php echo  (!empty($logisticPartnerDetail->v_logistic_partner_name) ? $logisticPartnerDetail->v_logistic_partner_name : '' ) ?></option>
	                                        		<?php 
	                                        	}
	                                        }
	                                    ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="etd_dispatch_date">{{ trans("messages.tracking-no") }}</label>
                                    <input type="text" name="tracking_no" class="form-control" placeholder="{{ trans('messages.tracking-no') }}" value="{{old('tracking_no',  ( (isset($recordInfo) && (!empty($recordInfo->v_tracking_no))) ?  $recordInfo->v_tracking_no : '' ) )}}" <?php echo $disableForm ?>>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="etd_dispatch_date">{{ trans("messages.pro-number") }}</label>
                                    <input type="text" name="pro_number" class="form-control" placeholder="{{ trans('messages.pro-number') }}" value="{{old('pro_number',  ( (isset($recordInfo) && (!empty($recordInfo->v_pro_number))) ?  $recordInfo->v_pro_number : '' ) )}}" <?php echo $disableForm ?>>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="etd_dispatch_date">{{ trans("messages.logistic-cost-usd") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="logistic_cost_usd" class="form-control" placeholder="{{ trans('messages.logistic-cost-usd') }}" value="{{old('logistic_cost_usd',  ( (isset($recordInfo) && (strlen($recordInfo->d_logistic_cost_in_usd) > 0)) ?  $recordInfo->d_logistic_cost_in_usd : 0 ) )}}" readOnly <?php echo $disableForm ?>>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="etd_dispatch_date">{{ trans("messages.collection-date") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="collection_date" class="form-control" placeholder="{{ trans('messages.collection-date') }}" value="{{old('collection_date',  ( (isset($recordInfo) && (!empty($recordInfo->dt_collection_date))) ?  clientDate($recordInfo->dt_collection_date) : '' ) )}}" <?php echo $disableForm ?>>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="etd_dispatch_date">{{ trans("messages.delivery-date") }}</label>
                                    <input type="text" name="delivery_date" class="form-control" placeholder="{{ trans('messages.delivery-date') }}" value="{{old('delivery_date',  ( (isset($recordInfo) && (!empty($recordInfo->dt_delivery_date))) ?  clientDate($recordInfo->dt_delivery_date) : '' ) )}}" <?php echo $disableForm ?>>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="etd_dispatch_date">{{ trans("messages.weight-lbs") }}</label>
                                    <input type="text" name="weight_lbs" class="form-control" placeholder="{{ trans('messages.weight-lbs') }}" value="{{old('weight_lbs',  ( (isset($recordInfo) && (!empty($recordInfo->d_weight))) ?  $recordInfo->d_weight : '' ) )}}" onchange="onlyDecimal(this)" onkeyup="onlyDecimal(this)" <?php echo $disableForm ?>>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="etd_dispatch_date">{{ trans("messages.comments") }}</label>
                                    <input type="text" name="comments" class="form-control" placeholder="{{ trans('messages.comments') }}" value="{{old('comments',  ( (isset($recordInfo) && (!empty($recordInfo->v_comments))) ?  $recordInfo->v_comments : '' ) )}}" <?php echo $disableForm ?>>
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
                                                                <th style="width:70px;min-width:70px;">{{ trans("messages.sr-no") }}</th>
                                                                <th style="max-width:250px;min-width:200px;">{{ trans("messages.type") }} <span class="text-danger">*</span></th>
                                                                <th style="max-width:350px;min-width:350px;">{{ trans("messages.documents") }} </th>
                                                                <th style="max-width:250px;min-width:200px;">{{ trans("messages.remarks") }} </th>
                                                                <th style="width:70px;min-width:70px;">{{ trans("messages.view") }}</th>
                                                                <th style="width:70px;min-width:70px;">{{ trans("messages.action") }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="agent-to-warehouse-document-tbody">
                                                        	<?php 
                                                        	if( isset($recordInfo->documentInfo) && (!empty($recordInfo->documentInfo)) && (count($recordInfo->documentInfo) > 0 ) ){
                                                        			foreach ($recordInfo->documentInfo as $countKey => $agentWarehouseDocumentDetail){
                                                        			$columIndex  = ( $countKey +  1 );
                                                        			?>
                                                        			<tr>
	                                                                	<td class="table-index text-center" style="width:70px;min-width:70px;">{{ $columIndex }}</td>
	                                                                	<td class="text-left">
	                                                                    	<select  <?php echo $documentForm ?> name="edit_type_<?php echo $agentWarehouseDocumentDetail->i_id ?>" class="form-control warehouse-document-type">
	                                                                        	<option value="">{{ trans("messages.select") }}</option>
	                                                                        	<?php 
									                                        	if(!empty($documentTypeRecordDetails)){
									                                        		foreach ($documentTypeRecordDetails as $documentTypeRecordDetail){
									                                        			$encodevDocumentTypeId  = Wild_tiger::encode($documentTypeRecordDetail->i_id);
									                                        			$selected = '';
									                                        			if( isset($agentWarehouseDocumentDetail->i_document_type_id) && ( $agentWarehouseDocumentDetail->i_document_type_id == $documentTypeRecordDetail->i_id ) ){
									                                        				$selected = "selected='selected'";
									                                        			}
									                                        			?>
									                                        			<option value="{{ $encodevDocumentTypeId }}" {{$selected}}>{{ (!empty($documentTypeRecordDetail->v_document_type_name) ? $documentTypeRecordDetail->v_document_type_name : '' ) }}</option>
									                                        			<?php 
									                                        		}
									                                        	} 
										                                  	 ?>
	                                                                    	</select>
	                                                                	</td>
	                                                                	 <?php $documentFiles = (json_decode($agentWarehouseDocumentDetail->v_document_file_path)); ?>
		                                                                <td class="text-left">
		                                                                    <div class="custom-file">
		                                                                        <input type="file"  <?php echo $documentForm ?> class="custom-file-input warehouse-document-file" id="document_<?php echo $agentWarehouseDocumentDetail->i_id ?>" name="edit_file_<?php echo $agentWarehouseDocumentDetail->i_id ?>[]" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
		                                                                        <label class="custom-file-label" for="document_<?php echo $agentWarehouseDocumentDetail->i_id ?>"><?php echo (!empty($documentFiles) ? ( count($documentFiles) > 1 ? trans('messages.multiple-files') : ( isset($documentFiles[0]) ? basename($documentFiles[0]) : '' ) )  : trans('messages.choose-file') ) ?></label>
		                                                                    </div>
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text"  <?php echo $documentForm ?> class="form-control" name="edit_remarks_<?php echo $agentWarehouseDocumentDetail->i_id ?>" value="<?php echo (isset($agentWarehouseDocumentDetail->v_document_remark) ? $agentWarehouseDocumentDetail->v_document_remark : '' ); ?>">
		                                                                </td>
		                                                                <td class="actions-col">
		                                                                   <?php 
		                                                                	if(!empty($documentFiles)){
			                                                                	foreach ($documentFiles as $documentFile){
			                                                                		$imagePath = (config('constants.FILE_STORAGE_URL_PATH').$documentFile);
			                                                                	?>
			                                                                	<div class="download-link-items">
				                                                                  <a title="{{trans('messages.remove')}}" href="javascript:void(0);" data-file-name="{{ basename($imagePath) }}" onclick="removeUploadedFile(this);" data-record-id="{{ $agentWarehouseDocumentDetail->i_id }}" data-field-name="document" class="close-icon"><i class="fa fa-times "></i></a>
				                                                                  <a title="{{ basename($imagePath) }}" href="{{ $imagePath }}" target="_blank" class="btn btn-sm btn-danger mb-1 download-icon-items"><i class="fa fa-download"></i></a>
				                                                                </div>
		                                                                    	<?php 
			                                                                	}
		                                                                	}?>
		                                                                </td>
	
	                                                                <td style="width:70px;min-width:70px;">
	                                                                <?php if(empty($documentForm)) { ?>	
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
	                                                                    <select name="type_1"  <?php echo $documentForm ?> class="form-control warehouse-document-type">
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
	                                                                        <input type="file"  <?php echo $documentForm ?> class="custom-file-input warehouse-document-file" id="document_1" name="file_1[]" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
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
	                                                                    <select name="type_2"  <?php echo $documentForm ?> class="form-control warehouse-document-type">
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
	                                                                        <input type="file" <?php echo $documentForm ?>  class="custom-file-input warehouse-document-file" id="document_2" name="file_2[]" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
	                                                                        <label class="custom-file-label" for="document_2">{{ trans('messages.choose-file') }}</label>
	                                                                    </div>
	                                                                </td>
	                                                                <td class="text-left">
	                                                                    <input type="text" class="form-control" name="remarks_2" <?php echo $documentForm ?> >
	                                                                </td>
	                                                                <td class="actions-col">
	                                                                   
	                                                                </td>
	
	                                                                <td style="width:70px;min-width:70px;">
	                                                                <?php if(empty($documentForm)) { ?>
	                                                                <button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button>
	                                                                <?php } ?>
	                                                                </td>
	                                                                
	                                                            </tr>
                                                            <?php }?>
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
                                                                <th class="text-left" style="width:100px;min-width:100px;">{{ trans("messages.final-total-usd") }}</th>
                                                                <th class="text-left" style="width:250px;min-width:250px;">{{ trans("messages.attach-documents") }}</th>
                                                                <th class="text-center" style="width:80px;min-width:80px;">{{ trans("messages.documents") }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="agent-to-warehouse-transport-tbody">
	                                                        <?php 
	                                                        if( isset($recordInfo->invoiceInfo) && (!empty($recordInfo->invoiceInfo)) && (count($recordInfo->invoiceInfo) > 0 ) ){
	                                                        	foreach ($recordInfo->invoiceInfo as $countKey => $agentWarehouseTransportDetail){
	                                                        		$countIndex = ($countKey + 1 );
	                                                        		?>
	                                                        		<tr>
		                                                                <td class="table-index">{{$countIndex}}</td>
		                                                                <td class="text-left">
			                                                                <select <?php echo $documentForm ?> name="edit_name_<?php echo $agentWarehouseTransportDetail->i_id ?>" class="form-control agent-warehouse-transporter-name select2">
										                                        <option value="">{{ trans("messages.select") }}</option>
										                                        <?php 
										                                        if(!empty($logisticPartnerDetails)){
										                                        	foreach ($logisticPartnerDetails as $logisticPartnerDetail){
										                                        		$encodeLogisticPartnerId = Wild_tiger::encode($logisticPartnerDetail->i_id);
										                                        		$selected = '';
										                                        		if( isset($agentWarehouseTransportDetail->i_logistic_partner_master_id) && ( $agentWarehouseTransportDetail->i_logistic_partner_master_id == $logisticPartnerDetail->i_id ) ){
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
		                                                                    <input type="text" <?php echo $documentForm ?> class="form-control agent-warehouse-transporter-inv-no" name="edit_inv_no_<?php echo $agentWarehouseTransportDetail->i_id ?>" placeholder="{{ trans('messages.inv-no') }}" value="<?php echo (isset($agentWarehouseTransportDetail->v_invoice_no) ? $agentWarehouseTransportDetail->v_invoice_no : '' ); ?>">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-freight" name="edit_freight_<?php echo $agentWarehouseTransportDetail->i_id ?>" placeholder="{{ trans('messages.freight') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($agentWarehouseTransportDetail->d_freight_charge) ? $agentWarehouseTransportDetail->d_freight_charge : '' ); ?>">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-custom" name="edit_custom_<?php echo $agentWarehouseTransportDetail->i_id ?>" placeholder="{{ trans('messages.custom') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($agentWarehouseTransportDetail->d_custom_charge) ? $agentWarehouseTransportDetail->d_custom_charge : '' ); ?>">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-duty" name="edit_duty_<?php echo $agentWarehouseTransportDetail->i_id ?>" placeholder="{{ trans('messages.duty') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($agentWarehouseTransportDetail->d_duty_charge) ? $agentWarehouseTransportDetail->d_duty_charge : '' ); ?>">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-other" name="edit_other_<?php echo $agentWarehouseTransportDetail->i_id ?>" placeholder="{{ trans('messages.other') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($agentWarehouseTransportDetail->d_other_charge) ? $agentWarehouseTransportDetail->d_other_charge : '' ); ?>">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-vat" name="edit_vat_<?php echo $agentWarehouseTransportDetail->i_id ?>" placeholder="{{ trans('messages.vat') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($agentWarehouseTransportDetail->d_vat_charge) ? $agentWarehouseTransportDetail->d_vat_charge : '' ); ?>">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <div class="input-group align-items-center flex-nowrap">
		                                                                        <label class="mb-0" for=""><span class="agent-warehouse-total-value"><?php echo (isset($agentWarehouseTransportDetail->d_total_charge) ? $agentWarehouseTransportDetail->d_total_charge :'')?></span></label>
		                                                                        <div class="input-group-prepend">
		                                                                            <select class="form-control ml-2" name="edit_currency_id_<?php echo $agentWarehouseTransportDetail->i_id ?>" <?php echo $documentForm ?> >
		                                                                                <option  value="">{{trans('messages.currency')}}</option>
		                                                                               <?php 
												                                        if(!empty($currencyRecordDetails)){
												                                        	foreach ($currencyRecordDetails as $currencyRecordDetail){
												                                        		$encodeCurrencyrId  = Wild_tiger::encode($currencyRecordDetail->i_id);
												                                        		$selected = '';
												                                        		if( isset($agentWarehouseTransportDetail->i_invoice_currency_id) && ( $agentWarehouseTransportDetail->i_invoice_currency_id == $currencyRecordDetail->i_id ) ){
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
		                                                                    <input type="text" <?php echo $documentForm ?> class="form-control agent-to-warehouse-con-rate" name="edit_cov_rate_<?php echo $agentWarehouseTransportDetail->i_id ?>" placeholder="{{ trans('messages.cov-rate') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($agentWarehouseTransportDetail->d_conversion_rate) ? $agentWarehouseTransportDetail->d_conversion_rate : '' ); ?>">
		                                                                </td>
		                                                                <td class="text-left"><span class="agent-warehouse-final-rate"><?php echo (isset($agentWarehouseTransportDetail->d_final_charge) ? $agentWarehouseTransportDetail->d_final_charge : '' ); ?></span></td>
		                                                                <?php $invoiceFiles = (json_decode($agentWarehouseTransportDetail->v_invoice_file_path)); ?>	
		                                                                <td class="text-left">
		                                                                    <div class="custom-file">
		                                                                        <input type="file" <?php echo $documentForm ?> class="custom-file-input" id="invoice_<?php echo $agentWarehouseTransportDetail->i_id ?>" name="edit_invoice_file_<?php echo $agentWarehouseTransportDetail->i_id ?>[]" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
		                                                                        <label class="custom-file-label" for="invoice_<?php echo $agentWarehouseTransportDetail->i_id ?>"><?php echo (!empty($invoiceFiles) ? ( count($invoiceFiles) > 1 ? trans('messages.multiple-files') : ( isset($invoiceFiles[0]) ? basename($invoiceFiles[0]) : '' ) )  : trans('messages.choose-file') ) ?></label>
		                                                                    </div>
		                                                                </td>
		                                                                <td class="actions-col">
		                                                                   <?php 
		                                                                	if(!empty($invoiceFiles)){
			                                                                	foreach ($invoiceFiles as $invoiceFile){
			                                                                		$invoicePath = (config('constants.FILE_STORAGE_URL_PATH').$invoiceFile);
			                                                                	?>
			                                                                	<div class="download-link-items">
		                                                                  			<?php if(empty($documentForm)) { ?>
		                                                                  			<a title="{{trans('messages.remove')}}"  href="javascript:void(0);" data-file-name="{{ basename($invoicePath) }}" onclick="removeUploadedFile(this);" data-record-id="{{ $agentWarehouseTransportDetail->i_id }}" data-field-name="invoice"  class="close-icon"><i class="fa fa-times "></i></a>
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
	                                                                                <option  value="">{{trans('messages.currency')}}</option>
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
	                                                                                <option  value="">{{trans('messages.currency')}}</option>
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
										<?php ## view time a disabled  aavu joi and role admin hoi to disabled na avu joi a mate ni conditon .?>
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
                    	<?php if( empty($statusDisableForm) ){?>
                        	<button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ (isset($recordInfo) && ( $recordInfo->i_id > 0 ) ? trans('messages.update') : trans('messages.submit')) }}">{{ (isset($recordInfo) && ( $recordInfo->i_id > 0 ) ? trans('messages.update') : trans('messages.submit')) }}</button>
                        <?php } ?>
                        <a href="{{ config('constants.USA_CONTAINER_CLUBBING_MASTER_URL') }}" class="btn btn-outline-secondary shadow-sm btn-wide" title="{{ trans('messages.cancel') }}">{{ trans("messages.cancel") }}</a>
                    </div>
				{!! Form::close() !!}
            </div>

        </div>
    </section>
</main>
<script>
	var module_url = "{{ config('constants.USA_CONTAINER_CLUBBING_MASTER_URL') }}" + '/';
	
    $("#add-usa-container-form").validate({
        errorClass: "invalid-input",
        rules: {
        	type: {
                required: true
            },
            from_warehouse: {
                required: true
            },
            to_location: {
                required: true
            },
            'box_pallet[]': {
                required: true
            },
            /* total_box: {
                required: true
            },            
            total_pallet: {
                required: true
            }, */
            booking_date: {
                required: true
            },
            carrier_company: {
                required: true
            },
            logistic_cost_usd: {
                required: true
            },
            collection_date: {
                required: true
            },
            delivery_date : {
            	required : function(element){
   		    		return ( ($.trim($("[name='status']").find('option:selected').attr('data-status-id')) == '{{ config("constants.DELIVERED_STATUS_ID") }}') || ($.trim($("[name='status']").find('option:selected').attr('data-status-id')) == '{{ config("constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID") }}') ? true : false )
            	} 
   			},
            status: {
                required: true
            },
        },
        messages: {
        	type: {
                required: "{{ trans('messages.required-select-field-validation', ['fieldName' => trans('messages.type')]) }}"
            },
            from_warehouse: {
            	required: "{{ trans('messages.required-select-field-validation', ['fieldName' => trans('messages.from-warehouse')]) }}"
            },
            to_location: {
            	required: "{{ trans('messages.required-select-field-validation', ['fieldName' => trans('messages.to-location')]) }}"
            },

            'box_pallet[]':{
            	required: "{{ trans('messages.required-select-field-validation', ['fieldName' => trans('messages.box-pallet')]) }}"
            },
            total_box: {
            	required: "{{ trans('messages.required-enter-field-validation', ['fieldName' => trans('messages.total-box')]) }}",
            	noSpace : true
            },
            total_pallet: {
            	required: "{{ trans('messages.required-enter-field-validation', ['fieldName' => trans('messages.total-pallet')]) }}",
            	noSpace : true
            },
            booking_date: {
            	required: "{{ trans('messages.required-enter-field-validation', ['fieldName' => trans('messages.booking-date')]) }}",
            },
            status: {
            	required: "{{ trans('messages.required-select-field-validation', ['fieldName' => trans('messages.status')]) }}",
            },
            carrier_company:{
            	required: "{{ trans('messages.required-select-field-validation', ['fieldName' => trans('messages.carrier-company')]) }}",                
            },
            logistic_cost_usd:{
            	required: "{{ trans('messages.required-enter-field-validation', ['fieldName' => trans('messages.logistic-cost-usd')]) }}",
            	noSpace: true
            },
            collection_date:{
            	required: "{{ trans('messages.required-enter-field-validation', ['fieldName' => trans('messages.collection-date')]) }}",
            },
            delivery_date:{
            	required: "{{ trans('messages.required-select-field-validation', ['fieldName' => trans('messages.delivery-date')]) }}",
            }
        },
        submitHandler: function(form) {            
			var usa_container_box_pallet_mismach = false;
			var usa_container_box_pallet_status = false;
			var usa_container_box_pallet_no_status = false;
            var fba_selection_status = true;
			var first_selected_box_pallets = '';

			var usa_container_checked_row_number = 0;

			$('.usa-container-clubbing-fba-record-details table tbody tr').each(function(){
       			if($(this).find('.usa-container-clubbing-selection').prop('checked') == true){
       			 	fba_selection_status = false;
					var usa_container_final_boxes_pallets = $.trim($(this).find('.final-boxes-pallets').val());
					var usa_container_number_of_box_pallet = $.trim($(this).find('.number-of-box-pallet').val());

					
					if( usa_container_final_boxes_pallets == "" || usa_container_final_boxes_pallets == null ){
						$(this).find('.final-boxes-pallets').focus();
						usa_container_box_pallet_status = true;
					} else {
						
						if((first_selected_box_pallets == '' || first_selected_box_pallets == null) && usa_container_final_boxes_pallets != '' && usa_container_final_boxes_pallets != null){
							first_selected_box_pallets = usa_container_final_boxes_pallets;
						}
						
						if(first_selected_box_pallets != usa_container_final_boxes_pallets){
							$(this).find('.final-boxes-pallets').focus();
							usa_container_box_pallet_mismach = true;
						}
					}

					if( ( usa_container_number_of_box_pallet == "" || usa_container_number_of_box_pallet == null ) &&  usa_container_box_pallet_status != true && usa_container_box_pallet_mismach != true){
						$(this).find('.number-of-box-pallet').focus();
						usa_container_box_pallet_no_status = true;
					}
       			}
				usa_container_checked_row_number++;
			})
			
			if( fba_selection_status != false ){
            	alertifyMessage("error","{{ trans('messages.required-atleast-checkbox-selection') }} ");
        		return false;
            }
			
			if( usa_container_box_pallet_status != false ){
         		alertifyMessage("error","{{ trans('messages.required-final-box-pallet') }} ");
            	return false;
            }

			if( usa_container_box_pallet_no_status != false ){
         		alertifyMessage("error","{{ trans('messages.required-number-of-box-pallet') }} ");
            	return false;
            }
			
			if( usa_container_box_pallet_mismach != false ){
         		alertifyMessage("error","{{ trans('messages.mismatch-final-box-pallet') }} ");
            	return false;
            }
	
            
        	var usa_container_clubbing_type_status = false;
       	 	var usa_container_clubbing_file_status = false;
       		$('.agent-to-warehouse-document-tbody tr').each(function(){
       			var usa_container_clubbing_type = $.trim($(this).find('.warehouse-document-type').val());
       			var usa_container_clubbing_file = $.trim($(this).find('.warehouse-document-file').val());
        		var usa_container_clubbing_file_valid = $.trim($(this).find('.warehouse-document-file').attr('data-valid-file'));
				if(usa_container_clubbing_file_valid != "" && usa_container_clubbing_file_valid != null && usa_container_clubbing_file_valid == "{{ strtolower(config('constants.SELECTION_YES'))}}"){
        			if( ( usa_container_clubbing_type == "" || usa_container_clubbing_type == null ) && (usa_container_clubbing_type_status != true) ){
						$.trim($(this).find('.warehouse-document-type').focus());
						usa_container_clubbing_file_status = true;
                	}
        		}
       		});

       		if( usa_container_clubbing_file_status != false ){
        		alertifyMessage("error","{{ trans('messages.require-document-type') }} ");
           		return false;
            }

       		var usa_container_clubbing_transporter_name_status = false;
            var usa_container_clubbing_transporter_inv_no_status = false;
            $('.agent-to-warehouse-transport-tbody tr').each(function(){
         		var usa_container_clubbing_transporter_name = $.trim($(this).find('.agent-warehouse-transporter-name').val());
         		var usa_container_clubbing_transporter_inv_no = $.trim($(this).find('.agent-warehouse-transporter-inv-no').val());

				if(usa_container_clubbing_transporter_name != "" && usa_container_clubbing_transporter_name != null){
					usa_container_clubbing_transporter_name_status = true;
					if( ( usa_container_clubbing_transporter_inv_no == "" || usa_container_clubbing_transporter_inv_no == null ) && (usa_container_clubbing_transporter_inv_no_status != true) ){
						$.trim($(this).find('.agent-warehouse-transporter-inv-no').focus());
						usa_container_clubbing_transporter_inv_no_status = true;
                	}
         		} 
         	});
           
            if( usa_container_clubbing_transporter_inv_no_status != false ){
         		alertifyMessage("error","{{ trans('messages.require-inv-no') }} ");
            	return false;
            }

            countTotalRecord();
            
            var confirm_box = '{{ trans("messages.add-usa-container-clubbing") }}';
        	var confirm_box_msg = '{{ trans("messages.common-module-confirm-msg" , [ "action" => trans("messages.add") , "module" => trans("messages.usa-container-clubbing") ] ) }}';

        	<?php if (isset($recordInfo) && ($recordInfo->i_id > 0)) { ?>
        		confirm_box = '{{ trans("messages.update-usa-container-clubbing") }}';
        		confirm_box_msg = '{{ trans("messages.common-module-confirm-msg" , [ "action" => trans("messages.update") , "module" => trans("messages.usa-container-clubbing") ] ) }}';
        	<?php } ?>
        	
			alertify.confirm(confirm_box,confirm_box_msg,function() {
        		$("[name='usa_container_clubbing_document_type_count']").val(agent_warehouse_document_type_count);
        		$("[name='usa_container_clubbing_transporter_count']").val(agent_warehouse_transporter_count);
        		$("[name='usa_container_checked_row_number']").val(usa_container_checked_row_number);
        		$("[name='logistic_partner_name']").prop('disabled', false);
        		$("[name='to']").prop('disabled', false);
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
        $("[name='collection_date'], [name='delivery_date'], [name='booking_date']").datetimepicker({
            useCurrent: false,
            viewMode: 'days',
            ignoreReadonly: true,
            widgetPositioning: {
                vertical: 'bottom'
            },
            format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',
        });

        <?php if(isset($recordInfo) && !empty($recordInfo) && !empty($recordInfo->i_id)) { ?>
        	getFbaRecordDetails();
        <?php } ?>
    });
	
    function getFbaRecordDetails(thisitem){
		var from_warehouse_id = $.trim($("[name='from_warehouse']").val());
		var to_location_id = $.trim($("[name='to_location']").val());
		var status = $.trim($("[name='status']").val());
		var record_id = $.trim($("[name='record_id']").val());
		var disable_form = '{{ (isset($disableForm) && !empty($disableForm) ? $disableForm : '') }}';
		// var box_pallet_type = $.trim($("[name='box_pallet[]']").val());
		$('[name="total_box"]').val('0');
		$('[name="total_pallet"]').val('0');
		
		if( (from_warehouse_id != "" && from_warehouse_id != null) || (to_location_id != "" && to_location_id != null) ){
			$.ajax({
				type: "POST",
				url: module_url + 'getFbaRecordDetails',
				data: {
					"_token": "{{ csrf_token() }}",
					'from_warehouse_id': from_warehouse_id,'to_location_id':to_location_id,'status':status,'record_id':record_id,'disable_form':disable_form
				},
				beforeSend: function() {
					//block ui
					showLoader();
				},
				success: function(response) {
					hideLoader();
					if(response !="" && response != null){
						$('.usa-container-clubbing-fba-record').html(response);
						$('.usa-container-clubbing-fba-record-details').show();
						countTotalRecord();
					}
				},
				error: function() {
					hideLoader();
				}
			});
		} else {
			$('.usa-container-clubbing-fba-record-details').hide();
		}
	} 	

    $(document).on('keyup', '.table-part-search' , function(){
	    var search = $.trim($(this).val()).toUpperCase();
		$($(this).parents('.usa-container-clubbing-fba-record-details').find('table tbody tr')).each(function(index) {
			$row = $(this);
            var part_name = $row.find(".search-fba-id").text().toUpperCase();
            if (part_name.indexOf(search) > -1) {
				$row.show();
			} else {
				$row.hide();
			}
	    });
	});

	function countTotalRecord(){
		var total_boxes = 0;
		var total_pallets = 0;
		var selected_first_type = '';

		if($('.usa-container-clubbing-fba-record-details').is(':visible')){
			$('.usa-container-clubbing-fba-record-details table tbody tr').each(function(){
				if($(this).find('.usa-container-clubbing-selection').prop('checked') == true){
					var selected_first_type = $.trim($(this).find('.final-boxes-pallets').val());
					var number_of_box_pallet_value = $.trim($(this).find('.number-of-box-pallet').val());
					
					if(selected_first_type != '' && selected_first_type != null && number_of_box_pallet_value != '' && number_of_box_pallet_value != null){
						if(selected_first_type == '<?php echo config("constants.BOX")?>'){
							total_boxes += parseInt(number_of_box_pallet_value);
						}
						if(selected_first_type == '<?php echo config("constants.PALLET")?>'){
							total_pallets += parseInt(number_of_box_pallet_value);
						}
					}
				}
			});
		}

		$('[name="total_box"]').val(total_boxes);
		$('[name="total_pallet"]').val(total_pallets);
	}
</script>

<script>
  // Local counters initialized from current DOM
  (function(){
    try {
      window.agent_warehouse_document_type_count = $('.agent-to-warehouse-document-tbody').find('select[name^="type_"]').length || 0;
      window.agent_warehouse_transporter_count = $('.agent-to-warehouse-transport-tbody').find('select[name^="name_"]').length || 0;
    } catch(e) {}
  })();

  function addNewDocumentRow(thisitem){
    if (typeof window.agent_warehouse_document_type_count === 'undefined') { window.agent_warehouse_document_type_count = 0; }
    window.agent_warehouse_document_type_count++;
    var idx = window.agent_warehouse_document_type_count;
    var html = '';
    html += '<tr>';
    html += '<td class="table-index text-center" style="width:70px;min-width:70px;">'+ idx +'</td>';
    html += '<td class="text-left">';
    html += '<select name="type_'+ idx +'" class="form-control warehouse-document-type">';
    html += '<option value="">{{ trans("messages.select") }}</option>';
    <?php 
    if(!empty($documentTypeRecordDetails)){
      foreach ($documentTypeRecordDetails as $documentTypeRecordDetail){
        $encodevDocumentTypeId  = Wild_tiger::encode($documentTypeRecordDetail->i_id);
        ?>
        html += '<option value="{{ $encodevDocumentTypeId }}">' + {!! json_encode($documentTypeRecordDetail->v_document_type_name ?? '') !!} + '</option>';
        <?php 
      }
    } 
    ?>
    html += '</select>';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<div class="custom-file">';
    html += '<input type="file" class="custom-file-input warehouse-document-file" id="document_'+ idx +'" name="file_'+ idx +'[]" multiple onchange="validFile(this,\'pdf_doc_jpg_png_jpeg_xls\')">';
    html += '<label class="custom-file-label" for="document_'+ idx +'">{{ trans("messages.choose-file") }}</label>';
    html += '</div>';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<input type="text" class="form-control" name="remarks_'+ idx +'">';
    html += '</td>';
    html += '<td class="actions-col">';
    html += '</td>';
    html += '<td style="width:70px;min-width:70px;">';
    <?php if( isset($documentForm) && empty($documentForm) ) { ?>
    html += '<button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button>';
    <?php } ?>
    html += '</td>';
    html += '</tr>';

    if( $('.agent-to-warehouse-document-tbody').find('tr').length > 0 ){
      $(html).insertAfter($('.agent-to-warehouse-document-tbody').find('tr:last'));
    } else {
      $('.agent-to-warehouse-document-tbody').html(html);
    }
    reindexTable('agent-to-warehouse-document-tbody');
  }

  function addNewInvoiceRow(thisitem){
    if (typeof window.agent_warehouse_transporter_count === 'undefined') { window.agent_warehouse_transporter_count = 0; }
    window.agent_warehouse_transporter_count++;
    var idx = window.agent_warehouse_transporter_count;
    var html = '';
    html += '<tr>';
    html += '<td class="table-index">'+ idx +'</td>';
    html += '<td class="text-left">';
    html += '<select name="name_'+ idx +'" class="form-control agent-warehouse-transporter-name select2">';
    html += '<option value="">{{ trans("messages.select") }}</option>';
    <?php 
    if(!empty($logisticPartnerDetails)){
      foreach ($logisticPartnerDetails as $logisticPartnerDetail){
        $encodeLogisticPartnerId = Wild_tiger::encode($logisticPartnerDetail->i_id);
        ?>
        html += '<option value="{{ $encodeLogisticPartnerId }}">' + {!! json_encode($logisticPartnerDetail->v_logistic_partner_name ?? '') !!} + '</option>';
        <?php 
      }
    }
    ?>
    html += '</select>';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<input type="text" class="form-control agent-warehouse-transporter-inv-no" name="inv_no_'+ idx +'" placeholder="{{ trans('messages.inv-no') }}">';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<input type="text" class="form-control agent-to-warehouse-freight" name="freight_'+ idx +'" placeholder="{{ trans('messages.freight') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<input type="text" class="form-control agent-to-warehouse-custom" name="custom_'+ idx +'" placeholder="{{ trans('messages.custom') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<input type="text" class="form-control agent-to-warehouse-duty" name="duty_'+ idx +'" placeholder="{{ trans('messages.duty') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<input type="text" class="form-control agent-to-warehouse-other" name="other_'+ idx +'" placeholder="{{ trans('messages.other') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<input type="text" class="form-control agent-to-warehouse-vat" name="vat_'+ idx +'" placeholder="{{ trans('messages.vat') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<div class="input-group align-items-center flex-nowrap">';
    html += '<label class="mb-0" for=""><span class="agent-warehouse-total-value"></span></label>';
    html += '<div class="input-group-prepend">';
    html += '<select class="form-control ml-2" name="currency_id_'+ idx +'" onchange="getTotalNumberOfValue(this)">';
    html += '<option value="">{{ trans("messages.currency") }}</option>';
    <?php 
    if(!empty($currencyRecordDetails)){
      foreach ($currencyRecordDetails as $currencyRecordDetail){
        $encodeCurrencyId  = Wild_tiger::encode($currencyRecordDetail->i_id);
        ?>
        html += '<option value="{{ $encodeCurrencyId }}">' + {!! json_encode($currencyRecordDetail->v_currency_code ?? '') !!} + '</option>';
        <?php 
      }
    } 
    ?>
    html += '</select>';
    html += '</div>';
    html += '</div>';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<input type="text" class="form-control agent-to-warehouse-con-rate" name="cov_rate_'+ idx +'" placeholder="{{ trans('messages.cov-rate') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
    html += '</td>';
    html += '<td class="text-left"><span class="agent-warehouse-final-rate"></span></td>';
    html += '<td class="text-left">';
    html += '<div class="custom-file">';
    html += '<input type="file" class="custom-file-input" id="invoice_'+ idx +'" name="invoice_file_'+ idx +'[]" multiple onchange="validFile(this,\'pdf_doc_jpg_png_jpeg_xls\')">';
    html += '<label class="custom-file-label" for="invoice_'+ idx +'">{{ trans('messages.choose-file') }}</label>';
    html += '</div>';
    html += '</td>';
    html += '<td class="actions-col">';
    html += '</td>';
    html += '</tr>';

    if( $('.agent-to-warehouse-transport-tbody').find('tr').length > 0 ){
      $(html).insertAfter($('.agent-to-warehouse-transport-tbody').find('tr:last'));
    } else {
      $('.agent-to-warehouse-transport-tbody').html(html);
    }
    reindexTable('agent-to-warehouse-transport-tbody');
    if ($.fn.select2) { $('.select2').select2(); }
  }
</script>
@endsection