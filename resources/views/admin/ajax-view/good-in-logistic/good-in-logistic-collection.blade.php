												
<?php //echo '<pre>';print_r($goodInBuyercollectionDetails);die;
if(count($goodInBuyercollectionDetails) > 0){
	$index= 0;
	foreach ($goodInBuyercollectionDetails as $goodInBuyercollectionDetail){
		$encodeRecordId = Wild_tiger::encode($goodInBuyercollectionDetail->i_id);
		$checkBoxValue = ( isset($recordInfo->goodInLogisticCollection) ? json_decode(json_encode($recordInfo->goodInLogisticCollection),true) : [] );
		$checkboxId = (!empty($checkBoxValue) ? (array_column($checkBoxValue, 'i_goods_in_buyer_detail_id')) :[] );
		$deliveryTypeRecord = (!empty($checkBoxValue) ? (array_column($checkBoxValue, 'e_collection_delivery_type')) : [] );
		$warehouseRecord = (!empty($checkBoxValue) ? (array_column($checkBoxValue, 'i_collection_delivery_location_id')) :[] );
		$deliveryRemarks = (!empty($checkBoxValue) ? (array_column($checkBoxValue, 'dt_collection_delivery_remark')) : [] );
		$goodInBuyercollectionDetailId = (!empty($goodInBuyercollectionDetail->i_id) ? $goodInBuyercollectionDetail->i_id : '');
		$remarks = '';
		$editDeliveryType = "";
		$editCollectionLocation = "";
		
		if(in_array($goodInBuyercollectionDetail->i_id, $checkboxId)){
			$searchKey = array_search($goodInBuyercollectionDetail->i_id,$checkboxId);
			if(strlen($searchKey)  > 0 ){
				$remarks = $checkBoxValue[$searchKey]['dt_collection_delivery_remark'];
				$editDeliveryType = $checkBoxValue[$searchKey]['e_collection_delivery_type'];
				$editCollectionLocation = $checkBoxValue[$searchKey]['i_collection_delivery_location_id'];
			}
		}
		
		$checked = "";
		if( isset($checkboxId) && (in_array($goodInBuyercollectionDetail->i_id, $checkboxId)) ) {
			$checked = "checked='checked'";
		}
		
		?>
		<tr>
			<td class="text-center">
	            <div class="form-check form-check-inline  pt-2 pb-2 mr-0">
	           		<input class="form-check-input logistic-part-selection" {{ $disableForm }} type="checkbox" id="checkbox_<?php echo $goodInBuyercollectionDetail->i_id?>" name="checkbox_collection[<?php echo $goodInBuyercollectionDetailId ?>]" value="<?php echo (!empty($encodeRecordId) ? $encodeRecordId : 0)?>" <?php echo $checked ?>>
	           		<label class="form-check-label"  for="checkbox_<?php echo $goodInBuyercollectionDetail->i_id?>"></label>
	            </div>
	    	</td>
	    	<td class="text-center" style="width:70px;min-width:70px;">{{++$index}}</td>
           	<td class="text-left"><?php echo (isset($goodInBuyercollectionDetail->v_goods_in_buyer_detail_no) ? $goodInBuyercollectionDetail->v_goods_in_buyer_detail_no :'')?></td>
           	<td class="text-left"><?php echo (isset($goodInBuyercollectionDetail->goodInBuyerMaster->dt_order_date) ? clientDate($goodInBuyercollectionDetail->goodInBuyerMaster->dt_order_date) :'')?></td>
           	<td class="text-left"><?php echo (isset($goodInBuyercollectionDetail->goodInBuyerMaster->v_po_sales_invoice_no) ? $goodInBuyercollectionDetail->goodInBuyerMaster->v_po_sales_invoice_no :'')?></td>
            <td class="text-left"><?php echo (isset($goodInBuyercollectionDetail->goodInBuyerMaster->d_po_amount) ? $goodInBuyercollectionDetail->goodInBuyerMaster->d_po_amount :'') .'<br>'.(isset($goodInBuyercollectionDetail->goodInBuyerMaster->poCurrencyMaster->v_currency_code) ? $goodInBuyercollectionDetail->goodInBuyerMaster->poCurrencyMaster->v_currency_code : '') ?></td>
           	<td class="text-left"><?php echo (isset($goodInBuyercollectionDetail->supplierMaster->v_supplier_address) ? $goodInBuyercollectionDetail->supplierMaster->v_supplier_address :'')?></td>
           	<td class="text-left"><?php echo (isset($goodInBuyercollectionDetail->goodInBuyerMaster->supplierMaster->v_supplier_name) ? $goodInBuyercollectionDetail->goodInBuyerMaster->supplierMaster->v_supplier_name :'')?></td>
           	<td class="text-left"><?php echo (isset($goodInBuyercollectionDetail->goodInBuyerMaster->e_mode_of_transport) ? $goodInBuyercollectionDetail->goodInBuyerMaster->e_mode_of_transport :'')?></td>
			<td class="text-left"><b><?php echo (  (isset($goodInBuyercollectionDetail->e_logistic_record_status) && (!empty($goodInBuyercollectionDetail->e_logistic_record_status)) ) ? $goodInBuyercollectionDetail->e_logistic_record_status : '' ) ?></b>
           		<select name="collection_delivery_type[<?php echo $goodInBuyercollectionDetailId ?>]" {{ $disableForm }} class="form-control collection-delivery-type-record" <?php echo ( ( isset($editDeliveryType) && ( !empty($editDeliveryType) ) ) ? ( ( session()->get('role') == config('constants.ROLE_ADMIN') ) ? '' : 'disabled' )  : '' )   ?> >
           			<option value="">{{ trans("messages.select") }}</option>
           			<?php 
                    if(!empty($deliveryTypeInfo)){
                    	foreach ($deliveryTypeInfo as  $key => $deliveryType){
                    		$selected ='';
                    		
                    		if( $key == config('constants.CANCELLED_DELIVERY_TYPE') ){
                    			continue;
                    		}
                    		
                    		if( $editDeliveryType == $key ){
                    			$selected = "selected='selected'";
                    		}
                    		?>
                   			<option value="{{ $key}}" {{ $selected }}>{{ $deliveryType }}</option>
							<?php 
                        }
                     }
                 	 ?>
           		</select>
            </td>
            <td class="text-left">
            	<select name="delivery_location[<?php echo $goodInBuyercollectionDetailId ?>]" {{ $disableForm }} class="form-control collection-delivery-location-record">
               		<option value="">{{ trans("messages.select") }}</option>
                   		<?php 
                     	if(!empty($warehouseRecordDetails)){
                      		foreach ($warehouseRecordDetails as $warehouseRecordDetail){
                            	$encodeWarehouseId  = Wild_tiger::encode($warehouseRecordDetail->i_id);
                            	$selected ='';
	                      		if( $editCollectionLocation == $warehouseRecordDetail->i_id ){
	                    			$selected = "selected='selected'";
	                    		}
                               	?>
                         		<option value="{{ $encodeWarehouseId }}" {{ $selected }} data-record-id="{{ (!empty($warehouseRecordDetail->i_id) ? $warehouseRecordDetail->i_id : 0) }}">{{ (!empty($warehouseRecordDetail->v_warehouse_code) ? $warehouseRecordDetail->v_warehouse_code : '' ) }}</option>
                             	<?php 
                       		}
                      	} 
                  		?>
         		</select>
        	</td>
        	<td><input type="text" name="delivery_remarks[<?php echo $goodInBuyercollectionDetailId ?>]" {{ $disableForm }} class="form-control" placeholder="{{ trans('messages.delivery-remarks') }}" value="<?php echo (!empty($remarks) ? $remarks :'') ?>"></td>
		</tr>
		<?php 
	}
} else {
	?>
	<tr>
		<td colspan="11" class="text-center">{{ trans('messages.no-record-found')}}</td>
	</tr>
	<?php 
}
?>												