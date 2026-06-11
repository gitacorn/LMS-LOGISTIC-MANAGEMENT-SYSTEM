					
					<div class="row dependant-field-selection">
					<?php if(empty($statusInfo)){?>
						<div class="col-md-12">
							<div class="form-group">
								<label for="fba_no" class="control-label">{{ trans('messages.fba-po-invoice') }}<span class="text-danger">*</span></label>
								<input type="text" name="fba_no" class="form-control" placeholder="{{ trans('messages.fba-po-invoice') }}" value="{{ old('fba_no' , ( (isset($fbaSheetRecordInfo) && (!empty($fbaSheetRecordInfo->v_fba_po_no)) ? $fbaSheetRecordInfo->v_fba_po_no : ''  ) ) ) }}">
							</div>
						</div>
						<?php }?>
						<div class="col-md-6">
							<div class="form-group">
								<label for="destination" class="control-label">{{ trans('messages.destination') }}</label>
								<select name="destination" class="form-control" onchange="locationMasterInfo(this)">
									<?php 
									if(!empty($designationDetails)){
										foreach ($designationDetails as $key => $designationDetail){
											$selected = '';
                                       			if( isset($fbaSheetRecordInfo->e_destination) && ( $fbaSheetRecordInfo->e_destination == $key) ){
                                       				$selected = "selected='selected'";
                                       			}
											?>
											<option value="{{ $key }}" {{ $selected }}><?php echo $designationDetail ?></option>
											<?php 
										}
									}
									?>
								</select>
							</div>
						</div>
						<?php //if(empty($statusInfo)){?>
						<div class="col-md-6">
							<div class="form-group">
								<label for="ref_id" class="control-label">{{ trans('messages.ref-id') }}<span class="text-danger">*</span></label>
								<input type="text" name="ref_id" class="form-control" placeholder="{{ trans('messages.ref-id') }}" value="{{ old('ref_id' , ( (isset($fbaSheetRecordInfo) && (!empty($fbaSheetRecordInfo->v_ref_id)) ? $fbaSheetRecordInfo->v_ref_id : ''  ) ) ) }}">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="company" class="control-label">{{ trans('messages.company') }}<span class="text-danger">*</span></label>
								<input type="text" name="company" class="form-control" placeholder="{{ trans('messages.company') }}" value="{{ old('company' , ( (isset($fbaSheetRecordInfo) && (!empty($fbaSheetRecordInfo->v_company_code)) ? $fbaSheetRecordInfo->v_company_code : ''  ) ) ) }}">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="products" class="control-label">{{ trans('messages.products') }}<span class="text-danger">*</span></label>
								<input type="text" name="products" class="form-control" placeholder="{{ trans('messages.products') }}" value="{{ old('products' , ( (isset($fbaSheetRecordInfo) && (!empty($fbaSheetRecordInfo->v_product)) ? $fbaSheetRecordInfo->v_product : ''  ) ) ) }}">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="fba_value" class="control-label">{{ trans('messages.fba-value') }}</label>
								<input type="text" name="fba_value" class="form-control" placeholder="{{ trans('messages.fba-value') }}" value="{{ old('fba_value' , ( (isset($fbaSheetRecordInfo) && (!empty($fbaSheetRecordInfo->v_fba_value)) ? $fbaSheetRecordInfo->v_fba_value : ''  ) ) ) }}">
							</div>
						</div>
						
						<?php /*?>
						<div class="col-md-6">
							<div class="form-group">
								<label for="location" class="control-label">{{ trans('messages.location') }}<span class="text-danger">*</span></label>
								<input type="text" name="location" class="form-control" placeholder="{{ trans('messages.location') }}" value="{{ old('location' , ( (isset($fbaSheetRecordInfo) && (!empty($fbaSheetRecordInfo->v_location_code)) ? $fbaSheetRecordInfo->v_location_code : ''  ) ) ) }}">
							</div>
						</div>
						<?php */?>
						<div class="col-md-6">
							<div class="form-group">
								<label for="location" class="control-label">{{ trans('messages.location') }}</label>
								<select name="location" class="form-control location-master-info-list select2" data-value="{{ ( (isset($fbaSheetRecordInfo) && (!empty($fbaSheetRecordInfo->v_location_code)) ? $fbaSheetRecordInfo->v_location_code : ''  ) ) }}">
									<?php /*
									if(!empty($designationDetails)){
										foreach ($designationDetails as $key => $designationDetail){
											$selected = '';
                                       			if( isset($fbaSheetRecordInfo->e_destination) && ( $fbaSheetRecordInfo->e_destination == $key) ){
                                       				$selected = "selected='selected'";
                                       			}
											?>
											<option value="{{ $key }}" {{ $selected }}><?php echo $designationDetail ?></option>
											<?php 
										}
									} */
									?>
								</select>
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group">
								<label for="sku" class="control-label">{{ trans('messages.sku') }}</label>
								<input type="text" name="sku" class="form-control" placeholder="{{ trans('messages.sku') }}" value="{{ old('sku' , ( (isset($fbaSheetRecordInfo) && (!empty($fbaSheetRecordInfo->v_sku)) ? $fbaSheetRecordInfo->v_sku : ''  ) ) ) }}">
							</div>
						</div>
						<?php //} ?>
						<div class="col-md-6">
							<div class="form-group">
								<label for="units" class="control-label">{{ trans('messages.units') }}</label>
								<input type="text" name="units" class="form-control" placeholder="{{ trans('messages.units') }}" value="{{ old('units' , ( (isset($fbaSheetRecordInfo) && (!empty($fbaSheetRecordInfo->v_units)) ? $fbaSheetRecordInfo->v_units : ''  ) ) ) }}">
							</div>
						</div>
						<?php //if(empty($statusInfo)){?>
						<div class="col-md-6">
							<div class="form-group">
								<label for="boxes_units" class="control-label">{{ trans('messages.boxes-units') }}</label>
								<input type="text" name="boxes_units" class="form-control" placeholder="{{ trans('messages.boxes-units') }}" value="{{ old('boxes_units' , ( (isset($fbaSheetRecordInfo) && (!empty($fbaSheetRecordInfo->i_boxes_units)) ? $fbaSheetRecordInfo->i_boxes_units : ''  ) ) ) }}">
							</div>
						</div>
						
						<div class="col-md-12">
							<div class="form-group">
								<label for="amazon_address" class="control-label">{{ trans('messages.amazon-address') }}</label>
								<textarea class="form-control" name="amazon_address" rows="3" placeholder="{{ trans('messages.amazon-address') }}"><?php echo (isset($fbaSheetRecordInfo->v_amazon_address) ? $fbaSheetRecordInfo->v_amazon_address : '' ); ?></textarea>
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group">
								<label for="boxes" class="control-label">{{ trans('messages.boxes') }}</label>
								<input type="text" name="boxes" class="form-control" placeholder="{{ trans('messages.boxes') }}" value="{{ old('boxes' , ( (isset($fbaSheetRecordInfo) && (!empty($fbaSheetRecordInfo->v_boxes)) ? $fbaSheetRecordInfo->v_boxes : ''  ) ) ) }}">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="pallet" class="control-label">{{ trans('messages.pallet') }}</label>
								<input type="text" name="pallet" class="form-control" placeholder="{{ trans('messages.pallet') }}" value="{{ old('pallet' , ( (isset($fbaSheetRecordInfo) && (!empty($fbaSheetRecordInfo->v_pallet)) ? $fbaSheetRecordInfo->v_pallet : ''  ) ) ) }}">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="total_no_of_pallets" class="control-label">{{ trans('messages.Total-no-of-pallets') }}</label>
								<input type="text" name="total_no_of_pallets" class="form-control" placeholder="{{ trans('messages.Total-no-of-pallets') }}" value="{{ old('total_no_of_pallets' , ( (isset($fbaSheetRecordInfo) && (!empty($fbaSheetRecordInfo->i_total_no_of_pallets)) ? $fbaSheetRecordInfo->i_total_no_of_pallets : ''  ) ) ) }}">
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group">
								<label for="pallet_dimension" class="control-label">{{ trans('messages.pallet-dimension') }}</label>
								<input type="text" name="pallet_dimension" class="form-control" placeholder="{{ trans('messages.pallet-dimension') }}" value="{{ old('pallet_dimension' , ( (isset($fbaSheetRecordInfo) && (!empty($fbaSheetRecordInfo->v_pallet_dimension)) ? $fbaSheetRecordInfo->v_pallet_dimension : ''  ) ) ) }}">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="pallet_weight" class="control-label">{{ trans('messages.pallet-weight') }}</label>
								<input type="text" name="pallet_weight" class="form-control" placeholder="{{ trans('messages.pallet-weight') }}" value="{{ old('pallet_weight' , ( (isset($fbaSheetRecordInfo) && (!empty($fbaSheetRecordInfo->v_pallet_weight)) ? $fbaSheetRecordInfo->v_pallet_weight : ''  ) ) ) }}">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="pallet_number" class="control-label">{{ trans('messages.pallet-number') }}</label>
								<input type="text" name="pallet_number" class="form-control" placeholder="{{ trans('messages.pallet-number') }}" value="{{ old('pallet_number' , ( (isset($fbaSheetRecordInfo) && (!empty($fbaSheetRecordInfo->i_pallet_no)) ? $fbaSheetRecordInfo->i_pallet_no : ''  ) ) ) }}">
							</div>
						</div>
						<?php //}?>
					</div>
	