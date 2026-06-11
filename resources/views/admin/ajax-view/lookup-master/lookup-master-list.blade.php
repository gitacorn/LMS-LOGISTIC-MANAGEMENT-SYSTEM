@php $index =  ( $page_no - 1 ) * $perPageRecord @endphp
@if(count($recordDetails) > 0 )
	
	@foreach ($recordDetails as $key => $recordDetail) 
		<?php $encodeRecordId = Wild_tiger::encode($recordDetail->i_id) ?> 
		<tr  class="has-record">
          <td>{{ ++$index }}</td>
          <td>{{  ( (!empty($recordDetail->v_value)) ? $recordDetail->v_value : '' )   }}</td>
          <td class="actions-col">
          	<?php 
          	$checked = '';
          	if($recordDetail->t_is_active == 1){
          		$checked = 'checked="checked"';
          	}
          	?>
          	<div class="custom-control custom-switch status-class">
				<input type="checkbox" class="custom-control-input" <?php echo $checked ?> <?php echo (!empty($recordDetail->i_id) && $recordDetail->i_id == config('constants.DANGEROUS_GOODS_NON_HAZ_ID') ? 'disabled' : '')?> data-record-id="{{ $encodeRecordId }} " id="disable_{{ $key }}" data-another-module-name="{{ Wild_tiger::enumText($recordDetail->v_module_name) }}" onclick="updateRecordStatus(this,'{{ config('constants.LOOKUP_MODULE') }}')">
				<label class="custom-control-label record-status" for="disable_{{ $key }}">{{ (!empty($recordDetail->t_is_active == 1) ? trans("messages.enable") : trans("messages.disable") )}}</label>
			</div>
          </td>
          <td class="actions-col">
          	<div class="d-flex justify-content-center">
				<button title="{{ trans('messages.edit-record') }}" data-module-name="{{  $recordDetail->v_module_name }}" data-record-id="{{ $encodeRecordId }}" onclick="editLookupModal(this);" class="btn action-btn btn-info btn-sm mr-2"><i class="fas fa-pencil-alt"></i></button>
				<?php if(!empty($recordDetail->i_id) && $recordDetail->i_id != config('constants.DANGEROUS_GOODS_NON_HAZ_ID')){?>
				<button title="{{trans('messages.delete-record')}}" data-record-id="{{ $encodeRecordId }}" data-module-name="{{ config('constants.LOOKUP_MODULE') }}"  data-another-module-name="{{ Wild_tiger::enumText($recordDetail->v_module_name) }}" onclick="deleteRecord(this);" type="button" class="btn action-btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
				<?php }?>
			</div>
          </td>
          
   		</tr>                                  
@endforeach
	@if(!empty($pagination))
		<input name="current_page" type="hidden" id="current_page" value="{{ $pagination['current_page'] }}">
        <input name="last_page" type="hidden" id="last_page" value="{{ $pagination['last_page'] }}">
        <input name="per_page" type="hidden" id="per_page" value="{{ $pagination['per_page'] }}">
	@endif
@else
      <tr class="text-center"><td colspan="6">@lang('messages.no-record-found')</td></tr>        
@endif
@include('admin/common-display-count')

										