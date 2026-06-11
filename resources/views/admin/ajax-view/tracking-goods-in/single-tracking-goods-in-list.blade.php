
<?php 
if(count($recordDetails) > 0){
	$index= ($page_no - 1) * $perPageRecord;
	foreach ($recordDetails as $recordDetail){
		?>
		<tr>
			<td class="text-center">{{++$index}}</td>
			<td class="text-left"><?php echo (isset($recordDetail->dt_order_date) ? clientDate($recordDetail->dt_order_date) : '')?></td>
			<td class="text-left"><?php echo (isset($recordDetail->v_goods_in_buyer_detail_no) ? ($recordDetail->v_goods_in_buyer_detail_no) : '')?></td>
			<td class="text-left"><?php echo (isset($recordDetail->v_company_name) ? $recordDetail->v_company_name :'') ?></td>
			<td class="text-left"><?php echo (isset($recordDetail->buyer_user_company_name) ? $recordDetail->buyer_user_company_name :'') ?></td>
			<td class="text-left"><?php echo (isset($recordDetail->goods_buyer_name) ? $recordDetail->goods_buyer_name :'') ?></td>
			<td class="text-left"><?php echo (isset($recordDetail->v_supplier_name) ? $recordDetail->v_supplier_name :'').(isset($recordDetail->v_supplier_address) ? ' (' .$recordDetail->v_supplier_address.')' :'') ?></td>
			<td class="text-left"><?php echo  ( isset($recordDetail->v_po_sales_invoice_no) ?  ($recordDetail->v_po_sales_invoice_no) . '<br>'  :'' ) . ( isset($recordDetail->d_po_amount) ?  decimalAmount($recordDetail->d_po_amount) :'' ).' '.(isset($recordDetail->po_currency_code) ? ( $recordDetail->po_currency_code ) :'' ) ?></td>
			<td class="text-left"><?php echo isset($recordDetail->e_payment_status) ? $recordDetail->e_payment_status . (isset($recordDetail->payment_currency_name) ? '<br>' . $recordDetail->payment_currency_name . (isset($recordDetail->payment_currency_code) ? ' '.$recordDetail->payment_currency_code : '' ) : '' ) : ''  ?></td>
			<td class="text-left"><?php echo (isset($recordDetail->e_collection_type) ? $recordDetail->e_collection_type : '')?></td>
			<td class="text-left"><?php echo (isset($recordDetail->e_delivery_type) ? $recordDetail->e_delivery_type : '')?></td>
			<td class="text-left"><?php echo (isset($recordDetail->v_goods_in_logistic_master_no) ? $recordDetail->v_goods_in_logistic_master_no : '')?></td>
			<td class="text-left"><?php echo (isset($recordDetail->logistic_book_by_name) ? $recordDetail->logistic_book_by_name : '')?></td>
			<td class="text-left"><?php echo (isset($recordDetail->v_logistic_partner_name) ? $recordDetail->v_logistic_partner_name : '') . (isset($recordDetail->v_logistic_partner_code) ? '(' .$recordDetail->v_logistic_partner_code.')' : '')?></td>
			<td class="text-left"><?php echo (isset($recordDetail->dt_collection_date) ? clientDate($recordDetail->dt_collection_date) : '')?></td>
			<td class="text-left"><?php echo (isset($recordDetail->v_tracking_no) ? ($recordDetail->v_tracking_no) : '') .'<br>' .(isset($recordDetail->v_tracking_link) ? ($recordDetail->v_tracking_link) : '')?></td>
			<td class="text-left"><?php echo (isset($recordDetail->dt_delivery_date) ? clientDate($recordDetail->dt_delivery_date) : '')?></td>
			<td class="text-left"><?php echo (isset($recordDetail->d_invoice_total) ? decimalAmount($recordDetail->d_invoice_total) : '')?></td>
			<td class="text-left"><?php echo (isset($recordDetail->v_status) ? ($recordDetail->v_status) : '')?></td>
		</tr>
		<?php 
	}
	if(!empty($pagination)){?>
		<input name="current_page" type="hidden" id="current_page" value="{{ $pagination['current_page'] }}">
		<input name="last_page" type="hidden" id="last_page" value="{{ $pagination['last_page'] }}">
		<input name="per_page" type="hidden" id="per_page" value="{{ $pagination['per_page'] }}">
		<?php 
	}
	
} else {
	?>
	<tr>
		<td colspan="19" class="text-center">{{ trans('messages.no-record-found')}}</td>
	</tr>
	<?php 
}
?>
@include('admin/common-display-count')
			