@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')



<main class="page-height bg-light-color">
	<div class="breadcrumb-wrapper d-flex align-items-center border-navabr">
		<h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }} (<span class="total-record-count"></span>) </h1>
		<div class="ml-auto pt-sm-0 d-flex align-items-center">
			<?php if (checkPermission(config('permission_constants.ADD_GOODS_IN_BUYER')) != false){ ?>
			<button type="button" title="{{ trans('messages.import-good-in-buyer') }}" class="btn btn btn-theme text-white button-actions-top-bar border btn-sm mr-2 d-flex align-items-center" onclick="openImportExcelModal();"><i class="fas fa-download mr-md-1 fa-fw"></i><span class="d-md-block d-none">{{ trans("messages.import-good-in-buyer") }}</span></button>
			<?php /* <a href="{{ config('constants.GOODS_IN_BUYER_MASTER_URL') . '/create' }}" class="btn btn btn-theme text-white button-actions-top-bar  border btn-sm mr-2 d-flex align-items-center" title="{{ trans('messages.add-buyer') }}"><i class=" fas fa-plus mr-md-2"></i> <span class="d-md-block d-none">{{ trans('messages.add-buyer') }}</span></a> */?>
			<?php } ?>
			<button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center mr-2" data-toggle="collapse" data-target="#filter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none">{{ trans("messages.filter") }}</span></button>
			@if((checkPermission(config('permission_constants.EXCEL_EXPORT_GOODS_IN_BUYER')) != false))
            	<button type="button" title="{{ trans('messages.export-excel') }}"  class="btn btn btn-theme text-white button-actions-top-bar  border btn-sm mr-2 d-flex align-items-center" onclick="exportData();"><i class="fas fa-upload mr-md-1 fa-fw"></i><span class="d-md-block d-none">{{ trans("messages.export-excel") }}</span></button>
            @endif
		</div>
	</div>

	<section class="inner-wrapper-common-section main-listing-section">
		<div class="container-fluid">
			<div class="collapse" id="filter">
				<div class="card card-body mb-3">
					<div class="row align-items-center dependent-div-class">
						<div class="col-xl-6 col-md-12">
							<div class="form-group">
								<label for="search_by_logistic_partner_name" class="control-label">{{ trans("messages.search-by") }}</label>
								<input type="text" class="form-control twt-enter-search custom-input" name="search_by_logistic_partner_name" id="search_by_logistic_partner_name" placeholder="{{ trans('messages.search-by-good-in-buyer') }}">
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_buyer_company">{{ trans("messages.buyer-company") }}</label>
								<select name="search_buyer_company" class="form-control select2" multiple onchange="filterData();">
									<?php 
									if(!empty($companyRecordDetails)){
										foreach ($companyRecordDetails as $companyRecordDetail){
											$encodeCompanyId  = Wild_tiger::encode($companyRecordDetail->i_id);
											?>
                                        		<option value="{{ $encodeCompanyId }}">{{ (!empty($companyRecordDetail->v_company_name) ? $companyRecordDetail->v_company_name : '' ) }}</option>
                                        		<?php 
                                        	}
                                        } 
									?>
								</select>
							</div>
						</div>
						<?php /* 
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_user_company">{{ trans("messages.user-company") }}</label>
								<select name="search_user_company" class="form-control select2" multiple onchange="filterData();">
									<?php 
									if(!empty($companyRecordDetails)){
										foreach ($companyRecordDetails as $companyRecordDetail){
											$encodeCompanyId  = Wild_tiger::encode($companyRecordDetail->i_id);
											?>
                                        		<option value="{{ $encodeCompanyId }}">{{ (!empty($companyRecordDetail->v_company_name) ? $companyRecordDetail->v_company_name : '' ) }}</option>
                                        		<?php 
                                        	}
                                        } 
									?>
								</select>
							</div>
						</div>
						 */?>
						 <?php if (isset($loggedUserBuyerRoles) && !in_array(config('constants.BUYER'), $loggedUserBuyerRoles) && !in_array(config('constants.GOODS_IN_WAREHOUSE'), $loggedUserBuyerRoles)){ ?>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_buyer_name">{{ trans("messages.buyer-name") }}</label>
								<select name="search_buyer_name" class="form-control select2" multiple onchange="filterData();">
									<?php 
                                    if(!empty($userRecordDetails)){
                                    	foreach ($userRecordDetails as $userRecordDetail){
                                        	$encodevUserId  = Wild_tiger::encode($userRecordDetail->i_id);
                                        	?>
                                        	<option value="{{ $encodevUserId }}">{{ (!empty($userRecordDetail->v_name) ? $userRecordDetail->v_name : '' ) }}</option>
                                        	<?php 
                                   		}
                                    } 
                                   	?>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_user_buyer_name">{{ trans("messages.user-buyer-name") }}</label>
								<select name="search_user_buyer_name" class="form-control select2" multiple onchange="filterData();">
									<?php 
                                    if(!empty($userBuyerRecordDetails)){
                                    	foreach ($userBuyerRecordDetails as $userBuyerRecordDetail){
                                        	$encodeId  = Wild_tiger::encode($userBuyerRecordDetail->i_id);
                                        	?>
                                        	<option value="{{ $encodeId }}">{{ (!empty($userBuyerRecordDetail->v_name) ? $userBuyerRecordDetail->v_name : '' ) }}</option>
                                        	<?php 
                                   		}
                                    } 
                                   	?>
								</select>
							</div>
						</div>
						<?php } ?>
						<?php /* 
						<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6" >
							<div class="form-group">
								<label class="control-label" for="search_po_create_user_name">{{ trans("messages.po-create-user-name") }}</label>
								<select name="search_po_create_user_name" class="form-control select2" onchange="filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									 <?php 
									 if(!empty($poCreateUserDetails)){
									 	foreach ($poCreateUserDetails as $poCreateUserDetail){
									 		$encodeId  = Wild_tiger::encode($poCreateUserDetail->i_id);
									 		?>
									 	    <option value="{{ $encodeId }}"><?php echo  (!empty($poCreateUserDetail->v_name) ? $poCreateUserDetail->v_name . (!empty($poCreateUserDetail->v_department) ?  ' ('. $poCreateUserDetail->v_department . ')' : '' ) : '' ) ?></option>
									 	    <?php 
										}
									}
									 ?>
								</select>
							</div>
						</div>
						 */?>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label for="search_collection_delivery" class="control-label">{{ trans("messages.collection-delivery") }}</label>
								<select name="search_collection_delivery" class="form-control" onchange="showCollectionDeliveryData(this),filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                   	if(!empty($collectionDeliveryInfo)){
                                    	foreach ($collectionDeliveryInfo as $key=>$collectionDelivery){
                                     		?>
                                        	<option value="{{$key}}">{{ $collectionDelivery }}</option>
                                        	<?php 
                                        }
                                    }
                                   ?>
								</select>
							</div>
						</div>
						<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_delivery_collection_location">{{ trans("messages.delivery-location") }}</label>
								<select name="search_delivery_collection_location" class="form-control select2" onchange="filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									 <?php 
                                     if(!empty($warehouseDetails)){
                                     	foreach ($warehouseDetails as $warehouseDetail){
                                       		$encodeId = Wild_tiger::encode($warehouseDetail->i_id);
                                        	?>
                                        	<option value="{{ $encodeId }}">{{ (!empty($warehouseDetail->v_warehouse_name) ? $warehouseDetail->v_warehouse_name .(!empty($warehouseDetail->v_warehouse_code) ? ' (' .$warehouseDetail->v_warehouse_code .')' : '' ): '' ) }}</option>
                                        <?php 
                                    	}
                                     }
                                     ?>
								</select>
							</div>
						</div>
						<?php /*
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 delivery-collection-row" style="display: none">
							<div class="form-group">
								<label for="search_delivery_type" class="control-label">{{ trans("messages.delivery-type") }}</label>
								<select name="search_delivery_type" class="form-control" onchange="filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
									if(!empty($deliveryTypeInfo)){
										foreach ($deliveryTypeInfo as $key => $deliveryType){
											?>
                                       		<option value="{{$key}}">{{ $deliveryType }}</option>
                                        	<?php 
                                   		}
                                 	}
									?>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 delivery-collection-row" style="display: none">
							<div class="form-group">
								<label for="search_delivery_location" class="control-label">{{ trans("messages.delivery-location") }}</label>
								<select name="search_delivery_location" class="form-control" onchange="filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                        if(!empty($warehouseRecordDetails)){
                                        	foreach ($warehouseRecordDetails as $warehouseRecordDetail){
                                        		$encodeWarehouseId  = Wild_tiger::encode($warehouseRecordDetail->i_id);
                                        		?>
                                        		<option value="{{ $encodeWarehouseId }}">{{ (!empty($warehouseRecordDetail->v_warehouse_name) ? $warehouseRecordDetail->v_warehouse_name .(!empty($warehouseRecordDetail->v_warehouse_code) ? ' (' .$warehouseRecordDetail->v_warehouse_code .')' : '' ): '' ) }}</option>
                                        		<?php 
                                        	}
                                        } 
                                        ?>
								</select>
							</div>
						</div>
						<?php */?>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 delivery-collection-row" style="display: none">
							<div class="form-group">
								<label class="control-label" for="search_buyer_delivery_from_date">{{ trans("messages.buyer-delivery-from-date") }}</label>
								<input type="text" name="search_buyer_delivery_from_date" class="form-control" placeholder="{{ trans('messages.buyer-delivery-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 delivery-collection-row" style="display: none">
							<div class="form-group">
								<label class="control-label" for="search_buyer_delivery_to_date">{{ trans("messages.buyer-delivery-to-date") }}</label>
								<input type="text" name="search_buyer_delivery_to_date" class="form-control" placeholder="{{ trans('messages.buyer-delivery-to-date') }}">
							</div>
						</div>
						
						<?php /*?>
						<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 delivery-collection-location"  style="display: none">
							<div class="form-group">
								<label class="control-label" for="search_delivery_collection_location">{{ trans("messages.collection-location") }}</label>
								<select name="search_delivery_collection_location" class="form-control" onchange="filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									 <?php 
                                     if(!empty($warehouseDetails)){
                                     	foreach ($warehouseDetails as $warehouseDetail){
                                       		$encodeId = Wild_tiger::encode($warehouseDetail->i_id);
                                        	?>
                                        	<option value="{{ $encodeId }}">{{ (!empty($warehouseDetail->v_warehouse_name) ? $warehouseDetail->v_warehouse_name .(!empty($warehouseDetail->v_warehouse_code) ? ' (' .$warehouseDetail->v_warehouse_code .')' : '' ): '' ) }}</option>
                                        <?php 
                                    	}
                                     }
                                     ?>
								</select>
							</div>
						</div>
						<?php */?>
						<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 delivery-collection-location"  style="display: none">
							<div class="form-group">
								<label class="control-label" for="search_ready_for_collection">{{ trans("messages.ready-for-collection") }}</label>
								<select name="search_ready_for_collection" class="form-control" onchange="filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									 @if(!empty($readyForCollectionInfo))
	                                     	@foreach($readyForCollectionInfo as $key => $readyForCollection)
	                                        	<option value='{{ $key }}'>{{ $readyForCollection}}</option>
	                                       	@endforeach
	                                 	@endif
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_supplier_country">{{ trans("messages.supplier-country") }}</label>
								<select name="search_supplier_country" class="form-control select2" onchange="getSupplierDetails(this);">
								<option value="">{{ trans("messages.select") }}</option>
								<?php 
								if(!empty($countryMasterDetails)){
									foreach ($countryMasterDetails as $countryMasterDetail){
										$encodeSupplierCountryId  = Wild_tiger::encode($countryMasterDetail->i_id);
										?>
                                        <option value="{{ $encodeSupplierCountryId }}">{{ (!empty($countryMasterDetail->v_country_name) ? $countryMasterDetail->v_country_name : '' ) }}</option>
                                        <?php 
                                 	}
                                } 
								?>
								</select>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_supplier_name">{{ trans("messages.supplier-name") }}</label>
								<select name="search_supplier_name[]" class="form-control select2 supplier-name-list" multiple onchange="getSupplierLocationDetails(this);">
								<?php 
								if(!empty($supplierRecordDetails)){
									foreach ($supplierRecordDetails as $supplierRecordDetail){
										$encodeSupplierId  = Wild_tiger::encode($supplierRecordDetail->i_id);
										?>
                                        <option value="{{ $encodeSupplierId }}">{{ (!empty($supplierRecordDetail->v_supplier_name) ? $supplierRecordDetail->v_supplier_name : '' ) }}</option>
                                        <?php 
                                 	}
                                } 
								?>
								</select>
							</div>
						</div>
						<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_supplier_location">{{ trans("messages.supplier-location") }}</label>
								<select name="search_supplier_location" class="form-control select2 supplier-location-list" multiple onchange="filterData();">
									
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_order_from_date">{{ trans("messages.buyer-delivery-from-date") }}</label>
								<input type="text" name="search_order_from_date" class="form-control" placeholder="{{ trans('messages.buyer-delivery-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_order_to_date">{{ trans("messages.buyer-delivery-to-date") }}</label>
								<input type="text" name="search_order_to_date" class="form-control" placeholder="{{ trans('messages.buyer-delivery-to-date') }}">
							</div>
						</div>
						
						<?php /* 
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label for="search_payment_status" class="control-label">{{ trans("messages.payment-status") }}</label>
								<select name="search_payment_status" class="form-control" onchange="showPaymentStatusInfo(this),filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                    if(!empty($paymentStatusInfo)){
                                    	foreach ($paymentStatusInfo as $paymentStatus){
                                        	?>
                                        	<option value="{{$paymentStatus}}">{{ $paymentStatus }}</option>
                                        	<?php 
                                  		}
                               		}
                                    ?>
								</select>
							</div>
						</div>
						
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 payment-status-record-div">
							<div class="form-group">
								<label class="control-label" for="search_payment_from_date">{{ trans("messages.payment-from-date") }}</label>
								<input type="text" name="search_payment_from_date" class="form-control" placeholder="{{ trans('messages.payment-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 payment-status-record-div">
							<div class="form-group">
								<label class="control-label" for="search_payment_to_date">{{ trans("messages.payment-to-date") }}</label>
								<input type="text" name="search_payment_to_date" class="form-control" placeholder="{{ trans('messages.payment-to-date') }}">
							</div>
						</div>
						
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 custome-produre">
							<div class="form-group">
								<label for="search_custom_procedure_export" class="control-label">{{ trans("messages.custom-procedure-export") }}</label>
								<select name="search_custom_procedure_export" class="form-control" onchange="filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                    if(!empty($customProcedureInfo)){
                                    	foreach ($customProcedureInfo as $key => $customProcedure){
                                    		?>
                                    		<option value="{{$key}}">{{ $customProcedure }}</option>
                                   			<?php 
                                    	}
                               		}
                                   ?>
								</select>
							</div>
						</div>

						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 custome-produre">
							<div class="form-group">
								<label for="search_custom_procedure_import" class="control-label">{{ trans("messages.custom-procedure-import") }}</label>
								<select name="search_custom_procedure_import" class="form-control" onchange="filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
                                    if(!empty($customProcedureInfo)){
                                    	foreach ($customProcedureInfo as $key => $customProcedure){
                                    		?>
                                    		<option value="{{$key}}">{{ $customProcedure }}</option>
                                   			<?php 
                                    	}
                               		}
                                   ?>
								</select>
							</div>
						</div>
						
						 
						 <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 custome-produre">
							<div class="form-group">
								<label for="search_payment_terms" class="control-label">{{ trans("messages.payment-terms") }}</label>
								<select name="search_payment_terms" class="form-control" onchange="filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
										if(!empty($paymentTermsDetails)){
											foreach ($paymentTermsDetails as $paymentTermsDetail){
												$encodePaymentTermId = Wild_tiger::encode($paymentTermsDetail->i_id);
												?>
												<option value="{{ $encodePaymentTermId }}">{{ $paymentTermsDetail->v_value }}</option>
												<?php 
											}
										}
									?>
								</select>
							</div>
						</div>
						
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 custome-produre">
							<div class="form-group">
								<label for="search_goods_remark" class="control-label">{{ trans("messages.goods-remark") }}</label>
								<select name="search_goods_remark[]" class="form-control select2" multiple onchange="filterData();">
									<?php 
										if(!empty($goodsRemarksDetails)){
											foreach ($goodsRemarksDetails as $goodsRemarksDetail){
												$encodeGoodsRemarkId = Wild_tiger::encode($goodsRemarksDetail->i_id);
												?>
												<option value="{{ $encodeGoodsRemarkId }}">{{ $goodsRemarksDetail->v_value }}</option>
												<?php 
											}
										}
									?>
								</select>
							</div>
						</div>
						
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 custome-produre">
							<div class="form-group">
								<label for="search_dangerous_goods" class="control-label">{{ trans("messages.dangerous-goods") }}</label>
								<select name="search_dangerous_goods" class="form-control" onchange="filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
										if(!empty($dangerousGoodsDetails)){
											foreach ($dangerousGoodsDetails as $dangerousGoodsDetail){
												$encodeId = (!empty($dangerousGoodsDetail->i_id) ? Wild_tiger::encode($dangerousGoodsDetail->i_id) : 0);
												?>
												<option value="{{ $encodeId }}">{{ (!empty($dangerousGoodsDetail->v_value) ? $dangerousGoodsDetail->v_value : '') }}</option>
												<?php 
											}
										}
									?>
								</select>
							</div>
						</div>
						 */?>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_box_pallet_type">{{ trans("messages.pallet-box") }}</label>
								<select name="search_box_pallet_type" class="form-control" onchange="filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
									if(!empty($palletBoxInfo)){
										foreach ($palletBoxInfo as $key => $value){
											?>
										    <option value="{{$key}}"  >{{ $value }}</option>
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
								<label class="control-label" for="search_pallet_box_dimension">{{ trans("messages.dimension") }}</label>
								<select name="search_pallet_box_dimension" multiple class="form-control select2 pallet-box-dimension-div" onchange="filterData();">
									<?php 
										if(!empty($dimensionRecordDetails)){
											foreach ($dimensionRecordDetails as $dimensionRecordDetail){
												$encodevDimensionId  = Wild_tiger::encode($dimensionRecordDetail->i_id);
												?>
												<option value="{{ $encodevDimensionId }}">{{ (!empty($dimensionRecordDetail->v_dimension_name) ? $dimensionRecordDetail->v_dimension_name : '' ) . ' ('. (!empty($dimensionRecordDetail->v_dimension_size) ? $dimensionRecordDetail->v_dimension_size : '' ). ')' }}</option>
												<?php 
											}
										}
									?>
								</select>
							</div>
						</div>
						
						
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_boxes_dimension">{{ trans("messages.boxes-dimension") }}</label>
								<select name="search_boxes_dimension" multiple class="form-control select2" onchange="filterData();">
									<?php 
									if(!empty($dimensionBoxRecordDetails)){
										foreach ($dimensionBoxRecordDetails as $dimensionBoxRecordDetail){
											$encodevDimensionId  = Wild_tiger::encode($dimensionBoxRecordDetail->i_id);
											?>
											<option value="{{ $encodevDimensionId }}">{{ (!empty($dimensionBoxRecordDetail->v_dimension_name) ? $dimensionBoxRecordDetail->v_dimension_name : '' ) . ' ('. (!empty($dimensionBoxRecordDetail->v_dimension_size) ? $dimensionBoxRecordDetail->v_dimension_size : '' ). ')' }}</option>
											<?php 
										}
									} 
									?>
								</select>
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_pallets_dimension">{{ trans("messages.pallets-dimension") }}</label>
								<select name="search_pallets_dimension" multiple class="form-control select2" onchange="filterData();">
									<?php 
									if(!empty($dimensionPalletRecordDetails)){
										foreach ($dimensionPalletRecordDetails as $dimensionPalletRecordDetail){
											$encodevDimensionId  = Wild_tiger::encode($dimensionPalletRecordDetail->i_id);
											?>
											<option value="{{ $encodevDimensionId }}">{{ (!empty($dimensionPalletRecordDetail->v_dimension_name) ? $dimensionPalletRecordDetail->v_dimension_name : '' ) . ' ('. (!empty($dimensionPalletRecordDetail->v_dimension_size) ? $dimensionPalletRecordDetail->v_dimension_size : '' ). ')' }}</option>
											<?php 
										}
									} 
									?>
								</select>
							</div>
						</div>
						 					
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_pallets_type">{{ trans("messages.pallets-type") }}</label>
								<select name="search_pallets_type" class="form-control" onchange="filterData();">
									<option value="">{{ trans("messages.select") }}</option>
									 <?php 
                                     if(!empty($palletsTypeInfo)){
                                     	foreach ($palletsTypeInfo as $key => $palletsType){
                                       		?>
                                       		<option value="{{$key}}">{{ $palletsType }}</option>
                                        	<?php 
                                    	}
                                     }
                                     ?>
								</select>
							</div>
						</div>
						*/?>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_invoice_from_date">{{ trans("messages.invoice-from-date") }}</label>
								<input type="text" name="search_invoice_from_date" class="form-control" placeholder="{{ trans('messages.invoice-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_invoice_to_date">{{ trans("messages.invoice-to-date") }}</label>
								<input type="text" name="search_invoice_to_date" class="form-control" placeholder="{{ trans('messages.invoice-to-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_actual_payment_from_date">{{ trans("messages.actual-payment-from-date") }}</label>
								<input type="text" name="search_actual_payment_from_date" class="form-control" placeholder="{{ trans('messages.actual-payment-from-date') }}">
							</div>
						</div>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_actual_payment_to_date">{{ trans("messages.actual-payment-to-date") }}</label>
								<input type="text" name="search_actual_payment_to_date" class="form-control" placeholder="{{ trans('messages.actual-payment-to-date') }}">
							</div>
						</div>
						@if((checkPermission(config('permission_constants.VIEW_GOODS_IN_LOGISTIC')) != false))
						<div class="col-xl-4 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_status">{{ trans("messages.logistic") }} {{ trans("messages.status") }}</label>
								<a href="javascript:void(0)" class="float-right remove-all-btn">{{ trans("messages.remove-all")}}</a>
								<select name="search_status"  multiple class="form-control select2" onchange="filterData();">
									@if(!empty($statusMasterDetails))
								    	@foreach ($statusMasterDetails as $statusMasterDetail)
								    		<option value="{{ Wild_tiger::encode($statusMasterDetail->i_id)  }}">{{ (!empty($statusMasterDetail->v_status) ? $statusMasterDetail->v_status : '' ) }}</option>
								    	@endforeach
								    @endif
								</select>
								
							</div>
							
							
						</div>
						 @endif
						<?php /*?>
						<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="search_status">{{ trans("messages.status") }}</label>
								<select class="form-control" name="search_status">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="{{ config('constants.ENABLE_STATUS')}}">{{ trans("messages.enable") }}</option>
									<option value="{{ config('constants.DISABLE_STATUS')}}">{{ trans("messages.disable") }}</option>
								</select>
							</div>
						</div>
						<?php */ ?>
						
						<div class="col-xl-2 col-md-4 d-flex align-items-end gap pt-md-3">
							<button type="button" title="{{ trans('messages.search') }}" class="btn btn-theme text-white" onclick="filterData();">{{ trans("messages.search") }}</button>
							<button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers">{{ trans("messages.reset") }}</button>
						</div>
					</div>
				</div>
			</div>

			<div class="filter-result-wrapper ajax-view">
				@include( config('constants.AJAX_VIEW_FOLDER') . 'good-in-buyer/good-in-buyer-list')
			</div>

		</div>

	</section>
</main>


<div class="modal fade bd-example-modal-lg" id="import-good-in-buyer-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
             	{!! Form::open(array( 'id '=> 'import-good-in-buyer-excel-form' , 'method' => 'post' , 'files' => true , 'url' => 'good-in-buyer/import-excel')) !!}
                <div class="modal-header">
                    <h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">{{ trans('messages.import-good-in-buyer') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body add-lookup-modal-html">
                	<div class="row">
                		<div class="col-md-12">
							<div class="form-group mb-0">
								<label for="good_in_buyer_excel" class="control-label">{{ trans('messages.upload-excel') }}<span class="text-danger">*</span></label>
								<div class="custom-file">
									<input type="file" class="custom-file-input" id="upload_excel" name="good_in_buyer_excel">
									<label class="custom-file-label mb-0" for="upload_excel">{{ trans('messages.choose-file') }}</label>
								</div>
								<label id="upload_excel-error" class="invalid-input" for="upload_excel" style="display:none"></label>
							</div>
						</div>
						<div class="col-lg-12 mb-3">
                            <div class="d-flex align-items-center">
	                            <a href="{{ config('constants.IMPORT_GOODS_IN_BUYER_SAMPLE_FILE') }}" download class="text-theme btn shadow-none p-0 text-decoration-underline" title="{{ trans('messages.download-sample-file') }}">
	                            <span class="text-theme ml-1">{{ trans('messages.download-sample-file') }}</span></a>
                            </div>
						</div>
					</div>
                </div>
				<div class="modal-footer justify-content-center">
					<button type="submit" class="btn bg-theme text-white action-button" title="{{ trans('messages.submit') }}">{{ trans('messages.submit') }}</button>
					<button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
				</div>
            {!! Form::close() !!}
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
	$('#upload_excel').on('change',function(){
		var fileName = $(this).val().split('\\').pop();
		$(this).next('.custom-file-label').html(fileName);
	})
 	$("[name='search_order_from_date'],[name='search_order_to_date']").datetimepicker({
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
	$("[name='search_order_from_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
		var incrementDay = moment((e.date)).startOf('d');
	 	$("[name='search_order_to_date']").data('DateTimePicker').minDate(incrementDay);
		}else{
			$("[name='search_order_to_date']").data('DateTimePicker').minDate(false);
		}
	    $(this).data("DateTimePicker").hide();
	});

    $("[name='search_order_to_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
        var decrementDay = moment((e.date)).endOf('d');
        $("[name='search_order_from_date']").data('DateTimePicker').maxDate(decrementDay);
		}else{
			$("[name='search_order_from_date']").data('DateTimePicker').maxDate(false);
		}
        $(this).data("DateTimePicker").hide();
    });
    
});
<?php /* 
$(document).ready(function() {
 	$("[name='search_payment_from_date'],[name='search_payment_to_date']").datetimepicker({
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
	$("[name='search_payment_from_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
		var incrementDay = moment((e.date)).startOf('d');
	 	$("[name='search_payment_to_date']").data('DateTimePicker').minDate(incrementDay);
		}else{
			$("[name='search_payment_to_date']").data('DateTimePicker').minDate(false);
		}
	    $(this).data("DateTimePicker").hide();
	});

    $("[name='search_payment_to_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
        var decrementDay = moment((e.date)).endOf('d');
        $("[name='search_payment_from_date']").data('DateTimePicker').maxDate(decrementDay);
		}else{
			$("[name='search_payment_from_date']").data('DateTimePicker').maxDate(false);
		}
        $(this).data("DateTimePicker").hide();
    });
		
});
*/?>
$(document).ready(function() {
	$("[name='search_buyer_delivery_from_date'],[name='search_buyer_delivery_to_date'], [name='search_actual_payment_from_date'],[name='search_actual_payment_to_date'], [name='search_invoice_from_date'], [name='search_invoice_to_date'] ").datetimepicker({
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
	$("[name='search_buyer_delivery_from_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
		var incrementDay = moment((e.date)).startOf('d');
	 	$("[name='search_buyer_delivery_to_date']").data('DateTimePicker').minDate(incrementDay);
		}else{
			$("[name='search_buyer_delivery_to_date']").data('DateTimePicker').minDate(false);
		}
	    $(this).data("DateTimePicker").hide();
	});

    $("[name='search_buyer_delivery_to_date']").datetimepicker().on('dp.change', function(e) {
		if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
        var decrementDay = moment((e.date)).endOf('d');
        $("[name='search_buyer_delivery_from_date']").data('DateTimePicker').maxDate(decrementDay);
		}else{
			$("[name='search_buyer_delivery_from_date']").data('DateTimePicker').maxDate(false);
		}
        $(this).data("DateTimePicker").hide();
    });

		$("[name='search_actual_payment_from_date']").datetimepicker().on('dp.change', function(e) {
			if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
			var incrementDay = moment((e.date)).startOf('d');
			$("[name='search_actual_payment_to_date']").data('DateTimePicker').minDate(incrementDay);
			}else{
				$("[name='search_actual_payment_to_date']").data('DateTimePicker').minDate(false);
			}
				$(this).data("DateTimePicker").hide();
		});

		$("[name='search_actual_payment_to_date']").datetimepicker().on('dp.change', function(e) {
			if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
					var decrementDay = moment((e.date)).endOf('d');
					$("[name='search_actual_payment_from_date']").data('DateTimePicker').maxDate(decrementDay);
			}else{
				$("[name='search_actual_payment_from_date']").data('DateTimePicker').maxDate(false);
			}
					$(this).data("DateTimePicker").hide();
		});	


		$("[name='search_invoice_from_date']").datetimepicker().on('dp.change', function(e) {
			if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
			var incrementDay = moment((e.date)).startOf('d');
			$("[name='search_invoice_to_date']").data('DateTimePicker').minDate(incrementDay);
			}else{
				$("[name='search_invoice_to_date']").data('DateTimePicker').minDate(false);
			}
				$(this).data("DateTimePicker").hide();
		});

		$("[name='search_invoice_to_date']").datetimepicker().on('dp.change', function(e) {
			if( $.trim($(this).val()) != "" && $.trim($(this).val()) != ""  ){
					var decrementDay = moment((e.date)).endOf('d');
					$("[name='search_invoice_from_date']").data('DateTimePicker').maxDate(decrementDay);
			}else{
				$("[name='search_invoice_from_date']").data('DateTimePicker').maxDate(false);
			}
					$(this).data("DateTimePicker").hide();
		});	
		
});
</script>
<script>
var good_in_buyer_module_url = '{{config("constants.GOODS_IN_BUYER_MASTER_URL")}}' + '/';
function searchField(){
	var search_by_logistic_partner_name = $.trim($('[name="search_by_logistic_partner_name"]').val());
	var search_buyer_company = $.trim($('[name="search_buyer_company"]').val());
	//var search_user_company = $.trim($('[name="search_user_company"]').val());
	var search_buyer_name = $.trim($('[name="search_buyer_name"]').val());
	var search_user_buyer_name = $.trim($('[name="search_user_buyer_name"]').val());
	var search_supplier_name = $.trim($('[name="search_supplier_name[]"]').val());
	var search_supplier_location = $.trim($('[name="search_supplier_location"]').val());
	var search_order_from_date = $.trim($('[name="search_order_from_date"]').val());
	var search_order_to_date = $.trim($('[name="search_order_to_date"]').val());
	//var search_payment_status = $.trim($('[name="search_payment_status"]').val());
	//var search_payment_from_date = $.trim($('[name="search_payment_from_date"]').val());
	var search_payment_to_date = $.trim($('[name="search_payment_to_date"]').val());
	var search_collection_delivery = $.trim($('[name="search_collection_delivery"]').val());
	//var search_delivery_type = $.trim($('[name="search_delivery_type"]').val());
	//var search_delivery_location = $.trim($('[name="search_delivery_location"]').val());
	var search_buyer_delivery_from_date = $.trim($('[name="search_buyer_delivery_from_date"]').val());
	var search_buyer_delivery_to_date = $.trim($('[name="search_buyer_delivery_to_date"]').val());
	/* var search_custom_procedure_export = $.trim($('[name="search_custom_procedure_export"]').val());
	var search_custom_procedure_import = $.trim($('[name="search_custom_procedure_import"]').val()); */
	//var search_dangerous_goods = $.trim($('[name="search_dangerous_goods"]').val());
	/* var search_boxes_dimension = $.trim($('[name="search_boxes_dimension"]').val());
	var search_pallets_dimension = $.trim($('[name="search_pallets_dimension"]').val()); */
	//var search_pallet_box_dimension = $.trim($('[name="search_pallet_box_dimension"]').val());
	//var search_pallets_type = $.trim($('[name="search_pallets_type"]').val());
	var search_delivery_collection_location = $.trim($('[name="search_delivery_collection_location"]').val());
	var search_status = $.trim($('[name="search_status"]').val());
	var search_ready_for_collection = $.trim($('[name="search_ready_for_collection"]').val());
	var search_supplier_country = $.trim($('[name="search_supplier_country"]').val());
	var search_box_pallet_type = $.trim($('[name="search_box_pallet_type"]').val());
	var search_invoice_from_date = $.trim($('[name="search_invoice_from_date"]').val());
	var search_invoice_to_date = $.trim($('[name="search_invoice_to_date"]').val());
	var search_actual_payment_from_date = $.trim($('[name="search_actual_payment_from_date"]').val());
	var search_actual_payment_to_date = $.trim($('[name="search_actual_payment_to_date"]').val());
	//var search_po_create_user_name  = $.trim($('[name="search_po_create_user_name"]').val()); 
	//var search_payment_terms = $.trim($('[name="search_payment_terms"]').val());
	//var search_goods_remark  = $.trim($('[name="search_goods_remark[]"]').val()); 
	
	var searchData = {
    	'search_by_logistic_partner_name':search_by_logistic_partner_name,
        'search_buyer_company': search_buyer_company,
        //'search_user_company': search_user_company,
        'search_buyer_name': search_buyer_name,
        'search_user_buyer_name': search_user_buyer_name,
        'search_supplier_name': search_supplier_name,
        'search_supplier_location': search_supplier_location,
        'search_order_from_date': search_order_from_date,
        'search_order_to_date': search_order_to_date,
        //'search_payment_status': search_payment_status,
        //'search_payment_from_date': search_payment_from_date,
        'search_payment_to_date': search_payment_to_date,
        'search_collection_delivery': search_collection_delivery,
        //'search_delivery_type': search_delivery_type,
        //'search_delivery_location': search_delivery_location,
        'search_buyer_delivery_from_date': search_buyer_delivery_from_date,
        'search_buyer_delivery_to_date': search_buyer_delivery_to_date,
        /* 'search_custom_procedure_export': search_custom_procedure_export,
        'search_custom_procedure_import': search_custom_procedure_import, */
        //'search_dangerous_goods': search_dangerous_goods,
        /* 'search_boxes_dimension': search_boxes_dimension,
        'search_pallets_dimension': search_pallets_dimension, */
        //'search_pallet_box_dimension': search_pallet_box_dimension,
        //'search_pallets_type': search_pallets_type,
        'search_delivery_collection_location':search_delivery_collection_location,
        'search_status':search_status,
        'search_ready_for_collection':search_ready_for_collection,
        'search_supplier_country':search_supplier_country,
        //'search_po_create_user_name':search_po_create_user_name,
        'search_box_pallet_type':search_box_pallet_type,
        'search_invoice_from_date':search_invoice_from_date,
        'search_invoice_to_date':search_invoice_to_date,
        'search_actual_payment_from_date':search_actual_payment_from_date,
        'search_actual_payment_to_date':search_actual_payment_to_date,
        //'search_payment_terms':search_payment_terms,
        //'search_goods_remark':search_goods_remark,
	}

	const urlParams = new URLSearchParams(window.location.search);
    const urlWarehouse = urlParams.get('warehouse');
    const urlDeliveryDate = urlParams.get('delivery_date');
    if (urlWarehouse) {
        searchData['url_warehouse'] = urlWarehouse;
    }
    if (urlDeliveryDate) {
        searchData['url_delivery_date'] = urlDeliveryDate;
    }
	
    return searchData;
}
function filterData(){
	var searchFieldName = searchField();
	searchAjax(good_in_buyer_module_url + 'filter' , searchFieldName);
}
var paginationUrl = good_in_buyer_module_url + 'filter'

