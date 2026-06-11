<?php
$encodeRecordId = Wild_tiger::encode($recordDetail->i_id); 
$recordType = (!empty($recordDetail->e_record_type) ? $recordDetail->e_record_type :'' );
?>

<td class="sr-col">{{$rowIndex}}</td>
<td class="text-left">{{(!empty($recordDetail->v_warehouse_name) ? $recordDetail->v_warehouse_name :'' )}}</td>
<td class="text-left">{{(!empty($recordDetail->v_warehouse_code) ? $recordDetail->v_warehouse_code :'' )}}</td>
<td class="text-left">{{(!empty($recordDetail->v_warehouse_short_code) ? $recordDetail->v_warehouse_short_code :'' )}}</td>
<td class="text-left">{{(!empty($recordDetail->v_country_name) ? $recordDetail->v_country_name :'' )}}</td>
<?php if($recordType == config('constants.WAREHOUSE')){ ?>
<td class="text-left">{!! (!empty($recordDetail->v_warehouse_email) ? str_replace(',', '<br>', $recordDetail->v_warehouse_email) : '') !!}</td>
<?php } ?>
<td class="actions-col">
<?php
$checked ='';
$enableDisable = "";
if($recordDetail->t_is_active == 1){
	$checked = 'checked="checked"';
}

if($recordType == config('constants.WAREHOUSE')){
	$warehouse = 'warehouse-master';
	$editPermission = checkPermission(config('permission_constants.EDIT_WAREHOUSE'));
	$deletePermission = checkPermission(config('permission_constants.DELETE_WAREHOUSE'));
	$enableDisable = ( checkPermission(config('permission_constants.EDIT_WAREHOUSE')) != false ? '' : 'disabled' );
	
	
} else if($recordType == config('constants.PORT')){
	$warehouse = 'port-master';
	$editPermission = checkPermission(config('permission_constants.EDIT_PORT'));
	$deletePermission = checkPermission(config('permission_constants.DELETE_PORT'));
	$enableDisable = ( checkPermission(config('permission_constants.EDIT_PORT')) != false ? '' : 'disabled' );
	
} else {
	$warehouse = 'location-master';
	$editPermission = checkPermission(config('permission_constants.EDIT_LOCATION'));
	$deletePermission = checkPermission(config('permission_constants.DELETE_LOCATION'));
	$enableDisable = ( checkPermission(config('permission_constants.EDIT_LOCATION')) != false ? '' : 'disabled' ); 
}
?>
			<div class="custom-control custom-switch status-class">
				<input type="checkbox" class="custom-control-input" <?php echo $checked ?> <?php echo $enableDisable?> data-record-id="{{ $encodeRecordId }} "  id="disable_{{ $rowIndex }}"  onclick="updateRecordStatus(this,'{{$warehouse}}')">
				<label class="custom-control-label record-status" for="disable_{{ $rowIndex }}">{{ (!empty($recordDetail->t_is_active == 1) ? trans("messages.enable") : trans("messages.disable") )}}</label>
			</div>
			</td>
			<?php if( ($editPermission != false) || ($deletePermission != false) ){?>
			<td class="actions-col">
			<?php if($editPermission != false){?>
				<button type="button" title='{{ trans("messages.edit-record") }}' class="btn btn-sm btn-info mb-1" data-record-type="{{(!empty($recordDetail->e_record_type) ? $recordDetail->e_record_type :'' )}}" onclick="editWarehouseModel(this)" data-record-id="<?php echo $encodeRecordId?>"><i class="fas fa-fw fa-pencil-alt"></i></button>
			<?php }?>
			<?php if($deletePermission != false){?>
				<button type="button" title="{{trans('messages.delete-record')}}" data-record-id="{{ $encodeRecordId }}" data-module-name="{{$warehouse}}" onclick="deleteRecord(this);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
			<?php }?>
			</td>
		<?php }?>