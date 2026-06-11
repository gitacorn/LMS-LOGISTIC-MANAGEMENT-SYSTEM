<?php //echo '<pre>';print_r($recordDetails);die;
if(count($recordDetails) > 0){
	$index= ($page_no - 1) * $perPageRecord;
	foreach ($recordDetails  as $key=>$recordDetail){
		$encodeRecordId = Wild_tiger::encode($recordDetail->i_id);
		?>
		<tr>
			<td class="sr-col">{{++$index}}</td>
			<td class="text-left">{{(!empty($recordDetail->v_supplier_name) ? $recordDetail->v_supplier_name :'' )}}</td>
			<td class="text-left"><?php echo (!empty($recordDetail->supplier_code) ? implode('<br>' , explode(',' , $recordDetail->supplier_code ) )  :'' ) ?></td>
			<td class="text-left"><?php echo (!empty($recordDetail->e_record_status) ? implode('<br>' , explode(',' , $recordDetail->e_record_status ) )  :'' ) ?></td>
			<td class="text-left"><?php echo (!empty($recordDetail->supplier_address) ? implode('<br>' , explode(',' , $recordDetail->supplier_address ) )  :'' ) ?></td>
			<td class="text-left"><?php echo (!empty($recordDetail->country_name) ? implode('<br>' , explode(',' , $recordDetail->country_name ) )  :'' ) ?></td>
			<td class="text-left"><?php echo (!empty($recordDetail->supplier_contact_person_name) ? implode('<br>' , explode(',' , $recordDetail->supplier_contact_person_name ) )  :'' ) ?></td>
			<td class="text-left"><?php echo (!empty($recordDetail->supplier_contact_mobile) ? implode('<br>' , explode(',' , $recordDetail->supplier_contact_mobile ) )  :'' ) ?></td>
			<td class="text-left"><?php echo (!empty($recordDetail->supplier_contact_email) ? implode('<br>' , explode(',' , $recordDetail->supplier_contact_email ) )  :'' ) ?></td>
			
			<td class="actions-col">
			<?php
			$checked ='';
			if($recordDetail->t_is_active == 1){
				$checked = 'checked="checked"';
			}
			?>
			<div class="custom-control custom-switch status-class">
				<input type="checkbox" class="custom-control-input" <?php echo $checked ?> <?php echo ( checkPermission(config('permission_constants.EDIT_SUPPLIER')) != false ? '' : 'disabled' ) ?> data-record-id="{{ $encodeRecordId }} "  id="disable_{{ $index }}"  onclick="updateRecordStatus(this,'supplier-master')">
				<label class="custom-control-label record-status" for="disable_{{ $index }}">{{ (!empty($recordDetail->t_is_active == 1) ? trans("messages.enable") : trans("messages.disable") )}}</label>
			</div>
			</td>
			<?php 
			if( (checkPermission(config('permission_constants.EDIT_SUPPLIER')) != false) || (checkPermission(config('permission_constants.DELETE_SUPPLIER')) != false) ){
				?>
				<td class="actions-col">
				<?php if(checkPermission(config('permission_constants.EDIT_SUPPLIER')) != false){?>
						<a href="{{route('supplier-master.edit', $encodeRecordId )}}" title="{{trans('messages.edit-record')}}" class="btn btn-sm btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
				<?php }?>
				<?php if(checkPermission(config('permission_constants.DELETE_SUPPLIER')) != false){?>
					<button type="button" title="{{trans('messages.delete-record')}}" data-record-id="{{ $encodeRecordId }}" data-module-name="supplier-master" onclick="deleteRecord(this);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
				<?php }?>
				</td>
			<?php 
			}?>
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
		<td colspan="11" class="text-center">{{ trans('messages.no-record-found')}}</td>
	</tr>
	<?php 
}
 ?>
@include('admin/common-display-count')	