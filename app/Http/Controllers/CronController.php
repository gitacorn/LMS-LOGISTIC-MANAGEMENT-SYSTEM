<?php

namespace App\Http\Controllers;

use App\Http\Controllers\GuestController;
use App\BaseModel;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\CompanyMasterModel;
use App\WarehouseMasterModel;
use App\CustomerMasterModel;
use App\CustomerDetailModel;
use App\LoginHistory;
use Illuminate\Support\Facades\Log;
use App\GoodInLogisticMasterModel;
use App\UsWarehouseToAmazonMasterModel;
use App\CountrytoPortEuropeModel;
use App\CountrytoPortEuropeTransferDetailModel;
use App\GoodInBuyerDetailModel;
use App\GoodInBuyerMasterModel;
use App\LookupMaster;
use Illuminate\Support\Facades\DB;
use App\ReportModel;
use App\Models\WarehousePalletForecastLock;

class CronController extends GuestController
{
    //
    public function __construct(){
    	$this->baseModel = new BaseModel();
    }

    /**
     * Lock tomorrow's pallet forecast per warehouse at 23:59 today.
     * It reads the dashboard buyer delivery aggregation and stores a static snapshot
     * into warehouse_pallet_forecast_locks so that subsequent changes don't affect the locked value.
     */
    public function lockWarehousePalletForecasts(){
        try{
            $reportModel = new ReportModel();
            // getBuyerDeliveryDetails returns upcoming dates (today..+6 days)
            $buyerDetails = $reportModel->getBuyerDeliveryDetails();
            if(empty($buyerDetails)){
                return;
            }
            $tomorrowYmd = date('Y-m-d', strtotime('+1 day'));
            $totals = [];
            foreach($buyerDetails as $bd){
                $deliveryYmd = (!empty($bd->dt_delivery_date) ? date('Y-m-d', strtotime($bd->dt_delivery_date)) : null);
                if($deliveryYmd !== $tomorrowYmd){
                    continue;
                }
                // Determine warehouse id
                $warehouseId = null;
                if (isset($bd->i_warehouse_id) && !empty($bd->i_warehouse_id)) {
                    $warehouseId = (int) $bd->i_warehouse_id;
                } elseif (isset($bd->i_to_warehouse_id) && !empty($bd->i_to_warehouse_id)) {
                    $warehouseId = (int) $bd->i_to_warehouse_id;
                }
                if(empty($warehouseId)){
                    continue;
                }
                // Only pallets
                $pallets = (isset($bd->type_of_pallet_box) && $bd->type_of_pallet_box == config('constants.PALLET')) ? (int)$bd->total_pallets : 0;
                if(!isset($totals[$warehouseId])){
                    $totals[$warehouseId] = 0;
                }
                $totals[$warehouseId] += $pallets;
            }

            if(empty($totals)){
                return;
            }

            foreach($totals as $warehouseId => $palletCount){
                // Upsert only if not already locked
                $existing = WarehousePalletForecastLock::where('t_is_deleted',0)
                    ->where('i_warehouse_id', $warehouseId)
                    ->where('dt_forecast_date', $tomorrowYmd)
                    ->first();
                if($existing){
                    continue; // already locked
                }
                WarehousePalletForecastLock::create([
                    'i_warehouse_id' => $warehouseId,
                    'dt_forecast_date' => $tomorrowYmd,
                    'i_pallet_forecast' => (int)$palletCount,
                    'dt_locked_at' => date('Y-m-d H:i:s'),
                    't_is_active' => 1,
                    't_is_deleted' => 0,
                    'i_created_id' => 0,
                    'dt_created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        } catch(\Exception $e){
            // Silent catch to avoid breaking cron chain; optionally log
            \Log::error('lockWarehousePalletForecasts error: ' . $e->getMessage());
        }
    }

    public function manageFBASheet(){
    	
    	$this->curdModel =  New BaseModel();
    	$getPendingRecordDetails = $this->curdModel->getSingleRecordById( config('constants.IMPORT_SHEET_HISTORY_MASTER_TABLE') , [ 'i_id' , 'v_file_name' , 'v_file_path' , 'i_country_to_port_goods_out_master_id' , 'e_status' ] , [ 'e_status' => config('constants.PENDING_STATUS') , 't_is_deleted != ' => 1 ]  );
    	if(!empty($getPendingRecordDetails)){
    		$uploadedFilePath = config('constants.FILE_STORAGE_FILE_PATH') . config('constants.UPLOAD_FOLDER') .  $getPendingRecordDetails->v_file_path;
    		//$uploadedFilePath = config('constants.FILE_STORAGE_FILE_PATH') . 'upload_fba_file/fba-sheet_1670322059.xlsx';
    		$insertFBASheetMasterInfo = [];
    		$insertFBASheetMasterInfo['i_country_to_port_goods_out_master_id'] = $getPendingRecordDetails->i_country_to_port_goods_out_master_id;
    		$insertFBASheetMasterInfo['e_status'] = $getPendingRecordDetails->e_status;
    		
    		$insertFBASheetMasterRecord = $this->curdModel->insertTableData(config('constants.GOODS_OUT_FBA_SHEET_MASTER_TABLE'), $insertFBASheetMasterInfo);
    		
    		$this->curdModel->updateTableData(config('constants.IMPORT_SHEET_HISTORY_MASTER_TABLE'), ['e_status' => config('constants.PROCESSING_STATUS') ] , [ 'i_id' => $getPendingRecordDetails->i_id  ]  );
    		
    		$getExcelData = $this->getExcelData($uploadedFilePath);
    		
    		if( isset($getExcelData['status']) && $getExcelData['status'] != false ){
    			$rowDetails = ( isset($getExcelData['data']) ? $getExcelData['data']  : [] );
    			
    			$allExcelErrors = [];
    			if(!empty($rowDetails)){
    				foreach ($rowDetails as $key=> $rowDetail){
    					$excelRecordNo = ( $key + 1 );
    					$rowExcelData = [];
    					foreach( $rowDetail as $rowKey => $rowValue){
    						$rowKey = strtolower( trim($rowKey) );
    						$rowKey = str_replace(" ", "_", $rowKey);
    						$rowValue = ( trim($rowValue) );
    						switch (trim($rowKey)){
    							case 'fba_/_po_or_invoice_/_wh_ref._no.':
    								$rowExcelData['v_fba_po_no'] = (!empty($rowValue) ? $rowValue : null);
    								break;
    							case 'destination':
    								$rowExcelData['e_destination'] = (!empty($rowValue) ? $rowValue : null);
    								break;
    							case 'ref.id':
    								$rowExcelData['v_ref_id'] = (!empty($rowValue) ? $rowValue : null);
    								break;
    							case 'company':
    								$rowExcelData['v_company_code'] = (!empty($rowValue) ? $rowValue : null);
    								break;
    							case 'product':
    								$rowExcelData['v_product'] = (!empty($rowValue) ? $rowValue : null);
    								break;
    							case 'location':
    								$rowExcelData['v_location_code'] = (!empty($rowValue) ? $rowValue : null);
    								break;
    							case 'sku':
    								$rowExcelData['v_sku'] = (!empty($rowValue) ? $rowValue : null);
    								break;
    							case 'units':
    								$rowExcelData['v_units'] = (!empty($rowValue) ? $rowValue : null);
    								break;
    							case 'amazon_address':
    								$rowExcelData['v_amazon_address'] = (!empty($rowValue) ? $rowValue : null);
    								break;
    							case 'boxes(units)':
    								$rowExcelData['i_boxes_units'] = (!empty($rowValue) ? $rowValue : null);
    								break;
    							case 'boxes':
    								$rowExcelData['v_boxes'] = (!empty($rowValue) ? $rowValue : null);
    								break;
    							case 'pallet':
    								$rowExcelData['v_pallet'] = (!empty($rowValue) ? $rowValue : null);
    								break;
    							case 'total_no_of_pallets':
    								$rowExcelData['i_total_no_of_pallets'] = (!empty($rowValue) ? $rowValue : null);
    								break;
    							case 'pallet_dimension':
    								$rowExcelData['v_pallet_dimension'] = (!empty($rowValue) ? $rowValue : null);
    								break;
    							case 'pallet_weight_(kg)':
    								$rowExcelData['v_pallet_weight'] = (!empty($rowValue) ? $rowValue : null);
    								break;
    							case 'pallet_number':
    								$rowExcelData['i_pallet_no'] = (!empty($rowValue) ? $rowValue : null);
    								break;
    							case 'fba_value':
    								$rowExcelData['v_fba_value'] = (!empty($rowValue) ? $rowValue : null);
    								break;
    						}
    			
    					}
    			
    					if((!empty(array_filter($rowExcelData)))){
    						$masterExcelData[] = $rowExcelData;
    					}
    				}
    			
    			}
    			
    			$fbaNoWhere = [];
    			$fbaNoWhere['sheet_detail.t_is_deleted != '] = 1;
    			$fbaNoWhere['sheet_master.i_country_to_port_goods_out_master_id != '] = $getPendingRecordDetails->i_country_to_port_goods_out_master_id;;
    			$allGetAllPreviousFBANoDetail  = $this->curdModel->selectJoinData(config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE') . ' as sheet_detail', [ 'sheet_detail.v_fba_po_no' ] , [ [ 'tableName' => config('constants.GOODS_OUT_FBA_SHEET_MASTER_TABLE') . ' as sheet_master' , 'condition' => 'sheet_master.i_id =  sheet_detail.i_fba_sheet_master_id'  ] ]  , $fbaNoWhere );
    			$allGetAllPreviousFBANoArray = (!empty($allGetAllPreviousFBANoDetail) ? array_column(objectToArray($allGetAllPreviousFBANoDetail), 'v_fba_po_no') : [] );
    			
    			//$allGetAllPreviousEuropeToUKNoDetail  = $this->curdModel->selectData(config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_DETAIL_TABLE'), [ 'v_invoice_ref_no' ] , [ 't_is_deleted != ' => 1 ] );
    			//$allGetAllPreviousEuropeToUKNoArray = (!empty($allGetAllPreviousEuropeToUKNoDetail) ? array_column(objectToArray($allGetAllPreviousEuropeToUKNoDetail), 'v_invoice_ref_no') : [] );
    			$allGetAllPreviousInternalTransferArray    = $allGetAllPreviousEuropeToUKNoArray = [];
    			//$allGetAllPreviousInternalTransferDetail  = $this->curdModel->selectData(config('constants.GOODS_OUT_EUROPE_TRANSFER_DETAIL_TABLE'), [ 'v_invoice_ref_no' ] , [ 't_is_deleted != ' => 1 ] );
    			//$allGetAllPreviousInternalTransferArray = (!empty($allGetAllPreviousInternalTransferDetail) ? array_column(objectToArray($allGetAllPreviousInternalTransferDetail), 'v_invoice_ref_no') : [] );
    			
    			$allCompanyCodeDetails = CompanyMasterModel::where('t_is_deleted' , 0 )->get();
    			$allCustomerCodeDetails = CustomerDetailModel::where('t_is_deleted' , 0 )->get();
    			$allCustomerMasterDetails = CustomerMasterModel::where('t_is_deleted' , 0 )->get();
    			$allWarehouseDetails = WarehouseMasterModel::where('t_is_deleted' , 0 )->where('e_record_type' , config('constants.WAREHOUSE'))->where('i_country_id' ,config('constants.USA') )->get();
    			$allLocationDetails = WarehouseMasterModel::where('t_is_deleted' , 0 )->where('e_record_type' , config('constants.LOCATION'))->get();
    			
    			$allCompanyShortCodeInfo = (!empty($allCompanyCodeDetails) ? array_column(objectToArray($allCompanyCodeDetails), 'v_company_short_code') : [] );
    			$allCustomerCodeInfo = (!empty($allCustomerCodeDetails) ? array_column(objectToArray($allCustomerCodeDetails), 'v_customer_code') : [] );
    			$allCustomerNameMasterDetails = (!empty($allCustomerMasterDetails) ? array_column(objectToArray($allCustomerMasterDetails), 'v_customer_name') : [] );
    			$allWarehouseCodeInfo = (!empty($allWarehouseDetails) ? array_column(objectToArray($allWarehouseDetails), 'v_warehouse_code') : [] );
    			$allLocationCodeInfo = (!empty($allLocationDetails) ? array_column(objectToArray($allLocationDetails), 'v_warehouse_code') : [] );
    			
    			//echo "<pre>";print_r($masterExcelData);die;
    			
    			$allChildRecordIds = [];
    			if(!empty($masterExcelData)){
    				DB::beginTransaction();
    				foreach($masterExcelData as $recordKey =>  $masterExcel){
    					
    					$rowData = [];
    					$rowData['v_fba_po_no'] = ( isset($masterExcel['v_fba_po_no']) ? $masterExcel['v_fba_po_no'] : "" );
    					
    					$excelRecordNo = ( $recordKey + 2 );
    					$company = ( isset($masterExcel['v_company_code']) ? $masterExcel['v_company_code'] : "" );
    					$location = ( isset($masterExcel['v_location_code']) ? $masterExcel['v_location_code'] : "" );
    					
    					if(!empty($masterExcel['e_destination'])){
    						switch(strtolower($masterExcel['e_destination'])){
    							case strtolower(config('constants.AMAZON_FBA_SHEET')):
    								if( empty($company) ) {
    									$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.company')  , 'srNo' => $excelRecordNo ] ) ;
    								}
    								
    								if( !in_array($company,$allCompanyShortCodeInfo) ) {
    									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-info', [ 'columnName' => trans('messages.company')  , 'srNo' => $excelRecordNo ] ) ;
    								} else {
    									$searchCompanyCodeKey = array_search($company, $allCompanyShortCodeInfo);
    									if(strlen($searchCompanyCodeKey) > 0 ){
    										$rowData['i_amazon_company_short_code_id'] = ( isset($allCompanyCodeDetails[$searchCompanyCodeKey]->i_id) ? $allCompanyCodeDetails[$searchCompanyCodeKey]->i_id : null );
    									}
    								}
    								
    								if( empty($location) ) {
    									$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.location')  , 'srNo' => $excelRecordNo ] ) ;
    								}
    								if( !in_array($location,$allLocationCodeInfo) ) {
    									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-info', [ 'columnName' => trans('messages.location')  , 'srNo' => $excelRecordNo ] ) ;
    								} else {
    									$searchLocationCodeKey = array_search($location, $allLocationCodeInfo);
    									if(strlen($searchLocationCodeKey) > 0 ){
    										$rowData['i_amazon_location_code_id'] = ( isset($allLocationDetails[$searchLocationCodeKey]->i_id) ? $allLocationDetails[$searchLocationCodeKey]->i_id : null );
    									}
    								} 
    								break;
    							case strtolower(config('constants.WAREHOUSE_FBA_SHEET')):
    								
    								if( empty($location) ) {
    									$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.location')  , 'srNo' => $excelRecordNo ] ) ;
    								}
    								if( !in_array($location,$allWarehouseCodeInfo) ) {
    									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-info', [ 'columnName' => trans('messages.location')  , 'srNo' => $excelRecordNo ] ) ;
    								} else {
    									$searchUSALocationCodeKey = array_search($location, $allWarehouseCodeInfo);
    									if(strlen($searchUSALocationCodeKey) > 0 ){
    										$rowData['i_warehouse_warehouse_code_id'] = ( isset($allWarehouseDetails[$searchUSALocationCodeKey]->i_id) ? $allWarehouseDetails[$searchUSALocationCodeKey]->i_id : null );
    									}
    								}
    								break;
    							case strtolower(config('constants.CUSTOMER_FBA_SHEET')):
    								if( empty($company) ) {
    									$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.company')  , 'srNo' => $excelRecordNo ] ) ;
    								}
    								if( !in_array($company,$allCustomerNameMasterDetails) ) {
    									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-info', [ 'columnName' => trans('messages.company')  , 'srNo' => $excelRecordNo ] ) ;
    								} else {
    									$searchLocationCustomerCodeNameKey = array_search($company, $allCustomerNameMasterDetails);
    									if(strlen($searchLocationCustomerCodeNameKey) > 0 ){
    										$rowData['i_customer_company_name_id'] = ( isset($allCustomerMasterDetails[$searchLocationCustomerCodeNameKey]->i_id) ? $allCustomerMasterDetails[$searchLocationCustomerCodeNameKey]->i_id : null );
    									}
    								}
    								
    								if( empty($location) ) {
    									$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.location')  , 'srNo' => $excelRecordNo ] ) ;
    								}
    								if( !in_array($location,$allCustomerCodeInfo) ) {
    									$allExcelErrors[] = trans ( 'messages.error-sheet-row-not-into-master-info', [ 'columnName' => trans('messages.location')  , 'srNo' => $excelRecordNo ] ) ;
    								} else {
    									$searchLocationCustomerCodeCodeKey = array_search($location, $allCustomerCodeInfo);
    									if(strlen($searchLocationCustomerCodeCodeKey) > 0 ){
    										$rowData['i_customer_customer_code_id'] = ( isset($allCustomerCodeDetails[$searchLocationCustomerCodeCodeKey]->i_id) ? $allCustomerCodeDetails[$searchLocationCustomerCodeCodeKey]->i_id : null );
    									}
    								}
    								break;
    							default:
    								$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.destination')  , 'srNo' => $excelRecordNo ] ) ;
    								break;
    						}
    					}
    					if(empty($masterExcel['e_destination'])){
    						$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.destination')  , 'srNo' => $excelRecordNo ] ) ;
    					}
    					if(empty($masterExcel['v_fba_po_no'])){
    						$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.fba-sheet')  , 'srNo' => $excelRecordNo ] ) ;
    					}
    					if(empty($masterExcel['v_ref_id'])){
    						$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.ref-id')  , 'srNo' => $excelRecordNo ] ) ;
    					}
    					if(empty($masterExcel['v_location_code'])){
    						$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.location')  , 'srNo' => $excelRecordNo ] ) ;
    					}
    					if( isset($masterExcel['v_fba_po_no']) && (!empty($masterExcel['v_fba_po_no'])) && ( in_array($masterExcel['v_fba_po_no'], $allGetAllPreviousFBANoArray)) ){
    						$allExcelErrors[] = trans ( 'messages.error-duplicate-sheet-row-info', [ 'columnName' => trans('messages.fba-po-invoice')  , 'srNo' => $excelRecordNo ] ) ;
    					}
    				/* 	if(in_array($masterExcel['v_fba_po_no'], $allGetAllPreviousEuropeToUKNoArray)){
    						$allExcelErrors[] = trans ( 'messages.error-duplicate-sheet-row-info', [ 'columnName' => trans('messages.fba-po-invoice')  , 'srNo' => $excelRecordNo ] ) ;
    					}
    					if(in_array($masterExcel['v_fba_po_no'], $allGetAllPreviousInternalTransferArray)){
    						$allExcelErrors[] = trans ( 'messages.error-duplicate-sheet-row-info', [ 'columnName' => trans('messages.fba-po-invoice')  , 'srNo' => $excelRecordNo ] ) ;
    					} */
    					
    					if(!empty($allExcelErrors)){
    						continue;
    					}
    					
    					$rowData['i_fba_sheet_master_id'] = $insertFBASheetMasterRecord;
    					$rowData['v_fba_po_no'] = ( isset($masterExcel['v_fba_po_no']) ? $masterExcel['v_fba_po_no'] : "" );
    					$rowData['e_destination'] = ( isset($masterExcel['e_destination']) ? $masterExcel['e_destination'] : "" );
    					$rowData['v_ref_id'] = ( isset($masterExcel['v_ref_id']) ? $masterExcel['v_ref_id'] : "" );
    					$rowData['v_company_code'] = ( isset($masterExcel['v_company_code']) ? $masterExcel['v_company_code'] : "" );
    					$rowData['v_location_code'] = ( isset($masterExcel['v_location_code']) ? $masterExcel['v_location_code'] : "" );
    					$rowData['v_product'] = ( isset($masterExcel['v_product']) ? $masterExcel['v_product'] : "" );
    					$rowData['v_sku'] = ( isset($masterExcel['v_sku']) ? $masterExcel['v_sku'] : null );
    					$rowData['v_units'] = ( isset($masterExcel['v_units']) ? $masterExcel['v_units'] : null );
    					$rowData['v_amazon_address'] = ( isset($masterExcel['v_amazon_address']) ? $masterExcel['v_amazon_address'] : null );
    					$rowData['i_boxes_units'] = ( isset($masterExcel['i_boxes_units']) ? $masterExcel['i_boxes_units'] : null );
    					$rowData['v_boxes'] = ( isset($masterExcel['v_boxes']) ? $masterExcel['v_boxes'] : null );
    					$rowData['v_pallet'] = ( isset($masterExcel['v_pallet']) ? $masterExcel['v_pallet'] : null );
    					$rowData['i_total_no_of_pallets'] = ( isset($masterExcel['i_total_no_of_pallets']) ? $masterExcel['i_total_no_of_pallets'] : null );
    					$rowData['v_pallet_dimension'] = ( isset($masterExcel['v_pallet_dimension']) ? $masterExcel['v_pallet_dimension'] : null );
    					$rowData['v_pallet_weight'] = ( isset($masterExcel['v_pallet_weight']) ? $masterExcel['v_pallet_weight'] : null );
    					$rowData['i_pallet_no'] = ( isset($masterExcel['i_pallet_no']) ? $masterExcel['i_pallet_no'] : null );
    					$rowData['v_fba_value'] = ( isset($masterExcel['v_fba_value']) ? $masterExcel['v_fba_value'] : null );
    					//echo "<pre>";print_r($rowData);echo "<br><br>";
    					$insertFBADetailRecord = $this->curdModel->insertTableData(config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE'), $rowData);
    					$allChildRecordIds[] = $insertFBADetailRecord;
    					//echo $this->curdModel->last_query();echo "<br><br>";
    				
    					
    				}
    				
    				//echo "<pre>";print_r($allExcelErrors);die;
    				if(!empty($allExcelErrors)){
    					DB::rollback();
    					$updateFileInfo = [];
    					$updateFileInfo['e_status'] = config('constants.FAILED_STATUS');
    					$updateFileInfo['v_response'] = implode("," , $allExcelErrors );
    					$this->curdModel->updateTableData(config('constants.IMPORT_SHEET_HISTORY_MASTER_TABLE'), $updateFileInfo , [ 'i_id' => $getPendingRecordDetails->i_id ] ) ;
    					$this->curdModel->updateTableData(config('constants.GOODS_OUT_FBA_SHEET_MASTER_TABLE'), $updateFileInfo , [ 'i_id' => $insertFBASheetMasterRecord ] ) ;
    				} else {
    					DB::table(config('constants.GOODS_OUT_FBA_SHEET_MASTER_TABLE'))->where('i_country_to_port_goods_out_master_id', $getPendingRecordDetails->i_country_to_port_goods_out_master_id)->where('i_id', '!=' ,  $insertFBASheetMasterRecord)->update([ 't_is_active' => 0 , 't_is_deleted' => 1 , 'i_deleted_id' => 999 , 'dt_deleted_at' => date('Y-m-d H:i:s')  ] );
    					
    					//$this->curdModel->deleteTableData(config('constants.GOODS_OUT_FBA_SHEET_MASTER_TABLE'), [ 't_is_active' => 0 , 't_is_deleted' => 1  ] , [ 'i_country_to_port_goods_out_master_id' => $getPendingRecordDetails->i_country_to_port_goods_out_master_id , 'i_id != ' => $insertFBASheetMasterRecord ] ) ;
    					$fbaNoWhere = [];
    					$fbaNoWhere['sheet_detail.t_is_deleted != '] = 1;
    					$fbaNoWhere['sheet_master.i_country_to_port_goods_out_master_id'] = $getPendingRecordDetails->i_country_to_port_goods_out_master_id;;
    					$allGetAllPreviousFBANoDetail  = $this->curdModel->selectJoinData(config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE') . ' as sheet_detail', [ 'sheet_detail.v_fba_po_no' , 'sheet_detail.i_id' ] , [ [ 'tableName' => config('constants.GOODS_OUT_FBA_SHEET_MASTER_TABLE') . ' as sheet_master' , 'condition' => 'sheet_master.i_id =  sheet_detail.i_fba_sheet_master_id'  ] ]  , $fbaNoWhere );
    					$allGetAllPreviousFBANoIdArray = (!empty($allGetAllPreviousFBANoDetail) ? array_column(objectToArray($allGetAllPreviousFBANoDetail), 'i_id') : [] );
    						
    					if(!empty($allGetAllPreviousFBANoIdArray)){
    						if(!empty($allChildRecordIds)){
    							DB::table(config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE'))->whereIn('i_id', $allGetAllPreviousFBANoIdArray)->whereNotIn('i_id' , $allChildRecordIds )->update([ 't_is_active' => 0 , 't_is_deleted' => 1 , 'i_deleted_id' => 999 , 'dt_deleted_at' => date('Y-m-d H:i:s')  ] );
    						} else {
    							DB::table(config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE'))->whereIn('i_id', $allGetAllPreviousFBANoIdArray)->update([ 't_is_active' => 0 , 't_is_deleted' => 1 , 'i_deleted_id' => 999 , 'dt_deleted_at' => date('Y-m-d H:i:s')  ] );
    						}
    						
    					}
    					
    					DB::commit();
    					$updateFileInfo = [];
    					$updateFileInfo['e_status'] = config('constants.SUCCESS_STATUS');
    					$this->curdModel->updateTableData(config('constants.IMPORT_SHEET_HISTORY_MASTER_TABLE'), $updateFileInfo , [ 'i_id' => $getPendingRecordDetails->i_id ] ) ;
    					$this->curdModel->updateTableData(config('constants.GOODS_OUT_FBA_SHEET_MASTER_TABLE'), $updateFileInfo , [ 'i_id' => $insertFBASheetMasterRecord ] ) ;
    				}
    				
    			} else {
    				
    				DB::rollback();
    				$updateFileInfo = [];
    				$updateFileInfo['e_status'] = config('constants.FAILED_STATUS');
    				$updateFileInfo['v_response'] = trans('messages.no-data-found-for-import');
    				$this->curdModel->updateTableData(config('constants.IMPORT_SHEET_HISTORY_MASTER_TABLE'), $updateFileInfo , [ 'i_id' => $getPendingRecordDetails->i_id ] ) ;
    				$this->curdModel->updateTableData(config('constants.GOODS_OUT_FBA_SHEET_MASTER_TABLE'), $updateFileInfo , [ 'i_id' => $insertFBASheetMasterRecord ] ) ;
    			}
    		} else {
    			$updateFileInfo = [];
    			$updateFileInfo['e_status'] = config('constants.FAILED_STATUS');
    			$updateFileInfo['v_response'] = trans('messages.no-data-found-for-import');
    			$this->curdModel->updateTableData(config('constants.IMPORT_SHEET_HISTORY_MASTER_TABLE'), $updateFileInfo , [ 'i_id' => $getPendingRecordDetails->i_id ] ) ;
    			$this->curdModel->updateTableData(config('constants.GOODS_OUT_FBA_SHEET_MASTER_TABLE'), $updateFileInfo , [ 'i_id' => $insertFBASheetMasterRecord ] ) ;
    		}
    	}
    }
    
    public function logoutEntry(){
    	$this->curdModel =  New BaseModel();
    	$duration =( config('constants.LOGOUT_TIME_DURATION') + config('constants.FORCE_LOGOUT_AFTER_POPUP') ) . ' minute';
    	$getUserWhereRaw = "dt_login_time <= now() - interval ".$duration." and dt_logout_time is null and t_is_deleted = 0";
    	$getLoginUserDetails = LoginHistory::whereRaw($getUserWhereRaw)->get();
    	 
    	$loginCookieName = config('constants.LOGIN_COOKIE_NAME');
    	if(!empty($getLoginUserDetails)){
    		foreach($getLoginUserDetails as $getLoginUserDetail){
    			removeSession($getLoginUserDetail->i_login_id , $getLoginUserDetail->i_session_id );
    			$this->curdModel->updateTableData(config('constants.LOGIN_HISTORY_TABLE'), [ 'dt_logout_time' => date('Y-m-d H:i:s') , 'skip_id_address' => true ], [ 'i_id' => $getLoginUserDetail->i_id ] );
    			 
    			Log::info("remove sessio  of  login histopry id = " . $getLoginUserDetail->i_id);
    		}
    	}
    }
    
    public function managExistingBuyerRecord(){
    	$this->curdModel =  New BaseModel();
    	$getAllBuyerDetails = $this->curdModel->selectData( config('constants.GOODS_IN_BUYER_DETAIL_TABLE') , [ 'i_id' ] );
    	if(!empty($getAllBuyerDetails)){
    		foreach($getAllBuyerDetails as $getAllBuyerDetail){
    			$allGoodsInBuyerDetailId = $getAllBuyerDetail->i_id;
    			$allGoodInLogisticDetails = GoodInLogisticMasterModel::whereRaw(  "find_in_set( '".$allGoodsInBuyerDetailId."'  , i_goods_in_buyer_detail_id   )" )->get();
    				
    			$displayGoodInBuyerRecord = false;
    			$rowData = [];
    			$rowData['t_is_all_delivered_cancelled_ststus'] = 1;
    			if(count($allGoodInLogisticDetails) > 0 ){
    				if(!empty($allGoodInLogisticDetails)){
    					foreach($allGoodInLogisticDetails as $allGoodInLogisticDetail){
    						if((!empty($allGoodInLogisticDetail->i_status_id)) &&  !in_array( $allGoodInLogisticDetail->i_status_id , [ config("constants.DELIVERED_STATUS_ID") , config("constants.STATIC_STATUS_CANCELLED_ID")  ] ) ){
    							$displayGoodInBuyerRecord = true;
    						}
    			
    					}
    				}
    			} else {
    				$rowData['t_is_all_delivered_cancelled_ststus'] = 0;
    			}
    			if($displayGoodInBuyerRecord == true ){
    				$rowData['t_is_all_delivered_cancelled_ststus'] = 0;
    			}
    			$this->curdModel->updateTableData( config('constants.GOODS_IN_BUYER_DETAIL_TABLE') , $rowData, [ 'i_id' => $allGoodsInBuyerDetailId]);
    			
    		}
    	}
    }
	
    public function usWarehouseToAmazonFBADetails(){
    	$this->curdModel = new UsWarehouseToAmazonMasterModel();
    	$usWarehouseToAmazonMasterDetails = $this->curdModel->getRecordDetails(['count_record'=>true]);
    	
    	if(!empty($usWarehouseToAmazonMasterDetails)){
    		foreach ($usWarehouseToAmazonMasterDetails as $usWarehouseToAmazonMasterDetail){
    			
    			$usWarehouseMasterId = (!empty($usWarehouseToAmazonMasterDetail->i_id) ? $usWarehouseToAmazonMasterDetail->i_id :'');
    			echo "id = ".$usWarehouseMasterId;echo "<br><br>" ;
    			if(!empty($usWarehouseToAmazonMasterDetail->usWarehouseToAmazonDetails)){
    				foreach ($usWarehouseToAmazonMasterDetail->usWarehouseToAmazonDetails as $usWarehouseToAmazonDetail){
    					$recordData = [];
    					
    					$bookingDate = (!empty($usWarehouseToAmazonDetail->dt_booking_date) ? $usWarehouseToAmazonDetail->dt_booking_date : null);
    					$collectionDate = (!empty($usWarehouseToAmazonDetail->dt_collection_date) ? $usWarehouseToAmazonDetail->dt_collection_date : null);
    					$deliveryDate = (!empty($usWarehouseToAmazonDetail->dt_delivery_date) ? $usWarehouseToAmazonDetail->dt_delivery_date : null);
    					$remarks = (!empty($usWarehouseToAmazonDetail->v_remarks) ? $usWarehouseToAmazonDetail->v_remarks : null);
    					$trackingLink = (!empty($usWarehouseToAmazonDetail->v_tracking_link) ? $usWarehouseToAmazonDetail->v_tracking_link : null);
    					$amazonAppointmentDate = (!empty($usWarehouseToAmazonDetail->dt_amazon_appointment_date) ? $usWarehouseToAmazonDetail->dt_amazon_appointment_date : null);
    					$amazonAppointmentId = (!empty($usWarehouseToAmazonDetail->v_amazon_appointment_id) ? $usWarehouseToAmazonDetail->v_amazon_appointment_id : null);
    					$boxPalletType = (!empty($usWarehouseToAmazonDetail->e_box_pallet_type) ? $usWarehouseToAmazonDetail->e_box_pallet_type : null);
    					$totalNoOfPallets = (!empty($usWarehouseToAmazonDetail->i_total_no_of_pallets) ? $usWarehouseToAmazonDetail->i_total_no_of_pallets : null);
    						
    					$recordData['dt_booking_date'] = (!empty($usWarehouseToAmazonMasterDetail->dt_booking_date) ? $usWarehouseToAmazonMasterDetail->dt_booking_date : $bookingDate );
    					$recordData['dt_collection_date'] = (!empty($usWarehouseToAmazonMasterDetail->dt_collection_date) ? $usWarehouseToAmazonMasterDetail->dt_collection_date : $collectionDate);
    					$recordData['dt_delivery_date'] = (!empty($usWarehouseToAmazonMasterDetail->dt_delivery_date) ? $usWarehouseToAmazonMasterDetail->dt_delivery_date : $deliveryDate);
    					$recordData['v_remarks'] = (!empty($usWarehouseToAmazonMasterDetail->v_remarks) ? $usWarehouseToAmazonMasterDetail->v_remarks : $remarks);
    					$recordData['v_tracking_link'] = (!empty($usWarehouseToAmazonMasterDetail->v_tracking_link) ? $usWarehouseToAmazonMasterDetail->v_tracking_link : $trackingLink);
    					$recordData['dt_amazon_appointment_date'] = (!empty($usWarehouseToAmazonMasterDetail->dt_amazon_appointment_date) ? $usWarehouseToAmazonMasterDetail->dt_amazon_appointment_date : $amazonAppointmentDate);
    					$recordData['v_amazon_appointment_id'] = (!empty($usWarehouseToAmazonMasterDetail->v_amazon_appointment_id) ? $usWarehouseToAmazonMasterDetail->v_amazon_appointment_id : $amazonAppointmentId);
    					$recordData['e_box_pallet_type'] = (!empty($usWarehouseToAmazonMasterDetail->e_box_pallet_type) ? $usWarehouseToAmazonMasterDetail->e_box_pallet_type : $boxPalletType);
    					$recordData['i_total_no_of_pallets'] = (!empty($usWarehouseToAmazonMasterDetail->i_total_no_of_pallets) ? $usWarehouseToAmazonMasterDetail->i_total_no_of_pallets : $totalNoOfPallets);
    					
    					$this->curdModel->updateTableData( config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE') , $recordData, [ 'i_us_warehouse_to_amazon_master_id' => $usWarehouseMasterId]);
    					
    				}
    			}
    		}
    	}
    }
    public function toAmazonFBADetails(){
    	$this->curdModel = new CountrytoPortEuropeModel();
    	$toAmazonMasterDetails = $this->curdModel->getRecordDetails(['count_record'=>true]);
    	
    	if(!empty($toAmazonMasterDetails)){
    		foreach ($toAmazonMasterDetails as $toAmazonMasterDetail){
    			$toAmazonMasterId = (!empty($toAmazonMasterDetail->i_id) ? $toAmazonMasterDetail->i_id :'');
    			echo "id = ".$toAmazonMasterId;echo "<br><br>" ;
    			if(!empty($toAmazonMasterDetail->detailInfo)){
    				foreach ($toAmazonMasterDetail->detailInfo as $toAmazonMasterDetailInfo){
    					$recordData = [];
    					$bookingDate = (!empty($toAmazonMasterDetail->dt_booking_date) ? $toAmazonMasterDetail->dt_booking_date : null);
    					$collectionDate = (!empty($toAmazonMasterDetail->dt_collection_date) ? $toAmazonMasterDetail->dt_collection_date : null);
    					$deliveryDate = (!empty($toAmazonMasterDetail->dt_delivery_date) ? $toAmazonMasterDetail->dt_delivery_date : null);
    					$trackingLink = (!empty($toAmazonMasterDetail->v_tracking_link) ? $toAmazonMasterDetail->v_tracking_link : null);
    					$amazonAppointmentDate = (!empty($toAmazonMasterDetail->dt_amazon_appointment_date) ? $toAmazonMasterDetail->dt_amazon_appointment_date : null);
    					$amazonAppointmentId = (!empty($toAmazonMasterDetail->v_amazon_appointment_id) ? $toAmazonMasterDetail->v_amazon_appointment_id : null);
    					
    					$recordData['dt_booking_date'] = (!empty($toAmazonMasterDetailInfo->dt_booking_date) ? $toAmazonMasterDetailInfo->dt_booking_date : $bookingDate );
    					$recordData['dt_collection_date'] = (!empty($toAmazonMasterDetailInfo->dt_collection_date) ? $toAmazonMasterDetailInfo->dt_collection_date : $collectionDate);
    					$recordData['dt_delivery_date'] = (!empty($toAmazonMasterDetailInfo->dt_delivery_date) ? $toAmazonMasterDetailInfo->dt_delivery_date : $deliveryDate);
    					$recordData['v_tracking_link'] = (!empty($toAmazonMasterDetailInfo->v_tracking_link) ? $toAmazonMasterDetailInfo->v_tracking_link : $trackingLink);
    					$recordData['dt_amazon_appointment_date'] = (!empty($toAmazonMasterDetailInfo->dt_amazon_appointment_date) ? $toAmazonMasterDetailInfo->dt_amazon_appointment_date : $amazonAppointmentDate);
    					$recordData['v_amazon_appointment_id'] = (!empty($toAmazonMasterDetailInfo->v_amazon_appointment_id) ? $toAmazonMasterDetailInfo->v_amazon_appointment_id : $amazonAppointmentId);
    					
    					$this->curdModel->updateTableData( config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_DETAIL_TABLE') , $recordData, [ 'i_country_to_port_europe_goods_master_id' => $toAmazonMasterId]);
    				}
    			}
    		}
    	}
    }
    
    public function updateEuropeTransferFormToWarehouse(){
    	$crudModel = new CountrytoPortEuropeTransferDetailModel();
    	$recordDetails = $crudModel->groupBy('i_europe_transfer_master_id')->get();
    	
    	$result = false;
    	if (!empty($recordDetails)){
    		foreach ($recordDetails as $recordDetail){
    			$europeTransferMasterId = (!empty($recordDetail->i_europe_transfer_master_id) ? $recordDetail->i_europe_transfer_master_id : 0);
    			$whereData = $updateData = [];
    			$updateData['i_from_warehouse_id'] = (!empty($recordDetail->i_warehouse_id) ? $recordDetail->i_warehouse_id : 0);
    			$updateData['i_to_warehouse_id'] = (!empty($recordDetail->i_location_id) ? $recordDetail->i_location_id : 0);
    			$result = $crudModel->updateTableData(config('constants.GOODS_OUT_EUROPE_TRANSFER_MASTER_TABLE'), $updateData, ['i_id' => $europeTransferMasterId]);
    		}
    	}
    	
    	if ($result != false){
    		echo 'i_from_warehouse_id & i_to_warehouse_id updated successfully!';
    	}else {
    		echo 'error occur while update columns';
    	}
    }
    
    public function getBuyerDetailsId(){
    	$goodInBuyerDetails = GoodInBuyerDetailModel::get();
    	$this->crudModel = new CountrytoPortEuropeModel();
    	
    	$buyerDetailsId = [];
    	if (!empty($goodInBuyerDetails)){
    		foreach ($goodInBuyerDetails as $goodInBuyerDetail){
    			if (!empty($goodInBuyerDetail->i_id)){
    				$query = GoodInLogisticMasterModel::whereRaw("find_in_set('".$goodInBuyerDetail->i_id."',i_goods_in_buyer_detail_id)")->where('t_is_deleted',0);
    				$result = $query->get();
    				
    				$where = [];
    				if (count($result) == 0){
    					$buyerDetailsId[] = $goodInBuyerDetail->i_id;
    					$where['t_in_use'] = 0;
    				} else {
    					$where['t_in_use'] = 1;
    				}
    				$this->crudModel->updateTableData(config('constants.GOODS_IN_BUYER_DETAIL_TABLE'), $where, ['i_id' => $goodInBuyerDetail->i_id]);
    			}
    		}
    	}
    	
    	var_dump(count($buyerDetailsId));die;
    	echo '<pre>';print_r($buyerDetailsId);die;
    }
    
    public function deleteGoodInBuyerMasterDataBasedOnDetailTable(){
    	
    	$goodInBuyerMasterDeleteData['t_is_active'] = 0;
    	$goodInBuyerMasterDeleteData['t_is_deleted'] = 1;
    	
    	$goodInBuyerMasterDetails = GoodInBuyerMasterModel::select('i_id')->where('t_is_deleted',0)->get();
    	
    	$rowAffected = 0;
    	
    	if (!empty($goodInBuyerMasterDetails)){
    		foreach ($goodInBuyerMasterDetails as $goodInBuyerMasterDetail){
    			$goodInBuyerDeletedRecordDetails = GoodInBuyerDetailModel::where('i_goods_in_buyer_master_id' , $goodInBuyerMasterDetail->i_id)->where('t_is_deleted',0)->get();
    			if (count($goodInBuyerDeletedRecordDetails) == 0){
    				$result = $this->baseModel->deleteTableData(config('constants.GOODS_IN_BUYER_MASTER_TABLE') , $goodInBuyerMasterDeleteData ,  ['i_id' => $goodInBuyerMasterDetail->i_id]);
    				if ($result != false){
    					++$rowAffected;
    				}
    			}
    		}
    	}
    	echo $rowAffected . ' row affected';die;
    }
    
    public function updateNewMasterInfoGoodIn() {
    	
    	Log::info("update new master detail in good in cron start");
    	
    	$getGoodInBuyerMasterDetails = GoodInBuyerMasterModel::where('t_is_deleted', 0)->get();
    	$getAllLookupDetails = LookupMaster::whereIn('v_module_name', [config('constants.PAYMENT_TERMS_LOOKUP'), config('constants.DANGEROUS_GOODS_LOOKUP'), config('constants.GOODS_REMARK_LOOKUP')])->get();
    	
    	$getAllLookupDetails = collect($getAllLookupDetails);
    	
    	$getUpdateArray = [];
    	
    	$result = false;
    	DB::beginTransaction();
    	
    	foreach ($getGoodInBuyerMasterDetails as $getGoodInBuyerMasterDetail){
    		$paymentTerm = isset($getGoodInBuyerMasterDetail->v_payment_remark) && !empty($getGoodInBuyerMasterDetail->v_payment_remark) ? $getGoodInBuyerMasterDetail->v_payment_remark : '';
    		$dangerousGoods = isset($getGoodInBuyerMasterDetail->e_dangerous_goods) && !empty($getGoodInBuyerMasterDetail->e_dangerous_goods) ? $getGoodInBuyerMasterDetail->e_dangerous_goods : '';
    		$goodsRemarks = isset($getGoodInBuyerMasterDetail->v_goods_remarks) && !empty($getGoodInBuyerMasterDetail->v_goods_remarks) ? $getGoodInBuyerMasterDetail->v_goods_remarks : '';
    		
    		$rowUpdateData = [];
    		
    		$recordId = (isset($getGoodInBuyerMasterDetail->i_id) && !empty($getGoodInBuyerMasterDetail->i_id) ? $getGoodInBuyerMasterDetail->i_id : '');
    		if(isset($paymentTerm) && !empty($paymentTerm)){    			
    			$getTermInfo = isset($getAllLookupDetails) && !empty($getAllLookupDetails) ? $getAllLookupDetails->where('v_module_name', config('constants.PAYMENT_TERMS_LOOKUP'))->where('v_value', $paymentTerm)->first() : [];
    			
    			if (isset($getTermInfo) && !empty($getTermInfo)){
    				$rowUpdateData['i_payment_terms_id'] = isset($getTermInfo->i_id) && !empty($getTermInfo->i_id) ? $getTermInfo->i_id : 0;   				
    			}
    		}
    		
    		if(isset($dangerousGoods) && !empty($dangerousGoods)){    			 
    			$getDangerousGoodsInfo = isset($getAllLookupDetails) && !empty($getAllLookupDetails) ? $getAllLookupDetails->where('v_module_name', config('constants.DANGEROUS_GOODS_LOOKUP'))->where('v_value', $dangerousGoods)->first() : [];
    			 
    			if (isset($getDangerousGoodsInfo) && !empty($getDangerousGoodsInfo)){
    				$rowUpdateData['i_dangerous_goods_id'] = isset($getDangerousGoodsInfo->i_id) && !empty($getDangerousGoodsInfo->i_id) ? $getDangerousGoodsInfo->i_id : 0;
    			}
    		}
    		
    		if(isset($goodsRemarks) && !empty($goodsRemarks)){    			
    			$goodRemarkArray = explode(',', $goodsRemarks);    			
    			
    			$getGoodRemarkIds = [];
    			if(isset($goodRemarkArray) && !empty($goodRemarkArray)){
    				$getGoodRemarkIds = isset($getAllLookupDetails) && !empty($getAllLookupDetails) ? $getAllLookupDetails->where('v_module_name', config('constants.GOODS_REMARK_LOOKUP'))->whereIn('v_value', $goodRemarkArray)->pluck('i_id')->toArray() : [];
    				$getGoodRemarkId = isset($getGoodRemarkIds) && !empty($getGoodRemarkIds) ? implode(',', $getGoodRemarkIds) : '';
    			}    			 
    			 
    			if (isset($getGoodRemarkId) && !empty($getGoodRemarkId)){
    				$rowUpdateData['v_goods_remark_ids'] = isset($getGoodRemarkId) && !empty($getGoodRemarkId) ? $getGoodRemarkId : 0;
    			}
    		}
    		
    		try{
    			$result = $this->baseModel->updateTableData(  config('constants.GOODS_IN_BUYER_MASTER_TABLE') , $rowUpdateData , [ 'i_id' => $recordId ]);
    		}catch (\Exception $e){
    			$result = false;
    			DB::rollBack();
    			Log::error($e->getMessage());
    		}
    		
    		//$getUpdateArray[] = $rowUpdateData; 
    	}
    	
    	if ($result != false){
    		DB::commit();
    	}
    	
    	Log::info("update new master detail in good in cron end");
    	
    }
    
    public function removeImportGoodInBuyerPermission(){
    	$employeeDetails = $this->baseModel->selectData(config('constants.LOGIN_MASTER_TABLE') , ['i_id' , 'v_permission'] , ['v_role' => config('constants.ROLE_USER')]);
    	
    	$result = false;
    	DB::beginTransaction();
    	
    	try{
    		if (!empty($employeeDetails)){
    			foreach ($employeeDetails as $employeeDetail){
    				if (!empty($employeeDetail->v_permission)){
    					$permissionIdArray = explode(',', $employeeDetail->v_permission);
    		
    					$searchKey = array_search(config('permission_constants.ADD_GOODS_IN_BUYER'), $permissionIdArray);
    					if ($searchKey !== false){
    						unset($permissionIdArray[$searchKey]);
    					}
    		
    					$finalPermissionIds = (!empty($permissionIdArray) ? implode(',', $permissionIdArray) : null );
    		
    					$this->baseModel->updateTableData(config('constants.LOGIN_MASTER_TABLE') , ['v_permission' => $finalPermissionIds] , ['i_id' => $employeeDetail->i_id]);
    				}
    			}
    			$result = true;
    		}
    	}catch (\Exception $e){
    		$result = false;
    		DB::rollBack();
    		Log::error($e->getMessage());
    	}
    	
    	if ($result != false){
    		DB::commit();
    	}else {
    		DB::rollBack();
    	}
    }
    
    public function sendMailToPendingDeliveryOfBuyer(){
    	Log::info("Send Mail To Pending Delivery Of Buyer Cron Start");
    	
    	$tableName = config('constants.GOODS_IN_BUYER_MASTER_TABLE');
    	$selectData = [
    			$tableName.'.i_id',
    			$tableName.'.v_po_sales_invoice_no',
    			$tableName.'.e_pallet_box_type',
    			$tableName.'.i_no_of_pallet_box',
    			$tableName.'.dt_delivery_date',
    			$tableName.'.v_buyer_employee_ids',
    			$tableName.'.v_user_buyer_ids',
    			$tableName.'.i_delivery_location_id',
    			$tableName.'.i_main_supplier_id',
    			$tableName.'.t_is_deleted',
    			'gbd.i_id as detail_id',
    			'gbd.t_in_use',
    			'glm.i_id as logistic_id',
    			'glm.i_status_id',
    			'glm.t_is_deleted as log_t_is_deleted',
    			'warehouse.v_warehouse_name',
    			'warehouse.v_warehouse_email',
    			'supplier.v_supplier_name'
    	];
    	
    	$query = GoodInBuyerMasterModel::select($selectData);
    	$query->join(config('constants.GOODS_IN_BUYER_DETAIL_TABLE') . ' as gbd', 'gbd.i_goods_in_buyer_master_id', '=', $tableName.'.i_id');
    	$query->leftJoin(config('constants.GOODS_IN_LOGISTIC_MASTER_TABLE') . ' as glm', 'glm.i_goods_in_buyer_detail_id', '=', 'gbd.i_id');
    	$query->join(config('constants.WAREHOUSE_MASTER_TABLE') . ' as warehouse', $tableName.'.i_delivery_location_id', '=', 'warehouse.i_id');
    	$query->join(config('constants.SUPPLIER_MASTER_TABLE') . ' as supplier', $tableName.'.i_main_supplier_id', '=', 'supplier.i_id');
    	
    	$todayDate = date('Y-m-d');
    	
    	$commonWhereString = $tableName.".t_is_deleted = 0 AND ".$tableName.".dt_delivery_date < '".$todayDate;
    	
    	$query->whereRaw(" gbd.t_is_deleted = 0 AND (( ".$commonWhereString."' AND gbd.t_in_use = 0) OR (".$commonWhereString."' AND glm.i_status_id NOT IN (".config('constants.DELIVERED_STATUS_ID').",".config('constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID').",".config('constants.STATIC_STATUS_CANCELLED_ID').") AND glm.t_is_deleted = 0)) ");
    	$query->where($tableName.'.t_is_deleted', 0);
    	$query->groupBy($tableName.'.i_id');
    	
    	$recordDetails = $query->get();
    	
    	if (!empty($recordDetails)){
    		$finalMailInsertData = [];
    		$dailyCcMailDetails = LookupMaster::where('v_module_name',config('constants.DAILY_MAIL_LOOKUP'))->where('t_is_active',1)->pluck('v_value')->toArray();
    		
    		foreach ($recordDetails as $recordDetail){
    			$poSalesInvoiceNo = (!empty($recordDetail->v_po_sales_invoice_no) ? $recordDetail->v_po_sales_invoice_no : '');
    			$warehouseName = (isset($recordDetail->warehouseMaster) && !empty($recordDetail->warehouseMaster->v_warehouse_name) ? $recordDetail->warehouseMaster->v_warehouse_name : '');
    				
    			$buyerIdsAndEmails = (isset($recordDetail->employeeBuyerNameMaster) && count($recordDetail->employeeBuyerNameMaster) > 0 ? $recordDetail->employeeBuyerNameMaster->pluck('v_email', 'i_id')->toArray() : []);
    			$userbuyerIdsAndEmails = (isset($recordDetail->userBuyerNameMaster) && count($recordDetail->userBuyerNameMaster) > 0 ? $recordDetail->userBuyerNameMaster->pluck('v_email', 'i_id')->toArray() : []);
    			
    			$allMailArray = [];
    	
    			$getBuyerAndUserBuyerMailIds = [];
    			if (!empty($buyerIdsAndEmails)){
    				foreach ($buyerIdsAndEmails as $loginId => $email){
    					if (!empty($email)){
    						$allMailArray[] = $email;
    						$getBuyerAndUserBuyerMailIds[$loginId] = $email;
    					}
    				}
    			}
    	
    			if (!empty($userbuyerIdsAndEmails)){
    				foreach ($userbuyerIdsAndEmails as $loginId => $email){
    					if (!empty($email)){
    						$allMailArray[] = $email;
    						$getBuyerAndUserBuyerMailIds[$loginId] = $email;
    					}
    				}
    			}
    	
    			$warehouseMailArray = (isset($recordDetail->warehouseMaster) && !empty($recordDetail->warehouseMaster->v_warehouse_email) ? explode(',', $recordDetail->warehouseMaster->v_warehouse_email) : '');
    	
    			if (!empty($warehouseMailArray)){
    				$allMailArray = array_merge($allMailArray, $warehouseMailArray);
    			}
    	
    			$allMailArray = array_unique(array_filter($allMailArray));
    			if (!empty($allMailArray)){
    				$mailData = [];
    				$mailData['poSalesInvoiceNo'] = $poSalesInvoiceNo;
    				$mailData['palletsBoxesType'] = (!empty($recordDetail->e_pallet_box_type) ? $recordDetail->e_pallet_box_type : '');
    				$mailData['noOfPalletBox'] = (!empty($recordDetail->i_no_of_pallet_box) ? $recordDetail->i_no_of_pallet_box : '');
    				$mailData['warehouseName'] = $warehouseName;
    				$mailData['supplierName'] = (isset($recordDetail->supplierMaster) && !empty($recordDetail->supplierMaster->v_supplier_name) ? $recordDetail->supplierMaster->v_supplier_name : '');
    				$mailData['buyerDeliveryDate'] = (!empty($recordDetail->dt_delivery_date) ? $recordDetail->dt_delivery_date : '');
    					
    				$config = [];
    				$config['viewName'] = 'admin/daily-buyer-mail';
    				$config['mailData'] = $mailData;
    				$config['subject'] = $poSalesInvoiceNo . ' GOODS WAS NOT DELIVERED TO ' . $warehouseName;
    	
    				$config['receiverEmail'] = $allMailArray;
    	
    				if (!empty($dailyCcMailDetails)){
    					$config['ccEmail'] = array_unique(array_filter($dailyCcMailDetails));
    				}
    					
    				$sendMail = [];
    				try{
    					$sendMail = sendMailSMTP($config);
    				}catch(Exception $e){
    					Log::error($e->getMessage());
    				}
    					
    				$emailHistoryRecord = [];
    				$emailHistoryRecord['i_good_in_buyer_id'] = (!empty($recordDetail->i_id) ? $recordDetail->i_id : null);
    				$emailHistoryRecord['v_subject'] = (!empty($config ['subject']) ? $config ['subject'] : null);
    				$emailHistoryRecord['v_content'] = view('admin/daily-buyer-mail')->with($mailData)->render();
    	
    				if(!empty($sendMail) && isset($sendMail['status']) && $sendMail['status'] != false){
    					$emailHistoryRecord['e_status'] = config('constants.SUCCESS_STATUS');
    				} else {
    					$emailHistoryRecord['e_status'] = config('constants.FAILED_STATUS');
    					$emailHistoryRecord['v_response'] = (!empty($sendMail['msg']) ? $sendMail['msg'] : null);
    				}
    					
    				$buyerAndUserBuyerMailInsertData = [];
    				if (!empty($getBuyerAndUserBuyerMailIds)){
    					$buyerAndUserBuyerMailInsertData = array_map(function($mail) use ($emailHistoryRecord, $getBuyerAndUserBuyerMailIds, $dailyCcMailDetails){
    						$searchKey = array_search($mail, $getBuyerAndUserBuyerMailIds);
    	
    						$insertMailData = [];
    						$insertMailData['i_login_user_id'] = (strlen($searchKey) > 0 ? $searchKey : null);
    						$insertMailData['i_good_in_buyer_id'] = $emailHistoryRecord['i_good_in_buyer_id'];
    						$insertMailData['v_received_email'] = $mail;
    						$insertMailData['v_cc_email'] = (!empty($dailyCcMailDetails) ? implode(',', $dailyCcMailDetails) : '');
    						$insertMailData['v_subject'] = $emailHistoryRecord['v_subject'];
    						$insertMailData['v_content'] = $emailHistoryRecord['v_content'];
    						$insertMailData['v_response'] = (isset($emailHistoryRecord['v_response']) ? $emailHistoryRecord['v_response'] : null);
    						$insertMailData['e_status'] = $emailHistoryRecord['e_status'];
    						$insertMailData = array_merge($insertMailData, $this->baseModel->insertDateTimeData());
    						return $insertMailData;
    					}, $getBuyerAndUserBuyerMailIds);
    				}
    					
    				$deliveryLocationMailInsertData = [];
    				if (!empty($warehouseMailArray)){
    					$deliveryLocationMailInsertData = array_map(function($mail) use ($emailHistoryRecord, $dailyCcMailDetails){
    						$insertMailData = [];
    						$insertMailData['i_login_user_id'] = null;
    						$insertMailData['i_good_in_buyer_id'] = $emailHistoryRecord['i_good_in_buyer_id'];
    						$insertMailData['v_received_email'] = $mail;
    						$insertMailData['v_cc_email'] = (!empty($dailyCcMailDetails) ? implode(',', $dailyCcMailDetails) : '');
    						$insertMailData['v_subject'] = $emailHistoryRecord['v_subject'];
    						$insertMailData['v_content'] = $emailHistoryRecord['v_content'];
    						$insertMailData['v_response'] = (isset($emailHistoryRecord['v_response']) ? $emailHistoryRecord['v_response'] : null);
    						$insertMailData['e_status'] = $emailHistoryRecord['e_status'];
    						$insertMailData = array_merge($insertMailData, $this->baseModel->insertDateTimeData());
    						return $insertMailData;
    					}, $warehouseMailArray);
    				}
    				
    				if (!empty($buyerAndUserBuyerMailInsertData)){
    					$finalMailInsertData = array_merge($finalMailInsertData,$buyerAndUserBuyerMailInsertData);
    				}
    				if (!empty($deliveryLocationMailInsertData)){
    					$finalMailInsertData = array_merge($finalMailInsertData,$deliveryLocationMailInsertData);
    				}
    			}
    		}
    		
    		$result = false;
    		DB::beginTransaction();
    		
    		try {
    			if (!empty($finalMailInsertData)){
    				DB::table(config('constants.EMAIL_HISTORY_TABLE'))->insert($finalMailInsertData);
    			}
    			
    			$result = true;
    		} catch (\Exception $e){
    			Log::error($e->getMessage());
    		}
    		 
    		if ($result != false){
    			DB::commit();
    		} else {
    			DB::rollBack();
    		}
    	}
    	
    	Log::info("Send Mail To Pending Delivery Of Buyer Cron End");
    }
}