<?php
$encodeRecordId = Wild_tiger::encode($recordDetail->i_id); ?>
<td class="sr-col">{{$rowIndex}}</td>
<td class="text-left">{{(!empty($recordDetail->v_company_name) ? $recordDetail->v_company_name :'' )}}</td>
<td class="text-left">{{(!empty($recordDetail->v_company_code) ? $recordDetail->v_company_code :'' )}}</td>
<td class="text-left">{{(!empty($recordDetail->v_company_short_code) ? $recordDetail->v_company_short_code :'' )}}</td>
<td class="text-left">{{(!empty($recordDetail->v_country_name) ? $recordDetail->v_country_name :'' )}}</td>
<td class="text-left">{!! (!empty($recordDetail->v_email) ? str_replace(',', '<br>', $recordDetail->v_email) : '') !!}</td>
<td class="actions-col">
<?php
$checked ='';
if($recordDetail->t_is_active == 1){
	$checked = 'checked="checked"';
}
?>
<div class="custom-control custom-switch status-class">
	<input type="checkbox" class="custom-control-input" <?php echo $checked ?> <?php echo ( checkPermission(config('permission_constants.EDIT_COMPANY')) != false ? '' : 'disabled' ) ?> data-record-id="{{ $encodeRecordId }} "  id="disable_{{ $rowIndex }}"  onclick="updateRecordStatus(this,'company-master')">
	<label class="custom-control-label record-status" for="disable_{{ $rowIndex }}">{{ (!empty($recordDetail->t_is_active == 1) ? trans("messages.enable") : trans("messages.disable") )}}</label>
</div>
</td>
<?php 
if( (checkPermission(config('permission_constants.EDIT_COMPANY')) != false) || (checkPermission(config('permission_constants.DELETE_COMPANY')) != false) ){?>
	<td class="actions-col">
		
			<?php if(checkPermission(config('permission_constants.EDIT_COMPANY')) != false){?>
				<button type="button" title='{{ trans("messages.edit-record") }}' class="btn btn-sm btn-info mb-1" onclick="editCompanyModel(this)" data-record-id="<?php echo $encodeRecordId?>"><i class="fas fa-fw fa-pencil-alt"></i></button>
			<?php } ?>
			<?php if(checkPermission(config('permission_constants.DELETE_COMPANY')) != false){?>
				<button type="button" title="{{trans('messages.delete-record')}}" data-record-id="{{ $encodeRecordId }}" data-module-name="company-master" onclick="deleteRecord(this);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
			<?php } ?>
		
		
	</td>
<?php 
}?>