function getSupplierLocationDetails(thisitem){
	var supplier_record_id = $.trim($('[name="search_supplier_name[]"]').val());
	
	$.ajax({
    	type:'post',
    	data:{ "_token": "{{ csrf_token() }}" , 'supplier_record_id' : supplier_record_id },
    	url: good_in_buyer_module_url + 'getSupplierLocationDetails',
    	beforeSend: function() {
    		//block ui
    		showLoader();
    	},
    	success: function(response) {
    		hideLoader();
    		if(response != "" && response != null){
    			$('.supplier-location-list').html(response);
			}else{
				$('.supplier-location-list').html("");
			}
    		filterData();
    	},
    	error: function() {
			hideLoader();
		}
    });
}
function showCollectionDeliveryData(thisitem){
	var search_collection_delivery = $.trim($('[name="search_collection_delivery"]').val());
	if(search_collection_delivery !="" && search_collection_delivery != null){
		if(search_collection_delivery == '{{config("constants.COLLECTION")}}'){
			$('.delivery-collection-row').hide();
			$('.delivery-collection-location').show();
		} else {
			$('.delivery-collection-row').show();
			$('.delivery-collection-location').hide();
		}
	} else {
		$('.delivery-collection-row').hide();
		$('.delivery-collection-location').hide();
	}
} 

