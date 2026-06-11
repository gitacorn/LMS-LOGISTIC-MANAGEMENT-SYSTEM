<?php
$encodeRecordId = Wild_tiger::encode($recordDetail->i_id); ?>
<td class="sr-col">{{$rowIndex}}</td>
<td class="text-left">{{(!empty($recordDetail->v_status) ? $recordDetail->v_status :'' )}}</td>
<td class="text-left">{{(!empty($recordDetail->i_sequence) ? $recordDetail->i_sequence :'' )}}</td>
<td class="actions-col">
<?php
$checked ='';
if($recordDetail->t_is_active == 1){
	$checked = 'checked="checked"';
}
?>
<div class="custom-control custom-switch status-class">
	<input type="checkbox" class="custom-control-input" <?php echo $checked ?> <?php echo ( in_array( $recordDetail->i_id , [ config('constants.DELIVERED_STATUS_ID') , config('constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID'),config('constants.STATIC_STATUS_CANCELLED_ID') ] ) ? 'disabled' : '' ) ?> <?php echo ( checkPermission(config('permission_constants.EDIT_STATUS')) != false ? '' : 'disabled' ) ?> data-record-id="{{ $encodeRecordId }} "  id="disable_{{ $rowIndex }}"  onclick="updateRecordStatus(this,'status-master')">
	<label class="custom-control-label record-status" for="disable_{{ $rowIndex }}">{{ (!empty($recordDetail->t_is_active == 1) ? trans("messages.enable") : trans("messages.disable") )}}</label>
</div>
</td>
<?php if( (checkPermission(config('permission_constants.EDIT_STATUS')) != false) || (checkPermission(config('permission_constants.DELETE_STATUS')) != false) ){?>
<td class="actions-col">
<?php if(!in_array( $recordDetail->i_id , [ config('constants.DELIVERED_STATUS_ID') , config('constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID') ,config('constants.STATIC_STATUS_CANCELLED_ID')] )) { ?>
<?php if(checkPermission(config('permission_constants.EDIT_STATUS')) != false){?>
	<button type="button" title='{{ trans("messages.edit-record") }}' class="btn btn-sm btn-info mb-1" onclick="editStatusModel(this)" data-record-id="<?php echo $encodeRecordId?>"><i class="fas fa-fw fa-pencil-alt"></i></button>
	<?php 
	} 
	if(checkPermission(config('permission_constants.DELETE_STATUS')) != false){?>
		<button type="button" title="{{trans('messages.delete-record')}}" data-record-id="{{ $encodeRecordId }}" data-module-name="status-master"   onclick="deleteRecord(this);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
	<?php 
	}?>
	<?php } ?>
</td>
<?php 
}?>