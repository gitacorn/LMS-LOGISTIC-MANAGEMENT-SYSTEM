					<?php  
					if($recordType == config("constants.WAREHOUSE")){
						$warehouseName = trans('messages.warehouse-name');
						$warehouseCode = trans('messages.warehouse-code');
						$warehouseAddress = trans('messages.warehouse-address');
						$warehouseCountry = trans('messages.warehouse-country');
					} else if($recordType == config("constants.PORT")){
						$warehouseName = trans('messages.port-name');
						$warehouseCode = trans('messages.port-code');
						$warehouseAddress = trans('messages.port-address');
						$warehouseCountry = trans('messages.port-country');
					} else {
						$warehouseName = trans('messages.location-name');
						$warehouseCode = trans('messages.location-code');
						$warehouseAddress = trans('messages.location-address');
						$warehouseCountry = trans('messages.location-country');
					}?>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="warehouse_name" class="control-label">{{ $warehouseName }}<span class="text-danger">*</span></label>
								<input type="text" name="warehouse_name" class="form-control" placeholder="{{ $warehouseName }}" value="{{ old('warehouse_name' , ( (isset($recordInfo) && (!empty($recordInfo->v_warehouse_name)) ? $recordInfo->v_warehouse_name : ''  ) ) ) }}"> 
							</div>
						</div>
						<?php if( $recordType != config("constants.PORT") ) { ?>
						<div class="col-md-12">
							<div class="form-group">
								<label for="warehouse_code" class="control-label">{{ $warehouseCode }}<span class="text-danger">*</span></label>
								<input type="text" name="warehouse_code" class="form-control" placeholder="{{ $warehouseCode }}" value="{{ old('warehouse_code' , ( (isset($recordInfo) && (!empty($recordInfo->v_warehouse_code)) ? $recordInfo->v_warehouse_code : ''  ) ) ) }}">
							</div>
						</div>
						<?php } ?>
						<div class="col-md-12">
							<div class="form-group">
								<label for="warehouse_short_code" class="control-label">{{ $warehouseAddress }}<span class="text-danger">*</span></label>
								<textarea name="warehouse_short_code" class="form-control" placeholder="{{ $warehouseAddress }}">{{old('warehouse_short_code',( (isset($recordInfo) && (!empty($recordInfo->v_warehouse_short_code)) ? $recordInfo->v_warehouse_short_code :  ''  ) )) }}</textarea>
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label for="select_country_name" class="control-label">{{ $warehouseCountry }}<span class="text-danger">*</span></label>
								<select class="form-control" name="select_country_name">
									<option value="">{{ trans("messages.select") }}</option>
									<?php 
									if(!empty($countryRecordDetails)){
										foreach ($countryRecordDetails as $countryRecordDetail){
											$selected="";
                           					if(isset($recordInfo) && ($recordInfo->i_country_id == $countryRecordDetail->i_id)){
                            					$selected = "selected='selected'";
                           					}
                            				?>
											<option value="<?php echo Wild_tiger::encode($countryRecordDetail->i_id)?>"<?php echo $selected?>><?php echo $countryRecordDetail->v_country_name?></option>
										                         
											<?php 
										}
									}
									?>
								</select>
							</div>
						</div>
						<?php if( $recordType == config("constants.WAREHOUSE") ) { ?>
						<div class="col-md-12">
							<div class="form-group">
								<label for="warehouse_mail" class="control-label">{{ trans('messages.email') }}</label>
								<input type="text" name="warehouse_mail" class="form-control" placeholder="{{ trans('messages.email') }}" value="{{ old('warehouse_mail' , ( (isset($recordInfo) && (!empty($recordInfo->v_warehouse_email)) ? str_replace(',', ', ', $recordInfo->v_warehouse_email) : ''  ) ) ) }}">
								<div class="notes-class pt-2">
									<p class="track-notes-title mb-0"><b style="color: red;">Note :</b> <span>{{ trans('messages.you-can-add-multiple-emails-using-comma-separated') }}</span></p>
								</div>
							</div>
						</div>
						<?php } ?>
					</div>