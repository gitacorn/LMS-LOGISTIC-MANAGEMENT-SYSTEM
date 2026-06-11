@php $index =  ( $page_no - 1 ) * $perPageRecord @endphp
@if(count($recordDetails) > 0 )
	
	@foreach ($recordDetails as $key => $recordDetail) 
		<?php $encodeRecordId = Wild_tiger::encode($recordDetail->i_id) ?> 
		<tr  class="has-record">
          <td>{{ ++$index }}</td>
          <td class="text-left" style="min-width: 80px;max-width:80px;">{{  ( (!empty($recordDetail->v_name)) ? $recordDetail->v_name : '' )   }}</td>
          <td style="min-width: 80px;max-width:80px;">{{  ( (!empty($recordDetail->dt_created_at)) ? clientDateTime ( $recordDetail->dt_created_at ) : '' )   }}</td>
          <td style="min-width: 200px;max-width:200px;">{{  ( (!empty($recordDetail->v_ip)) ? ( $recordDetail->v_ip ) : '' )   }}</td>
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

										