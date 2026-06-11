<?php
$encodeRecordId = Wild_tiger::encode($recordDetail->i_id); ?>
<td class="sr-col">{{$rowIndex}}</td>
<td class="text-left">{{(!empty($recordDetail->e_dimension) ? $recordDetail->e_dimension :'' )}}</td>
<td class="text-left">{{(!empty($recordDetail->v_dimension_name) ? $recordDetail->v_dimension_name :'' )}}</td>
<td class="text-left">{{(!empty($recordDetail->v_dimension_size) ? $recordDetail->v_dimension_size :'' )}}</td>

<td class="actions-col">
<?php
$checked ='';
if($recordDetail->t_is_active == 1){
	$checked = 'checked="checked"';
}
?>
<div class="custom-control custom-switch status-class">
	<input type="checkbox" class="custom-control-input" <?php echo $checked ?> <?php echo ( checkPermission(config('permission_constants.EDIT_DIMENSION')) != false ? '' : 'disabled' ) ?> data-record-id="{{ $encodeRecordId }} "  id="disable_{{ $rowIndex }}"  onclick="updateRecordStatus(this,'dimension-master')">
	<label class="custom-control-label record-status" for="disable_{{ $rowIndex }}">{{ (!empty($recordDetail->t_is_active == 1) ? trans("messages.enable") : trans("messages.disable") )}}</label>
</div>
</td>
<?php 
if( (checkPermission(config('permission_constants.EDIT_DIMENSION')) != false) || (checkPermission(config('permission_constants.DELETE_DIMENSION')) != false) ){?>
<td class="actions-col">
<?php if(checkPermission(config('permission_constants.EDIT_DIMENSION')) != false){?>
		<button type="button" title='{{ trans("messages.edit-record") }}' class="btn btn-sm btn-info mb-1" onclick="editDimensionModel(this)" data-record-id="<?php echo $encodeRecordId?>"><i class="fas fa-fw fa-pencil-alt"></i></button>
<?php }
	if(checkPermission(config('permission_constants.DELETE_DIMENSION')) != false){?>
		<button type="button" title="{{trans('messages.delete-record')}}" data-record-id="{{ $encodeRecordId }}" data-module-name="dimension-master"   onclick="deleteRecord(this);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
	<?php 
	}?>
</td>
<?php 
}?>