
<?php 
if(count($goodInBuyerDeliveryDetails) > 0){
	$index= 0;
	foreach ($goodInBuyerDeliveryDetails as $goodInBuyerDeliveryDetail){
		$encodeRecordId = Wild_tiger::encode($goodInBuyerDeliveryDetail->i_id);
		$checked = "";
		$disabled = '';
		if( isset($recordInfo) && (!empty($recordInfo->i_goods_in_buyer_detail_id)) && ( $recordInfo->i_goods_in_buyer_detail_id == $goodInBuyerDeliveryDetail->i_id ) ) {
			$checked = "checked='checked'";
			$disabled = 'disabled';
			
		}
		?>
		<tr>
			<td class="text-center"><input class="logistic-part-selection" <?php echo $disabled ?> type="radio" name="logistic_delivery" id="radio_<?php echo $goodInBuyerDeliveryDetail->i_id?>" value="<?php echo $encodeRecordId?>" <?php echo $checked ?>></td>
			<td class="text-center" style="width:70px;min-width:70px;">{{++$index}}</td>
			<td class="text-left"><?php echo (isset($goodInBuyerDeliveryDetail->v_goods_in_buyer_detail_no) ? $goodInBuyerDeliveryDetail->v_goods_in_buyer_detail_no :'')?></td>
	        <td class="text-left"><?php echo (isset($goodInBuyerDeliveryDetail->supplierMaster->v_supplier_address) ? $goodInBuyerDeliveryDetail->supplierMaster->v_supplier_address :'')?></td>
	        <td class="text-left"><?php echo (isset($goodInBuyerDeliveryDetail->goodInBuyerMaster->dt_order_date) ? clientDate($goodInBuyerDeliveryDetail->goodInBuyerMaster->dt_order_date) :'')?></td>
	        <td class="text-left"><?php echo (isset($goodInBuyerDeliveryDetail->goodInBuyerMaster->dt_delivery_date) ? clientDate($goodInBuyerDeliveryDetail->goodInBuyerMaster->dt_delivery_date) :'')?></td>
	        <td class="text-left"><?php echo (isset($goodInBuyerDeliveryDetail->goodInBuyerMaster->v_po_sales_invoice_no) ? $goodInBuyerDeliveryDetail->goodInBuyerMaster->v_po_sales_invoice_no :'')?></td>
	        <td class="text-left"><?php echo (isset($goodInBuyerDeliveryDetail->goodInBuyerMaster->d_po_amount) ? decimalAmount($goodInBuyerDeliveryDetail->goodInBuyerMaster->d_po_amount) . (isset($goodInBuyerDeliveryDetail->goodInBuyerMaster->poCurrencyMaster->v_currency_code) ? ' ('. $goodInBuyerDeliveryDetail->goodInBuyerMaster->poCurrencyMaster->v_currency_code . ') ' : '') :'') ?></td>
	        <td class="text-left"><?php echo (isset($goodInBuyerDeliveryDetail->goodInBuyerMaster->e_mode_of_transport) ? $goodInBuyerDeliveryDetail->goodInBuyerMaster->e_mode_of_transport :'')?></td>
			<?php /* <td class="text-left"><?php echo (isset($goodInBuyerDeliveryDetail->goodInBuyerMaster->e_delivery_type) ? $goodInBuyerDeliveryDetail->goodInBuyerMaster->e_delivery_type :'')?></td> */?> 
	        <td class="text-left"><?php echo (isset($goodInBuyerDeliveryDetail->e_logistic_record_status) ? $goodInBuyerDeliveryDetail->e_logistic_record_status :'')?></td> 
        </tr>                                                                                          
		<?php 
	}
} else {
	?>
	<tr>
		<td colspan="10" class="text-center">{{ trans('messages.no-record-found')}}</td>
	</tr>
	<?php 
}
?>												