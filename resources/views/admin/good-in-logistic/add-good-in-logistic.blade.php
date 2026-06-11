@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-md-flex border-navabr align-items-center">
        <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }}</h1>
        <nav aria-label="breadcrumb" class="d-flex ml-auto mr-3">
            <ol class="breadcrumb bg-transparent p-0 mb-0 align-self-end">
                <li class="breadcrumb-item"><a href="{{ url('good-in-logistic') }}" class="category-add-link">{{ trans("messages.logistic") }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
            </ol>
        </nav>
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
					<li class="document-text ml-auto logistic-master-no"><?php echo (!empty($recordInfo->v_goods_in_logistic_master_no) ? $recordInfo->v_goods_in_logistic_master_no : '')?></li>
					<?php }?>
                </ul>
            </div>
            {{ Wild_tiger::readMessage() }}
            <div class="card mb-3" id="details">
                <h3 class="title-goods"><i class="fas fa-level-down-alt list-icon mr-2"></i>{{ trans("messages.goods-in-entry-details") }}</h3>
                
                {!! Form::open(array( 'id '=> 'add-good-in-logistic-form' , 'method' => 'post' , 'files' => true , 'url' => 'good-in-logistic/add')) !!}
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
                    <?php /* <p class=""><span class="font-weight-bold">Note: </span>Single selection of supplier name is allowed in case of delivery.</p> */?>
                        <div class="row form-start-field">
                        <div class="col-xl-3 col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label for="collection_delivery" class="control-label">{{ trans("messages.collection-delivery") }}<span class="text-danger">*</span></label>
                                    <select name="collection_delivery" class="form-control" <?php echo $disableForm ?> onchange="showGoodInBuyerInfo(this);getSupplierInfo(this);showBoxPalletsOnDelivery(this)" <?php echo ( (isset($recordInfo) && (!empty($recordInfo->i_id)) ) ? 'disabled' : '' ) ?>>
                                       <option value="">{{ trans("messages.select") }}</option>
                                       <?php 
                                        if(!empty($collectionDeliveryInfo)){
                                        	foreach ($collectionDeliveryInfo as  $key => $collectionDelivery){
                                        		$selected = '';
                                        		if((isset($recordInfo->e_logistic_collection_type) && ( $recordInfo->e_logistic_collection_type == $key) ) || ((!empty($collectionDeliveryRecordInfo)) && ($collectionDeliveryRecordInfo == $key ))){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{ $key}}" {{ $selected }}>{{ $collectionDelivery }}</option>
                                        		<?php 
                                        	}
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-5 col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="supplier_name">{{ trans("messages.supplier-name") }}<span class="text-danger">*</span></label>
                                    <select name="supplier_name" class="form-control select2  <?php /* multiple-supplier-collection */?> supplier-lilt"  <?php echo $disableForm ?> onchange="getGoodInBuyerDetails(this)" <?php echo ( (isset($recordInfo) && (!empty($recordInfo->i_id)) ) ? 'disabled' : '' ) ?>>
                                    	<option value="">{{ trans("messages.select") }}</option>
                                        <?php $supplierIds = (!empty($recordInfo->v_supplier_ids) ? explode(',', $recordInfo->v_supplier_ids) : [])?>
                                        <?php 
                                        if(!empty($supplierRecordDetails)){
                                        	foreach ($supplierRecordDetails as $supplierRecordDetail){
                                        		$encodeSupplierId  = Wild_tiger::encode($supplierRecordDetail->i_id);
                                        		$selected = '';
                                        		if (isset($recordInfo) && !empty($recordInfo->v_supplier_ids) && !empty($supplierRecordDetail->i_id) && $recordInfo->v_supplier_ids == $supplierRecordDetail->i_id || (isset($supplierId) && !empty($supplierId) && $supplierId == $supplierRecordDetail->i_id)){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{ $encodeSupplierId }}" {{ $selected }}><?php echo (!empty($supplierRecordDetail->v_supplier_name) ? $supplierRecordDetail->v_supplier_name : '') ?></option>
                                        		<?php 
                                        	}
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            

                            <div class="col-lg-12 good-in-logistic-deliver-row" <?php echo ( isset($recordInfo) && ($recordInfo->e_logistic_collection_type == config('constants.DELIVERY')) ? '' : 'style="display:none"' )  ?>>
                                <div class="form-group mb-0">
                                    <div class="logistic-partner">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered table-sm pb-4">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th style="max-width:70px;min-width:70px;">{{ trans("messages.select") }} </th>
                                                        <th style="max-width:70px;min-width:70px;">{{ trans("messages.sr-no") }}</th>
                                                        <th class="text-left" style="max-width:90px;min-width:90px;">{{ trans("messages.entry-no") }} </th>
                                                        <th class="text-left" style="max-width:120px;min-width:120px;">{{ trans("messages.supplier-location") }} </th>
                                                        <th class="text-left" style="max-width:100px;min-width:100px;">{{ trans("messages.order-date") }} </th>
                                                        <th class="text-left" style="max-width:100px;min-width:100px;">{{ trans("messages.delivery-date") }} </th>
                                                        <th class="text-left" style="max-width:auto;min-width:150px;">{{ trans("messages.po-no-sales-invoice-no") }}</th>
                                                        <th class="text-left" style="max-width:auto;min-width:150px;">{{ trans("messages.po-no-sales-invoice-amount") }}</th>
														<th class="text-left" style="max-width:auto;min-width:150px;">{{ trans("messages.mode-of-transport") }}</th>
                                                        <?php /* <th class="text-left" style="max-width:auto;min-width:150px;">{{ trans("messages.current-buyer-delivery-status") }}</th> */?>
                                                        <th class="text-left" style="max-width:auto;min-width:150px;">{{ trans("messages.current-logistic") }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="good-in-buyer-delivery">
                                                	<?php 
                                                	if(isset($recordInfo) && ($recordInfo->e_logistic_collection_type == config('constants.DELIVERY'))){
                                                		$deliveryDetails = [];
                                                		$deliveryDetails['goodInBuyerDeliveryDetails'] = $getGoodInBuyerDetails;
                                                		$deliveryDetails['recordInfo'] = $recordInfo;
                                                		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'good-in-logistic/good-in-logistic-delivery' )->with ( $deliveryDetails )->render();
                                                		echo $html;
                                                	}
                                                	
                                                	?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6 good-in-logistic-deliver-row" <?php echo ( isset($recordInfo) && ($recordInfo->e_logistic_collection_type == config('constants.DELIVERY')) ? '' : 'style="display:none"' )  ?>>
                                <div class="form-group">
                                    <label for="delivery_type" class="control-label">{{ trans("messages.delivery-type") }}<span class="text-danger">*</span></label>
                                    <select name="delivery_type" class="form-control" <?php echo $disableForm ?> <?php echo ( ( isset($recordInfo) && ( $recordInfo->i_id > 0  ) ) ? ( ( session()->get('role') == config('constants.ROLE_ADMIN') ) ? '' : 'disabled' )  : '' )   ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                       <?php 
                                        if(!empty($deliveryTypeInfo)){
                                        	foreach ($deliveryTypeInfo as  $key => $deliveryType){
                                        		
                                        		if( $key == config('constants.CANCELLED_DELIVERY_TYPE') ){
                                        			continue;
                                        		}
                                        		
                                        		$selected = '';
                                        		if( isset($recordInfo->e_logistic_delivery_type) && ( $recordInfo->e_logistic_delivery_type == $key) ){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{ $key}}" {{ $selected }}>{{ $deliveryType }}</option>
                                        		<?php 
                                        	}
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
							<div class="col-lg-2 col-md-6 good-in-logistic-deliver-row" <?php echo ( isset($recordInfo) && ($recordInfo->e_logistic_collection_type == config('constants.DELIVERY')) ? '' : 'style="display:none"' )  ?>>
                                <div class="form-group">
                                    <label class="control-label" for="delivery_delivery_date">{{ trans("messages.delivery-date") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="delivery_delivery_date" <?php echo $disableForm ?> class="form-control" placeholder="{{ trans('messages.delivery-date') }}" value="{{old('delivery_delivery_date',  ( (isset($recordInfo) && (!empty($recordInfo->dt_delivery_date))) ?  clientDate($recordInfo->dt_delivery_date) : '' ) )}}">
                                </div>
                            </div>
                            
                            <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 box-pallets-div" <?php echo (isset($recordInfo) && !empty($recordInfo->e_logistic_collection_type) && $recordInfo->e_logistic_collection_type == config('constants.DELIVERY') ? '' : "style='display: none;'")?>>
                                <div class="form-group">
                                    <label class="control-label" for="no_of_pallets_boxes">{{ trans("messages.no-of-pallets-boxes") }}<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend mr-2">
                                            <select class="form-control" name="pallets_boxes_type" id="pallets_boxes_type" <?php echo (isset($recordInfo) && ( empty($recordInfo->i_no_of_pallet_box) ) && !empty($recordInfo->e_logistic_collection_type) && $recordInfo->e_logistic_collection_type == config('constants.DELIVERY') && !empty($recordInfo->i_status_id) && ( in_array( $recordInfo->i_status_id , [ config('constants.DELIVERED_STATUS_ID') , config('constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID')  ]  ) ) ? '' : $disableForm)?>>
                                                <option value="{{ config('constants.BOX') }}" <?php echo ( (isset($recordInfo) && (!empty($recordInfo->e_dimension)) && $recordInfo->e_dimension == config('constants.BOX')) ? 'selected' : '' )?>>{{ trans("messages.box") }}</option>
                                                <option value="{{ config('constants.PALLET') }}" <?php echo ( (isset($recordInfo) && (!empty($recordInfo->e_dimension)) && $recordInfo->e_dimension == config('constants.PALLET')) ? 'selected' : (isset($recordInfo) && !empty($recordInfo) ? '' : 'selected') )?> >{{ trans("messages.pallet") }}</option>
                                            </select>
                                        </div>
                                        <input type="text" class="form-control" onchange="naturalNumber(this)" name="no_of_pallets_boxes" id="no_of_pallets_boxes" placeholder="{{  trans('messages.no-of-pallets-boxes')  }}" <?php echo (isset($recordInfo) && ( empty($recordInfo->i_no_of_pallet_box) ) && !empty($recordInfo->e_logistic_collection_type) && $recordInfo->e_logistic_collection_type == config('constants.DELIVERY') && !empty($recordInfo->i_status_id) && ( in_array( $recordInfo->i_status_id , [ config('constants.DELIVERED_STATUS_ID') , config('constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID')  ]  ) ) ? '' : $disableForm) ?> value="{{old('no_of_pallets_boxes',  ( (isset($recordInfo) && (!empty($recordInfo->i_no_of_pallet_box))) ?  $recordInfo->i_no_of_pallet_box : '' ) )}}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 good-in-logistic-collection-row"<?php echo ( isset($recordInfo) && ($recordInfo->e_logistic_collection_type == config('constants.COLLECTION')) ? '' : 'style="display:none"' )  ?>>
                                <div class="form-group">
                                    <div class="logistic-partner logistic-partner-collection">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered table-sm pb-4">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th style="width:50px;min-width:50px;">{{ trans("messages.select") }} </th>
                                                        <th style="width:50px;min-width:50px;">{{ trans("messages.sr-no") }}</th>
                                                        <th class="text-left" style="width:70px;min-width:70px;">{{ trans("messages.entry-no") }} </th>
                                                        <th class="text-left" style="width:100px;min-width:100px;">{{ trans("messages.order-date") }} </th>
                                                        <th class="text-left" style="width:100px;min-width:120px;">{{ trans("messages.po-no-sales-invoice-no") }}</th>
                                                        <th class="text-left" style="width:100px;min-width:120px;">{{ trans("messages.po-no-sales-invoice-amount") }}</th>
                                                        <th class="text-left" style="width:150px;min-width:150px;">{{ trans("messages.supplier-location-collection") }}</th>
                                                        <th class="text-left" style="width:150px;min-width:150px;">{{ trans("messages.supplier-name") }}</th>
														<th class="text-left" style="width:150px;min-width:150px;">{{ trans("messages.mode-of-transport") }}</th>
                                                        <th class="text-left" style="width:150px;min-width:150px;">{{ trans("messages.delivery-type") }}<span class="text-danger">*</span></th>
                                                        <th class="text-left" style="width:180px;min-width:180px;">{{ trans("messages.delivery-location") }}<span class="text-danger">*</span></th>
                                                        <th class="text-left" style="width:180px;min-width:180px;">{{ trans("messages.delivery-remarks") }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="good-in-logistic-collection-type">
                                                    <?php 
	                                                	if(isset($recordInfo) && ($recordInfo->e_logistic_collection_type == config('constants.COLLECTION'))){
	                                                		$collectionDetails = [];
	                                                		$collectionDetails['goodInBuyercollectionDetails'] = $getGoodInBuyerDetails;
	                                                		$collectionDetails['recordInfo'] = $recordInfo;
	                                                		$collectionDetails['deliveryTypeInfo'] = $deliveryTypeInfo;
	                                                		$collectionDetails['warehouseRecordDetails'] = $warehouseRecordDetails;
	                                                		$collectionDetails['disableForm'] = $disableForm;
	                                                		$html = view (config('constants.AJAX_VIEW_FOLDER') . 'good-in-logistic/good-in-logistic-collection' )->with ( $collectionDetails )->render();
	                                                		echo $html;	
	                                                	}
                                                	?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 good-in-logistic-collection-row" <?php echo ( isset($recordInfo) && ($recordInfo->e_logistic_collection_type == config('constants.COLLECTION')) ? '' : 'style="display:none"' )  ?>>
                                <div class="form-group">
                                    <label for="book_by" class="control-label">{{ trans("messages.book-by") }}<span class="text-danger">*</span></label>
                                    <select name="book_by" class="form-control select2" <?php echo $disableForm ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
	                                        if(!empty($userRecordDetails)){
	                                        	foreach ($userRecordDetails as $userRecordDetail){
	                                        		$encodevUserId  = Wild_tiger::encode($userRecordDetail->i_id);
	                                        		$selected = '';
	                                        		if( isset($recordInfo->i_book_employee_id) && ( $recordInfo->i_book_employee_id == $userRecordDetail->i_id ) ){
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

                            <div class="col-lg-3 col-sm-6 good-in-logistic-collection-row" <?php echo ( isset($recordInfo) && ($recordInfo->e_logistic_collection_type == config('constants.COLLECTION')) ? '' : 'style="display:none"' )  ?>>
                                <div class="form-group">
                                    <label for="logistic_partner" class="control-label">{{ trans("messages.logistic-partner") }}<span class="text-danger">*</span></label>
                                    <select name="logistic_partner" class="form-control select2" <?php echo $disableForm ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
	                                        if(!empty($logisticPartnerDetails)){
	                                        	foreach ($logisticPartnerDetails as $logisticPartnerDetail){
	                                        		$encodeId  = Wild_tiger::encode($logisticPartnerDetail->i_id);
	                                        		$selected = '';
	                                        		if( isset($recordInfo->i_logistic_partner_id) && ( $recordInfo->i_logistic_partner_id == $logisticPartnerDetail->i_id ) ){
	                                        			$selected = "selected='selected'";
	                                        		}
	                                        		?>
	                                        		<option value="{{ $encodeId }}" {{ $selected }}><?php echo  (!empty($logisticPartnerDetail->logisticPartnerMaster->v_logistic_partner_name) ? $logisticPartnerDetail->logisticPartnerMaster->v_logistic_partner_name . (!empty($logisticPartnerDetail->v_logistic_partner_code) ?  ' ('. $logisticPartnerDetail->v_logistic_partner_code : '' ) .')' : '' ) ?></option>
	                                        		<?php 
	                                        	}
	                                        } 
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-2 col-sm-4 good-in-logistic-collection-row" <?php echo ( isset($recordInfo) && ($recordInfo->e_logistic_collection_type == config('constants.COLLECTION')) ? '' : 'style="display:none"' )  ?>>
                                <div class="form-group">
                                    <label class="control-label" for="collection_date">{{ trans("messages.collection-date") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="collection_date" <?php echo $disableForm ?> class="form-control" placeholder="{{ trans('messages.collection-date') }}" value="{{old('collection_date',  ( (isset($recordInfo) && (!empty($recordInfo->dt_collection_date))) ?  clientDate($recordInfo->dt_collection_date) : '' ) )}}">
                                </div>
                            </div>

                            <div class="col-lg-2 col-sm-4 good-in-logistic-collection-row" <?php echo ( isset($recordInfo) && ($recordInfo->e_logistic_collection_type == config('constants.COLLECTION')) ? '' : 'style="display:none"' )  ?>>
                                <div class="form-group">
                                    <label class="control-label" for="delivery_date">{{ trans("messages.delivery-date") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="delivery_date" <?php echo $disableForm ?> class="form-control" placeholder="{{ trans('messages.delivery-date') }}" value="{{old('delivery_date',  ( (isset($recordInfo) && (!empty($recordInfo->dt_delivery_date))) ?  clientDate($recordInfo->dt_delivery_date) : '' ) )}}">
                                </div>
                            </div>

                            <div class="col-lg-2 col-sm-4 good-in-logistic-collection-row" <?php echo ( isset($recordInfo) && ($recordInfo->e_logistic_collection_type == config('constants.COLLECTION')) ? '' : 'style="display:none"' )  ?>>
                                <div class="form-group">
                                    <label for="booking_ref_no" class="control-label">{{ trans("messages.booking-ref-no") }}</label>
                                    <input type="text" name="booking_ref_no" <?php echo $disableForm ?> class="form-control" placeholder="{{ trans('messages.booking-ref-no') }}" value="{{old('booking_ref_no',  ( (isset($recordInfo) && (!empty($recordInfo->v_booking_ref_no))) ?  $recordInfo->v_booking_ref_no : '' ) )}}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-2 col-sm-4">
                                <div class="form-group">
                                    <label for="tracking_no" class="control-label">{{ trans("messages.tracking-no") }}</label>
                                    <input type="text" name="tracking_no" <?php echo $disableForm ?> class="form-control" placeholder="{{ trans('messages.tracking-no') }}" value="{{old('tracking_no',  ( (isset($recordInfo) && (!empty($recordInfo->v_tracking_no))) ?  ($recordInfo->v_tracking_no) : '' ) )}}">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-4">
                                <div class="form-group">
                                    <label for="tracking_link" class="control-label">{{ trans("messages.tracking-link") }}</label>
                                    <input type="text" name="tracking_link" <?php echo $disableForm ?> class="form-control" placeholder="{{ trans('messages.tracking-link') }}" value="{{old('tracking_link',  ( (isset($recordInfo) && (!empty($recordInfo->v_tracking_link))) ? ($recordInfo->v_tracking_link) : '' ) )}}">
                                </div>
                            </div>

                            <div class="col-lg-2 col-sm-4">
                                <div class="form-group">
                                    <label for="insurance_status" class="control-label">{{ trans("messages.insurance-status") }}</label>
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

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label for="insurance_status_comments" class="control-label">{{ trans("messages.insurance-status-comments") }}</label>
                                    <input type="text" <?php echo $disableForm ?> name="insurance_status_comments" class="form-control" placeholder="{{ trans('messages.insurance-status-comments') }}" value="{{old('insurance_status_comments',  ( (isset($recordInfo) && (!empty($recordInfo->v_insurance_comment))) ? ($recordInfo->v_insurance_comment) : '' ) )}}">
                                </div>
                            </div>
							<div class="col-lg-2 col-sm-4">
                                <div class="form-group">
                                    <label class="control-label" for="goods_in_date">{{ trans("messages.goods-in-date") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="goods_in_date" class="form-control" <?php echo $disableForm ?> placeholder="{{ trans('messages.goods-in-date') }}" value="{{old('goods_in_date',  ( (isset($recordInfo) && (!empty($recordInfo->dt_goods_in_date))) ?  clientDate($recordInfo->dt_goods_in_date) : '' ) )}}">
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
                                                        <tbody class="good-in-logistic-master-tbody">
                                                        	<?php 
                                                        	if( isset($recordInfo->goodInLogisticDocument) && (!empty($recordInfo->goodInLogisticDocument)) && (count($recordInfo->goodInLogisticDocument) > 0 ) ){
                                                        		foreach ($recordInfo->goodInLogisticDocument as $countKey => $goodInLogisticDocumentDetail){
                                                        			$columIndex  = ( $countKey +  1 );
                                                        			?>
	                                                        		<tr>
		                                                                <td class="table-index text-center" style="width:70px;min-width:70px;">{{$columIndex}}</td>
		                                                                <td class="text-left">
		                                                                    <select  <?php echo $documentForm ?> name="edit_type_<?php echo $goodInLogisticDocumentDetail->i_id ?>" class="form-control good-in-logistic-type">
		                                                                    	<option value="">{{ trans("messages.select") }}</option>
		                                                                        <?php 
		                                                                        if(!empty($documentTypeRecordDetails)){
		                                                                        	foreach ($documentTypeRecordDetails as $documentTypeRecordDetail){
		                                                                        		$encodevDocumentTypeId  = Wild_tiger::encode($documentTypeRecordDetail->i_id);
		                                                                        		$selected = '';
		                                                                        		if( isset($goodInLogisticDocumentDetail->i_document_type_id) && ( $goodInLogisticDocumentDetail->i_document_type_id == $documentTypeRecordDetail->i_id ) ){
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
		                                                                <?php $documentFiles = (json_decode($goodInLogisticDocumentDetail->v_document_file_path)); ?>
		                                                                <td class="text-left">
		                                                                    <div class="custom-file">
		                                                                        <input type="file" <?php echo $documentForm ?> class="custom-file-input good-in-logistic-file" id="document_<?php echo $goodInLogisticDocumentDetail->i_id ?>" name="edit_file_<?php echo $goodInLogisticDocumentDetail->i_id ?>[]" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
		                                                                        <label class="custom-file-label" for="document_<?php echo $goodInLogisticDocumentDetail->i_id ?>"><?php echo (!empty($documentFiles) ? ( count($documentFiles) > 1 ? trans('messages.multiple-files') : ( isset($documentFiles[0]) ? basename($documentFiles[0]) : '' ) )  : trans('messages.choose-file') ) ?></label>
		                                                                    </div>
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" <?php echo $documentForm ?> class="form-control" name="edit_remarks_<?php echo $goodInLogisticDocumentDetail->i_id ?>" value="<?php echo (isset($goodInLogisticDocumentDetail->v_document_remark) ? $goodInLogisticDocumentDetail->v_document_remark : '' ); ?>">
		                                                                </td>
		                                                                <td class="actions-col">
																		<?php 
		                                                                	if(!empty($documentFiles)){
			                                                                	foreach ($documentFiles as $documentFile){
			                                                                		$imagePath = (config('constants.FILE_STORAGE_URL_PATH').$documentFile);
			                                                                	?>
			                                                                	<div class="download-link-items">
				                                                                  <a title="{{trans('messages.remove')}}" href="javascript:void(0);" data-file-name="{{ basename($imagePath) }}" onclick="removeUploadedFile(this);" data-record-id="{{ $goodInLogisticDocumentDetail->i_id }}" data-field-name="document" class="close-icon"><i class="fa fa-times "></i></a>
				                                                                  <a title="{{ basename($imagePath) }}" href="{{ $imagePath }}" target='_blank' class="btn btn-sm btn-danger mb-1 download-icon-items"><i class="fa fa-download"></i></a>
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
	                                                                    <select name="type_1" <?php echo $documentForm ?> class="form-control good-in-logistic-type">
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
	                                                                        <input type="file" <?php echo $documentForm ?> class="custom-file-input good-in-logistic-file" id="document_1" multiple  name="file_1[]" onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
	                                                                        <label class="custom-file-label" for="document_1">{{ trans('messages.choose-file') }}</label>
	                                                                    </div>
	                                                                </td>
	                                                                <td class="text-left">
	                                                                    <input type="text" <?php echo $documentForm ?> class="form-control" name="remarks_1">
	                                                                </td>
	                                                                <td class="actions-col">
	                                                                </td>
	
	                                                                <td style="width:70px;min-width:70px;"></td>
	                                                            </tr>
                                                           <?php }?>
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
                                                                <th class="text-left" style="width:100px;min-width:100px;">{{ trans("messages.final-total") }}</th>
                                                                <th class="text-left" style="width:250px;min-width:250px;">{{ trans("messages.attach-documents") }}</th>
                                                                <th class="text-center" style="width:80px;min-width:80px;">{{ trans("messages.documents") }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="good-in-logistic-transporter-tbody">
                                                        	<?php 
                                                        	if( isset($recordInfo->goodInLogisticInvoice) && (!empty($recordInfo->goodInLogisticInvoice)) && (count($recordInfo->goodInLogisticInvoice) > 0 ) ){
                                                        		foreach ($recordInfo->goodInLogisticInvoice as $countKey => $goodInLogisticTransportDetail){
                                                        			$countIndex = ($countKey + 1 );
                                                        			?>
		                                                        	<tr>
		                                                                <td class="table-index">{{ $countIndex }}</td>
		                                                                <td class="text-left">
		                                                                	<select name="edit_name_<?php echo $goodInLogisticTransportDetail->i_id ?>" {{ $invoiceForm }} class="form-control good-in-logistic-transporter-name select2">
		                                                                    	<option value="">{{ trans("messages.select") }}</option>
		                                                                        <?php 
		                                                                        if(!empty($logisticPartnerRecordDetails)){
		                                                                        	foreach ($logisticPartnerRecordDetails as $logisticPartnerRecordDetail){
		                                                                        		$encodeId  = Wild_tiger::encode($logisticPartnerRecordDetail->i_id);
		                                                                        		$selected = '';
		                                                                        		if( isset($goodInLogisticTransportDetail->i_logistic_partner_master_id) && ( $goodInLogisticTransportDetail->i_logistic_partner_master_id == $logisticPartnerRecordDetail->i_id ) ){
		                                                                        			$selected = "selected='selected'";
		                                                                        		}
		                                                                        		?>
		                                                                        		<option value="{{ $encodeId }}" {{ $selected}}>{{ (!empty($logisticPartnerRecordDetail->v_logistic_partner_name) ? $logisticPartnerRecordDetail->v_logistic_partner_name : '' ) }}</option>
		                                                                        		<?php 
		                                                                        	}
		                                                                        }	
		                                                                        ?>
		                                                                    </select>
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" {{ $invoiceForm }} class="form-control good-in-logistic-transporter-inv-no" name="edit_inv_no_<?php echo $goodInLogisticTransportDetail->i_id ?>" placeholder="{{ trans('messages.inv-no') }}" value="<?php echo (isset($goodInLogisticTransportDetail->v_invoice_no) ? $goodInLogisticTransportDetail->v_invoice_no : '' ); ?>">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" {{ $invoiceForm }} class="form-control agent-to-warehouse-freight" name="edit_freight_<?php echo $goodInLogisticTransportDetail->i_id ?>" placeholder="{{ trans('messages.freight') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($goodInLogisticTransportDetail->d_freight_charge) ? $goodInLogisticTransportDetail->d_freight_charge : '' ); ?>">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" {{ $invoiceForm }} class="form-control agent-to-warehouse-custom" name="edit_custom_<?php echo $goodInLogisticTransportDetail->i_id ?>" placeholder="{{ trans('messages.custom') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($goodInLogisticTransportDetail->d_custom_charge) ? $goodInLogisticTransportDetail->d_custom_charge : '' ); ?>">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" {{ $invoiceForm }} class="form-control agent-to-warehouse-duty" name="edit_duty_<?php echo $goodInLogisticTransportDetail->i_id ?>" placeholder="{{ trans('messages.duty') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($goodInLogisticTransportDetail->d_duty_charge) ? $goodInLogisticTransportDetail->d_duty_charge : '' ); ?>">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" {{ $invoiceForm }} class="form-control agent-to-warehouse-other" name="edit_other_<?php echo $goodInLogisticTransportDetail->i_id ?>" placeholder="{{ trans('messages.other') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($goodInLogisticTransportDetail->d_other_charge) ? $goodInLogisticTransportDetail->d_other_charge : '' ); ?>">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" {{ $invoiceForm }} class="form-control agent-to-warehouse-vat" name="edit_vat_<?php echo $goodInLogisticTransportDetail->i_id ?>" placeholder="{{ trans('messages.vat') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($goodInLogisticTransportDetail->d_vat_charge) ? $goodInLogisticTransportDetail->d_vat_charge : '' ); ?>">
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <div class="input-group align-items-center flex-nowrap">
		                                                                        <label class="mb-0" for=""><span class="agent-warehouse-total-value"><?php echo (isset($goodInLogisticTransportDetail->d_total_charge) ? $goodInLogisticTransportDetail->d_total_charge :'')?></span></label>
		                                                                        <div class="input-group-prepend">
		                                                                            <select class="form-control ml-2" {{ $invoiceForm }} name="edit_amount_<?php echo $goodInLogisticTransportDetail->i_id ?>">
		                                                                             <option selected value="">Currency</option>
		                                                                                <?php 
												                                        if(!empty($currencyRecordDetails)){
												                                        	foreach ($currencyRecordDetails as $currencyRecordDetail){
												                                        		$encodeCurrencyrId  = Wild_tiger::encode($currencyRecordDetail->i_id);
												                                        		$selected = '';
												                                        		if( isset($goodInLogisticTransportDetail->i_currency_id) && ( $goodInLogisticTransportDetail->i_currency_id == $currencyRecordDetail->i_id ) ){
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
																		<?php $invoiceFiles = (json_decode($goodInLogisticTransportDetail->v_invoice_file_path)); ?>	
		                                                                <td class="text-left">
		                                                                    <input type="text" {{ $invoiceForm }} class="form-control agent-to-warehouse-con-rate" name="edit_cov_rate_<?php echo $goodInLogisticTransportDetail->i_id ?>" placeholder="{{ trans('messages.cov-rate') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)" value="<?php echo (isset($goodInLogisticTransportDetail->d_conversion_rate) ? $goodInLogisticTransportDetail->d_conversion_rate : '' ); ?>">
		                                                                </td>
		                                                                <td class="text-left"><span class="agent-warehouse-final-rate"><?php echo (isset($goodInLogisticTransportDetail->d_final_charge) ? $goodInLogisticTransportDetail->d_final_charge : '' ); ?></span></td>
		                                                               	<td class="text-left">
		                                                                    <div class="custom-file">
		                                                                        <input type="file" {{ $invoiceForm }} class="custom-file-input" id="invoice_document_<?php echo $goodInLogisticTransportDetail->i_id ?>" multiple name="edit_invoice_file_<?php echo $goodInLogisticTransportDetail->i_id ?>[]" onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
		                                                                        <label class="custom-file-label" for="invoice_document_<?php echo $goodInLogisticTransportDetail->i_id ?>"><?php echo (!empty($invoiceFiles) ? ( count($invoiceFiles) > 1 ? trans('messages.multiple-files') : ( isset($invoiceFiles[0]) ? basename($invoiceFiles[0]) : '' ) )  : trans('messages.choose-file') ) ?></label>
		                                                                    </div>
		                                                                </td>
		                                                                
				                                                                
		                                                                <td class="actions-col">
																			<?php 
			                                                                	if(!empty($invoiceFiles)){
				                                                                	foreach ($invoiceFiles as $invoiceFile){
				                                                                		$invoicePath = (config('constants.FILE_STORAGE_URL_PATH').$invoiceFile);
				                                                                	?>
				                                                                	<div class="download-link-items">
			                                                                  			<a title="{{trans('messages.remove')}}"  href="javascript:void(0);" data-file-name="{{ basename($invoicePath) }}" onclick="removeUploadedFile(this);" data-record-id="{{ $goodInLogisticTransportDetail->i_id }}" data-field-name="invoice"  class="close-icon"><i class="fa fa-times "></i></a>
			                                                                  			<a title="{{ basename($invoicePath) }}" href="{{ $invoicePath }}" target='_blank' class="btn btn-sm btn-danger mb-1 download-icon-items"><i class="fa fa-download"></i></a>
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
	                                                                 	<select name="name_1" {{ $invoiceForm }} class="form-control good-in-logistic-transporter-name select2">
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
	                                                                    <input type="text" {{ $invoiceForm }} class="form-control good-in-logistic-transporter-inv-no" name="inv_no_1" placeholder="{{ trans('messages.inv-no') }}">
	                                                                </td>
	                                                                <td class="text-left">
	                                                                    <input type="text" {{ $invoiceForm }} class="form-control agent-to-warehouse-freight" name="freight_1" placeholder="{{ trans('messages.freight') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
	                                                                </td>
	                                                                <td class="text-left">
	                                                                    <input type="text" {{ $invoiceForm }} class="form-control agent-to-warehouse-custom" name="custom_1" placeholder="{{ trans('messages.custom') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
	                                                                </td>
	                                                                <td class="text-left">
	                                                                    <input type="text" {{ $invoiceForm }} class="form-control agent-to-warehouse-duty" name="duty_1" placeholder="{{ trans('messages.duty') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
	                                                                </td>
	                                                                <td class="text-left">
	                                                                    <input type="text" {{ $invoiceForm }} class="form-control agent-to-warehouse-other" name="other_1" placeholder="{{ trans('messages.other') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
	                                                                </td>
	                                                                <td class="text-left">
	                                                                    <input type="text" {{ $invoiceForm }} class="form-control agent-to-warehouse-vat" name="vat_1" placeholder="{{ trans('messages.vat') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
	                                                                </td>
	                                                                <td class="text-left">
	                                                                    <div class="input-group align-items-center flex-nowrap">
	                                                                        <label class="mb-0" for=""><span class="agent-warehouse-total-value"></span></label>
	                                                                        <div class="input-group-prepend">
	                                                                            <select class="form-control ml-2" name="amount_1" {{ $invoiceForm }}>
	                                                                             <option selected value="">Currency</option>
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
	                                                                    <input type="text" {{ $invoiceForm }} class="form-control agent-to-warehouse-con-rate" name="cov_rate_1" placeholder="{{ trans('messages.cov-rate') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
	                                                                </td>
	                                                                <td class="text-left"><span class="agent-warehouse-final-rate"></span></td>
	                                                                <td class="text-left">
	                                                                    <div class="custom-file">
	                                                                        <input type="file" {{ $invoiceForm }} class="custom-file-input" id="invoice_document_1" multiple name="invoice_file_1[]" onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
	                                                                        <label class="custom-file-label" for="invoice_document_1">Choose file</label>
	                                                                    </div>
	                                                                </td>
	                                                                <td class="actions-col">
	                                                                </td>
	                                                            </tr>
	
	                                                            <tr>
	                                                                <td class="table-index">2</td>
	                                                                <td class="text-left">
	                                                                	<select name="name_2" {{ $invoiceForm }} class="form-control good-in-logistic-transporter-name select2">
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
	                                                                    <input type="text" {{ $invoiceForm }} class="form-control good-in-logistic-transporter-inv-no" name="inv_no_2" placeholder="{{ trans('messages.inv-no') }}">
	                                                                </td>
	                                                                <td class="text-left">
	                                                                    <input type="text" {{ $invoiceForm }} class="form-control agent-to-warehouse-freight" name="freight_2" placeholder="{{ trans('messages.freight') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
	                                                                </td>
	                                                                <td class="text-left">
	                                                                    <input type="text" {{ $invoiceForm }} class="form-control agent-to-warehouse-custom" name="custom_2" placeholder="{{ trans('messages.custom') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
	                                                                </td>
	                                                                <td class="text-left">
	                                                                    <input type="text" {{ $invoiceForm }} class="form-control agent-to-warehouse-duty" name="duty_2" placeholder="{{ trans('messages.duty') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
	                                                                </td>
	                                                                <td class="text-left">
	                                                                    <input type="text" {{ $invoiceForm }} class="form-control agent-to-warehouse-other" name="other_2" placeholder="{{ trans('messages.other') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
	                                                                </td>
	                                                                <td class="text-left">
	                                                                    <input type="text" {{ $invoiceForm }} class="form-control agent-to-warehouse-vat" name="vat_2" placeholder="{{ trans('messages.vat') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
	                                                                </td>
	                                                                <td class="text-left">
	                                                                    <div class="input-group align-items-center flex-nowrap">
	                                                                        <label class="mb-0" for=""><span class="agent-warehouse-total-value"></span></label>
	                                                                        <div class="input-group-prepend">
	                                                                            <select class="form-control ml-2" name="amount_2" {{ $invoiceForm }}>
	                                                                                <option selected value="">Currency</option>
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
	                                                                    <input type="text" {{ $invoiceForm }} class="form-control agent-to-warehouse-con-rate" name="cov_rate_2" placeholder="{{ trans('messages.cov-rate') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">
	                                                                </td>
	                                                                <td class="text-left"><span class="agent-warehouse-final-rate"></span></td>
	                                                                <td class="text-left">
	                                                                    <div class="custom-file">
	                                                                        <input type="file" {{ $invoiceForm }} class="custom-file-input" id="invoice_document_2" multiple name="invoice_file_2[]" onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
	                                                                        <label class="custom-file-label" for="invoice_document_2">Choose file</label>
	                                                                    </div>
	                                                                </td>
	                                                                <td class="actions-col">
	                                                                </td>
	                                                            </tr>
                                                            <?php }?>
                                                        </tbody>
                                                    </table>
                                                    <?php if(empty($invoiceForm)) { ?>
                                                    <button type="button" class="btn btn-sm bg-theme text-white add-new-row mb-3" title="{{ trans('messages.add-new') }}" onclick="addNewTransporterInvoiceRow(this)"><i class="fa fa-plus fa-fw"></i>{{ trans("messages.add-new") }}</button>
                                                    <?php } ?>
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
                                        <select name="status" class="form-control" {{ ( $statusDisableForm )  }}>
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
                                        <label for="status_comments" class="control-label">{{ trans("messages.warehouse-comments") }}</label>
                                        <input type="text" name="status_comments" <?php echo $disableForm ?> class="form-control" placeholder="{{ trans('messages.warehouse-comments') }}" value="{{old('status_comments',  ( (isset($recordInfo) && (!empty($recordInfo->v_status_comment))) ? ($recordInfo->v_status_comment) : '' ) )}}">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 submit-sticky">
                   	<?php //if(empty($documentForm)) { ?> 
	                    <?php if( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ) { ?>
	                    	<input type="hidden" name="record_id" value="{{ Wild_tiger::encode($recordInfo->i_id) }}">
							<input type="hidden" name="skip_mail" id="skip_mail_flag" value="0">
	                    		<?php ## view time a updatebutton na aavu joi and role admin hoi to avu joi ana mate ni conditon .?>
	                    		<?php if( empty($statusDisableForm) || (  isset($recordInfo) && ($recordInfo->e_logistic_collection_type == config('constants.COLLECTION')) && ( $viewRequest != true ) ) ){?>
	                        		<button type="button" id="logistic-submit-btn" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.update') }}">{{ trans("messages.update") }}</button>
	                        	<?php } ?>
	                    <?php } else {?>
	                    	<button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.submit') }}">{{ trans("messages.submit") }}</button>
	                    <?php }?>
                    <?php //} ?>
                    	<a href="{{ config('constants.GOODS_IN_LOGITIC_MASTER_URL') }}" class="btn btn-outline-secondary shadow-sm btn-wide" title="{{ trans('messages.cancel') }}">{{ trans("messages.cancel") }}</a>
                    </div>
                    <input type="hidden" name="good_in_logistic_document_type_count" value="">
                    <input type="hidden" name="good_in_logistic_transporter_count" value="">
                    {!! Form::close() !!}
					
{{-- ── Mail confirmation modal (edit mode only) ── --}}
<?php if( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ): ?>
<div class="modal fade" id="mailConfirmModal" tabindex="-1" role="dialog" aria-labelledby="mailConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mailConfirmModalLabel">
                    <i class="fas fa-envelope mr-2"></i> Send Notification Email?
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-1">The status has been changed to <strong id="modal-status-name"></strong>.</p>
                <p class="mb-0">Do you want to send the notification email to all recipients?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="modal-no-btn">
                    <i class="fas fa-times mr-1"></i> No, Save Only
                </button>
                <button type="button" class="btn bg-theme text-white" id="modal-yes-btn">
                    <i class="fas fa-paper-plane mr-1"></i> Yes, Send Email
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    /* Previous status ID stored when the page loaded (edit mode) */
    var previousStatusId = {{ isset($recordInfo) && !empty($recordInfo->i_status_id) ? (int)$recordInfo->i_status_id : 0 }};
    var formSubmitting   = false;

    $('#logistic-submit-btn').on('click', function() {
        /* Trigger jQuery Validate first */
        var form = $('#add-good-in-logistic-form');
        if (!form.valid()) return;

        var $statusSelect  = $('[name="status"]');
        var selectedOption = $statusSelect.find('option:selected');
        var newStatusId    = parseInt(selectedOption.attr('data-status-id') || 0);
        var newStatusName  = $.trim(selectedOption.text());

        /*
         * Show popup ONLY when:
         *   - This is an edit (previousStatusId > 0)
         *   - The NEW selected status is DELIVERED (ID 5)
         * For any other status change — submit directly, no popup.
         * First-time create never reaches here (button is type="submit" not type="button")
         */
        var deliveredStatusId = {{ config('constants.DELIVERED_STATUS_ID') }};
        if (previousStatusId > 0 && newStatusId === deliveredStatusId) {
            /* Show the modal with the new status name */
            $('#modal-status-name').text(newStatusName);
            $('#mailConfirmModal').modal('show');
        } else {
            /* Status unchanged or first save — submit directly without popup */
            $('#skip_mail_flag').val('0');
            formSubmitting = true;
            form.submit();
        }
    });

    /* "Yes, Send Email" — submit normally */
    $('#modal-yes-btn').on('click', function() {
        $('#mailConfirmModal').modal('hide');
        $('#skip_mail_flag').val('0');
        formSubmitting = true;
        $('#add-good-in-logistic-form').submit();
    });

    /* "No, Save Only" — skip mail */
    $('#modal-no-btn').on('click', function() {
        $('#mailConfirmModal').modal('hide');
        $('#skip_mail_flag').val('1');
        formSubmitting = true;
        $('#add-good-in-logistic-form').submit();
    });
})();
</script>
<?php endif; ?>
            </div>
        </div>
    </section>
</main>
<script>
    $("#add-good-in-logistic-form").validate({
        errorClass: "invalid-input",
        rules: {
            supplier_name: {
                required: true,
                noSpace:true
            },
            collection_delivery: {
                required: true,
                noSpace:true
            },
            delivery_type: {
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
            collection_date: {
    		    required: function(element){
        		   return ( ($.trim($("[name='status']").find('option:selected').attr('data-status-id')) == '{{ config("constants.DELIVERED_STATUS_ID") }}') || ($.trim($("[name='status']").find('option:selected').attr('data-status-id')) == '{{ config("constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID") }}') ? true : false ) 
    			},
    		    noSpace: true
    	   },
    	   no_of_pallets_boxes: {
    		   required: function(){
    			   return ( ($.trim($('[name="collection_delivery"]').val()) != '' && $.trim($('[name="collection_delivery"]').val()) != null && $.trim($('[name="collection_delivery"]').val()) == '{{ config("constants.DELIVERY") }}')  ? true : false)
    		   }
           },
    	   delivery_date: {
	   		    required: function(element){
	   		    	return ( ($.trim($("[name='status']").find('option:selected').attr('data-status-id')) == '{{ config("constants.DELIVERED_STATUS_ID") }}') || ($.trim($("[name='status']").find('option:selected').attr('data-status-id')) == '{{ config("constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID") }}') ? true : false ) 
	   			},
	   		    noSpace: true
   	   	   },
   	   	  delivery_delivery_date: {
   		    	required: function(element){
   		    		return ( ($.trim($("[name='status']").find('option:selected').attr('data-status-id')) == '{{ config("constants.DELIVERED_STATUS_ID") }}') || ($.trim($("[name='status']").find('option:selected').attr('data-status-id')) == '{{ config("constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID") }}') ? true : false ) 
   		  		},
   		    	noSpace: true
	   	   },
	   	goods_in_date: {
            required: true,
        },

        },
        messages: {
        	supplier_name: {
                required: "{{ trans('messages.require-supplier-name') }}"
            },
            collection_delivery: {
                required: "{{ trans('messages.require-collection-delivery') }}"
            },
            delivery_type: {
                required: "{{ trans('messages.require-delivery-type') }}"
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
            collection_date:  {
                required: "{{ trans('messages.require-collection-date') }}"
            },
            delivery_date:  {
                required: "{{ trans('messages.require-delivery-date') }}"
            },
            no_of_pallets_boxes:  {
                required: "{{ trans('messages.require-no-of-pallets-boxes') }}"
            },
            delivery_delivery_date:  {
                required: "{{ trans('messages.require-delivery-date') }}"
            },
            goods_in_date:  {
                required: "{{ trans('messages.required-enter-field-validation', ['fieldName' => trans('messages.goods-in-date')]) }}"
            },
        },
        submitHandler: function(form) {

        	var good_in_buyer_type_status = false;
	       	var good_in_buyer_file_status = false;
	       	$('.good-in-logistic-master-tbody tr').each(function(){
	       		var good_in_buyer_type = $.trim($(this).find('.good-in-logistic-type').val());
	       		var good_in_buyer_file = $.trim($(this).find('.good-in-logistic-file').val());
	       		var good_in_buyer_file_valid = $.trim($(this).find('.good-in-logistic-file').attr('data-valid-file'));
	       		
	       		if( good_in_buyer_file_valid != "" && good_in_buyer_file_valid != null && good_in_buyer_file_valid == "{{ strtolower(config('constants.SELECTION_YES'))}}"){
	       			
	       			if( ( good_in_buyer_type == "" || good_in_buyer_type == null ) && (good_in_buyer_type_status != true) ){
							$.trim($(this).find('.good-in-logistic-type').focus());
							good_in_buyer_file_status = true;
	               	}
	       		}
	       		
	       	});
       	
       		if( good_in_buyer_file_status != false ){
       			alertifyMessage("error","{{ trans('messages.require-document-type') }} ");
          		return false;
            }

			var good_in_logistic_transporter_name_status = false;
            var good_in_logistic_transporter_inv_no_status = false;
            $('.good-in-logistic-transporter-tbody tr').each(function(){
         		var good_in_logistic_transporter_name = $.trim($(this).find('.good-in-logistic-transporter-name').val());
         		var good_in_logistic_transporter_inv_no = $.trim($(this).find('.good-in-logistic-transporter-inv-no').val());

				if(good_in_logistic_transporter_name != "" && good_in_logistic_transporter_name != null){
					good_in_logistic_transporter_name_status = true;
					if( ( good_in_logistic_transporter_inv_no == "" || good_in_logistic_transporter_inv_no == null ) && (good_in_logistic_transporter_inv_no_status != true) ){
						$.trim($(this).find('.good-in-logistic-transporter-inv-no').focus());
            			good_in_logistic_transporter_inv_no_status = true;
                	}
         		}
         	});
           
            if( good_in_logistic_transporter_inv_no_status != false ){
         		alertifyMessage("error","{{ trans('messages.require-inv-no') }} ");
            	return false;
            }

            var checked_logistic_length = $('.logistic-part-selection:checked').length;
            var collection_delivery = $.trim($('[name="collection_delivery"]').val());
       		
			if(checked_logistic_length > 0){
				var add_supplier_detail = false;
				var collection_delivery_type_status = false;
				var collection_delivery_loaction_status = false;
				$('.logistic-part-selection:checked').each(function(){
					add_supplier_detail = true;
					if(collection_delivery == '{{ config("constants.COLLECTION") }}'){
						var collection_delivery_type = $.trim($(this).parents('tr').find('.collection-delivery-type-record').val());
						var collection_delivery_loaction = $.trim($(this).parents('tr').find('.collection-delivery-location-record').val());
						
						if( ( collection_delivery_type == "" || collection_delivery_type == null ) && (collection_delivery_type_status != true ) ){
							$(this).parents('tr').find('.collection-delivery-type-record').focus();
							collection_delivery_type_status = true;
						}
						if( ( collection_delivery_loaction == "" || collection_delivery_loaction == null ) && ( collection_delivery_type_status != true && collection_delivery_loaction_status != true ) ){
							$(this).parents('tr').find('.collection-delivery-location-record').focus();
							collection_delivery_loaction_status = true;
						}	
		           	}
					
				});
				if( collection_delivery_type_status != false ){
	         		alertifyMessage("error","{{ trans('messages.require-delivery-type') }} ");
	            	return false;
	             }
				if( collection_delivery_loaction_status != false ){
	         		alertifyMessage("error","{{ trans('messages.require-delivery-location') }} ");
	            	return false;
	             }
			} else {
				alertifyMessage("error","{{ trans('messages.required-atleast-one-checkbox') }} ");
        		return false;
			}


			var delivery_location_identical = true;
			var old_delivery_location = '';
    		var delivery_location = '';

    		if(collection_delivery == '{{ config("constants.COLLECTION") }}'){
    			$('.logistic-part-selection').each(function(){
                	if($(this).prop('checked') != false){
                		delivery_location = $.trim($(this).parents('tr').find('.collection-delivery-location-record option:selected').attr('data-record-id'));
                		if(delivery_location != '' && delivery_location != null && old_delivery_location != '' && old_delivery_location != null && old_delivery_location != delivery_location){
                			delivery_location_identical = false;
                    	}
                		old_delivery_location = delivery_location;
                    }
                });
        	}

    		if( delivery_location_identical != true ){
       			alertifyMessage("error","{{ trans('messages.identical-delivery-location') }} ");
          		return false;
            }
        	
        	var confirm_box = "";
            var confirm_box_msg = "";
            <?php if( isset($recordInfo) && (  $recordInfo->i_id > 0 ) ) { ?>
  					confirm_box = "{{ trans('messages.update-good-in-logistic') }}";
					confirm_box_msg = "{{ trans ( 'messages.confirm-good-in-logistic-update-msg') }}";
   
			<?php }else{?>
					confirm_box = "{{ trans('messages.add-logistic') }}";
 					confirm_box_msg = "{{ trans ( 'messages.confirm-good-in-logistic-add-msg') }}";
			<?php }?>
           
        	alertify.confirm(confirm_box,confirm_box_msg,function() {
        		$("[name='supplier_name']").prop('disabled', false);
        		$("[name='delivery_type']").prop('disabled', false);
        		$('input:disabled').prop('disabled', false);
 				$('select:disabled').prop('disabled', false);
 				$('.logistic-part-selection').prop('disabled', false);
 				
        		$("[name='collection_delivery']").prop('disabled', false);
        		$("[name='collection_delivery_type[]']").prop('disabled', false);
            	$("[name='good_in_logistic_document_type_count']").val(good_in_logistic_document_type_count);
            	$("[name='good_in_logistic_transporter_count']").val(good_in_logistic_transporter_count);
 				showLoader()
                form.submit();
         	},function() {});

        }
    });
    
    $(document).ready(function() {

        //init date time picker
        $("[name='collection_date'],[name='delivery_date'],[name='delivery_delivery_date'], [name='goods_in_date']").datetimepicker({
            useCurrent: false,
            viewMode: 'days',
            ignoreReadonly: true,
            widgetPositioning: {
                vertical: 'bottom'
            },
            format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

        });
        <?php if( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ) { ?>
		 		var collection_date = '{{ ( isset($recordInfo->dt_collection_date) ? $recordInfo->dt_collection_date : '') }}';
		 		var delivery_date = '{{ ( isset($recordInfo->dt_delivery_date) ? $recordInfo->dt_delivery_date : '') }}';
		 		var delivery_delivery_date = '{{ ( isset($recordInfo->dt_delivery_date) ? $recordInfo->dt_delivery_date : '') }}';
	
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
	
		 		if(delivery_delivery_date != "" && delivery_delivery_date != null){
					//$("[name='delivery_delivery_date']").data('DateTimePicker').minDate(moment(delivery_delivery_date).startOf('d'));
				} else {
					//$("[name='delivery_delivery_date']").data('DateTimePicker').minDate(moment().startOf('d'));
				}
			
		<?php } else {?>
				//$("[name='collection_date']").data('DateTimePicker').minDate(moment().startOf('d'));
				//$("[name='delivery_date']").data('DateTimePicker').minDate(moment().startOf('d'));
				//$("[name='delivery_delivery_date']").data('DateTimePicker').minDate(moment().startOf('d'));
		<?php } ?>
        
    });
    function showGoodInBuyerInfo(thisitem){
    	var collection_delivery = $.trim($('[name="collection_delivery"]').val());
    	var get_supplier_id = '{{ (!empty($supplierId) ? $supplierId : 0 ) }}';
    	/* if(get_supplier_id == 0 ){
    		$('.multiple-supplier-collection option').remove();
        } */
    	
    	if(collection_delivery == '{{config("constants.COLLECTION")}}'){
        	
        	$('.good-in-logistic-collection-row').show();
    		$('.good-in-logistic-deliver-row').hide	();
    		
    	} else if(collection_delivery == '{{config("constants.DELIVERY")}}'){
    		
    		/* if(get_supplier_id == 0 ){
    			$('.multiple-supplier-collection').val("").trigger('change');
            } */
    		$('.good-in-logistic-deliver-row').show();
			$('.good-in-logistic-collection-row').hide();
    	} else {
    		$('.good-in-logistic-deliver-row').hide();
   		 	$('.good-in-logistic-collection-row').hide();
   		 	$('.supplier-lilt').html('<option value="">{{ trans("messages.select") }}</option>');
        }

    	//$(".multiple-supplier-collection").select2();
    }
    var good_in_logistic_document_type_count = 1;
    
    function addNewRow(thisitem){
    	good_in_logistic_document_type_count++;
      	var html =""; 
      	html += '<tr>';
      	html += '<td class="table-index text-center" style="width:70px;min-width:70px;">'+good_in_logistic_document_type_count+'</td>';
      	html += '<td class="text-left">';
      	html += '<select name="type_'+good_in_logistic_document_type_count+'" class="form-control good-in-logistic-type">';
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
        html += '<input type="file" class="custom-file-input good-in-logistic-file" id="document_'+good_in_logistic_document_type_count+'" name="file_'+good_in_logistic_document_type_count+'[]" multiple onchange="validFile(this,\'pdf_doc_jpg_png_jpeg_xls\')">';
        html += '<label class="custom-file-label" for="document_'+good_in_logistic_document_type_count+'">Choose file</label>';
        html += '</div>';
        html += '</td>';
        html += '<td class="text-left">';
        html += '<input type="text" class="form-control" name="remarks_'+good_in_logistic_document_type_count+'">';
        html += '</td>';
        html += '<td class="actions-col">';
        html += '</td>';
		html += '<td style="width:70px;min-width:70px;"><button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button></td>';
        html += '</tr>'; 
        if( $('.good-in-logistic-master-tbody').find('tr').length > 0 ){
    		$(html).insertAfter($('.good-in-logistic-master-tbody').find('tr:last'));	
    	} else {
    		$('.good-in-logistic-master-tbody').html(html);
    	}
    	reindexTable('good-in-logistic-master-tbody');
   	}
    var good_in_logistic_module_url = '{{config("constants.GOODS_IN_LOGITIC_MASTER_URL")}}' + '/';

   	function getGoodInBuyerDetails(thisitem){
   		var supplier_record_id = $.trim($('[name="supplier_name"]').val());
   		var collection_delivery = $.trim($('[name="collection_delivery"]').val());
   		//if(supplier_record_id != "" && supplier_record_id != null){
   			$.ajax({
   		    	type:'post',
   		    	data:{"_token": "{{ csrf_token() }}",'supplier_record_id':supplier_record_id,'collection_delivery':collection_delivery},
   		    	url: good_in_logistic_module_url + 'getGoodInBuyerDetails',
   		    	beforeSend: function() {
   		    		//block ui
   		    		showLoader();
   		    	},	
   		    	success: function(response) {
   	   		    	hideLoader();
   		    		if(response != "" && response != null){

   		    			if( collection_delivery != "" && collection_delivery != null ){
							switch(collection_delivery){
								case '{{ config("constants.COLLECTION") }}':
									$('.good-in-logistic-collection-type').html(response);
									$('.good-in-buyer-delivery').html("");
									break;
								case '{{ config("constants.DELIVERY") }}':
									$('.good-in-buyer-delivery').html(response);
									$('.good-in-logistic-collection-type').html("");
									break;
							}
   	   		    		} else {
   	   		    			$('.good-in-logistic-collection-type').html("");
   	   		    			$('.good-in-buyer-delivery').html("");
   	   	   	   		    }
   	   		    		
   	   		    	} else {
   	   		    		$('.good-in-buyer-delivery').html("");
	   		    		$('.good-in-logistic-collection-type').html("");
   	   		    	}
   		    		
   		    	},
   		    	error: function() {
   					hideLoader();
   				}
   		    });
   		//}
   	}
	var good_in_logistic_transporter_count = 2;
   	function addNewTransporterInvoiceRow(thisitem){
   		good_in_logistic_transporter_count++;
   		var html = "";
   		html += '<tr>';
   		html += '<td class="table-index">'+good_in_logistic_transporter_count+'</td>';
   		html += '<td class="text-left">';
   		//html += '<input type="text" class="form-control good-in-logistic-transporter-name" name="name_'+good_in_logistic_transporter_count+'" placeholder="{{ trans('messages.name') }}">';
   		html += '<select name="name_'+good_in_logistic_transporter_count+'" class="form-control good-in-logistic-transporter-name select2">';
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
   		html += '<input type="text" class="form-control good-in-logistic-transporter-inv-no" name="inv_no_'+good_in_logistic_transporter_count+'" placeholder="{{ trans('messages.inv-no') }}">';
   		html += '</td>';
   		html += '<td class="text-left">';
   		html += '<input type="text" class="form-control agent-to-warehouse-freight" name="freight_'+good_in_logistic_transporter_count+'" placeholder="{{ trans('messages.freight') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
   		html += '</td>';
   		html += '<td class="text-left">';
   		html += '<input type="text" class="form-control agent-to-warehouse-custom" name="custom_'+good_in_logistic_transporter_count+'" placeholder="{{ trans('messages.custom') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
   		html += '</td>';
   		html += '<td class="text-left">';
   		html += '<input type="text" class="form-control agent-to-warehouse-duty" name="duty_'+good_in_logistic_transporter_count+'" placeholder="{{ trans('messages.duty') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
   		html += '</td>';
   		html += '<td class="text-left">';
   		html += '<input type="text" class="form-control agent-to-warehouse-other" name="other_'+good_in_logistic_transporter_count+'" placeholder="{{ trans('messages.other') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
   		html += '</td>';
   	    html += '<td class="text-left">';
   	    html += '<input type="text" class="form-control agent-to-warehouse-vat" name="vat_'+good_in_logistic_transporter_count+'" placeholder="{{ trans('messages.vat') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
   	 	html += '</td>';
   		html += '<td class="text-left">';
   		html += '<div class="input-group align-items-center flex-nowrap">';
   		html += '<label class="mb-0" for=""><span class="agent-warehouse-total-value"></span></label>';
   		html += '<div class="input-group-prepend">';
   		html += '<select class="form-control ml-2" name="amount_'+good_in_logistic_transporter_count+'">';
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
        html += '<input type="text" class="form-control agent-to-warehouse-con-rate" name="cov_rate_'+good_in_logistic_transporter_count+'" placeholder="{{ trans('messages.cov-rate') }}" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
        html += '</td>';
        html += '<td class="text-left"><span class="agent-warehouse-final-rate"></span></td>';
        html += '<td class="text-left">';
        html += '<div class="custom-file">';
        html += '<input type="file" class="custom-file-input" id="invoice_document_'+good_in_logistic_transporter_count+'" multiple name="invoice_file_'+good_in_logistic_transporter_count+'[]" onchange="validFile(this,\'pdf_doc_jpg_png_jpeg_xls\')">';
        html += '<label class="custom-file-label" for="invoice_document_'+good_in_logistic_transporter_count+'">Choose file</label>';
        html += '</div>';
        html += '</td>';
        html += '<td class="actions-col">';
        html += '</td>';
        html += '</tr>';
      
        if( $('.good-in-logistic-transporter-tbody').find('tr').length > 0 ){
    		$(html).insertAfter($('.good-in-logistic-transporter-tbody').find('tr:last'));	
    	} else {
    		$('.good-in-logistic-transporter-tbody').html(html);
    	}
    	reindexTable('good-in-logistic-transporter-tbody');
		$(function(){
			$('.select2').select2();
		})
   	}

	$(document).ready(function(){
		
		var get_supplier_id = '{{ (!empty($supplierId) ? $supplierId : 0 ) }}';
		if((get_supplier_id !="" && get_supplier_id != null) && (get_supplier_id > 0)){
			var delevery_collection = $.trim($('[name="collection_delivery"]').val());
			if(delevery_collection != "" && delevery_collection != null){
				$("[name='supplier_name']").trigger('change');
			 	$("[name='collection_delivery']").trigger('change');
			}
		}
		
		/* $('.multiple-supplier-collection').select2({
		    templateResult: function(option) {
			    var collection_delivery = $(option.element).parents('.form-start-field').find("[name='collection_delivery']").val();
			    var data_show_status = $(option.element).attr('data-show');
			    if( data_show_status != "" && data_show_status != null && data_show_status == 1 ){
					if( collection_delivery == "{{ config('constants.COLLECTION') }}"){
						return null;
					}

				}
			    return option.text;
		    }
		}); */
   	});
	
	function getSupplierInfo(thisitem){
		var get_supplier_id = '{{ (!empty($supplierId) ? $supplierId : 0 ) }}';
		var delevery_collection = $.trim($('[name="collection_delivery"]').val());
		if(get_supplier_id > 0){
			var supplier_name  = $.trim($('[name="supplier_name"]').val());
		}
		//if(delevery_collection != "" && delevery_collection != null){
   			$.ajax({
   		    	type:'post',
   		    	data:{"_token": "{{ csrf_token() }}",'delevery_collection':delevery_collection,'supplier_name':supplier_name },
   		    	url: good_in_logistic_module_url + 'getSupplierInfo',
   		    	beforeSend: function() {
   		    		//block ui
   		    		showLoader();
   		    	},	
   		    	success: function(response) {
   	   		    	hideLoader();
   		    		if(response != "" && response != null){
						$('.supplier-lilt').html(response);
   	   		    		
   	   		    	} else{
   	   		    		$('.supplier-lilt').html('<option value="">{{ trans("messages.select") }}</option>');
   	   		    	}
   		    	},
   		    	error: function() {
   					hideLoader();
   				}
   		    });
   		//}
		
	}	
	function showBoxPalletsOnDelivery(thisitem){
		var collection_type = $.trim($(thisitem).val());
		
		if(collection_type != '' && collection_type != null && collection_type == '{{ config("constants.DELIVERY") }}'){
			$('.box-pallets-div').show();
		}else{
			$('.box-pallets-div').hide();
		}
	}
</script>
@endsection