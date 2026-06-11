<div class="row">
	<div class="col-md-12">
		<div class="form-group">
			<label for="currency_name" class="control-label">{{ trans('messages.currency-name') }}<span class="text-danger">*</span></label>
			<input type="text" name="currency_name" class="form-control" placeholder="{{ trans('messages.currency-name') }}" value="{{ old('currency_name' , ( (isset($recordInfo) && (!empty($recordInfo->v_currency_name)) ? $recordInfo->v_currency_name : ''  ) ) ) }}"> 
		</div>
	</div>
	<div class="col-md-12">
		<div class="form-group">
			<label for="currency_code" class="control-label">{{ trans('messages.currency-symbol') }}<span class="text-danger">*</span></label>
			<input type="text" name="currency_code" class="form-control" placeholder="{{ trans('messages.currency-symbol') }}" value="{{ old('currency_code' , ( (isset($recordInfo) && (!empty($recordInfo->v_currency_code)) ? $recordInfo->v_currency_code : ''  ) ) ) }}">
		</div>
	</div>
	<div class="col-md-12">
		<div class="form-group">
			<label for="gbp_conversation_rate" class="control-label">{{ trans('messages.gbp-conversation-rate') }}<span class="text-danger">*</span></label>
			<input type="text" name="gbp_conversation_rate" class="form-control" placeholder="{{ trans('messages.gbp-conversation-rate') }}" value="{{ old('gbp_conversation_rate' , ( (isset($recordInfo) && !empty($recordInfo) && (isset($recordInfo->d_gbp_conversation_rate) && !empty($recordInfo->d_gbp_conversation_rate) && $recordInfo->d_gbp_conversation_rate > 0) ? $recordInfo->d_gbp_conversation_rate : ''  ) ) ) }}" onkeyup="onlyDecimalWithZero(this)">
		</div>
	</div>
</div>