					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="status" class="control-label">{{ trans('messages.status') }}<span class="text-danger">*</span></label>
								<input type="text" name="status" class="form-control" placeholder="{{ trans('messages.status') }}" value="{{ old('status' , ( (isset($recordInfo) && (!empty($recordInfo->v_status)) ? $recordInfo->v_status : ''  ) ) ) }}"> 
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label for="sequence" class="control-label">{{ trans('messages.sequence') }}<span class="text-danger">*</span></label>
								<input type="text" name="sequence" onkeyup="onlyNumber(this)" class="form-control" placeholder="{{ trans('messages.sequence') }}" value="{{ old('sequence' , ( (isset($recordInfo) && (!empty($recordInfo->i_sequence)) ? $recordInfo->i_sequence : ''  ) ) ) }}">
							</div>
						</div>
					</div>