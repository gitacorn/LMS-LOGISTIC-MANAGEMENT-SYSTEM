<?php
$encodeRecordId = Wild_tiger::encode($recordDetail->i_id); ?>
<td class="sr-col">{{$rowIndex}}</td>
<td class="text-left">{{(!empty($recordDetail->v_currency_name) ? $recordDetail->v_currency_name :'' )}}</td>
<td class="text-left">{{(!empty($recordDetail->v_currency_code) ? $recordDetail->v_currency_code :'' )}}</td>
<td class="text-left">{{(!empty($recordDetail->d_gbp_conversation_rate) ? $recordDetail->d_gbp_conversation_rate :'' )}}</td>
<td class="actions-col">
<?php
$checked ='';
if($recordDetail->t_is_active == 1){
	$checked = 'checked="checked"';
}
?>
<div class="custom-control custom-switch status-class">
	<input type="checkbox" class="custom-control-input" <?php echo $checked ?> <?php echo ( checkPermission(config('permission_constants.EDIT_CURRENCY')) != false ? '' : 'disabled' ) ?>  data-record-id="{{ $encodeRecordId }} "  id="disable_{{ $rowIndex }}"  onclick="updateRecordStatus(this,'currency-master')">
	<label class="custom-control-label record-status" for="disable_{{ $rowIndex }}">{{ (!empty($recordDetail->t_is_active == 1) ? trans("messages.enable") : trans("messages.disable") )}}</label>
</div>
</td>
<?php if( (checkPermission(config('permission_constants.EDIT_CURRENCY')) != false) || (checkPermission(config('permission_constants.DELETE_CURRENCY')) != false) ){?>
<td class="actions-col">
<?php if(checkPermission(config('permission_constants.EDIT_CURRENCY')) != false){?>
		<button type="button" title='{{ trans("messages.edit-record") }}' class="btn btn-sm btn-info mb-1" onclick="editCurrencyModel(this)" data-record-id="<?php echo $encodeRecordId?>"><i class="fas fa-fw fa-pencil-alt"></i></button>
	<?php }
	if(checkPermission(config('permission_constants.DELETE_CURRENCY')) != false){?>
		<button type="button" title="{{trans('messages.delete-record')}}" data-record-id="{{ $encodeRecordId }}" data-module-name="currency-master"   onclick="deleteRecord(this);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
	<?php 
	}?>
</td>
<?php 
}?>