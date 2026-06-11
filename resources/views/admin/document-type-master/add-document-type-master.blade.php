					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="document_type" class="control-label">{{ trans('messages.document-type') }}<span class="text-danger">*</span></label>
								<div class="radio-boxes form-row p-1 bg-white">
									<div class="radio-box col-lg-4 col-6 mb-2">
										<div class="form-check">
											<input class="form-check-input" type="radio" name="document_type" id="logistic" value="{{ config('constants.LOGISTIC')}}"<?php echo (!isset($recordInfo) ? 'checked' : '' ) ?> <?php echo ( (  isset($recordInfo) && (!empty($recordInfo->e_document_type)) && ( $recordInfo->e_document_type ==  config('constants.LOGISTIC') ) ) ? 'checked' : '' ) ?>>
											<label class="form-check-label custom-type-label btn stock-btn" for="logistic">{{ trans('messages.logistic') }}</label>
										</div>
									</div>
									<div class="radio-box col-lg-4 col-6 mb-2">
										<div class="form-check">
											<input class="form-check-input" type="radio" name="document_type" id="buyer" value="{{ config('constants.BUYER')}}" <?php echo ( (  isset($recordInfo) && (!empty($recordInfo->e_document_type)) && ( $recordInfo->e_document_type ==  config('constants.BUYER') ) ) ? 'checked' : '' ) ?>>
											<label class="form-check-label custom-type-label btn stock-btn" for="buyer">{{ trans('messages.buyer') }}</label>
										</div>
									</div>
									<label id="document_type-error" class="invalid-input" for="document_type"></label>
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label for="document_name" class="control-label">{{ trans('messages.document-name') }}<span class="text-danger">*</span></label>
								<input type="text" name="document_name" class="form-control" placeholder="{{ trans('messages.document-name') }}" value="{{ old('document_name' , ( (isset($recordInfo) && (!empty($recordInfo->v_document_type_name)) ? $recordInfo->v_document_type_name : ''  ) ) ) }}">
							</div>
						</div>
					</div>