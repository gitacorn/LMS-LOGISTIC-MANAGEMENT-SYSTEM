<?php //echo '<pre>';print_r($recordDetails);die;
if(count($recordDetails) > 0){
	$index= ($page_no - 1) * $perPageRecord;
	foreach ($recordDetails  as $key=>$recordDetail){
		$encodeRecordId = Wild_tiger::encode($recordDetail->i_id);
		
		?>
		<tr>
			<td class="sr-col">{{++$index}}</td>
			<td class="text-left">{{(!empty($recordDetail->v_name) ? $recordDetail->v_name :'' )}}</td>
			<td class="text-left">{{(!empty($recordDetail->v_email) ? $recordDetail->v_email :'' )}}</td>
			<td class="text-left">{{(!empty($recordDetail->v_mobile) ? $recordDetail->v_mobile :'' )}}</td>
			<td class="text-left">{{(!empty($recordDetail->v_department) ? $recordDetail->v_department :'' )}}</td>
			<td class="text-left">{{(!empty($recordDetail->v_record_type) ? $recordDetail->v_record_type :'' )}}</td>
			<td class="text-left">{{(!empty($recordDetail->v_warehouse_name) ? $recordDetail->v_warehouse_name .(!empty($recordDetail->v_warehouse_code) ? ' ('.$recordDetail->v_warehouse_code. ')' :'' ) :'' )}}</td>
			<td class="actions-col">
			<?php
			$checked ='';
			if($recordDetail->t_is_active == 1){
				$checked = 'checked="checked"';
			}
			?>
			<div class="custom-control custom-switch status-class">
				<input type="checkbox" class="custom-control-input" <?php echo $checked ?> <?php echo ( checkPermission(config('permission_constants.EDIT_EMPLOYEE_MASTER')) != false ? '' : 'disabled' ) ?> data-record-id="{{ $encodeRecordId }} "  id="disable_{{ $index }}"  onclick="updateRecordStatus(this,'users')">
				<label class="custom-control-label record-status" for="disable_{{ $index }}">{{ (!empty($recordDetail->t_is_active == 1) ? trans("messages.enable") : trans("messages.disable") )}}</label>
			</div>
			</td>
			<?php if( (checkPermission(config('permission_constants.EDIT_EMPLOYEE_MASTER')) != false) || (checkPermission(config('permission_constants.DELETE_EMPLOYEE_MASTER')) != false) ){?>
			<td class="actions-col">
				<?php if(checkPermission(config('permission_constants.EDIT_EMPLOYEE_MASTER')) != false){?>
				<a href="{{route('user.edit', $encodeRecordId )}}" title="{{trans('messages.edit-record')}}" class="btn btn-sm btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
				<?php } ?>
				<?php if(checkPermission(config('permission_constants.DELETE_EMPLOYEE_MASTER')) != false){?>
				<button type="button" title="{{trans('messages.delete-record')}}" data-record-id="{{ $encodeRecordId }}" data-module-name="users" onclick="deleteRecord(this);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
				<?php } ?>
			</td>
			<?php } ?>
		</tr>
		<?php
		
	}
	if(!empty($pagination)){?>
			<input name="current_page" type="hidden" id="current_page" value="{{ $pagination['current_page'] }}">
			<input name="last_page" type="hidden" id="last_page" value="{{ $pagination['last_page'] }}">
			<input name="per_page" type="hidden" id="per_page" value="{{ $pagination['per_page'] }}">
			<?php 
		}
} else{
	?>
	<tr>
	<td colspan="9" class="text-center">{{ trans('messages.no-record-found')}}
	</td>
</tr>
	<?php 
}
 ?>
@include('admin/common-display-count')	