function showPaymentStatusInfo(thisitem){
	var search_payment_status = $.trim($('[name="search_payment_status"]').val());
	if(search_payment_status == '{{config("constants.NOT_PAID_PAYMENT_STATUS")}}'){
		$('.payment-status-record-div').hide();
	} else {
		$('.payment-status-record-div').show();
	}
}
 
function updateCancelledStatus(thisitem){
	var buyer_detail_record_id = $.trim($(thisitem).attr('data-record-id'));
	if(buyer_detail_record_id != "" && buyer_detail_record_id != null){

		alertify.confirm("{{ trans('messages.update-cancelled-status')}}","{{ trans('messages.confirm-update-cancelled-status')}}",function() {  
			$.ajax({
		    	type:'post',
		    	data:{"_token": "{{ csrf_token() }}",'buyer_detail_record_id':buyer_detail_record_id},
		    	url: good_in_buyer_module_url + 'updateDetailCancelledStatus',
		    	dataType : 'json',
		    	beforeSend: function() {
		    		//block ui
		    		showLoader();
		    	},	
		    	success: function(response) {
		    		hideLoader();
					if( response.status_code == 1 ){
		    			//$(thisitem).parents('tr').find('.delete-record-button').remove();
						var master_record_id = $.trim($(thisitem).parents('tr').attr('data-record-id')); 	
						$(thisitem).parents('tr').find('.delivery-type-status').html("{{ config('constants.CANCELLED_DELIVERY_TYPE') }}");
						$(thisitem).remove();
						alertifyMessage('success',response.message);
						$(".ajax-view tr").each(function(){
							var row_master_id = $.trim($(this).attr('data-record-id'));
							if( master_record_id == row_master_id ){
								$(this).find('.delivery-type-status').html("{{ config('constants.CANCELLED_DELIVERY_TYPE') }}");
							}
						})
					} else if( response.status_code == 101 ) {
			    		alertifyMessage('error',response.message);
				    }
		    	},
		    	error: function() {
					hideLoader();
				}
		    });
		},function() {});	
	}
	
}

