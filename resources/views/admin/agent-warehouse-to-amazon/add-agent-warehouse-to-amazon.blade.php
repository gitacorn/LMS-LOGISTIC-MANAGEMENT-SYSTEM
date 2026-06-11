
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
                    <li class="document-list"><a href="#details" class="document-text">{{ trans("messages.details") }}</a></li>
                    <li class="document-list"><a href="#documents" class="document-text">{{ trans("messages.documents") }}</a></li>
                    <li class="document-list"><a href="#transporter" class="document-text">{{ trans("messages.transporter-invoice") }}</a></li>
                    <li class="document-list"><a href="#status" class="document-text">{{ trans("messages.status") }}</a></li>
                    <?php if( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ) { ?>
					<li class="document-text ml-auto logistic-master-no"><?php echo (!empty($recordInfo->v_agent_to_warehouse_record_no) ? $recordInfo->v_agent_to_warehouse_record_no : '')?></li>
					<?php }?>
                </ul>
            </div>
            <div class="card mb-3 good-in-buyer-class" id="details">
                <h3 class="title-goods"><i class="fas fa-level-up-alt list-icon mr-2"></i>{{ trans("messages.details") }}</h3>
                {!! Form::open(array( 'id '=> 'add-agent-warehouse-to-amazon-form' , 'method' => 'post' , 'files' => true , 'url' => 'agent-warehouse-to-amazon/add')) !!}
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
                        <div class="row warehouse-amazon-list">
                            <div class="col-lg-3 col-md-6 col-sm-6">
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
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="logistic_partner_name">{{ trans("messages.from") }}<span class="text-danger">*</span></label>
                                    <select name="logistic_partner_name" <?php echo $disableForm ?> class="form-control select2" onchange="getPortToagentContainerDetails(this)" <?php echo ( (isset($recordInfo) && (!empty($recordInfo->i_id)) ) ? 'disabled' : '' ) ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
                                        if(!empty($logisticPartnerRecordDetails)){
                                        	foreach ($logisticPartnerRecordDetails as $logisticPartnerRecordDetail){
                                        		$encodeLogisticPartnerDetailId = Wild_tiger::encode($logisticPartnerRecordDetail->i_id);
                                        		$selected = '';
                                        		if( isset($recordInfo->i_from_logistic_partner_detail_id) && ( $recordInfo->i_from_logistic_partner_detail_id == $logisticPartnerRecordDetail->i_id) ){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{ $encodeLogisticPartnerDetailId }}" {{ $selected }}><?php echo  (!empty($logisticPartnerRecordDetail->logisticPartnerMaster->v_logistic_partner_name) ? $logisticPartnerRecordDetail->logisticPartnerMaster->v_logistic_partner_name . (!empty($logisticPartnerRecordDetail->v_logistic_partner_code) ?  ' - '. $logisticPartnerRecordDetail->v_logistic_partner_code : '' ) : '' ) ?></option>
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
                                    <select name="select_containers[]"   <?php echo $disableForm ?>  class="form-control select2 container-list" multiple onchange="getFbaRecordDetails(this)" <?php echo ( (isset($recordInfo) && (!empty($recordInfo->i_id)) ) ? 'disabled' : '' ) ?>>
                                    <?php $containerIdInfo = (!empty($recordInfo->v_container_ids) ? explode("," , ( $recordInfo->v_container_ids ) ) : [] );
                                    if(!empty($getCountryToPortGoodsOutRecordDetails)){
                                    	foreach ($getCountryToPortGoodsOutRecordDetails as $getCountryToPortGoodsOutRecordDetail){
                                    		$encodeRecordId  = Wild_tiger::encode($getCountryToPortGoodsOutRecordDetail->country_port_master_id);
                                    		$selected = '';
                                    		if( !empty($containerIdInfo) && (in_array($getCountryToPortGoodsOutRecordDetail->country_port_master_id, $containerIdInfo) ) ){
                                    			$selected = "selected='selected'";
                                    		
                                    		}
                                    		?>
                                    		<option value="{{ $encodeRecordId }}" {{ $selected }}>{{ (!empty($getCountryToPortGoodsOutRecordDetail->v_country_to_port_record_no) ? $getCountryToPortGoodsOutRecordDetail->v_country_to_port_record_no .(!empty($getCountryToPortGoodsOutRecordDetail->e_transport_way) ? ' ('.$getCountryToPortGoodsOutRecordDetail->e_transport_way .')' :''):'')  }}</option>
                                    		<?php 
                                    	}
                                    }
                                    ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row fba-record-details" <?php echo ( isset($recordInfo) && ($recordInfo->i_id) ? '' : 'style="display:none"' )  ?>>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <div class="card shadow-none border">
                                        <div class="card-header">
                                            <span class="partner-tilte">
                                                {{ trans("messages.select-fba-goods") }}
                                            </span>
                                        </div>
                                        <div class="logistic-partner card-body logistic-partner-collection">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-bordered table-sm pb-4">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th style="width:50px;min-width:50px;">{{ trans("messages.select") }} </th>
                                                            <th style="width:50px;min-width:50px;">{{ trans("messages.sr-no") }}</th>
                                                            <th class="text-left" style="width:70px;min-width:70px;">{{ trans("messages.container-number") }} </th>
                                                            <th class="text-left" style="width:100px;min-width:100px;">{{ trans("messages.fba-po-invoice") }} </th>
                                                            <th class="text-left" style="width:100px;min-width:120px;">{{ trans("messages.ref-id") }}</th>
                                                            <th class="text-left" style="width:100px;min-width:120px;">{{ trans("messages.account-company") }}</th>
                                                            <th class="text-left" style="width:150px;min-width:150px;">{{ trans("messages.to-location") }}</th>
                                                            <th class="text-left" style="width:80px;min-width:80px;">{{ trans("messages.sku") }}
                                                            <th class="text-left" style="width:80px;min-width:80px;">{{ trans("messages.unit") }}</th>
                                                            <th class="text-left" style="width:80px;min-width:80px;">{{ trans("messages.pallet") }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="goods-out-fba-record">
                                                        <?php 
                                                        if(isset($recordInfo->i_id)){
                                                        	$getFbaRecordInfo = [];
                                                        	$getFbaRecordInfo['getFbaRecordDetails'] = $getFbaRecordDetails;
                                                        	$getFbaRecordInfo['recordInfo'] = $recordInfo;
                                                        	$html = view (config('constants.AJAX_VIEW_FOLDER') . 'agent-warehouse-to-amazon/agent-warehouse-to-amazon-fba-goods' )->with ( $getFbaRecordInfo )->render();
                                                       	 	echo $html;
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6 to-warehouse-info-hide-show-div">
                                <div class="form-group">
                                    <label class="control-label" for="to">{{ trans("messages.to") }}<span class="text-danger">*</span></label>
                                    <select name="to" class="form-control way-to-warehouse-list"  <?php echo $disableForm ?>  <?php echo ( (isset($recordInfo) && (!empty($recordInfo->i_id)) ) ? 'disabled' : '' ) ?> onchange="hideShowWarehouseField(this)">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
                                       	if(isset($wayToWarehouseDetails) && !empty($wayToWarehouseDetails)){
                                       		foreach ($wayToWarehouseDetails as $key => $wayToWarehouseDetail) {
                                       			$selected = '';
                                       			if( isset($recordInfo->e_to_location) && ( $recordInfo->e_to_location == $key) ){
                                       				$selected = "selected='selected'";
                                       			}
                                       			?>
                                       			<option value="{{ $key  }}" {{ $selected }}>{{ $wayToWarehouseDetail }}</option>
                                       			<?php 
                                       		}
                                       	}
                                       	?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6 to-warehouse-details-hide-show-div" {!! (isset($recordInfo) && !empty($recordInfo->e_to_location) && $recordInfo->e_to_location == config("constants.WAREHOUSE_FBA_SHEET") ? '' : 'style="display: none"') !!}>
                                <div class="form-group">
                                    <label class="control-label" for="to_warehouse">{{ trans("messages.warehouse") }}<span class="text-danger">*</span></label>
                                    <select name="to_warehouse" class="form-control select2"  <?php echo $disableForm ?>  <?php echo ( (isset($recordInfo) && (!empty($recordInfo->i_id)) ) ? 'disabled' : '' ) ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        @if (!empty($toWarehouseDetails))
                                        	@foreach ($toWarehouseDetails as $toWarehouseDetail)
	                                        	@php
		                                        	$selected = (isset($recordInfo) && !empty($recordInfo->i_to_warehouse_id) && $recordInfo->i_to_warehouse_id == $toWarehouseDetail->i_id ? 'selected' : '');
	                                        	@endphp
                                        		<option value="{{ Wild_tiger::encode($toWarehouseDetail->i_id) }}" {{ $selected }}>{{ (!empty($toWarehouseDetail->v_warehouse_name) ? $toWarehouseDetail->v_warehouse_name : '') }}</option>
                                        	@endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="book_by" class="control-label">{{ trans("messages.book-by") }}<span class="text-danger">*</span></label>
                                    <select name="book_by" class="form-control select2"  <?php echo $disableForm ?> >
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

                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="logistic_partner" class="control-label">{{ trans("messages.logistic-partner") }}<span class="text-danger">*</span></label>
                                    <select name="logistic_partner" class="form-control select2"  <?php echo $disableForm ?> >
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
                                        		<option value="{{ $encodeId }}" {{$selected}}><?php echo  (!empty($logisticPartnerRecordDetail->logisticPartnerMaster->v_logistic_partner_name) ? $logisticPartnerRecordDetail->logisticPartnerMaster->v_logistic_partner_name . (!empty($logisticPartnerRecordDetail->v_logistic_partner_code) ?  ' - '. $logisticPartnerRecordDetail->v_logistic_partner_code : '' ) : '' ) ?></option>
                                        		<?php 
                                        	}
                                        } 
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="booking_date">{{ trans("messages.booking-date") }}<span class="text-danger">*</span></label>
                                    <input type="text" <?php echo $disableForm ?> name="booking_date" class="form-control" placeholder="{{ trans('messages.booking-date') }}" value="{{old('booking_date',  ( (isset($recordInfo) && (!empty($recordInfo->dt_booking_date))) ?  clientDate($recordInfo->dt_booking_date) : '' ) )}}">
                                </div>
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="tracking_no" class="control-label">{{ trans("messages.tracking-no") }}<span class="text-danger">*</span></label>
                                    <input type="text" <?php echo $disableForm ?>  name="tracking_no" class="form-control" placeholder="{{ trans('messages.tracking-no') }}" value="{{old('tracking_no',  ( (isset($recordInfo) && (!empty($recordInfo->v_tracking_no))) ?  ($recordInfo->v_tracking_no) : '' ) )}}">
                                </div>
                            </div>

                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="tracking_link" class="control-label">{{ trans("messages.tracking-link") }}</label>
                                    <input type="text" <?php echo $disableForm ?>  name="tracking_link" class="form-control" placeholder="{{ trans('messages.tracking-link') }}" value="{{old('tracking_link',  ( (isset($recordInfo) && (!empty($recordInfo->v_tracking_link))) ?  $recordInfo->v_tracking_link : '' ) )}}">
                                </div>
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="collection_date">{{ trans("messages.collection-date") }} </label>
                                    <input type="text" <?php echo $disableForm ?> name="collection_date" class="form-control" placeholder="{{ trans('messages.collection-date') }}" value="{{old('collection_date',  ( (isset($recordInfo) && (!empty($recordInfo->dt_collection_date))) ?  clientDate($recordInfo->dt_collection_date) : '' ) )}}">
                                </div>
                            </div>

                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="delivery_date">{{ trans("messages.delivery-date") }} </label>
                                    <input type="text" <?php echo $disableForm ?>  name="delivery_date" class="form-control" placeholder="{{ trans('messages.delivery-date') }}" value="{{old('delivery_date',  ( (isset($recordInfo) && (!empty($recordInfo->dt_delivery_date))) ?  clientDate($recordInfo->dt_delivery_date) : '' ) )}}">
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
                                                                <th style="max-width:250px;min-width:250px;">{{ trans("messages.documents") }} </th>
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
                                                                <th class="text-left" style="width:100px;min-width:100px;">{{ trans("messages.total-logistic-cost-usd") }}</th>
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
                    <?php //if(empty($documentForm)) { ?> 
	                    <?php if( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ) { ?>
	                    		<input type="hidden" name="record_id" value="{{ Wild_tiger::encode($recordInfo->i_id) }}">
	                    		<?php ## view time a update button na aavu joi and role admin hoi to avu joi ana mate ni conditon .?>
	                    	<?php if( empty($statusDisableForm) ) {?>
		                      		<button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.update') }}">{{ trans("messages.update") }}</button>
		                     <?php }?>
	                     <?php } else {?>
	                        	<button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.submit') }}">{{ trans("messages.submit") }}</button>
	                    <?php }?>
                    <?php //}?>
                        <a href="{{ config('constants.AGENT_WAREHOUSE_TO_AMAZON_MASTER_URL') }}" class="btn btn-outline-secondary shadow-sm btn-wide" title="{{ trans('messages.cancel') }}">{{ trans("messages.cancel") }}</a>
                    </div>
					<input type="hidden" name="agent_warehouse_document_type_count" value="">
                    <input type="hidden" name="agent_warehouse_transporter_count" value="">
                 {!! Form::close() !!}
            </div>

        </div>
    </section>
</main>
<script>
    $("#add-agent-warehouse-to-amazon-form").validate({
        errorClass: "invalid-input",
        rules: {
            way_of_transport: {
                required: true,
                noSpace:true
            },
            logistic_partner_name: {
                required: true,
                noSpace:true
            },
            to: {
                required:true
            },
            'select_containers[]': {
                required: true,
                noSpace:true
            },
            to_warehouse: {
                required: function(){
                    return ($.trim($('[name="to"]').val()) != '' && $.trim($('[name="to"]').val()) != null && $.trim($('[name="to"]').val()) == '{{ config("constants.WAREHOUSE_FBA_SHEET") }}' ? true : false);
				}
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
            logistic_partner_name: {
                required: "{{ trans('messages.require-from') }}"
            },
            to: {
                required: "{{ trans('messages.require-to') }}"
            },
            'select_containers[]': {
                required: "{{ trans('messages.require-select-containers') }}"
            },
            to_warehouse: {
                required: "{{ trans('messages.require-warehouse') }}"
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
        	var agent_warehouse_type_status = false;
       	 	var agent_warehouse_file_status = false;
       		$('.agent-to-warehouse-document-tbody tr').each(function(){
       			var agent_warehouse_type = $.trim($(this).find('.warehouse-document-type').val());
       			var agent_warehouse_file = $.trim($(this).find('.warehouse-document-file').val());
        		var agent_warehouse_file_valid = $.trim($(this).find('.warehouse-document-file').attr('data-valid-file'));
				if(agent_warehouse_file_valid != "" && agent_warehouse_file_valid != null && agent_warehouse_file_valid == "{{ strtolower(config('constants.SELECTION_YES'))}}"){
					
        			if( ( agent_warehouse_type == "" || agent_warehouse_type == null ) && (agent_warehouse_type_status != true) ){
						$.trim($(this).find('.warehouse-document-type').focus());
						agent_warehouse_file_status = true;
                	}
        		}
       		});
       		
       		if( agent_warehouse_file_status != false ){
        		alertifyMessage("error","{{ trans('messages.require-document-type') }} ");
           		return false;
            }

       		var agent_warehouse_transporter_name_status = false;
            var agent_warehouse_transporter_inv_no_status = false;
            $('.agent-to-warehouse-transport-tbody tr').each(function(){
         		var agent_warehouse_transporter_name = $.trim($(this).find('.agent-warehouse-transporter-name').val());
         		var agent_warehouse_transporter_inv_no = $.trim($(this).find('.agent-warehouse-transporter-inv-no').val());

				if(agent_warehouse_transporter_name != "" && agent_warehouse_transporter_name != null){
					agent_warehouse_transporter_name_status = true;
					if( ( agent_warehouse_transporter_inv_no == "" || agent_warehouse_transporter_inv_no == null ) && (agent_warehouse_transporter_inv_no_status != true) ){
						$.trim($(this).find('.agent-warehouse-transporter-inv-no').focus());
						agent_warehouse_transporter_inv_no_status = true;
                	}
         		} 
         	});
           
            if( agent_warehouse_transporter_inv_no_status != false ){
         		alertifyMessage("error","{{ trans('messages.require-inv-no') }} ");
            	return false;
            }
            var checked_warehouse_length = $('.agent-warehouse-to-amazon-selection:checked').length;
           
            if(checked_warehouse_length > 0){
            	var fba_selection_status = false;
	            $('.agent-warehouse-to-amazon-selection:checked').each(function(){
	            	fba_selection_status = true;
	            });
            }
            if( fba_selection_status != true ){
            	alertifyMessage("error","{{ trans('messages.required-atleast-checkbox-selection') }} ");
        		return false;
            }
       		var confirm_box = "";
            var confirm_box_msg = "";
            <?php if( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ) { ?>
					confirm_box = "{{ trans('messages.update-agent-warehouse-to-amazon') }}";
					confirm_box_msg = "{{ trans ( 'messages.confirm-agent-warehouse-to-amazon-update-msg') }}";

			<?php }else{?>
					confirm_box = "{{ trans('messages.add-agent-warehouse-to-amazon') }}";
					confirm_box_msg = "{{ trans ( 'messages.confirm-agent-warehouse-to-amazon-add-msg') }}";
			<?php }?>
			alertify.confirm(confirm_box,confirm_box_msg,function() {
        		$("[name='agent_warehouse_document_type_count']").val(agent_warehouse_document_type_count);
        		$("[name='agent_warehouse_transporter_count']").val(agent_warehouse_transporter_count);
        		$("[name='select_containers[]']").prop('disabled', false);
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
        $(function () {
      	  //$("[name='booking_date']").data('DateTimePicker').maxDate(moment().endOf('d'));
       });
        $("[name='collection_date']").datetimepicker({
            useCurrent: false,
            viewMode: 'days',
            ignoreReadonly: true,
            widgetPositioning: {
                vertical: 'bottom'
            },
            //minDate: moment().startOf('d'),
            format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

        });
        $("[name='delivery_date']").datetimepicker({
            useCurrent: false,
            viewMode: 'days',
            ignoreReadonly: true,
            widgetPositioning: {
                vertical: 'bottom'
            },
            //minDate: moment().startOf('d'),
            format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

        });

    });
   
   var agent_warehouse_module_url = '{{config("constants.AGENT_WAREHOUSE_TO_AMAZON_MASTER_URL")}}' + '/';
	
	function getFbaRecordDetails(thisitem){
		var country_port_master_id = $.trim($(thisitem).val());

		$.ajax({
			type: "POST",
			url: agent_warehouse_module_url + 'getFbaRecordDetails',
			data: {
				"_token": "{{ csrf_token() }}",
				'country_port_master_id': country_port_master_id
			},
			beforeSend: function() {
				showLoader();
			},
			success: function(response) {
				hideLoader();
				if(response !="" && response != null){
					$('.goods-out-fba-record').html(response);
					$('.fba-record-details').show();
				} else {
					$('.fba-record-details').hide();
				}
			},
			error: function() {
				hideLoader();
			}
		});
	}
	function getWayToDropdownDetails(thisitem){
		var destination_array = [];
		$('[name="checkbox[]"]').each(function(){
			if($(this).prop('checked') != false){
				destination_array.push($.trim($(this).attr('data-destination')));
			}
		});
		
		destination_array = $.unique(destination_array);
		
		if(destination_array != '' && destination_array != null){
			var html = '<option value="">{{ trans("messages.select") }}</option>';
			
			switch (true){
				case (($.inArray("{{ config('constants.AMAZON_FBA_SHEET') }}", destination_array) != -1) && ($.inArray("{{ config('constants.CUSTOMER_FBA_SHEET') }}", destination_array) != -1) && ($.inArray("{{ config('constants.WAREHOUSE_FBA_SHEET') }}", destination_array) != -1)) || (($.inArray("{{ config('constants.CUSTOMER_FBA_SHEET') }}", destination_array) != -1) && ($.inArray("{{ config('constants.WAREHOUSE_FBA_SHEET') }}", destination_array) != -1)):
					break;
				case ($.inArray("{{ config('constants.CUSTOMER_FBA_SHEET') }}", destination_array) != -1) && ($.inArray("{{ config('constants.WAREHOUSE_FBA_SHEET') }}", destination_array) != -1):
					break;
				case ($.inArray("{{ config('constants.AMAZON_FBA_SHEET') }}", destination_array) != -1) && ($.inArray("{{ config('constants.WAREHOUSE_FBA_SHEET') }}", destination_array) != -1):
					html += '<option value="{{ config("constants.AMAZON_FBA_SHEET") }}">{{ trans("messages.amazon") }}</option>';
					break;
				case ($.inArray("{{ config('constants.AMAZON_FBA_SHEET') }}", destination_array) != -1) && ($.inArray("{{ config('constants.CUSTOMER_FBA_SHEET') }}", destination_array) != -1):
					break;
				case ($.inArray("{{ config('constants.WAREHOUSE_FBA_SHEET') }}", destination_array) != -1):
					break;
				case ($.inArray("{{ config('constants.CUSTOMER_FBA_SHEET') }}", destination_array) != -1):
					html += '<option value="{{ config("constants.CUSTOMER_FBA_SHEET") }}">{{ trans("messages.customer") }}</option>';
					break;
				case ($.inArray("{{ config('constants.AMAZON_FBA_SHEET') }}", destination_array) != -1):
					html += '<option value="{{ config("constants.AMAZON_FBA_SHEET") }}">{{ trans("messages.amazon") }}</option>';
					break;
			}
			
			html += '<option value="{{ config("constants.WAREHOUSE_FBA_SHEET") }}">{{ trans("messages.warehouse") }}</option>';

			$('.way-to-warehouse-list').html(html);
			$('.to-warehouse-info-hide-show-div').show();
			hideShowWarehouseField($("[name='to']"));
		} else {
			$('.to-warehouse-info-hide-show-div').hide();
			$('.to-warehouse-details-hide-show-div').hide();
		}
	}
	function hideShowWarehouseField(thisitem){
		var to_warehouse = $.trim($(thisitem).val());

		if(to_warehouse != '' && to_warehouse != null && to_warehouse == '{{ config("constants.WAREHOUSE_FBA_SHEET") }}'){
			$('[name="to_warehouse"]').val('');
			$('[name="to_warehouse"]').trigger('change');
			$('.to-warehouse-details-hide-show-div').show();	
		} else {
			$('.to-warehouse-details-hide-show-div').hide();
		}
	}
</script>
@endsection