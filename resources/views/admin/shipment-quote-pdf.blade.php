<html>

<head>
</head>

<body>

    <div>
        <table width="100%" style="padding: 20px 20px 0 20px;font-family: 'Poppins', sans-serif;">
            <thead style="padding-bottom: 10px;">
                <tr>
                    <th style="width:20%; border: 0;">

                    </th>
                    <th style="width:60%; border: 0;">
                        <img src="{{ asset('images/shipment-logo.png') }}" style="width:40%; padding-bottom:10px;">
                        <br />
                        <p style="font-size:12px;">
                        </p>
                    </th>
                    <th style="width:20%; text-align:right; border: 0;">
                    </th>
                </tr>
            </thead>
        </table>

        <div style="font-family: 'Poppins', sans-serif;">
            <h2 style="text-align:center;">{{ trans("messages.shipment-collection-request-form")}}</h2>
        </div>
        <table border="1" cellpadding="5" style="width:100%;margin:10px 20px 20px 20px;text-align: left;border: 1px solid;font-family: 'Poppins', sans-serif;border-collapse:collapse;" cellspacing="2">
            <tbody>
                <tr>
                    <th style="text-align:left;width:50%;line-height:22px;vertical-align:top;">{{  trans('messages.purchase-order-number') }}:</th>
                    <td style="vertical-align:middle"><strong>{{ (!empty($pdfRecordInfo->goodInBuyerMaster->v_po_sales_invoice_no) ? $pdfRecordInfo->goodInBuyerMaster->v_po_sales_invoice_no : '') }}</strong></td>
                </tr>
                <?php 
                    $supplierAddressInfo = ( isset($pdfRecordInfo->goodInBuyerMaster->supplierMaster->supplierDetail) ? json_decode(json_encode($pdfRecordInfo->goodInBuyerMaster->supplierMaster->supplierDetail),true) : [] );
                    
                    $registeredAddress = '';
                    $countryName = '';
                    if(!empty($supplierAddressInfo)){
                    	foreach ($supplierAddressInfo as $supplierAddress){
                    		if(($pdfRecordInfo->supplierMaster->i_country_id == $supplierAddress['i_country_id']) && (config('constants.REGISTERED_STATUS') == $supplierAddress['e_record_status'])){
                    			$registeredAddress = (!empty($supplierAddress['v_supplier_address']) ? $supplierAddress['v_supplier_address'] .(!empty($supplierAddress['country_master']['v_country_name']) ? ' ('.$supplierAddress['country_master']['v_country_name'] .')' :''):'');
                    		}
                    		$countryName = (!empty($supplierAddress['country_master']['v_country_name']) ? $supplierAddress['country_master']['v_country_name']  :'');
                    	}
                    }
                   ?>
                <tr>
                    <th style="text-align:left;width:50%;line-height:22px;vertical-align:top;height:150px">{{  trans('messages.supplier-name') }}<br>{{  trans('messages.pick-up-address-of-suppliers-w-h') }}<br>{{  trans('messages.supplier-country') }}</th>
                    <td style="vertical-align:top;line-height:24px"><strong>{{ (!empty($pdfRecordInfo->supplierMaster->supplierMaster->v_supplier_name) ? $pdfRecordInfo->supplierMaster->supplierMaster->v_supplier_name : '') }} <br> {{ (!empty($pdfRecordInfo->supplierMaster->v_supplier_address) ? $pdfRecordInfo->supplierMaster->v_supplier_address .(!empty($countryName) ? ' ('.  $countryName . ')' :''): '') }} <br>{{ (!empty($pdfRecordInfo->supplierMaster->countryMaster->v_country_name) ? $pdfRecordInfo->supplierMaster->countryMaster->v_country_name : '') }}</strong></td>
                </tr>
                <tr>
                    <th style="text-align:left;width:50%;line-height:22px;vertical-align:top;height:150px">@if(!empty($registeredAddress)) {{  trans('messages.invoice-address-if-different-from-pick-up-address') }} @endif</th>
                    <td style="vertical-align:top;line-height:24px"><strong>{{ $registeredAddress }}</strong></td>
                </tr>
                <tr>
                    <th style="text-align:left;width:50%;line-height:22px;vertical-align:top;height:150px">{{  trans('messages.collection-details') }}</th>
                    <td style="vertical-align:top;line-height:24px">
	                    <?php $dimensionInfo = ( isset($pdfRecordInfo->goodInBuyerMaster->dimensionMaster) && !empty($pdfRecordInfo->goodInBuyerMaster->dimensionMaster) ? json_decode(json_encode($pdfRecordInfo->goodInBuyerMaster->dimensionMaster),true) : [] ); ?>
	                    
	                    {{ trans('messages.no-of-boxes') }}: 
	                    @if(isset($pdfRecordInfo->goodInBuyerMaster->e_pallet_box_type) && !empty($pdfRecordInfo->goodInBuyerMaster->e_pallet_box_type) && $pdfRecordInfo->goodInBuyerMaster->e_pallet_box_type == config('constants.BOX')) 
	                    	@if(isset($pdfRecordInfo->goodInBuyerMaster->i_no_of_pallet_box) && !empty($pdfRecordInfo->goodInBuyerMaster->i_no_of_pallet_box)) 
	                    		<strong>{{ $pdfRecordInfo->goodInBuyerMaster->i_no_of_pallet_box }}</strong> 
	                    	@endif 
	                    @endif
	                    
	                    <br> {{ trans('messages.no-of-pallets') }}: 
	                    @if(isset($pdfRecordInfo->goodInBuyerMaster->e_pallet_box_type) && !empty($pdfRecordInfo->goodInBuyerMaster->e_pallet_box_type) && $pdfRecordInfo->goodInBuyerMaster->e_pallet_box_type == config('constants.PALLET')) 
		                    @if(isset($pdfRecordInfo->goodInBuyerMaster->i_no_of_pallet_box) && !empty($pdfRecordInfo->goodInBuyerMaster->i_no_of_pallet_box)) 
		                    	<strong>{{ $pdfRecordInfo->goodInBuyerMaster->i_no_of_pallet_box }}</strong> 
		                    @endif 
	                    @endif 
	                    
	                    <br> {{ trans('messages.boxes-dimension') }}: 
	                    @if(isset($pdfRecordInfo->goodInBuyerMaster->e_pallet_box_type) && !empty($pdfRecordInfo->goodInBuyerMaster->e_pallet_box_type) && $pdfRecordInfo->goodInBuyerMaster->e_pallet_box_type == config('constants.BOX')) 
	                     	@if(!empty($dimensionInfo))
	                     	<strong>
								@foreach ($dimensionInfo as $dimension)
									{{ (!empty($dimension['v_dimension_name']) ? $dimension['v_dimension_name'] .(!empty($dimension['v_dimension_size']) ? ' (' . $dimension['v_dimension_size'] . ')' .(!$loop->last ? ', '  : '')  : '' ):'') }}
								@endforeach
							</strong> 
	                     	@endif 
	                    @endif
	                    
	                    <br> {{ trans('messages.pallets-dimension') }}: 
	                    @if(isset($pdfRecordInfo->goodInBuyerMaster->e_pallet_box_type) && !empty($pdfRecordInfo->goodInBuyerMaster->e_pallet_box_type) && $pdfRecordInfo->goodInBuyerMaster->e_pallet_box_type == config('constants.PALLET')) 
	                     	@if(!empty($dimensionInfo))
	                     	<strong>
								@foreach ($dimensionInfo as $dimension)
									{{ (!empty($dimension['v_dimension_name']) ? $dimension['v_dimension_name'] .(!empty($dimension['v_dimension_size']) ? ' (' . $dimension['v_dimension_size'] . ')' .(!$loop->last ? ', '  : '')  : '' ):'') }}
								@endforeach
							</strong> 
	                     	@endif 
	                    @endif
	                    <br>{{ trans('messages.gross-weight') }}: <strong>{{ (!empty($pdfRecordInfo->goodInBuyerMaster->d_weight) ? decimalAmount($pdfRecordInfo->goodInBuyerMaster->d_weight) .(!empty($pdfRecordInfo->goodInBuyerMaster->e_weight_unit) ? ' (' . $pdfRecordInfo->goodInBuyerMaster->e_weight_unit . ')' : ''): 0)}}</strong>
	                    <br> {{trans('messages.stackable') }}? <strong>{{ (!empty($pdfRecordInfo->goodInBuyerMaster->e_pallet_type) ? $pdfRecordInfo->goodInBuyerMaster->e_pallet_type : '') }} </strong> 
                    </td>
                </tr>
                <tr>
                    <th style="text-align:left;width:50%;line-height:22px;vertical-align:top;height:150px">{{  trans('messages.goods-type') }}</th>
                    <td style="vertical-align:top;line-height:24px">
	                    @if(isset($pdfRecordInfo->goodInBuyerMaster->buyerDangerousGoods->v_value) && !empty($pdfRecordInfo->goodInBuyerMaster->buyerDangerousGoods->v_value))
		                    @if((!empty($pdfRecordInfo->goodInBuyerMaster->i_dangerous_goods_id)) && $pdfRecordInfo->goodInBuyerMaster->i_dangerous_goods_id == config('constants.DANGEROUS_GOODS_NON_HAZ_ID'))
		                    	{{ trans('messages.no-hazardous') }}: <strong><img src="{{  asset('images/check.png') }}" alt="" style="width:12px;"></strong>
		                    	<br>{{ trans('messages.if-hazardous-dg') }}</td>
		                    @else
		                    {{ $pdfRecordInfo->goodInBuyerMaster->buyerDangerousGoods->v_value }}
		                    @endif
                     	@endif
                </tr>
                <tr>
                    <th style="text-align:left;line-height:22px;vertical-align:middle;height:100px;font-size:20px" colspan="2">{{  trans('messages.customs-import-export-both' , ['type' => (!empty($pdfRecordInfo->goodInBuyerMaster->e_customs_procedure) ? $pdfRecordInfo->goodInBuyerMaster->e_customs_procedure : '')]) }}</th>
                </tr>
                <?php /* 
                <tr>
                    <th style="text-align:left;width:50%;line-height:22px;vertical-align:top;">{{  trans('messages.if-customs-import') }}</th>
                    <td style="vertical-align:top;line-height:24px">{{ trans('messages.customer-imported-by') }}: <strong>{{ (!empty($pdfRecordInfo->goodInBuyerMaster->e_customer_procedure_import) ? $pdfRecordInfo->goodInBuyerMaster->e_customer_procedure_import : '') }}</strong></td>
                </tr>
                <tr>
                    <th style="text-align:left;width:50%;line-height:22px;vertical-align:top;">{{  trans('messages.if-customs-export') }}</th>
                    <td style="vertical-align:top;line-height:24px">{{ trans('messages.customer-export-by') }}: <strong>{{ (!empty($pdfRecordInfo->goodInBuyerMaster->e_customer_procedure_export) ? $pdfRecordInfo->goodInBuyerMaster->e_customer_procedure_export : '') }}</strong></td>
                </tr>
                 */?>
                <tr>
                    <th style="text-align:left;width:50%;line-height:22px;vertical-align:top;">{{  trans('messages.pick-up-timings-warehouse-open-close-time') }}</th>
                    <td style="vertical-align:top;line-height:24px"><strong>{{ (!empty($pdfRecordInfo->supplierMaster->v_timings) ? $pdfRecordInfo->supplierMaster->v_timings : '') }}</strong></td>
                </tr>
                <?php /* 
                <tr>
                    <th style="text-align:left;width:50%;line-height:22px;vertical-align:top;height:100px">{{ trans('messages.required-pick-up-reference-for-transport-company-driver') }}</th>
                    <td style="vertical-align:middle;line-height:24px"><strong>{{ (!empty($pdfRecordInfo->goodInBuyerMaster->e_pickup_reference) ? $pdfRecordInfo->goodInBuyerMaster->e_pickup_reference .(!empty($pdfRecordInfo->goodInBuyerMaster->v_reference) ? ' - ' .$pdfRecordInfo->goodInBuyerMaster->v_reference :''):'') }}</strong></td>
                </tr>
                 */?>
                <tr>
                    <th style="text-align:left;width:50%;line-height:22px;vertical-align:top;">{{ trans('messages.contact-info-warehouse-logistics') }}</th>
                    <td style="vertical-align:top;line-height:24px"><strong><?php echo (!empty($pdfRecordInfo->supplierMaster->v_contact_person_name) ? $pdfRecordInfo->supplierMaster->v_contact_person_name  :'') .(!empty($pdfRecordInfo->supplierMaster->v_contact_mobile) ? '<br>'.$pdfRecordInfo->supplierMaster->v_contact_mobile  :'') .(!empty($pdfRecordInfo->supplierMaster->v_contact_email) ? '<br>'.$pdfRecordInfo->supplierMaster->v_contact_email :'') ?></strong></td>
                </tr>
                <tr>
                    <th style="text-align:left;width:50%;line-height:22px;vertical-align:top;height:170px">{{ trans('messages.delivery-address') }}:</th>
                    <td style="vertical-align:top;line-height:27px"><strong>{{ (!empty($pdfRecordInfo->goodInBuyerMaster->warehouseMaster->v_warehouse_name) ? $pdfRecordInfo->goodInBuyerMaster->warehouseMaster->v_warehouse_name .(!empty($pdfRecordInfo->goodInBuyerMaster->warehouseMaster->v_warehouse_code) ? ' ('.$pdfRecordInfo->goodInBuyerMaster->warehouseMaster->v_warehouse_code .(!empty($pdfRecordInfo->goodInBuyerMaster->warehouseMaster->countryMaster->v_country_name) ? ' - '.$pdfRecordInfo->goodInBuyerMaster->warehouseMaster->countryMaster->v_country_name : '') .')': '' ):'') }}</strong></td>
                </tr>
            </tbody>

        </table>
    </div>



</body>

</html>