// Apply URL query params to filters on page load (so dashboard links work)
$(function(){
	try {
		const params = new URLSearchParams(window.location.search);
		let hasParams = false;
		
		const paramMap = {
			'search_collection_delivery': 'search_collection_delivery',
			'search_status': 'search_status',
			'search_from_country': 'search_supplier_country',
			'search_to_warehouse': 'search_delivery_collection_location',
			'search_buyer_delivery_from_date': 'search_buyer_delivery_from_date',
			'search_buyer_delivery_to_date': 'search_buyer_delivery_to_date',
			'search_logistic_delivery_from_date': 'search_order_from_date',
			'search_logistic_delivery_to_date': 'search_order_to_date'
		};

		for (const [urlParam, fieldName] of Object.entries(paramMap)) {
			const val = params.get(urlParam);
			if (val && val !== '') {
				let $field = $('[name="' + fieldName + '"]');
				if ($field.length) {
					$field.val(val);
					if ($field.hasClass('select2')) {
						$field.trigger('change');
					}
					hasParams = true;
				}
			}
		}

		if (hasParams) {
			if (typeof showCollectionDeliveryData === 'function') {
				showCollectionDeliveryData();
			}
			// give any select2 change a moment then trigger filter
			setTimeout(function(){ filterData(); }, 200);
		}
	} catch (e) {
		console.error('applyQueryParamsToFilters', e);
	}
});

