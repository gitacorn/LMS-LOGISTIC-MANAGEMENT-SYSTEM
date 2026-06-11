<?php
$encodeRecordId = Wild_tiger::encode($recordDetail->i_id); ?>
<td class="sr-col">{{$rowIndex}}</td>
<td class="text-left">{{(!empty($recordDetail->e_document_type) ? $recordDetail->e_document_type :'' )}}</td>
<td class="text-left">{{(!empty($recordDetail->v_document_type_name) ? $recordDetail->v_document_type_name :'' )}}</td>

<td class="actions-col">
<?php
$checked ='';
if($recordDetail->t_is_active == 1){
	$checked = 'checked="checked"';
}
?>
	<div class="custom-control custom-switch status-class">
		<input type="checkbox" class="custom-control-input" <?php echo $checked ?> {{  ( in_array( $recordDetail->i_id , [ config('constants.DOCUMENT_TYPE_PACKING_LIST_ID') , config('constants.DOCUMENT_TYPE_INVOICE_ID') ] ) ? 'disabled' : '' ) }} <?php echo ( checkPermission(config('permission_constants.EDIT_DOCUMENT_TYPE')) != false ? '' : 'disabled' ) ?> data-record-id="{{ $encodeRecordId }} "  id="disable_{{ $rowIndex }}"  onclick="updateRecordStatus(this,'document-type-master')">
		<label class="custom-control-label record-status" for="disable_{{ $rowIndex }}">{{ (!empty($recordDetail->t_is_active == 1) ? trans("messages.enable") : trans("messages.disable") )}}</label>
	</div>
</td>
<?php 
if( (checkPermission(config('permission_constants.EDIT_DOCUMENT_TYPE')) != false) || (checkPermission(config('permission_constants.DELETE_DOCUMENT_TYPE')) != false) ){
?>
	<td class="actions-col">
	<?php 
		if(checkPermission(config('permission_constants.EDIT_DOCUMENT_TYPE')) != false){
		?>
			<button type="button" title='{{ trans("messages.edit-record") }}' class="btn btn-sm btn-info mb-1" onclick="editDocumentTypeModel(this)" data-record-id="<?php echo $encodeRecordId?>"><i class="fas fa-fw fa-pencil-alt"></i></button>
			<?php 
		}
		if(checkPermission(config('permission_constants.DELETE_DOCUMENT_TYPE')) != false){
		?>
		@if(!empty($recordDetail->i_id) && ($recordDetail->i_id != config('constants.DOCUMENT_TYPE_PACKING_LIST_ID')) && ($recordDetail->i_id != config('constants.DOCUMENT_TYPE_INVOICE_ID')))
			<button type="button" title="{{trans('messages.delete-record')}}" data-record-id="{{ $encodeRecordId }}" data-module-name="document-type-master"   onclick="deleteRecord(this);" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
		@endif
		<?php 
		}?>
	</td>
<?php }?>