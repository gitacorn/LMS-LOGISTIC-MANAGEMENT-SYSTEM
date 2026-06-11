					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="country_name" class="control-label">{{ trans('messages.country-name') }}<span class="text-danger">*</span></label>
								<input type="text" name="country_name" class="form-control" placeholder="{{ trans('messages.country-name') }}" value="{{ old('country_name' , ( (isset($recordInfo) && (!empty($recordInfo->v_country_name)) ? $recordInfo->v_country_name : ''  ) ) ) }}"> 
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label for="country_code" class="control-label">{{ trans('messages.country-code') }}<span class="text-danger">*</span></label>
								<input type="text" name="country_code" class="form-control" placeholder="{{ trans('messages.country-code') }}" value="{{ old('country_code' , ( (isset($recordInfo) && (!empty($recordInfo->v_country_code)) ? $recordInfo->v_country_code : ''  ) ) ) }}">
							</div>
						</div>
					</div>