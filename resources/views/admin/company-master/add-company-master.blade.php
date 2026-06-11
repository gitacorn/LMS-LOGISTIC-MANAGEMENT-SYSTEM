					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="company_name" class="control-label">{{ trans('messages.company-name') }}<span class="text-danger">*</span></label>
								<input type="text" name="company_name" class="form-control" placeholder="{{ trans('messages.company-name') }}" value="{{ old('company_name' , ( (isset($recordInfo) && (!empty($recordInfo->v_company_name)) ? $recordInfo->v_company_name : ''  ) ) ) }}"> 
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label for="company_code" class="control-label">{{ trans('messages.company-code') }}<span class="text-danger">*</span></label>
								<input type="text" name="company_code" class="form-control" placeholder="{{ trans('messages.company-code') }}" value="{{ old('company_code' , ( (isset($recordInfo) && (!empty($recordInfo->v_company_code)) ? $recordInfo->v_company_code : ''  ) ) ) }}">
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label for="company_short_code" class="control-label">{{ trans('messages.company-short-code') }}<span class="text-danger">*</span></label>
								<input type="text" name="company_short_code" class="form-control" placeholder="{{ trans('messages.company-short-code') }}" value="{{ old('company_short_code' , ( (isset($recordInfo) && (!empty($recordInfo->v_company_short_code)) ? $recordInfo->v_company_short_code : ''  ) ) ) }}">
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								
								<label for="select_country_name" class="control-label">{{ trans('messages.company-country') }}<span class="text-danger">*</span></label>
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
						<div class="col-md-12">
							<div class="form-group">
								<label for="email" class="control-label">{{ trans('messages.email') }}</label>
								<input type="text" name="email" class="form-control" placeholder="{{ trans('messages.email') }}" value="{{ old('email' , ( (isset($recordInfo) && (!empty($recordInfo->v_email)) ? str_replace(',', ', ', $recordInfo->v_email) : ''  ) ) ) }}">
								<div class="notes-class pt-2">
									<p class="track-notes-title mb-0"><b style="color: red;">Note :</b> <span>{{ trans('messages.you-can-add-multiple-emails-using-comma-separated') }}</span></p>
								</div>
							</div>
						</div>
					</div>