					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="dimension_type" class="control-label">{{ trans('messages.dimension-type') }}<span class="text-danger">*</span></label>
								<div class="radio-boxes form-row p-1 bg-white">
									<div class="radio-box col-lg-4 col-6 mb-2">
										<div class="form-check">
											<input class="form-check-input" type="radio" name="dimension_type" id="box" value="{{ config('constants.BOX')}}" <?php echo (!isset($recordInfo) ? 'checked' : '' ) ?> <?php echo ( (  isset($recordInfo) && (!empty($recordInfo->e_dimension)) && ( $recordInfo->e_dimension ==  config('constants.BOX') ) ) ? 'checked' : '' ) ?>>
											<label class="form-check-label custom-type-label btn stock-btn" for="box">{{ trans('messages.box') }}</label>
										</div>
									</div>
									<div class="radio-box col-lg-4 col-6 mb-2">
										<div class="form-check">
											<input class="form-check-input" type="radio" name="dimension_type" id="pallet" value="{{ config('constants.PALLET')}}" <?php echo ( (  isset($recordInfo) && (!empty($recordInfo->e_dimension)) && ( $recordInfo->e_dimension ==  config('constants.PALLET') ) ) ? 'checked' : '' ) ?>>
											<label class="form-check-label custom-type-label btn stock-btn" for="pallet">{{ trans('messages.pallet') }}</label>
										</div>
									</div>
									<label id="dimension_type-error" class="invalid-input" for="dimension_type"></label>
								</div>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label for="dimension_name" class="control-label">{{ trans('messages.dimension-name') }}<span class="text-danger">*</span></label>
								<input type="text" name="dimension_name" class="form-control" placeholder="{{ trans('messages.dimension-name') }}"value="{{ old('dimension_name' , ( (isset($recordInfo) && (!empty($recordInfo->v_dimension_name)) ? $recordInfo->v_dimension_name : ''  ) ) ) }}"> 
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label for="dimension_size" class="control-label">{{ trans('messages.dimension-size') }}<span class="text-danger">*</span></label>
								<input type="text" name="dimension_size" class="form-control" placeholder="{{ trans('messages.dimension-size') }}" value="{{ old('dimension_size' , ( (isset($recordInfo) && (!empty($recordInfo->v_dimension_size)) ? $recordInfo->v_dimension_size : ''  ) ) ) }}">
							</div>
						</div>
					</div>