function exportData(){
	var searchData = searchField();
	var export_info = {};
	export_info.url = good_in_buyer_module_url + 'filter';
	export_info.searchData = searchData;
	dataExportIntoExcel(export_info);
}

$("#import-good-in-buyer-excel-form").validate({
    errorClass: "invalid-input",
    rules: {
    	good_in_buyer_excel: { required : true,  extension : 'xlsx' },
    },
    messages: {
    	good_in_buyer_excel: { required : '{{ trans("messages.required-upload-excel-file") }}' , extension : "{{ trans('messages.only-allowed-file-types' , [ 'fileTypes' => 'Xlsx' ] )  }}" },
    },
    submitHandler: function(form) {
		alertify.confirm( '{{ trans("messages.confirm-import") }}',  '<?php echo sprintf(  trans('messages.confirm-import-msg') , trans('messages.good-in-buyer') )?>', function() {
			showLoader();
        	form.submit();
		},function() {} );
    }
});

function openImportExcelModal(){
	openBootstrapModal('import-good-in-buyer-modal');
}

 function getSupplierDetails(thisitem){
	var supplier_country_id = $.trim($('[name="search_supplier_country"]').val());
	if(supplier_country_id !="" && supplier_country_id != null){
		$.ajax({
	    	type:'post',
	    	data:{"_token": "{{ csrf_token() }}",'supplier_country_id':supplier_country_id},
	    	url: good_in_buyer_module_url + 'getSupplierDetails',
	    	beforeSend: function() {
	    		// block ui
	    		showLoader();
	    	},	
	    	success: function(response) {
	    		hideLoader();
	    		if(response !="" && response != null){
	    			$('.supplier-name-list').html(response);
	    			
				} else {
					$('.supplier-name-list').html("");
				}
	    	},
	    	error: function() {
				hideLoader();
			}
	    });
	}
} 
</script>
<script type="text/javascript" src="{{ asset ('js/twt_fixed_pagination.js') . '?ver=' .config('constants.CSS_JS_VERSION') }}"></script>
@endsection