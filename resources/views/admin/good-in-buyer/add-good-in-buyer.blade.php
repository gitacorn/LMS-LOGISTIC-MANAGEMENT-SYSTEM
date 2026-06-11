@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-md-flex border-navabr align-items-center">
        <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }}</h1>
        <nav aria-label="breadcrumb" class="d-flex ml-auto mr-3">
            <ol class="breadcrumb bg-transparent p-0 mb-0 align-self-end">
                <li class="breadcrumb-item"><a href="{{ config('constants.GOODS_IN_BUYER_MASTER_URL') }}" class="category-add-link">{{ trans("messages.buyer") }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle}}</li>
            </ol>
        </nav>
    </div>

    <section class="inner-wrapper-common-sections main-listing-section">
        <div class="container-fluid">
            <div class="card document-card mb-3">
                <ul class="document-items">
                    <li class="document-list"><a href="#details" class="document-text">{{ trans("messages.details") }}</a></li>
                    <li class="document-list"><a href="#documents" class="document-text">{{ trans("messages.documents") }}</a></li>
					<?php if( isset($recordInfo) && ( $recordInfo->goodInBuyerMaster->i_id > 0 ) ) { ?>
						<li class="document-text ml-auto buyer-detail-no"><?php echo (!empty($recordInfo->v_goods_in_buyer_detail_no) ? $recordInfo->v_goods_in_buyer_detail_no :'')?></li>
					<?php }?>
                </ul>
            </div>
            <div class="card mb-3 good-in-buyer-class" id="details">
                <h3 class="title-goods"><i class="fas fa-level-down-alt list-icon mr-2"></i>{{ trans("messages.goods-in-entry-details") }}</h3>
                {!! Form::open(array( 'id '=> 'add-good-in-buyer-master-form' , 'method' => 'post' , 'files' => true , 'url' => 'good-in-buyer/add')) !!}
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
                        <div class="row supplier-list-row dependent-div-class">
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="buyer_company">{{ trans("messages.buyer-company") }}<span class="text-danger">*</span></label>
                                    <select name="buyer_company" class="form-control select2" <?php echo ( $viewForm != false ?  $disableForm : '' )  ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
                                        if(!empty($companyRecordDetails)){
                                        	foreach ($companyRecordDetails as $companyRecordDetail){
                                        		$encodeCompanyId  = Wild_tiger::encode($companyRecordDetail->i_id);
                                        		$selected = '';
                                        		if( isset($recordInfo->goodInBuyerMaster->i_buyer_company_id) && ( $recordInfo->goodInBuyerMaster->i_buyer_company_id == $companyRecordDetail->i_id ) ){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{ $encodeCompanyId }}" {{ $selected }}>{{ (!empty($companyRecordDetail->v_company_name) ? $companyRecordDetail->v_company_name : '' ) }}</option>
                                        		<?php 
                                        	}
                                        } 
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="user_company">{{ trans("messages.user-company") }}<span class="text-danger">*</span></label>
                                    <select name="user_company[]" class="form-control select2" multiple="" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?>>
                                        <?php 
                                        $userCompanyData = (!empty($recordInfo->goodInBuyerMaster->v_user_company_ids) ? explode("," , ( $recordInfo->goodInBuyerMaster->v_user_company_ids ) ) : [] );
                                        if(!empty($companyRecordDetails)){
                                        	foreach ($companyRecordDetails as $companyRecordDetail){
                                        		$encodeCompanyId  = Wild_tiger::encode($companyRecordDetail->i_id);
                                        		$selected = '';
                                        		if( isset($recordInfo->goodInBuyerMaster->v_user_company_ids) && (in_array($companyRecordDetail->i_id, $userCompanyData) ) ){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{ $encodeCompanyId }}" {{ $selected }}>{{ (!empty($companyRecordDetail->v_company_name) ? $companyRecordDetail->v_company_name : '' ) }}</option>
                                        		<?php 
                                        	}
                                        } 
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="buyer_name">{{ trans("messages.buyer-name") }}<span class="text-danger">*</span></label>
                                    <select name="buyer_name[]" class="form-control select2" multiple <?php echo ( $viewForm != false ?  $disableForm : '' ) ?>>
                                        <?php 
                                        $userRecordId = (!empty($recordInfo->goodInBuyerMaster->v_buyer_employee_ids) ? explode("," , ( $recordInfo->goodInBuyerMaster->v_buyer_employee_ids ) ) : [] );
                                        if(!empty($userRecordDetails)){
                                        	foreach ($userRecordDetails as $userRecordDetail){
                                        		$encodevUserId  = Wild_tiger::encode($userRecordDetail->i_id);
                                        		$selected = '';
                                        		if( isset($recordInfo->goodInBuyerMaster->v_buyer_employee_ids) && ( in_array($userRecordDetail->i_id, $userRecordId) ) ){
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
                            
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="user_buyer_name">{{ trans("messages.user-buyer-name") }}<span class="text-danger">*</span></label>
                                    <select name="user_buyer_name[]" class="form-control select2" multiple <?php echo ( $viewForm != false ?  $disableForm : '' ) ?>>
                                        <?php 
	                                        $userBuyerRecordId = (!empty($recordInfo->goodInBuyerMaster->v_user_buyer_ids) ? explode("," , ( $recordInfo->goodInBuyerMaster->v_user_buyer_ids ) ) : []);
	                                        if(!empty($userBuyerRecordDetails)){
	                                        	foreach ($userBuyerRecordDetails as $userBuyerRecordDetail){
	                                        		$encodeUserBuyerId  = Wild_tiger::encode($userBuyerRecordDetail->i_id);
	                                        		$selected = '';
	                                        		if( isset($recordInfo->goodInBuyerMaster->v_buyer_employee_ids) && ( in_array($userBuyerRecordDetail->i_id, $userBuyerRecordId) ) ){
	                                        			$selected = "selected='selected'";
	                                        		}
	                                        		?>
	                                        		<option value="{{ $encodeUserBuyerId }}" {{ $selected }}><?php echo  (!empty($userBuyerRecordDetail->v_name) ? $userBuyerRecordDetail->v_name . (!empty($userBuyerRecordDetail->v_department) ?  ' ('. $userBuyerRecordDetail->v_department . ')' : '' ) : '' ) ?></option>
	                                        		<?php 
	                                        	}
	                                        } 
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="collection_delivery" class="control-label">{{ trans("messages.collection-delivery") }}<span class="star">*</span></label>
                                    <select name="collection_delivery" <?php echo $disableForm ?> class="form-control" onchange="collectionRecordInfo()" <?php echo ( ( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ) ? 'disabled' : '' )  ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
                                        if(!empty($collectionDeliveryInfo)){
                                        	foreach ($collectionDeliveryInfo as  $key => $collectionDelivery){
                                        		$selected = '';
                                        		if( isset($recordInfo->goodInBuyerMaster->e_collection_type) && ( $recordInfo->goodInBuyerMaster->e_collection_type == $key) ){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{ $key}}" {{$selected}}>{{ $collectionDelivery }}</option>
                                        		<?php 
                                        	}
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <?php /* 
							<div class="col-lg-2 col-md-3 col-sm-6 delivery-collection-location-record" {{ ( isset($recordInfo) && ($recordInfo->goodInBuyerMaster->e_collection_type == config('constants.COLLECTION')) ? '' : 'style=display:none' )  }}>
                                <div class="form-group">
                                    <label class="control-label" for="collection_location">{{ trans("messages.collection-location") }}</label>
                                    <select name="collection_location" class="form-control select2" {{ $disableForm }} >
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
                                        if(!empty($warehouseDetails)){
                                        	foreach ($warehouseDetails as $warehouseDetail){
                                        		$encodeId = Wild_tiger::encode($warehouseDetail->i_id);
                                        		$selected = '';
                                        		if( isset($recordInfo->goodInBuyerMaster->i_delivery_location_id) && ( $recordInfo->goodInBuyerMaster->i_delivery_location_id == $warehouseDetail->i_id) ){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{ $encodeId }}" {{$selected}}>{{ (!empty($warehouseDetail->v_warehouse_name) ? $warehouseDetail->v_warehouse_name .(!empty($warehouseDetail->v_warehouse_code) ? ' (' .$warehouseDetail->v_warehouse_code .')' : '' ): '' ) }}</option>
                                        		<?php 
                                        	}
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                             */?>
                            <div class="col-lg-2 col-md-3 col-sm-6 delivery-collection-location-record" {{ ( isset($recordInfo) && ($recordInfo->goodInBuyerMaster->e_collection_type == config('constants.COLLECTION')) ? '' : 'style=display:none' )  }}>
                                <div class="form-group">
                                    <label class="control-label" for="ready_for_collection">{{ trans("messages.ready-for-collection") }}<span class="star">*</span></label>
                                    <select name="ready_for_collection" class="form-control" {{ ( $viewForm != false ?  $disableForm : '' ) }}>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        @if(!empty($readyForCollectionInfo))
	                                     	@foreach($readyForCollectionInfo as $key => $readyForCollection)
	                                     	{{ $selected = ''}}
	                                        	@if( (isset($recordInfo) ) && ($recordInfo->goodInBuyerMaster->e_ready_for_collection_status == $key))
	                                        		{{ $selected = "selected='selected'"}}
	                                        	@endif
	                                        	<option value='{{ $key }}' {{ $selected }}>{{ $readyForCollection}}</option>
	                                       	@endforeach
	                                 	@endif
                                    </select>
                                </div>
                            </div>
                            <?php /*
                             <div class="col-lg-2 col-md-3 col-sm-6 delivery-collection-location-record" {{ ( isset($recordInfo) && ($recordInfo->goodInBuyerMaster->e_collection_type == config('constants.COLLECTION')) ? '' : 'style=display:none' )  }}>
                                <div class="form-group">
                                    <label for="is_multiple_allowed" class="control-label">{{ trans("messages.pickup-reference") }}<span class="star">*</span></label>
                                    <div class="radio-boxes form-row p-1 bg-white">
                                        <div class="radio-box col-sm-4 col-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="pickup_reference" {{ ( $viewForm != false ?  $disableForm : '' ) }} id="pickup_reference_yes" onchange="showRefenceInfo(this)" value="{{  config('constants.SELECTION_YES') }}" {{ ( (  isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->e_pickup_reference)) && ( $recordInfo->goodInBuyerMaster->e_pickup_reference ==  config('constants.SELECTION_YES') ) ) ? 'checked' : '' ) }} >
                                                <label class="form-check-label custom-type-label btn stock-btn" for="pickup_reference_yes">{{ trans('messages.yes') }}</label>
                                            </div>
                                        </div>
                                        <div class="radio-box col-sm-4 col-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="pickup_reference" {{ ( $viewForm != false ?  $disableForm : '' ) }} id="pickup_reference_no"   onchange="showRefenceInfo(this)"  value="{{ config('constants.SELECTION_NO')}}" {{ ( (  isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->e_pickup_reference)) && ( $recordInfo->goodInBuyerMaster->e_pickup_reference ==  config('constants.SELECTION_NO') ) ) ? 'checked' : '' ) }} >
                                                <label class="form-check-label custom-type-label btn stock-btn" for="pickup_reference_no">{{ trans('messages.no') }}</label>
                                            </div>
                                        </div>
                                        <label id="pickup_reference-error" class="invalid-input" for="pickup_reference"></label>
                                    </div>
                                </div>
                            </div>
                             
                            <div class="col-lg-2 col-md-3 col-sm-6 pickup-refence-yes-div" {{ ( isset($recordInfo) && ($recordInfo->goodInBuyerMaster->e_pickup_reference == config('constants.SELECTION_YES')) ? '' : 'style=display:none' )  }}>
                                <div class="form-group">
                                    <label for="reference" class="control-label">{{ trans("messages.reference") }}<span class="star">*</span></label>
                                    <input type="text" name="reference" <?php echo ( $viewForm != false ?  $disableForm : '' )  ?> class="form-control" placeholder="{{ trans('messages.reference') }}" value="{{old('reference',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->v_reference))) ?  $recordInfo->goodInBuyerMaster->v_reference : '' ) )}}">
                                </div>
                            </div>
                            
                            <div class="col-lg-2 col-md-4 col-sm-6 deliver-record-div" <?php echo ( isset($recordInfo) && ($recordInfo->goodInBuyerMaster->e_collection_type == config('constants.DELIVERY')) ? '' : 'style="display:none"' )  ?>>
                                <div class="form-group">
                                    <label for="delivery_type" class="control-label">{{ trans("messages.delivery-type") }}<span class="star">*</span></label>
                                    <select name="delivery_type"  class="form-control" <?php echo ( ( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ) ? ( ( session()->get('role') == config('constants.ROLE_ADMIN') ) ? '' : $disableForm ) : '' )  ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
                                        if(!empty($deliveryTypeInfo)){
                                        	foreach ($deliveryTypeInfo as  $key => $deliveryType){
                                        		$selected = '';
                                        		if( isset($recordInfo->goodInBuyerMaster->e_delivery_type) && ( $recordInfo->goodInBuyerMaster->e_delivery_type == $key) ){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{ $key}}" {{$selected}}>{{ $deliveryType }}</option>
                                        		<?php 
                                        	}
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                             
                            <div class="col-lg-2 col-md-3 col-sm-6 deliver-record-div" <?php echo ( isset($recordInfo) && ($recordInfo->goodInBuyerMaster->e_collection_type == config('constants.DELIVERY')) ? '' : 'style="display:none"' )  ?>>
                                <div class="form-group">
                                    <label for="booking_ref_no" class="control-label">{{ trans("messages.booking-ref-no") }}<span class="star">*</span></label>
                                    <input type="text" name="booking_ref_no" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> class="form-control" placeholder="{{ trans('messages.booking-ref-no') }}" value="{{old('booking_ref_no',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->v_booking_ref_no))) ?  $recordInfo->goodInBuyerMaster->v_booking_ref_no : '' ) )}}">
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-3 col-sm-6 deliver-record-div" <?php echo ( isset($recordInfo) && ($recordInfo->goodInBuyerMaster->e_collection_type == config('constants.DELIVERY')) ? '' : 'style="display:none"' )  ?>>
                                <div class="form-group">
                                    <label for="collection_reference_no" class="control-label">{{ trans("messages.collection-reference-no") }}</label>
                                    <input type="text" name="collection_reference_no" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> class="form-control" placeholder="{{ trans('messages.collection-reference-no') }}"  value="{{old('collection_reference_no',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->v_collection_reference_no))) ?  $recordInfo->goodInBuyerMaster->v_collection_reference_no : '' ) )}}">
                                </div>
                            </div>
							*/ ?>
                            <div class="col-lg-3 col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label for="delivery_location" class="control-label">{{ trans("messages.delivery-location") }}<span class="star">*</span></label>
                                    <select name="delivery_location" class="form-control select2" <?php echo $disableForm ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
                                        if(!empty($warehouseRecordDetails)){
                                        	foreach ($warehouseRecordDetails as $warehouseRecordDetail){
                                        		$encodeWarehouseId  = Wild_tiger::encode($warehouseRecordDetail->i_id);
                                        		$selected = '';
                                        		if( isset($recordInfo->goodInBuyerMaster->i_delivery_location_id) && ( $recordInfo->goodInBuyerMaster->i_delivery_location_id == $warehouseRecordDetail->i_id) ){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{ $encodeWarehouseId }}" {{ $selected }}>{{ (!empty($warehouseRecordDetail->v_warehouse_name) ? $warehouseRecordDetail->v_warehouse_name .(!empty($warehouseRecordDetail->v_warehouse_code) ? ' (' .$warehouseRecordDetail->v_warehouse_code .')' : '' ): '' ) }}</option>
                                        		<?php 
                                        	}
                                        } 
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-2 col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="delivery_date">{{ trans("messages.buyer-delivery-date") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="delivery_date" class="form-control" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> placeholder="{{ trans('messages.buyer-delivery-date') }}" value="{{old('delivery_date',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->dt_delivery_date))) ?  clientDate($recordInfo->goodInBuyerMaster->dt_delivery_date) : '' ) )}}">
                                </div>
                            </div>

                            <?php /* 
                            <div class="col-lg-2 col-md-4 col-sm-6 deliver-record-div" <?php echo ( isset($recordInfo) && ($recordInfo->goodInBuyerMaster->e_collection_type == config('constants.DELIVERY')) ? '' : 'style="display:none"' )  ?>>
                                <div class="form-group">
                                    <label class="control-label" for="delivery_remarks">{{ trans("messages.delivery-remarks") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="delivery_remarks" class="form-control" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> placeholder="{{ trans('messages.delivery-remarks') }}" value="{{old('delivery_remarks',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->v_delivery_remarks))) ?  $recordInfo->goodInBuyerMaster->v_delivery_remarks : '' ) )}}">
                                </div>
                            </div>
                             */?>
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="supplier_name">{{ trans("messages.supplier-name") }}<span class="text-danger">*</span></label>
                                    <select name="supplier_name" class="form-control select2" onchange="getSupplierLocation(this)" <?php echo $disableForm ?> <?php echo ( ( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ) ? 'disabled' : '' ) ?> >
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
                                        if(!empty($supplierRecordDetails)){
                                        	foreach ($supplierRecordDetails as $supplierRecordDetail){
                                        		$encodeSupplierId  = Wild_tiger::encode($supplierRecordDetail->i_id);
                                        		$selected = '';
                                        		if( isset($recordInfo->goodInBuyerMaster->i_main_supplier_id) && ( $recordInfo->goodInBuyerMaster->i_main_supplier_id == $supplierRecordDetail->i_id ) ){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{ $encodeSupplierId }}" {{$selected}}>{{ (!empty($supplierRecordDetail->v_supplier_name) ? $supplierRecordDetail->v_supplier_name : '' ) }}</option>
                                        		<?php 
                                        	}
                                        } 
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="supplier_location">{{ trans("messages.supplier-location") }}<span class="text-danger">*</span></label>
                                    <select name="supplier_location[]" class="form-control select2 location-list" <?php echo $disableForm ?> >
                                        <option value="">{{ trans("messages.select") }}</option>
                                       <?php 
                                       $supplierLocationInfo = (!empty($recordInfo->goodInBuyerMaster->v_supplier_ids) ? explode("," , ( $recordInfo->goodInBuyerMaster->v_supplier_ids ) ) : [] );
                                      
                                       if(!empty($supplierLocationRecordDetails)){
                                        	foreach ($supplierLocationRecordDetails as $supplierLocationRecordDetail){
                                        		$encodesupplierLocationId  = Wild_tiger::encode($supplierLocationRecordDetail->i_id);
                                        		$supplierLocation = $supplierLocationRecordDetail->v_supplier_address;
                                        		$supplierName = $supplierLocationRecordDetail->supplierMaster->v_supplier_name;
                                        		$supplierLocationName = $supplierName.'('.$supplierLocation.')';
                                        		$selected = '';
                                        		if( !empty($supplierLocationInfo) && (in_array($supplierLocationRecordDetail->i_id, $supplierLocationInfo) ) ){
                                        			$selected = "selected='selected'";
                                        			 
                                        		}
                                        		?>
                                        		<option value="{{ $encodesupplierLocationId }}" {{$selected}}>{{ $supplierLocationName }}</option>
                                        		<?php
                                        		?>
		                                      <?php    		
                                        	}
                                        } 
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="order_date">{{ trans("messages.order-date") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="order_date" class="form-control" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> placeholder="{{ trans('messages.order-date') }}" value="{{old('order_date',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->dt_order_date))) ?  clientDate($recordInfo->goodInBuyerMaster->dt_order_date) : '' ) )}}">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="invoice_date">{{ trans("messages.invoice-date") }}</label>
                                    <input type="text" name="invoice_date" class="form-control" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> placeholder="{{ trans('messages.invoice-date') }}" value="{{old('invoice_date',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->dt_invoice_date))) ?  clientDate($recordInfo->goodInBuyerMaster->dt_invoice_date) : '' ) )}}">
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="po_no_sales_invoice_no">{{ trans("messages.po-number") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="po_no_sales_invoice_no" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> class="form-control" placeholder="{{ trans('messages.po-number') }}" value="{{old('po_no_sales_invoice_no',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->v_po_sales_invoice_no))) ?  $recordInfo->goodInBuyerMaster->v_po_sales_invoice_no : '' ) )}}">
                                </div>
                            </div>
                            
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="vendor_number">{{ trans("messages.vendor-number") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="vendor_number" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> class="form-control" placeholder="{{ trans('messages.vendor-number') }}" value="{{old('vendor_number',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->v_vendor_number))) ?  $recordInfo->goodInBuyerMaster->v_vendor_number : '' ) )}}">
                                </div>
                            </div>
                            
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="invoice_no">{{ trans("messages.invoice-number") }}</label>
                                    <input type="text" id="invoice_no" name="invoice_no" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> class="form-control" placeholder="{{ trans('messages.invoice-number') }}" value="{{old('po_no_sales_invoice_no',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->v_invoice_no))) ?  $recordInfo->goodInBuyerMaster->v_invoice_no : '' ) )}}">
                                </div>
                            </div>

							<div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="po_create_user_name">{{ trans("messages.po-create-user-name") }}<span class="text-danger">*</span></label>
                                    <select name="po_create_user_name" class="form-control select2" <?php echo ( $viewForm != false ? $disableForm : '' ) ?>>
                                    	<option value="">{{ trans("messages.select") }}</option>
                                        <?php 
	                                        if(!empty($poCreateUserDetails)){
	                                        	foreach ($poCreateUserDetails as $poCreateUserDetail){
	                                        		$encodeId  = Wild_tiger::encode($poCreateUserDetail->i_id);
	                                        		$selected = '';
	                                        		if(isset($recordInfo->goodInBuyerMaster->i_po_create_user_id) && !empty($recordInfo->goodInBuyerMaster->i_po_create_user_id) && $recordInfo->goodInBuyerMaster->i_po_create_user_id == $poCreateUserDetail->i_id){
	                                        			$selected = "selected='selected'";
	                                        		}
	                                        		?>
	                                        		<option value="{{ $encodeId }}" {{ $selected }}><?php echo  (!empty($poCreateUserDetail->v_name) ? $poCreateUserDetail->v_name . (!empty($poCreateUserDetail->v_department) ?  ' ('. $poCreateUserDetail->v_department . ')' : '' ) : '' ) ?></option>
	                                        		<?php 
	                                        	}
	                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

							<div class="col-xl-2 col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="po_creation_date">{{ trans("messages.po-creation-date") }}</label>
                                    <input type="text" name="po_creation_date" class="form-control" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> placeholder="{{ trans('messages.po-creation-date') }}" value="{{old('po_creation_date',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->dt_po_creation_date))) ?  clientDate($recordInfo->goodInBuyerMaster->dt_po_creation_date) : '' ) )}}">
                                </div>
                            </div>

							<div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="po_amount_with_vat">{{ trans("messages.po-amount-with-vat") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="po_amount_with_vat" class="form-control" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> placeholder="{{ trans('messages.po-amount-with-vat') }}" onkeyup="onlyDecimal(this)" value="{{old('po_amount_with_vat',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->d_po_amount_with_vat))) ?  $recordInfo->goodInBuyerMaster->d_po_amount_with_vat : '' ) )}}">
                                </div>
                            </div>

                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="po_no_sales_invoice_amount">{{ trans("messages.po-no-sales-invoice-amount") }}<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <select class="form-control mr-2" name="po_no_sales_invoice_amount" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?>>
                                                <option selected value="">Currency</option>
                                               <?php 
		                                        if(!empty($currencyRecordDetails)){
		                                        	foreach ($currencyRecordDetails as $currencyRecordDetail){
		                                        		$encodeCurrencyrId  = Wild_tiger::encode($currencyRecordDetail->i_id);
		                                        		$selected = '';
		                                        		if( isset($recordInfo->goodInBuyerMaster->i_po_currency_id) && ( $recordInfo->goodInBuyerMaster->i_po_currency_id == $currencyRecordDetail->i_id ) ){
		                                        			$selected = "selected='selected'";
		                                        		}
		                                        		?>
		                                        		<option value="{{ $encodeCurrencyrId }}" {{$selected}}>{{ (!empty($currencyRecordDetail->v_currency_code) ? $currencyRecordDetail->v_currency_code : '' ) }}</option>
		                                        		<?php 
		                                        	}
		                                        } 
		                                        ?>
                                            </select>
                                        </div>
                                        <input type="text" name="po_no_amount" class="form-control" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> aria-label="Text input with dropdown button" onkeyup="onlyDecimal(this)" placeholder="{{ trans('messages.amount') }}" value="{{old('po_no_amount',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->d_po_amount))) ?  $recordInfo->goodInBuyerMaster->d_po_amount : '' ) )}}">
                                    </div>
                                    <label style="display:none" id="po_no_sales_invoice_amount-error" class="invalid-input" for="po_no_sales_invoice_amount" style="display:none;"></label>
                                    <label style="display:none" id="po_no_amount-error" class="invalid-input" for="po_no_amount" style="display:none;"></label>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="brand">{{ trans("messages.brand") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="brand" class="form-control" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> placeholder="{{ trans('messages.brand') }}" value="{{old('brand',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->v_brand))) ?  $recordInfo->goodInBuyerMaster->v_brand : '' ) )}}">
                                </div>
                            </div>

							<?php /* 
                            <div class="col-lg-2 col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label for="payment_status" class="control-label">{{ trans("messages.payment-status") }}<span class="star">*</span></label>
                                    <select name="payment_status" class="form-control" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> onchange="paymentStatusInfo()">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
                                        if(!empty($paymentStatusInfo)){
                                        	foreach ($paymentStatusInfo as  $key => $paymentStatus){
                                        		$selected = '';
                                        		if( isset($recordInfo->goodInBuyerMaster->e_payment_status) && ( $recordInfo->goodInBuyerMaster->e_payment_status == $key ) ){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{ $key}}" {{$selected}}>{{ $paymentStatus }}</option>
                                        		<?php 
                                        	}
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                             */?>
                            <div class="col-lg-2 col-md-3 col-sm-6 payment-status-div">
                                <div class="form-group">
                                    <label class="control-label" for="payment_date">{{ trans("messages.payment-date") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="payment_date" class="form-control" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> placeholder="{{ trans('messages.payment-date') }}" value="{{old('order_date',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->dt_payment_date))) ?  clientDate($recordInfo->goodInBuyerMaster->dt_payment_date) : '' ) )}}">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 payment-status-div">
                                <div class="form-group">
                                    <label class="control-label" for="actual_payment_date">{{ trans("messages.actual-payment-date") }}</label>
                                    <input type="text" name="actual_payment_date" class="form-control" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> placeholder="{{ trans('messages.actual-payment-date') }}" value="{{old('actual_payment_date',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->dt_actual_payment_date))) ?  clientDate($recordInfo->goodInBuyerMaster->dt_actual_payment_date) : '' ) )}}">
                                </div>
                            </div>
                            
                            <?php /* 
                            <div class="col-xl-2 col-md-4 col-sm-6 payment-status-div">
                                <div class="form-group">
                                    <label class="control-label" for="amount">{{ trans("messages.amount") }}<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <select class="form-control mr-2" name="amount" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?>>
                                                <option selected value="">Currency</option>
                                                <?php 
		                                        if(!empty($currencyRecordDetails)){
		                                        	foreach ($currencyRecordDetails as $currencyRecordDetail){
		                                        		$encodeCurrencyrId  = Wild_tiger::encode($currencyRecordDetail->i_id);
		                                        		$selected = '';
		                                        		if( isset($recordInfo->goodInBuyerMaster->i_payment_currency_id) && ( $recordInfo->goodInBuyerMaster->i_payment_currency_id == $currencyRecordDetail->i_id ) ){
		                                        			$selected = "selected='selected'";
		                                        		}
		                                        		?>
		                                        		<option value="{{ $encodeCurrencyrId }}" {{$selected}}>{{ (!empty($currencyRecordDetail->v_currency_code) ? $currencyRecordDetail->v_currency_code : '' ) }}</option>
		                                        		<?php 
		                                        	}
		                                        } 
		                                        ?>
                                            </select>
                                        </div>
                                        <input type="text" class="form-control payment-status-div" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?>  onkeyup="onlyDecimal(this)" name="sales_invoice_amount" placeholder="{{ trans('messages.amount') }}" value="{{old('sales_invoice_amount',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->d_payment_amount))) ?  $recordInfo->goodInBuyerMaster->d_payment_amount : '' ) )}}">
                                    </div>
                                    <label style="display:none" id="amount-error" class="invalid-input" for="amount"></label>
                                </div>
                            </div>
                             */?>
                            
                            
                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="prepayment_percentage">{{ trans("messages.prepayment-percentage") }}</label>
                                    <input type="text" name="prepayment_percentage" id="prepayment_percentage" class="form-control" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> min="0" max="100"  onkeyup="onlyDecimalWithZero(this)" placeholder="{{ trans('messages.prepayment-percentage') }}" value="{{old('prepayment_percentage',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->d_prepayment_percentage))) ?  $recordInfo->goodInBuyerMaster->d_prepayment_percentage : '' ) )}}">
                                </div>
                            </div>
                            
                            <div class="col-lg-2 col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="payment_terms">{{ trans("messages.payment-terms") }}<span class="text-danger">*</span></label>
                                    <?php /* <input type="text" name="payment_terms" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> class="form-control" placeholder="{{ trans('messages.payment-terms') }}" value="{{old('payment_terms',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->v_payment_remark))) ?  $recordInfo->goodInBuyerMaster->v_payment_remark : '' ) )}}"> */?>
                                    <select name="payment_terms" class="form-control" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php  
	                                        if(!empty($paymentTermsDetails)){
	                                        	foreach ($paymentTermsDetails as $paymentTermsDetail){
	                                        		$encoded  = Wild_tiger::encode($paymentTermsDetail->i_id);
	                                        		$selected = '';
	                                        		if( isset($recordInfo->goodInBuyerMaster->i_payment_terms_id) && !empty($recordInfo->goodInBuyerMaster->i_payment_terms_id) && $recordInfo->goodInBuyerMaster->i_payment_terms_id == $paymentTermsDetail->i_id ){
	                                        			$selected = "selected='selected'";
	                                        		}
	                                        		?>
	                                        		<option value="{{ $encoded }}" {{$selected}}><?php echo (!empty($paymentTermsDetail->v_value) ? $paymentTermsDetail->v_value : '')?></option>
	                                        		<?php 
	                                        	}
	                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="customs_procedure" class="control-label">{{ trans("messages.customs-procedure") }}<span class="star">*</span></label>
                                    <select name="customs_procedure" class="form-control" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php  
	                                        if(!empty($customsProcedureInfo)){
	                                        	foreach ($customsProcedureInfo as $key => $value){
	                                        		$selected = '';
	                                        		if( isset($recordInfo->goodInBuyerMaster->e_customs_procedure) && !empty($recordInfo->goodInBuyerMaster->e_customs_procedure) && ( $recordInfo->goodInBuyerMaster->e_customs_procedure == $key) ){
	                                        			$selected = "selected='selected'";
	                                        		}
	                                        		?>
	                                        		<option value="{{$key}}" {{$selected}}>{{ $value }}</option>
	                                        		<?php 
	                                        	}
	                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <?php /* 
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="custom_procedure_export" class="control-label">{{ trans("messages.custom-procedure-export") }}<span class="star">*</span></label>
                                    <select name="custom_procedure_export" class="form-control" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php  
                                        if(!empty($customProcedureInfo)){
                                        	foreach ($customProcedureInfo as $key => $customProcedure){
                                        		$selected = '';
                                        		if( isset($recordInfo->goodInBuyerMaster->e_customer_procedure_export) && ( $recordInfo->goodInBuyerMaster->e_customer_procedure_export == $key) ){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{$key}}" {{$selected}}>{{ $customProcedure }}</option>
                                        		<?php 
                                        	}
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="custom_procedure_import" class="control-label">{{ trans("messages.custom-procedure-import") }}<span class="star">*</span></label>
                                    <select name="custom_procedure_import" class="form-control" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
                                        if(!empty($customProcedureInfo)){
                                        	foreach ($customProcedureInfo as  $key => $customProcedure){
                                        		$selected = '';
                                        		if( isset($recordInfo->goodInBuyerMaster->e_customer_procedure_import) && ( $recordInfo->goodInBuyerMaster->e_customer_procedure_import == $key) ){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{ $key}}" {{$selected}}>{{ $customProcedure }}</option>
                                        		<?php 
                                        	}
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                             */?>


                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="dangerous_goods" class="control-label">{{ trans("messages.dangerous-goods") }}<span class="star">*</span></label>
                                    <select name="dangerous_goods" class="form-control" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
	                                        if(!empty($dangerousGoodsDetails)){
	                                        	foreach ($dangerousGoodsDetails as $dangerousGoodsDetail){
	                                        		$encodeId = (!empty($dangerousGoodsDetail->i_id) ? Wild_tiger::encode($dangerousGoodsDetail->i_id) : 0);
	                                        		$selected = '';
	                                        		if( isset($recordInfo->goodInBuyerMaster->i_dangerous_goods_id) && !empty($recordInfo->goodInBuyerMaster->i_dangerous_goods_id) && ( $recordInfo->goodInBuyerMaster->i_dangerous_goods_id == $dangerousGoodsDetail->i_id) ){
	                                        			$selected = "selected='selected'";
	                                        		}
	                                        		?>
	                                        		<option value="{{ $encodeId }}" {{$selected}}>{{ (!empty($dangerousGoodsDetail->v_value) ? $dangerousGoodsDetail->v_value : '') }}</option>
	                                        		<?php 
	                                        	}
	                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
							<div class="col-lg-2 col-md-4 col-sm-6">
								<div class="form-group">
									<label class="control-label" for="goods_remarks">{{ trans("messages.goods-remark") }}<span class="text-danger">*</span></label>
									<select name="goods_remarks[]" class="form-control select2" multiple <?php echo ( $viewForm != false ?  $disableForm : '' ) ?>>
										<?php 
											if(!empty($goodsRemarksDetails)){
												foreach ($goodsRemarksDetails as $goodsRemarksDetails){
													$encodeId = (!empty($goodsRemarksDetails->i_id) ? Wild_tiger::encode($goodsRemarksDetails->i_id) : 0);
													$selected = '';
													if( isset($recordInfo->goodInBuyerMaster->v_goods_remark_ids) && !empty($recordInfo->goodInBuyerMaster->v_goods_remark_ids) && ( in_array($goodsRemarksDetails->i_id, explode(',', $recordInfo->goodInBuyerMaster->v_goods_remark_ids)) ) ){
														$selected = "selected='selected'";
													}
													?>
													<option value="{{ $encodeId }}" {{$selected}}>{{ (!empty($goodsRemarksDetails->v_value) ? $goodsRemarksDetails->v_value : '') }}</option>
													<?php 
												}
											}
										?>
									</select>
								</div>
							</div>                            
							
							<?php /* 
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="no_of_boxes">{{ trans("messages.no-of-boxes") }}</label>
                                    <input type="text" class="form-control" name="no_of_boxes"  <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> onkeyup="onlyNumber(this)" placeholder="{{ trans('messages.no-of-boxes') }}" value="{{old('no_of_boxes',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->i_no_boxes))) ?  $recordInfo->goodInBuyerMaster->i_no_boxes : '' ) )}}">
                                </div>
                            </div>
							 */?>
                            
                            <div class="col-lg-2 col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="pallets_boxes_type">{{ trans("messages.pallet-box") }}<span class="text-danger">*</span></label>
                                    <select name="pallets_boxes_type" class="form-control" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> onchange="getDimensionInfo(this)">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
	                                        if(!empty($palletBoxInfo)){
	                                        	foreach ($palletBoxInfo as $key => $value){
	                                        		$selected = '';
	                                        		if( isset($recordInfo->goodInBuyerMaster->e_pallet_box_type) && !empty($recordInfo->goodInBuyerMaster->e_pallet_box_type) && ( $recordInfo->goodInBuyerMaster->e_pallet_box_type == $key) ){
	                                        			$selected = "selected='selected'";
	                                        		}
	                                        		?>
	                                        		<option value="{{$key}}" {{$selected}}>{{ $value }}</option>
	                                        		<?php 
	                                        	}
	                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="no_of_pallets_boxes">{{ trans("messages.no-of-pallet-box") }}<span class="text-danger">*</span></label>
                                    <input type="text" name="no_of_pallets_boxes" class="form-control" onchange="naturalNumber(this)" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> placeholder="{{ trans('messages.no-of-pallet-box') }}" value="{{old('no_of_pallets_boxes',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->i_no_of_pallet_box))) ? $recordInfo->goodInBuyerMaster->i_no_of_pallet_box : '' ) )}}">
                                </div>
                            </div>
                            
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="pallet_box_dimension">{{ trans("messages.dimension") }}</label>
                                    <select name="pallet_box_dimension[]" class="form-control select2 pallet-box-dimension-div" multiple <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> >
                                        <?php 
	                                        $dimensionBoxRecordId = (!empty($recordInfo->goodInBuyerMaster->v_dimension_ids) ? explode("," , ( $recordInfo->goodInBuyerMaster->v_dimension_ids ) ) : [] );
	                                        if(!empty($dimensionRecordDetails)){
	                                        	foreach ($dimensionRecordDetails as $dimensionRecordDetail){
	                                        		$encodevDimensionId = Wild_tiger::encode($dimensionRecordDetail->i_id);
	                                        		$selected = '';
	                                        		if( isset($recordInfo->goodInBuyerMaster->v_dimension_ids) && (in_array($dimensionRecordDetail->i_id, $dimensionBoxRecordId)) ){
	                                        			$selected = "selected='selected'";
	                                        		}
	                                        		?>
	                                        		<option value="{{ $encodevDimensionId }}" {{$selected}}>{{ (!empty($dimensionRecordDetail->v_dimension_name) ? $dimensionRecordDetail->v_dimension_name : '' ) . ' ('. (!empty($dimensionRecordDetail->v_dimension_size) ? $dimensionRecordDetail->v_dimension_size : '' ). ')'}}</option>
	                                        		<?php 
	                                        	}
	                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <?php /* 
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="boxes_dimension">{{ trans("messages.boxes-dimension") }}</label>
                                    <select name="boxes_dimension[]" class="form-control select2"  multiple <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> >
                                        <?php 
                                        $dimensionBoxRecordId = (!empty($recordInfo->goodInBuyerMaster->v_box_dimension_ids) ? explode("," , ( $recordInfo->goodInBuyerMaster->v_box_dimension_ids ) ) : [] );
                                        if(!empty($dimensionBoxRecordDetails)){
                                        	foreach ($dimensionBoxRecordDetails as $dimensionBoxRecordDetail){
                                        		$encodevDimensionId  = Wild_tiger::encode($dimensionBoxRecordDetail->i_id);
                                        		$selected = '';
                                        		if( isset($recordInfo->goodInBuyerMaster->v_box_dimension_ids) && (in_array($dimensionBoxRecordDetail->i_id, $dimensionBoxRecordId)) ){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{ $encodevDimensionId }}" {{$selected}}>{{ (!empty($dimensionBoxRecordDetail->v_dimension_name) ? $dimensionBoxRecordDetail->v_dimension_name : '' ) . ' ('. (!empty($dimensionBoxRecordDetail->v_dimension_size) ? $dimensionBoxRecordDetail->v_dimension_size : '' ). ')'}}</option>
                                        		<?php 
                                        	}
                                        } 
                                        ?>
                                    </select>
                                </div>
                            </div>
                             
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="pallet_box_dimension">{{ trans("messages.boxes-dimension") }}</label>
                                    <select name="pallet_box_dimension[]" class="form-control select2" multiple <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> >
                                        <?php 
	                                        $dimensionBoxRecordId = (!empty($recordInfo->goodInBuyerMaster->v_box_dimension_ids) ? explode("," , ( $recordInfo->goodInBuyerMaster->v_box_dimension_ids ) ) : [] );
	                                        if(!empty($dimensionBoxRecordDetails)){
	                                        	foreach ($dimensionBoxRecordDetails as $dimensionBoxRecordDetail){
	                                        		$encodevDimensionId  = Wild_tiger::encode($dimensionBoxRecordDetail->i_id);
	                                        		$selected = '';
	                                        		if( isset($recordInfo->goodInBuyerMaster->v_box_dimension_ids) && (in_array($dimensionBoxRecordDetail->i_id, $dimensionBoxRecordId)) ){
	                                        			$selected = "selected='selected'";
	                                        		}
	                                        		?>
	                                        		<option value="{{ $encodevDimensionId }}" {{$selected}}>{{ (!empty($dimensionBoxRecordDetail->v_dimension_name) ? $dimensionBoxRecordDetail->v_dimension_name : '' ) . ' ('. (!empty($dimensionBoxRecordDetail->v_dimension_size) ? $dimensionBoxRecordDetail->v_dimension_size : '' ). ')'}}</option>
	                                        		<?php 
	                                        	}
	                                        } 
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="no_of_pallets">{{ trans("messages.no-of-pallets") }}</label>
                                    <input type="text" class="form-control" onkeyup="onlyNumber(this)" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> name="no_of_pallets" placeholder="{{ trans('messages.no-of-pallets') }}" value="{{old('no_of_pallets',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->i_no_palltes))) ?  $recordInfo->goodInBuyerMaster->i_no_palltes : '' ) )}}">
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="pallets_dimension">{{ trans("messages.pallets-dimension") }}</label>
                                    <select name="pallets_dimension[]" class="form-control select2"  multiple <?php echo ( $viewForm != false ?  $disableForm : '' ) ?>>
                                        <?php 
                                        $dimensionPalletRecordId = (!empty($recordInfo->goodInBuyerMaster->v_pallet_dimension_ids) ? explode("," , ( $recordInfo->goodInBuyerMaster->v_pallet_dimension_ids ) ) : [] );
                                        if(!empty($dimensionPalletRecordDetails)){
                                        	foreach ($dimensionPalletRecordDetails as $dimensionPalletRecordDetail){
                                        		$encodevDimensionId  = Wild_tiger::encode($dimensionPalletRecordDetail->i_id);
                                        		$selected = '';
                                        		if( isset($recordInfo->goodInBuyerMaster->v_pallet_dimension_ids) && (in_array($dimensionPalletRecordDetail->i_id, $dimensionPalletRecordId)) ){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{ $encodevDimensionId }}" {{$selected}}>{{ (!empty($dimensionPalletRecordDetail->v_dimension_name) ? $dimensionPalletRecordDetail->v_dimension_name : '' ) . ' ('. (!empty($dimensionPalletRecordDetail->v_dimension_size) ? $dimensionPalletRecordDetail->v_dimension_size : '' ). ')' }}</option>
                                        		<?php 
                                        	}
                                        } 
                                        ?>
                                    </select>
                                </div>
                            </div>
                             */?>

                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="pallets_type">{{ trans("messages.pallets-type") }}</label>
                                    <select name="pallets_type" class="form-control" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?>>
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <?php 
                                        if(!empty($palletsTypeInfo)){
                                        	foreach ($palletsTypeInfo as $key => $palletsType){
                                        		$selected = '';
                                        		if( isset($recordInfo->goodInBuyerMaster->e_pallet_type) && ( $recordInfo->goodInBuyerMaster->e_pallet_type == $key) ){
                                        			$selected = "selected='selected'";
                                        		}
                                        		?>
                                        		<option value="{{$key}}" {{$selected}}>{{ $palletsType }}</option>
                                        		<?php 
                                        	}
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>



                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="weight">{{ trans("messages.gross-weight") }}</label>
                                    <div class="input-group">
                                        <input type="text" name="weight" class="form-control" onkeyup="onlyDecimal(this)" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> placeholder="{{ trans('messages.gross-weight') }}" value="{{old('no_of_boxes',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->d_weight))) ?  $recordInfo->goodInBuyerMaster->d_weight : '' ) )}}">
                                        <?php /* 
                                        <div class="input-group-prepend">
                                            <select class="form-control ml-2" name="weight_unit" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?>>
                                                <option value="">{{ trans("messages.select") }}</option>
                                                <?php 
		                                        if(!empty($weightUnitInfo)){
		                                        	foreach ($weightUnitInfo as $key => $weightUnit){
		                                        		$selected = '';
		                                        		if( isset($recordInfo->goodInBuyerMaster->e_weight_unit) && ( $recordInfo->goodInBuyerMaster->e_weight_unit == $key) ){
		                                        			$selected = "selected='selected'";
		                                        		}
		                                        		?>
		                                        		<option value="{{$key}}" {{$selected}}>{{ $weightUnit }}</option>
		                                        		<?php 
		                                        	}
		                                        }
		                                        ?>
                                            </select>
                                        </div>
                                         */?>
                                    </div>
                                    <label id="weight_unit-error" class="invalid-input" style="display:none" for="weight_unit" style="display:none;"></label>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="net_weight">{{ trans("messages.net-weight") }}</label>
                                    <div class="input-group">
                                        <input type="text" name="net_weight" class="form-control" onkeyup="onlyDecimal(this)" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> placeholder="{{ trans('messages.net-weight') }}" value="{{old('net_weight',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->d_net_weight))) ?  $recordInfo->goodInBuyerMaster->d_net_weight : '' ) )}}">
                                        <?php /* 
                                        <div class="input-group-prepend">
                                            <select class="form-control ml-2" name="net_weight_unit" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?>>
                                                <option value="">{{ trans("messages.select") }}</option>
                                                <?php 
		                                        if(!empty($weightUnitInfo)){
		                                        	foreach ($weightUnitInfo as $key => $weightUnit){
		                                        		$selected = '';
		                                        		if( isset($recordInfo->goodInBuyerMaster->e_net_weight_unit) && ( $recordInfo->goodInBuyerMaster->e_net_weight_unit == $key) ){
		                                        			$selected = "selected='selected'";
		                                        		}
		                                        		?>
		                                        		<option value="{{$key}}" {{$selected}}>{{ $weightUnit }}</option>
		                                        		<?php 
		                                        	}
		                                        }
		                                        ?>
                                            </select>
                                        </div>
                                         */?>
                                    </div>
                                    <label id="net_weight-error" class="invalid-input" for="net_weight" style="display:none;"></label>
                                    <label id="net_weight_unit-error" class="invalid-input" for="net_weight_unit" style="display:none;"></label>
                                </div>
                            </div>
                            
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="total_unit">{{ trans("messages.total-units") }}<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" name="total_unit" class="form-control" onkeyup="onlyNumber(this)" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> placeholder="{{ trans('messages.total-units') }}" value="{{old('total_unit',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->i_total_units))) ?  $recordInfo->goodInBuyerMaster->i_total_units : '' ) )}}">
                                    </div>
                                    <label id="total_unit-error" class="invalid-input" for="total_unit" style="display:none;"></label>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="buyer_comments">{{ trans("messages.buyer-comment") }}</label>
                                    <div class="input-group">
                                        <input type="text" name="buyer_comments" class="form-control" <?php echo ( $viewForm != false ?  $disableForm : '' ) ?> placeholder="{{ trans('messages.buyer-comment') }}" value="{{ old('buyer_comments', (isset($recordInfo) && !empty($recordInfo->goodInBuyerMaster->v_buyer_comments) ? $recordInfo->goodInBuyerMaster->v_buyer_comments : '')) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="mode_of_transport" class="control-label">{{ trans("messages.mode-of-transport") }}</label>
                                    <select name="mode_of_transport" <?php echo $disableForm ?> class="form-control">
                                        <option value="">{{ trans("messages.select") }}</option>
                                        <option value="ROW-CON" {{ (isset($recordInfo) && $recordInfo->goodInBuyerMaster->e_mode_of_transport == 'ROW-CON') ? "selected='selected'" : '' }}>ROW-CON</option>
                                        <option value="ROW-AIR" {{ (isset($recordInfo) && $recordInfo->goodInBuyerMaster->e_mode_of_transport == 'ROW-AIR') ? "selected='selected'" : '' }}>ROW-AIR</option>
                                        <option value="BY ROAD" {{ (isset($recordInfo) && $recordInfo->goodInBuyerMaster->e_mode_of_transport == 'BY ROAD') ? "selected='selected'" : '' }}>BY ROAD</option>
                                    </select>
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
                                                        <tbody class="good-in-buyer-master-tbody">
	                                                        <?php
	                                                        if( isset($recordInfo->goodInBuyerMaster->goodInBuyerDocument) && (!empty($recordInfo->goodInBuyerMaster->goodInBuyerDocument)) && (count($recordInfo->goodInBuyerMaster->goodInBuyerDocument) > 0 ) ){
	                                                        	foreach ($recordInfo->goodInBuyerMaster->goodInBuyerDocument as $countKey => $goodsInBuyerDocumentDetail){
	                                                        		$columIndex  = ( $countKey +  1 );
		                                                        	?>
			                                                        <?php $documentFiles = (json_decode($goodsInBuyerDocumentDetail->v_document_file_path)); ?>
			                                                        <tr>
		                                                                <td class="table-index text-center" style="width:70px;min-width:70px;">{{$columIndex}}</td>
		                                                                <td class="text-left">
		                                                                    <select <?php echo $documentForm ?> name="edit_type_<?php echo $goodsInBuyerDocumentDetail->i_id ?>" class="form-control good-in-buyer-type">
		                                                                        <option value="">{{ trans("messages.select") }}</option>
		                                                                        <?php 
										                                        if(!empty($documentTypeRecordDetails)){
										                                        	foreach ($documentTypeRecordDetails as $documentTypeRecordDetail){
										                                        		$encodevDocumentTypeId  = Wild_tiger::encode($documentTypeRecordDetail->i_id);
										                                        		$selected = '';
										                                        		if( isset($goodsInBuyerDocumentDetail->i_document_type_id) && ( $goodsInBuyerDocumentDetail->i_document_type_id == $documentTypeRecordDetail->i_id ) ){
										                                        			$selected = "selected='selected'";
										                                        		}
										                                        		?>
										                                        		<option value="{{ $encodevDocumentTypeId }}" {{ $selected }} data-document-type-id="{{ $documentTypeRecordDetail->i_id }}">{{ (!empty($documentTypeRecordDetail->v_document_type_name) ? $documentTypeRecordDetail->v_document_type_name : '' ) }}</option>
										                                        		<?php 
										                                        	}
										                                        } 
										                                        ?>
		                                                                    </select>
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <div class="custom-file">
		                                                                        <input <?php echo $documentForm ?> type="file" data-valid-file="yes" name="edit_file_<?php echo $goodsInBuyerDocumentDetail->i_id ?>[]" class="custom-file-input good-in-buyer-file" id="document_1" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
		                                                                        <label class="custom-file-label" for="document_1"><?php echo (!empty($documentFiles) ? ( count($documentFiles) > 1 ? trans('messages.multiple-files') : ( isset($documentFiles[0]) ? basename($documentFiles[0]) : '' ) )  : trans('messages.choose-file') ) ?></label>
		                                                                    </div>
		                                                                </td>
		                                                                <td class="text-left">
		                                                                    <input type="text" <?php echo $documentForm ?> class="form-control" name="edit_remarks_<?php echo $goodsInBuyerDocumentDetail->i_id ?>" value="<?php echo (isset($goodsInBuyerDocumentDetail->v_document_remark) ? $goodsInBuyerDocumentDetail->v_document_remark : '' ); ?>" >
		                                                                </td>
		                                                                
		                                                                 <td class="actions-col">
		                                                                <?php 
		                                                                	if(!empty($documentFiles)){
			                                                                	foreach ($documentFiles as $documentFile){
			                                                                		$imagePath = (config('constants.FILE_STORAGE_URL_PATH').$documentFile);
			                                                                		?>
				                                                                	<div class="download-link-items">
		                                                                  				<a title="{{trans('messages.remove')}}" href="javascript:void(0);" data-file-name="{{ basename($imagePath) }}" onclick="removeUploadedFile(this);" data-record-id="{{ $goodsInBuyerDocumentDetail->i_id }}" data-field-name="document" class="close-icon"><i class="fa fa-times "></i></a>
		                                                                  				<a title="{{ basename($imagePath) }}" href="{{ $imagePath }}" target='_blank' class="btn btn-sm btn-danger mb-1 download-icon-items"><i class="fa fa-download"></i></a>
		                                                                			</div>
		                                                                    	<?php 
			                                                                	}
		                                                                	}
		                                                                ?>
	                                                                   </td>
	
	                                                                	<td style="width:70px;min-width:70px;">
	                                                                		<?php if(empty($documentForm)) { ?>
	                                                                		<button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button>
	                                                                		<?php } ?>
	                                                                	</td>
		                                                            </tr>
	                                                        	<?php
	                                                        	}
															}else {?>
	                                                            <tr>
	                                                                <td class="table-index text-center" style="width:70px;min-width:70px;">1</td>
	                                                                <td class="text-left">
	                                                                    <select name="type_1" class="form-control good-in-buyer-type" <?php echo $documentForm ?>>
	                                                                        <option value="">{{ trans("messages.select") }}</option>
	                                                                        <?php 
									                                        if(!empty($documentTypeRecordDetails)){
									                                        	foreach ($documentTypeRecordDetails as $documentTypeRecordDetail){
									                                        		$encodevDocumentTypeId  = Wild_tiger::encode($documentTypeRecordDetail->i_id);
									                                        		?>
									                                        		<option value="{{ $encodevDocumentTypeId }}" data-document-type-id="{{ $documentTypeRecordDetail->i_id }}" >{{ (!empty($documentTypeRecordDetail->v_document_type_name) ? $documentTypeRecordDetail->v_document_type_name : '' ) }}</option>
									                                        		<?php 
									                                        	}
									                                        } 
									                                        ?>
	                                                                    </select>
	                                                                </td>
	                                                                <td class="text-left">
	                                                                    <div class="custom-file">
	                                                                        <input type="file" <?php echo $documentForm ?> name="file_1[]" class="custom-file-input good-in-buyer-file" id="document_1" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
	                                                                        <label class="custom-file-label" for="document_1">{{ trans('messages.choose-file') }}</label>
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
	                                                                    <select name="type_2" class="form-control good-in-buyer-type" <?php echo $documentForm ?>>
	                                                                        <option value="">{{ trans("messages.select") }}</option>
	                                                                        <?php 
									                                        if(!empty($documentTypeRecordDetails)){
									                                        	foreach ($documentTypeRecordDetails as $documentTypeRecordDetail){
									                                        		$encodevDocumentTypeId  = Wild_tiger::encode($documentTypeRecordDetail->i_id);
									                                        		?>
									                                        		<option value="{{ $encodevDocumentTypeId }}" data-document-type-id="{{ $documentTypeRecordDetail->i_id }}" >{{ (!empty($documentTypeRecordDetail->v_document_type_name) ? $documentTypeRecordDetail->v_document_type_name : '' ) }}</option>
									                                        		<?php 
									                                        	}
									                                        } 
									                                        ?>
	                                                                    </select>
	                                                                </td>
	                                                                <td class="text-left">
	                                                                    <div class="custom-file">
	                                                                        <input type="file" name="file_2[]" <?php echo $documentForm ?> class="custom-file-input good-in-buyer-file" id="document_2" multiple onchange="validFile(this,'pdf_doc_jpg_png_jpeg_xls')">
	                                                                        <label class="custom-file-label" for="document_2">{{ trans('messages.choose-file') }}</label>
	                                                                    </div>
	                                                                </td>
	                                                                <td class="text-left">
	                                                                    <input type="text" class="form-control" name="remarks_2" <?php echo $documentForm ?>>
	                                                                </td>
	                                                                <td class="actions-col">
	                                                                </td>
	
	                                                                <td style="width:70px;min-width:70px;">
	                                                                	<?php if(empty($documentForm)) { ?> 
	                                                                	<button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button>
	                                                                	<?php } ?>
	                                                                </td>
	                                                            </tr>
                                                            <?php 
															}?>
                                                        </tbody>
                                                    </table>
                                                    <?php if(empty($documentForm)) { ?> 
                                                    <button type="button" class="btn btn-sm bg-theme text-white add-new-row" title="{{ trans('messages.add-new') }}" onclick="addNewBuyerDocumentRow(this)"><i class="fa fa-plus fa-fw"></i>{{ trans("messages.add-new") }}</button>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 submit-sticky">
						<?php if(empty($documentForm)) { ?> 
                   	 		<?php if( isset($recordInfo) && ( $recordInfo->goodInBuyerMaster->i_id > 0 ) ) { ?>
                    			<input type="hidden" name="record_id" value="{{ Wild_tiger::encode($recordInfo->goodInBuyerMaster->i_id) }}">
                    			<button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.update') }}">{{ trans("messages.update") }}</button>
                        	<?php 
                        	} else { ?>
                        		<button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.submit') }}">{{ trans("messages.submit") }}</button>
                        	<?php } ?>
                        <?php } ?>
                        	
                        	<a href="{{ config('constants.GOODS_IN_BUYER_MASTER_URL') }}" class="btn btn-outline-secondary shadow-sm btn-wide" title="{{ trans('messages.cancel') }}">{{ trans("messages.cancel") }}</a>
                    </div>
            </div>
            <input type="hidden" name="good_in_buyer_count" value="">
             {!! Form::close() !!}
        </div>
        </div>
        </div>
    </section>
</main>
<script>
 var good_in_buyer_count = 2;
 <?php 
 if(!empty($goodsInBuyerDocumentDetails)){?>
 	good_in_buyer_count = '<?php echo count($goodsInBuyerDocumentDetails)?>';
 <?php 
 }
 ?>
    $("#add-good-in-buyer-master-form").validate({
        errorClass: "invalid-input",
        rules: {
            buyer_company: {
                required: true,
                noSpace:true
            },
            'user_company[]': {
                required: true,
                noSpace:true
            },
            'buyer_name[]': {
                required: true,
            },
            'user_buyer_name[]': {
                required: true,
            },
            supplier_name: {
                required: true,
                noSpace:true
            },
            'supplier_location[]': {
                required: true,
                noSpace:true
            },
            order_date: {
                required: true
            },
            po_no_sales_invoice_no: {
                required: true,
                noSpace:true,
                validateUniquePoSalesInvoiceNumber : true
            },
            po_no_sales_invoice_amount: {
                required: true,
                noSpace:true
            },
            vendor_number: {
                required: true,
                noSpace:true
            },
            po_amount_with_vat: {
                required: true,
                noSpace:true
            },
            brand: {
                required: true,
                noSpace:true
            },
            /* payment_status: {
                required: true,
                noSpace:true
            }, */
            payment_date: {
                required: true
            },
            po_create_user_name: {
                required: true
            },
            /* amount: {
                required: true,
                noSpace:true
            }, */
            payment_terms: {
                required: true
            },
            customs_procedure: {
                required: true
            },
            collection_delivery: {
                required: true,
            },
            ready_for_collection: {
                required: true,
            },
            delivery_type: {
                required: true,
                noSpace:true
            },
            booking_ref_no: {
                required: true,
                noSpace:true
            },
            delivery_location: {
                required: true,
                noSpace:true
            },
            delivery_date: {
                required: true,
                noSpace:true
            },
            /* 
            delivery_remarks: {
                required: true,
                noSpace:true
            },
            custom_procedure_export: {
                required: true,
                noSpace:true
            },
            custom_procedure_import: {
                required: true,
                noSpace:true
            }, */
            dangerous_goods: {
                required: true,
 				noSpace:true
            },
            'goods_remarks[]': {
                required: true,
            },
            no_of_pallets_boxes: {
                required: true,
 				noSpace:true,
 				validatePalletNoDateWise : true
            },
            pallets_boxes_type: {
                required: true,
            },
           /*  boxes_dimension: {
                required: true
            }, */
            /* weight_unit: {
                required: true,
                noSpace:true
            },*/
            po_no_amount: {
                required: true,
                noSpace:true
            },
            /* sales_invoice_amount: {
                required: true,
                noSpace:true
            }, */
            total_unit: {
            	required: true,
                noSpace:true
            },
            /* net_weight_unit: {
                required: true,
                noSpace:true
            }, */
            pickup_reference: {
                required: true,
                noSpace:true
            },
            reference: {
                required: true,
                noSpace:true
            },
        },
        messages: {
            buyer_company: {
                required: "{{ trans('messages.require-buyer-company') }}"
            },
            'user_company[]': {
                required: "{{ trans('messages.require-user-company') }}"
            },
            'buyer_name[]': {
                required: "{{ trans('messages.require-buyer-name') }}"
            },
            'user_buyer_name[]': {
                required: "{{ trans('messages.require-user-buyer-name') }}"
            },
            supplier_name: {
                required: "{{ trans('messages.require-supplier-name') }}"
            },
            'supplier_location[]': {
                required: "{{ trans('messages.require-supplier-location') }}"
            },
            order_date: {
                required: "{{ trans('messages.require-order-date') }}"
            },
            po_no_sales_invoice_no: {
                required: "{{ trans('messages.require-po-number') }}"
            },
            po_no_sales_invoice_amount: {
                required: "{{ trans('messages.require-currency') }}"
            },
            vendor_number: {
                required: "{{ trans('messages.require-vendor-number') }}"
            },
            po_amount_with_vat: {
                required: "{{ trans('messages.require-po-amount-with-vat') }}"
            },
            brand: {
                required: "{{ trans('messages.require-brand') }}"
            },
            /* payment_status: {
                required: "{{ trans('messages.require-payment-status') }}"
            }, */
            payment_date: {
                required: "{{ trans('messages.require-payment-date') }}"
            },
            po_create_user_name: {
                required: "{{ trans('messages.require-po-create-user-name') }}"
            },
            /* amount: {
                required: "{{ trans('messages.require-currency') }}"
            }, */
            payment_terms: {
                required: "{{ trans('messages.require-payment-terms') }}"
            },
            customs_procedure: {
                required: "{{ trans('messages.require-customs-procedure') }}"
            },
            collection_delivery: {
                required: "{{ trans('messages.require-collection-delivery') }}"
            },
            ready_for_collection: {
                required: "{{ trans('messages.require-ready-for-collection') }}"
            },
            delivery_type: {
                required: "{{ trans('messages.require-delivery-type') }}"
            },
            booking_ref_no: {
                required: "{{ trans('messages.require-booking-ref-no') }}"
            },
            delivery_location: {
                required: "{{ trans('messages.require-delivery-location') }}"
            },
            delivery_date: {
                required: "{{ trans('messages.require-buyer-delivery-date') }}"
            },
            /* 
            delivery_remarks: {
                required: "{{ trans('messages.require-delivery-remarks') }}"
            },
            custom_procedure_export: {
                required: "{{ trans('messages.require-custom-procedure-export') }}"
            },
            custom_procedure_import: {
                required: "{{ trans('messages.require-custom-procedure-import') }}"
            }, */
            dangerous_goods: {
                required: "{{ trans('messages.require-dangerous-goods') }}"
            },
            'goods_remarks[]': {
                required: "{{ trans('messages.require-goods-remark') }}"
            },
            no_of_pallets_boxes: {
                required: "{{ trans('messages.require-no-of-pallet-box') }}"
            },
            pallets_boxes_type: {
                required: "{{ trans('messages.require-pallets-boxes-type') }}"
            },
            pallets_type: {
                required: "{{ trans('messages.require-pallets-type') }}"
            },
            /* weight_unit: {
                required: "{{ trans('messages.require-weight-unit') }}"
            },
            sales_invoice_amount: {
                required: "{{ trans('messages.require-amount') }}"
            }, */
            po_no_amount: {
                required: "{{ trans('messages.require-po-no-sales-invoice-amount') }}"
            },
            total_unit:{
            	required: "{{ trans('messages.require-total-units') }}"
            },
            pickup_reference:{
            	required: "{{ trans('messages.require-pickup-reference') }}"
            },
            reference:{
            	required: "{{ trans('messages.require-reference') }}"
            },
            /* net_weight_unit: {
                required: "{{ trans('messages.require-weight-unit') }}"
            }, */
        },
        submitHandler: function(form) {
        	var good_in_buyer_type_status = true;
       	 	var ready_for_collection = $.trim($('[name="ready_for_collection"]').val());
       	 	
       	 	var find_packing_slip = true;
       	 	var find_invoice_slip = true;
			if((ready_for_collection !="" && ready_for_collection != null) && (ready_for_collection == "{{ config('constants.SELECTION_YES')}}")){
				find_packing_slip = false;
				find_invoice_slip = false;
	       	 	$('.good-in-buyer-master-tbody tr').each(function(){
			       	var good_in_buyer_type = $.trim($(this).find('.good-in-buyer-type').val());
			       	var good_in_buyer_file = $.trim($(this).find('.good-in-buyer-file').val());
			       	var good_in_buyer_file_valid = $.trim($(this).find('.good-in-buyer-file').attr('data-valid-file'));
			       	var good_in_buyer_type_id = $.trim($(this).find('option:selected').attr('data-document-type-id'));
			       	
			       if(good_in_buyer_type != "" && good_in_buyer_type != null){
			    	   good_in_buyer_type_status = false;

			    	   if( good_in_buyer_type_id != "" && good_in_buyer_type_id != null && good_in_buyer_type_id == "{{ config('constants.DOCUMENT_TYPE_PACKING_LIST_ID') }}"){
							if( good_in_buyer_file_valid != "" && good_in_buyer_file_valid != null && good_in_buyer_file_valid == "{{ strtolower(config('constants.SELECTION_YES')) }}"){
								find_packing_slip = true;
								
							}
				       }
			    	   if( good_in_buyer_type_id != "" && good_in_buyer_type_id != null && good_in_buyer_type_id == "{{ config('constants.DOCUMENT_TYPE_INVOICE_ID') }}"){
							if( good_in_buyer_file_valid != "" && good_in_buyer_file_valid != null && good_in_buyer_file_valid == "{{ strtolower(config('constants.SELECTION_YES')) }}"){
								find_invoice_slip = true;
								
							}
				       }
			       	 }
			   });
	       	 	if( good_in_buyer_type_status != false ){
					alertifyMessage("error","{{ trans('messages.required-atleast-checkbox-selection') }} ");
				   	return false;
				}
			}

			if( find_packing_slip != true ){
				alertifyMessage("error","{{ trans('messages.require-document-packing-list') }} ");
				return false;
			}
			
			if( find_invoice_slip != true ){
				alertifyMessage("error","{{ trans('messages.require-document-invoice') }} ");
				return false;
			}
        	var confirm_box = "";
            var confirm_box_msg = "";
            <?php if( isset($recordInfo) && (  $recordInfo->goodInBuyerMaster->i_id > 0 ) ) { ?>
          			confirm_box = "{{ trans('messages.update-good-in-buyer') }}";
        			confirm_box_msg = "{{ trans ( 'messages.confirm-good-in-buyer-update-msg') }}";
           
    		<?php } else {?>
           			confirm_box = "{{ trans('messages.add-buyer') }}";
 					confirm_box_msg = "{{ trans ( 'messages.confirm-good-in-buyer-add-msg') }}";
 			<?php }?>
 			
 			alertify.confirm(confirm_box,confirm_box_msg,function() {
 				$("[name='collection_delivery']").prop('disabled', false);
 				$("[name='delivery_type']").prop('disabled', false);
 				$("[name='collection_location']").prop('disabled', false);
 				
 				$('input:disabled').prop('disabled', false);
 				$('select:disabled').prop('disabled', false);
 				$("[name='good_in_buyer_count']").val(good_in_buyer_count);
 				showLoader()
                form.submit();
         	},function() {});
        }
    });


var good_in_buyer_module_url = '{{config("constants.GOODS_IN_BUYER_MASTER_URL")}}' + '/';

$.validator.addMethod("validateUniquePoSalesInvoiceNumber", function (value, element) {
	var result = true;
	var record_id = $.trim($('[name="record_id"]').val());
	var po_no_sales_invoice_no = $.trim($('[name="po_no_sales_invoice_no"]').val());
	
	$.ajax({
		type: "POST",
		async: false,
		url: good_in_buyer_module_url +'checkUniquePoSalesInvoiceNumber',
		dataType: "json",
		data: {
			"_token": "{{ csrf_token() }}",
			'record_id' : record_id,
			'po_no_sales_invoice_no' : po_no_sales_invoice_no
		},
		success: function (response) {
			if (response.status_code == 1) {
				return false;
			} else {
				result = false;
				return true;
			}
		}
	});
	return result;
}, '<?php echo trans("messages.unique-po-sales-invoice-number")?>');


$.validator.addMethod("validatePalletNoDateWise", function (value, element) {
	var result = true;
	var record_id = $.trim($('[name="record_id"]').val());
	var no_of_pallets_boxes = $.trim($('[name="no_of_pallets_boxes"]').val());
	var record_type = $.trim($('[name="pallets_boxes_type"]').val());
	var warehouse = $.trim($('[name="delivery_location"]').val());
	var buyer_delivery_date = $.trim($('[name="delivery_date"]').val());
	
	$.ajax({
		type: "POST",
		async: false,
		url: good_in_buyer_module_url +'checkPalletLimit',
		dataType: "json",
		data: {
			"_token": "{{ csrf_token() }}",
			'record_id' : record_id,
			'no_of_pallets_boxes' : no_of_pallets_boxes,
			'record_type' : record_type,
			'warehouse' : warehouse,
			'buyer_delivery_date' : buyer_delivery_date
		},
		success: function (response) {
			if (response.status_code == 1) {
				return false;
			} else {
				result = false;
				return true;
			}
		}
	});
	return result;
}, '<?php echo trans("messages.pallet-limit-validation")?>');


function getSupplierLocation(thisitem){
	var supplier_record_id = $.trim($(thisitem).val());
	
	if(supplier_record_id != "" && supplier_record_id != null){
		$.ajax({
	    	type:'post',
	    	data:{"_token": "{{ csrf_token() }}",'supplier_record_id':supplier_record_id},
	    	url: good_in_buyer_module_url + 'getSupplierLocation',
	    	beforeSend: function() {
	    		//block ui
	    		showLoader();
	    	},	
	    	success: function(response) {
	    		hideLoader();
	    		if(response !="" && response != null){
	    			$(thisitem).parents('.supplier-list-row').find('.location-list').html(response);
	    			
				}
	    	},
	    	error: function() {
				hideLoader();
			}
	    });
	} 
}
<?php /* 
function paymentStatusInfo(){
	var payment_status = $.trim($('[name="payment_status"]').val());
	if(payment_status == '{{config("constants.PARTIAL_PAID_PAYMENT_STATUS")}}' || payment_status == '{{config("constants.PAID_PAYMENT_STATUS")}}'){
		$('.payment-status-div').show();
	} else {
		$('.payment-status-div').hide();
	}
}
 */?>
function collectionRecordInfo(){
	var collection_delivery = $.trim($('[name="collection_delivery"]').val());
	if(collection_delivery !='' && collection_delivery != null){
		switch(collection_delivery){
			case '{{config("constants.DELIVERY")}}' :
			$('.deliver-record-div').show();
			$('.delivery-collection-location-record').hide();
			break;
			case '{{config("constants.COLLECTION")}}' :
			$('.deliver-record-div').hide();
			$('.delivery-collection-location-record').show();
			break;
			
		}
	} else{
		$('.deliver-record-div').hide();
		$('.delivery-collection-location-record').hide();
	}
}
var good_in_buyer_count = good_in_buyer_count;

function addNewBuyerDocumentRow(thisitem){
	good_in_buyer_count++;
	var html = '';
	html += '<tr>';
	html += '<td class="table-index text-center" style="width:70px;min-width:70px;">'+good_in_buyer_count+'</td>';
	html += '<td class="text-left">';
	html += '<select name="type_'+good_in_buyer_count+'" class="form-control good-in-buyer-type">';
	html += '<option value="">{{ trans("messages.select") }}</option>';
    <?php 
    if(!empty($documentTypeRecordDetails)){
    	foreach ($documentTypeRecordDetails as $documentTypeRecordDetail){
        	$encodevDocumentTypeId  = Wild_tiger::encode($documentTypeRecordDetail->i_id);
        	?>
        	html += '<option value="{{ $encodevDocumentTypeId }}" data-document-type-id="{{ $documentTypeRecordDetail->i_id }}">{{ (!empty($documentTypeRecordDetail->v_document_type_name) ? $documentTypeRecordDetail->v_document_type_name : '' ) }}</option>';
       		<?php 
       	}
  	} 
    ?>
    html += '</select>';
	html += '</td>';
	html += '<td class="text-left">';
	html += '<div class="custom-file">';
	html += '<input type="file" name="file_'+good_in_buyer_count+'[]" class="custom-file-input good-in-buyer-file" id="document_'+good_in_buyer_count+'" multiple onchange="validFile(this,\'pdf_doc_jpg_png_jpeg_xls\')">';
	html += '<label class="custom-file-label" for="document_'+good_in_buyer_count+'"><?php echo trans('messages.choose-file') ?></label>';
	html += '</div>';
	html += '</td>';
	html += '<td class="text-left">';
	html += '<input type="text" class="form-control" name="remarks_'+good_in_buyer_count+'">';
	html += '</td>';
	html += '<td class="actions-col">';
	html += '</td>';
	html += '<td style="width:70px;min-width:70px;"><button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button></td>';
	html += '</tr>';
	if( $('.good-in-buyer-master-tbody').find('tr').length > 0 ){
		$(html).insertAfter($('.good-in-buyer-master-tbody').find('tr:last'));	
	} else {
		$('.good-in-buyer-master-tbody').html(html);
	}
	reindexTable('good-in-buyer-master-tbody');
}

</script>

<script>
   $(document).ready(function() {

        //init date time picker
         $("[name='delivery_date'] , [name='po_creation_date']").datetimepicker({
            useCurrent: false,
            viewMode: 'days',
            ignoreReadonly: true,
            widgetPositioning: {
                vertical: 'bottom'
            },
            format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',
		});

        <?php /* if( !isset($recordInfo) ) { ?>
      	$("[name='delivery_date']").data('DateTimePicker').minDate(moment().startOf('d'));
      	<?php } */?>
      	<?php if( isset($recordInfo) && ( $recordInfo->i_id > 0 ) ) { ?>
	     		var delivery_date = '{{ ( isset($recordInfo->goodInBuyerMaster->dt_delivery_date) ? $recordInfo->goodInBuyerMaster->dt_delivery_date : '') }}';
	     		if(delivery_date != "" && delivery_date != null){
	    			//$("[name='delivery_date']").data('DateTimePicker').minDate(moment(delivery_date).startOf('d'));
	    		}
	  	<?php } else {?>
	  		$("[name='delivery_date']").data('DateTimePicker').minDate(moment().startOf('d'));
	  	<?php } ?>

       //init date time picker
         $("[name='payment_date'], [name='actual_payment_date'], [name='invoice_date']").datetimepicker({
            useCurrent: false,
            viewMode: 'days',
            ignoreReadonly: true,
            widgetPositioning: {
                vertical: 'bottom'
            },
            format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

        });

         //init date time picker
         $("[name='order_date']").datetimepicker({
              useCurrent: false,
              viewMode: 'days',
              ignoreReadonly: true,
              widgetPositioning: {
                  vertical: 'bottom'
              },
             // minDate: moment().startOf('y'),
              maxDate: moment().endOf('d'),
              format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

          }); 

    }); 

	function showRefenceInfo(thisitem){
		var pickup_reference = $.trim($('[name="pickup_reference"]:checked').val());
	   	if(pickup_reference !="" && pickup_reference != null){
			switch(pickup_reference){
				case '{{config("constants.SELECTION_YES")}}' :
					$('.pickup-refence-yes-div').show();
				break;
				case '{{config("constants.SELECTION_NO")}}' :
					$('.pickup-refence-yes-div').hide();
				break;
			}	
	   } else {
		   $('.pickup-refence-yes-div').hide();
	   }
	}

</script>
@endsection