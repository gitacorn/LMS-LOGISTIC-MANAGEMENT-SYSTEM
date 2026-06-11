<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\MasterController;
use CheckLogin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Login;
use App\Helpers\Twt\Wild_tiger;
use App\Rules\InternationMobileFormat;
use DB;
use App\Helpers\Twt\Zoho_crm;
use App\Lead;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Response;
use App\BaseModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
use App\ReportModel;
use App\WarehouseMasterModel;
use App\CountryMasterModel;
use App\GoodInBuyerMasterModel;
use App\CompanyMasterModel;
use App\Models\WarehousePalletMasterModel;

class DashboardController extends MasterController
{
    //
	public $loginCookieName;
	public function __construct(){
		//parent::__construct();
		$this->middleware('checklogin');
		$this->BaseModel = new BaseModel();
		$this->loginCookieName = config('constants.LOGIN_COOKIE_NAME');
		$this->crudModel = new ReportModel();
	}
	
   	public function index(){
   		//echo "<pre>";print_r(session()->all());die;
   		$data['pageTitle'] =  trans('messages.dashboard');
   		
   		$data['countryDetails'] = CountryMasterModel::orderBy('v_country_name', 'ASC')->get();
   		$data['companyDetails'] = CompanyMasterModel::orderBy('v_company_name', 'ASC')->get();
   		$warehouseWhere = [];
   		$userLoginDetails = Login::where([
   					['t_is_active','=',1],
   					['i_id','=', session()->get('user_id')],
   					['v_role','=',config ( 'constants.ROLE_USER')]
   				])->orderBy('v_name', 'ASC')->first();
   		
   		if(!empty($userLoginDetails)){
   			if( (!empty($userLoginDetails->v_record_type)) &&  ( in_array( config ( 'constants.GOODS_IN_WAREHOUSE')  , explode("," , $userLoginDetails->v_record_type ) ) ) ){
   				$warehouseIds = (!empty($userLoginDetails->i_warehouse_id) ? $userLoginDetails->i_warehouse_id : 0 );
   				$warehouseWhere['i_id'] = $warehouseIds;
   			}
   		}
   		
   		$data['wareHouseDetails'] = WarehouseMasterModel::where($warehouseWhere)->where('e_record_type',config('constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
   		
   		$data['recordDetails'] = $this->getStatisticsGraphDetails();
   		$data['goodsOutStatistics'] = $this->getGoodsOutStatistics();
   		$whereData['glm.dt_delivery_date >='] = date("Y-m-d", strtotime("first day of previous month"));
   		$whereData['glm.dt_delivery_date <='] = date("Y-m-d", strtotime("last day of previous month"));
   		$averageDetails = $this->crudModel->getAverageSummaryDetails( $whereData  );
   		$data['averageDetails'] = $averageDetails;
			// Prepare top warehouses data (units & value) for outer chart ring
			$buyerDelivery = $this->crudModel->getBuyerDeliveryDetails();
			$warehouseLabels = $warehouseUnits = $warehouseValues = [];
			$top = 5; // top N warehouses
			if(!empty($buyerDelivery)){
				$counter = 0;
				foreach($buyerDelivery as $row){
					if($counter >= $top) break;
					$label = (isset($row->v_warehouse_name) ? $row->v_warehouse_name : 'Unknown');
					if(isset($row->v_warehouse_code) && !empty($row->v_warehouse_code)){
						$label .= ' (' . $row->v_warehouse_code . ')';
					}
					$warehouseLabels[] = $label;
					$warehouseUnits[] = (int) ($row->total_units ?? 0);
					$warehouseValues[] = (float) ($row->po_amount_with_vat_gbp ?? 0);
					$counter++;
				}
			}
			$data['buyerWarehouseLabels'] = json_encode($warehouseLabels);
			$data['buyerWarehouseUnits'] = json_encode($warehouseUnits);
			$data['buyerWarehouseValues'] = json_encode($warehouseValues);
   		return view('admin/dashboard' , $data);
	}
	
	protected function getStatisticsGraphDetails($whereData = []){
		
		$data = [];
		
		$recordType = config('constants.IN_TRANSIT_STATUS');
		$whereFromDate = $whereToDate = $whereFromCountry = $whereToWareHouse = null;
		if( isset( $whereData ) && (!empty($whereData)) ){
			if( isset($whereData['recordType']) ){
				$recordType = $whereData['recordType'];
			}
			if( isset($whereData['fromDate']) && (!empty($whereData['fromDate'])) ){
				$whereFromDate = $whereData['fromDate'];
			}
			if( isset($whereData['toDate']) && (!empty($whereData['toDate'])) ){
				$whereToDate = $whereData['toDate'];
			}
			if( isset($whereData['searchCountry']) && (!empty($whereData['searchCountry'])) ){
				$whereFromCountry = $whereData['searchCountry'];
			}
			if( isset($whereData['searchWareHouse']) && (!empty($whereData['searchWareHouse'])) ){
				$whereToWareHouse = $whereData['searchWareHouse'];
			}
		}
		
		$whereTrackingData = [
				'dashboardType' => true
		];
		
		$additionalTrackingData = [];

		if ($recordType == config('constants.DELIVERED_STATUS')) {
			$whereTrackingData['gbd.t_is_deleted != '] = 1;
			$whereTrackingData['gbd.e_logistic_record_status != '] = 'Cancelled';
			$additionalTrackingData['whereIn'] = ['glm.i_status_id', [config('constants.DELIVERED_STATUS_ID'), config('constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID')]];
		} else {
			$whereTrackingData['gbd.t_is_all_delivered_cancelled_ststus'] = 0;
		}
		
		if(!empty($whereFromDate)){
			$whereTrackingData['custom_function'][] = "date(glm.dt_delivery_date) >= '" . $whereFromDate . "'";
		}
		if(!empty($whereToDate)){
			$whereTrackingData['custom_function'][] = "date(glm.dt_delivery_date) <= '" . $whereToDate . "'";
		}
		if(!empty($whereFromCountry)){
			$whereTrackingData['custom_function'][] = "(sd.i_country_id in (" . $whereFromCountry . "))";
		}
		if(!empty($whereToWareHouse)){
			$whereTrackingData['custom_function'][] = "(gdm.i_delivery_location_id in (" . $whereToWareHouse . "))";
		}
		
		$totalTransactionDetails = $this->crudModel->getTrackingGoodInDetail($whereTrackingData, [], $additionalTrackingData);
		
		// Initialize totals
		$data['totalUnits'] = 0;
		$data['totalInTransitBoxes'] = 0;
		$data['totalInTransitPallets'] = 0;
		$data['totalAmount'] = 0;
		$data['deliveryCount'] = 0;
		$data['collectionCount'] = 0;
		
		$deliveryType = config('constants.DELIVERY');
		$collectionType = config('constants.COLLECTION');
		$palletType = config('constants.PALLET');
		$boxType = config('constants.BOX');
		
		$processedMasterIds = [];

		foreach ($totalTransactionDetails as $item) {
		    // Collection counts
		    if ($item->e_collection_type === $deliveryType) {
		        $data['deliveryCount']++;
		    } else if ($item->e_collection_type === $collectionType) {
		        $data['collectionCount']++;
		    }

		    // Deduplicate Master level values so we don't multiply them by the number of suppliers/details
		    $masterId = $item->master_id ?? null;
		    if ($masterId && in_array($masterId, $processedMasterIds)) {
		        continue; // Skip adding units, amounts, boxes, and pallets if we already added them for this master PO
		    }
		    if ($masterId) {
		        $processedMasterIds[] = $masterId;
		    }

		    // Total Units
		    $data['totalUnits'] += (int)($item->i_total_units ?? 0);

		    // Total PO Amount
		    $poAmount = (float)($item->d_po_amount_with_vat ?? 0);
		    $rate = (float)($item->po_gbp_conversation_rate ?? 0);
		    if ($poAmount > 0 && $rate > 0) {
		        $data['totalAmount'] += ($poAmount * $rate);
		    }

		    // Total Pallets / Boxes
		    $buyerType = $item->e_pallet_box_type ?? '';
		    $buyerQty = (int)($item->i_no_of_pallet_box ?? 0);

		    if ($buyerType === $palletType) {
		        $data['totalInTransitPallets'] += $buyerQty;
		    } else if ($buyerType === $boxType) {
		        $data['totalInTransitBoxes'] += $buyerQty;
		    }
		}
		
		return $data;
	}
	
	public function getStatisticsGraphFilter(Request $request){
		
		// Debug: Log received parameters
		\Log::info('getStatisticsGraphFilter received parameters:', [
			'search_status' => $request->post('search_status'),
			'search_from_date' => $request->post('search_from_date'),
			'search_to_date' => $request->post('search_to_date'),
			'search_from_country' => $request->post('search_from_country'),
			'search_to_warehouse' => $request->post('search_to_warehouse'),
			'all_post_data' => $request->post()
		]);
		
		$whereData = [];
		$whereData['recordType'] = ( !empty($request->post('search_status')) ? trim($request->post('search_status')) : config('constants.IN_TRANSIT_STATUS') );
		$whereData['fromDate'] = ( !empty($request->post('search_from_date')) ? dbDate($request->post('search_from_date')) : null );
		$whereData['toDate'] = ( !empty($request->post('search_to_date')) ? dbDate($request->post('search_to_date')) : null );
		
		// Debug: Log parsed values
		\Log::info('Parsed filter values:', $whereData);
		
		$searchCountries = (!empty($request->post('search_from_country')) ? explode(',' , $request->post('search_from_country')) : [] );
		if(!empty($searchCountries)){
			$searchDecodeCountry = array_map(function ($searchCountry){
				return (int)Wild_tiger::decode($searchCountry);
			}, $searchCountries);
			
			$allCountry = implode(',', $searchDecodeCountry);
			$whereData['searchCountry'] = $allCountry;
		}
		
		$searchWarehouses = (!empty($request->post('search_to_warehouse')) ? explode(',' , $request->post('search_to_warehouse')) : [] );
		if(!empty($searchWarehouses)){
			$searchDecodeWarehouse = array_map(function ($searchWarehouse){
				return (int)Wild_tiger::decode($searchWarehouse);
			}, $searchWarehouses);
					
			$allWareHouse = implode(',', $searchDecodeWarehouse);
			$whereData['searchWareHouse'] = $allWareHouse;
		}
		
		// Debug: Log final where data
		\Log::info('Final whereData for statistics:', $whereData);
		
		try {
			$data['recordDetails'] = $this->getStatisticsGraphDetails($whereData);
			\Log::info('Statistics data retrieved successfully', $data['recordDetails']);
		} catch (Exception $e) {
			\Log::error('Error in getStatisticsGraphDetails: ' . $e->getMessage());
			\Log::error($e->getTraceAsString());
			// Return empty data on error
			$data['recordDetails'] = [
				'totalUnits' => 0,
				'totalInTransitBoxes' => 0,
				'totalInTransitPallets' => 0,
				'totalAmount' => 0,
				'deliveryCount' => 0,
				'collectionCount' => 0
			];
		}
		
		$data['recordType'] = $whereData['recordType'];
		return view( config('constants.AJAX_VIEW_FOLDER').'dashboard/dashboard-statistics', $data);
	}
	
	public function getDonutChartFilter(Request $request){
		
		// Debug: Log received parameters
		\Log::info('getDonutChartFilter received parameters:', [
			'search_status' => $request->post('search_status'),
			'search_from_date' => $request->post('search_from_date'),
			'search_to_date' => $request->post('search_to_date'),
			'search_from_country' => $request->post('search_from_country'),
			'search_to_warehouse' => $request->post('search_to_warehouse'),
			'all_post_data' => $request->post()
		]);
		
		$whereData = [];
		$recordType = config('constants.IN_TRANSIT_STATUS');
		$whereFromDate = $whereToDate = $whereFromCountry = $whereToWareHouse = null;
		
		if(!empty($request->post('search_status'))){
			$recordType = trim($request->post('search_status'));
		}
		if(!empty($request->post('search_from_date'))){
			$whereFromDate = dbDate($request->post('search_from_date'));
		}
		if(!empty($request->post('search_to_date'))){
			$whereToDate = dbDate($request->post('search_to_date'));
		}
		
		// Debug: Log parsed values
		\Log::info('Parsed filter values:', [
			'recordType' => $recordType,
			'whereFromDate' => $whereFromDate,
			'whereToDate' => $whereToDate
		]);
		
		$searchCountries = (!empty($request->post('search_from_country')) ? explode(',' , $request->post('search_from_country')) : [] );
		if(!empty($searchCountries)){
			$searchDecodeCountry = array_map(function ($searchCountry){
				return (int)Wild_tiger::decode($searchCountry);
			}, $searchCountries);
			
			$allCountry = implode(',', $searchDecodeCountry);
			$whereFromCountry = $allCountry;
		}
		
		$searchWarehouses = (!empty($request->post('search_to_warehouse')) ? explode(',' , $request->post('search_to_warehouse')) : [] );
		if(!empty($searchWarehouses)){
			$searchDecodeWarehouse = array_map(function ($searchWarehouse){
				return (int)Wild_tiger::decode($searchWarehouse);
			}, $searchWarehouses);
					
			$allWareHouse = implode(',', $searchDecodeWarehouse);
			$whereToWareHouse = $allWareHouse;
		}
		
		// Debug: Log country/warehouse values
		\Log::info('Country/Warehouse filters:', [
			'searchCountries' => $searchCountries,
			'allCountry' => $whereFromCountry,
			'searchWarehouses' => $searchWarehouses,
			'allWareHouse' => $whereToWareHouse
		]);
		
		// Get tracking details with filters using same logic as tracking report
		$whereTrackingData = [
				'dashboardType' => true
		];
		
		// Apply status filter - handle DELIVERED vs In Transit
		$additionalTrackingData = [];
		if($recordType === 'DELIVERED' || $recordType === config('constants.DELIVERED_STATUS')){
			$whereTrackingData['gbd.t_is_all_delivered_cancelled_ststus'] = 1;
			$whereTrackingData['custom_function'][] = "glm.i_status_id in (" . config('constants.DELIVERED_STATUS_ID') . "," . config('constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID') . ")";
			// $whereTrackingData['gbd.t_is_deleted != '] = 1;
			// $whereTrackingData['gbd.e_logistic_record_status != '] = 'Cancelled';
			// $additionalTrackingData['whereIn'] = ['glm.i_status_id', [config('constants.DELIVERED_STATUS_ID'), config('constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID')]];
			\Log::info('Filtering for DELIVERED status');
		} else {
			// Default to In Transit - show non-delivered items
			$whereTrackingData['gbd.t_is_all_delivered_cancelled_ststus'] = 0;
			$whereTrackingData['custom_function'][] = "glm.i_status_id not in (" . config('constants.DELIVERED_STATUS_ID') . "," . config('constants.DELIVERED_BUT_DOCUMENT_PENDING_STATUS_ID') . "," . config('constants.STATIC_STATUS_CANCELLED_ID') . ")";
			\Log::info('Filtering for non-delivered (In Transit) status');
		}
		
		// Apply date filters using custom_function (same as tracking report)
		if(!empty($whereFromDate)){
			$whereTrackingData['custom_function'][] = "date(glm.dt_delivery_date) >= '" . $whereFromDate . "'";
		}
		if(!empty($whereToDate)){
			$whereTrackingData['custom_function'][] = "date(glm.dt_delivery_date) <= '" . $whereToDate . "'";
		}
		
		// Apply country filter (using same approach as tracking report)
		if(!empty($whereFromCountry)){
			$whereTrackingData['custom_function'][] = "(sd.i_country_id in (" . $whereFromCountry . "))";
		}
		
		// Apply warehouse filter  
		if(!empty($whereToWareHouse)){
			$whereTrackingData['custom_function'][] = "(gdm.i_delivery_location_id in (" . $whereToWareHouse . "))";
		}
		
		// Debug: Log final whereTrackingData
		\Log::info('Final whereTrackingData for donut chart:', $whereTrackingData);
		
		try {
			$totalTransactionDetails = $this->crudModel->getTrackingGoodInDetail($whereTrackingData, [], $additionalTrackingData);
			\Log::info('Total transaction details count:', ['count' => count($totalTransactionDetails)]);
		} catch (Exception $e) {
			\Log::error('Error in getTrackingGoodInDetail: ' . $e->getMessage());
			$totalTransactionDetails = collect([]);
		}
		
		// Ensure we have a collection
		if (!$totalTransactionDetails) {
			$totalTransactionDetails = collect([]);
		}
		
		$collectionTypes = [
		    'delivery' => config('constants.DELIVERY'),
		    'collection' => config('constants.COLLECTION'),
		];
		
		$typeCounts = collect($collectionTypes)->mapWithKeys(function ($type, $key) use ($totalTransactionDetails) {
		    return [
		        $key => $totalTransactionDetails->filter(function ($item) use ($type) {
		            return $item->e_collection_type === $type;
		        })->count(),
		    ];
		});
		
		// Debug: Log final counts
		\Log::info('Final type counts:', $typeCounts->toArray());
		
		$response = [
			'deliveryCount' => $typeCounts['delivery'],
			'collectionCount' => $typeCounts['collection']
		];
		
		// Debug: Log response
		\Log::info('Returning response:', $response);
		
		return response()->json($response);
	}
	
	public function getAvgDaysCostSummaryFilter(Request $request){
		
		$whereData = $additionalData = [];
		$searchCountries = (!empty($request->post('search_avg_days_country')) ? explode(',' , $request->post('search_avg_days_country')) : [] );
		 
		if(!empty($searchCountries)){
			$searchDecodeCountry = array_map(function ($searchCountry){
				return (int)Wild_tiger::decode($searchCountry);
			}, $searchCountries);
				
			$additionalData['whereIn'] = [ 'sd.i_country_id' , $searchDecodeCountry ];
			
		}
		
		$searchFromDate = ( !empty($request->post('search_from_month')) ?  getFirstLastDayOfMonth( $request->post('search_from_month') , 'first' ) : null );
		
		$searchToDate = ( !empty($request->post('search_to_month')) ?  getFirstLastDayOfMonth( $request->post('search_to_month') , 'last' ) :  null );
		
		if(!empty($searchFromDate)){
			$whereData['glm.dt_delivery_date >='] = $searchFromDate;
		}
		
		if(!empty($searchToDate)){
			$whereData['glm.dt_delivery_date <='] = $searchToDate;
		}
		
		$data['averageDetails'] = $this->crudModel->getAverageSummaryDetails( $whereData , [] , $additionalData );
		
		return view( config('constants.AJAX_VIEW_FOLDER').'dashboard/dashboard-avg-days', $data );
	}
	
	public function buyerDelivery(Request $request){
	    $data = $groupedData = [];
	    $data['dateArray'] = getUpcomingSixDates(); 
	    $buyerDetails =  $this->crudModel->getBuyerDeliveryDetails();
	    foreach ($buyerDetails as $key => $buyerDetail) {
	       
	        $key = (isset($buyerDetail->v_warehouse_name) && (!empty($buyerDetail->v_warehouse_name)) 
	                ? ($buyerDetail->v_warehouse_name) 
	                . (isset($buyerDetail->v_warehouse_code) && (!empty($buyerDetail->v_warehouse_code)) 
	                ? ' (' . $buyerDetail->v_warehouse_code . ')' 
	                : '' ) 
	                : null);
	        
	        if (!isset($groupedData[$key])) {
	            $groupedData[$key] = [
	                'data' => [],
	            ];
	        }
	    
	        $deliveryDate = clientDate($buyerDetail->dt_delivery_date);
	        $totalUnits = $buyerDetail->total_units;
	        $totalPallets = ($buyerDetail->type_of_pallet_box == config("constants.PALLET")) ? $buyerDetail->total_pallets : 0;
	        $totalBoxes = ($buyerDetail->type_of_pallet_box == config("constants.BOX")) ? $buyerDetail->total_boxes : 0;
	        $poAmountWithVat = $buyerDetail->po_amount_with_vat_gbp;
	        
	        // Try to determine warehouse id from the record (fields may vary in report)
	        $warehouseId = null;
	        if (isset($buyerDetail->i_warehouse_id) && !empty($buyerDetail->i_warehouse_id)) {
	            $warehouseId = (int) $buyerDetail->i_warehouse_id;
	        } elseif (isset($buyerDetail->i_to_warehouse_id) && !empty($buyerDetail->i_to_warehouse_id)) {
	            $warehouseId = (int) $buyerDetail->i_to_warehouse_id;
	        }
	        
	        // Lookup pallet limit for this warehouse/date (exact date only)
	        $palletLimit = null;
	        if (!empty($warehouseId)) {
	            $wpmWhere = [
	                'i_warehouse_id' => $warehouseId,
	                'dt_pallet_date' => dbDate($deliveryDate),
	                't_is_active' => 1,
	            ];
	            $palletLimitRow = (new WarehousePalletMasterModel())->getRecordDetails($wpmWhere + ['singleRecord' => true]);
	            if (!empty($palletLimitRow) && isset($palletLimitRow->i_pallet_limit)) {
	                $palletLimit = $palletLimitRow->i_pallet_limit;
	            }
	        }
	    
	        if (!isset($groupedData[$key]['data'][$deliveryDate])) {
	            $groupedData[$key]['data'][$deliveryDate] = [
	                'total_units' => 0,
	                'total_pallets' => 0,
	                'total_boxes' => 0,
	                'po_amount_with_vat_gbp' => 0,
	                'pallet_limit' => $palletLimit,
	            ];
	        }
	    
	        $groupedData[$key]['data'][$deliveryDate]['total_units'] += $totalUnits;
	        $groupedData[$key]['data'][$deliveryDate]['total_pallets'] += $totalPallets;
	        $groupedData[$key]['data'][$deliveryDate]['total_boxes'] += $totalBoxes;
	        $groupedData[$key]['data'][$deliveryDate]['po_amount_with_vat_gbp'] += $poAmountWithVat;
	        // If limit was not set earlier (first encounter), keep it; else, prefer a non-null value
	        if (!isset($groupedData[$key]['data'][$deliveryDate]['pallet_limit']) || is_null($groupedData[$key]['data'][$deliveryDate]['pallet_limit'])) {
	            $groupedData[$key]['data'][$deliveryDate]['pallet_limit'] = $palletLimit;
	        }
	    }
	    
	    $data['buyerDetails'] = $groupedData;
	    return view(config('constants.AJAX_VIEW_FOLDER') . 'dashboard/dashboard-buyer-delivery', $data);
	} 
	
	public function topSuppliersCompanyFilter(Request $request){
		if(!empty($request->post())){
			
			// Debug: Log all received parameters
			\Log::info('topSuppliersCompanyFilter received params:', $request->post());
			
			// Handle month filters (original functionality)
			$searchFromDate = ( !empty($request->post('search_from_month')) ?  getFirstLastDayOfMonth( $request->post('search_from_month') , 'first' ) : null );
			$searchToDate = ( !empty($request->post('search_to_month')) ?  getFirstLastDayOfMonth( $request->post('search_to_month') , 'last' ) : null );
			
			// Handle date filters from statistics
			$statisticsFromDate = ( !empty($request->post('search_from_date')) ? dbDate($request->post('search_from_date')) : null );
			$statisticsToDate = ( !empty($request->post('search_to_date')) ? dbDate($request->post('search_to_date')) : null );
			
			// Debug: Check isChart parameter
			$isChart = $request->post('isChart');
			\Log::info('isChart parameter:', ['value' => $isChart, 'type' => gettype($isChart), 'bool_check' => ($isChart == true)]);
			
			// Use statistics date range if available, otherwise use month filters
			if(!empty($statisticsFromDate)){
				$whereData['glm.dt_delivery_date >='] = $statisticsFromDate;
			} elseif(!empty($searchFromDate)){
				$whereData['glm.dt_delivery_date >='] = $searchFromDate;
			}
			
			if(!empty($statisticsToDate)){
				$whereData['glm.dt_delivery_date <='] = $statisticsToDate;
			} elseif(!empty($searchToDate)){
				$whereData['glm.dt_delivery_date <='] = $searchToDate;
			}
			
			// Handle country and warehouse filters from statistics
			$searchCountries = (!empty($request->post('search_from_country')) ? explode(',' , $request->post('search_from_country')) : [] );
			if(!empty($searchCountries)){
				$searchDecodeCountry = array_map(function ($searchCountry){
					return (int)Wild_tiger::decode($searchCountry);
				}, $searchCountries);
				
				$allCountry = implode(',', $searchDecodeCountry);
				$whereData['searchCountry'] = $allCountry;
			}
			
			$searchWarehouses = (!empty($request->post('search_to_warehouse')) ? explode(',' , $request->post('search_to_warehouse')) : [] );
			if(!empty($searchWarehouses)){
				$searchDecodeWarehouse = array_map(function ($searchWarehouse){
					return (int)Wild_tiger::decode($searchWarehouse);
				}, $searchWarehouses);
						
				$allWareHouse = implode(',', $searchDecodeWarehouse);
				$whereData['searchWareHouse'] = $allWareHouse;
			}
			
			// Handle status filter for chart
			if(!empty($request->post('search_status'))){
				$recordType = trim($request->post('search_status'));
				\Log::info('Top Suppliers - Status filter received:', ['status' => $recordType]);
				
				// If status is IN-TRANSIT or IN_TRANSIT, mark as in-transit filter
				if($recordType === config('constants.IN_TRANSIT_STATUS') || stripos($recordType, 'TRANSIT') !== false){
					$whereData['status_filter'] = 'IN_TRANSIT';
					\Log::info('Top Suppliers - Filtering for IN_TRANSIT');
				}
				// Otherwise defaults to DELIVERED (handled in model)
			}
			
			if(!empty($request->post('search_company'))){
				$whereData['gdm.i_buyer_company_id'] = (int)Wild_tiger::decode($request->post('search_company'));
			}
			
			$whereData['supplierDetails'] = true;
			
			\Log::info('Checking isChart condition:', [
				'isChart_post' => $request->post('isChart'),
				'isChart_bool' => (bool)$request->post('isChart'),
				'condition_result' => (!empty($request->post('isChart')) && $request->post('isChart') != false)
			]);
			
			if(!empty($request->post('isChart')) && ( $request->post('isChart') != false ) ){
				\Log::info('Rendering CHART view for top suppliers');
				$whereData['group_by'] = ['sd.i_supplier_id', 'gdm.i_buyer_company_id'];
				
				// If no date filters provided, use default previous month
				if(empty($statisticsFromDate) && empty($searchFromDate) && empty($statisticsToDate) && empty($searchToDate)){
					$whereData['glm.dt_delivery_date >='] = date("Y-m-d", strtotime("first day of previous month"));
					$whereData['glm.dt_delivery_date <='] = date("Y-m-d", strtotime("last day of previous month"));
				}
				
				$allData = $this->crudModel->getAverageSummaryDetails($whereData);

				$supplierGroups = [];
				if(!empty($allData)) {
					foreach($allData as $row) {
						$supName = $row->v_supplier_name ?? 'Unknown';
						if(!isset($supplierGroups[$supName])) {
							$supplierGroups[$supName] = [
								'total' => 0,
								'companies' => []
							];
						}
						$poAmt = (float)($row->po_amount_with_vat_gbp ?? 0);
						$supplierGroups[$supName]['total'] += $poAmt;
						$supplierGroups[$supName]['companies'][] = [
							'name' => $row->v_company_name ?? 'Unknown',
							'amount' => $poAmt
						];
					}
				}
				
				uasort($supplierGroups, function($a, $b) {
					return $b['total'] <=> $a['total'];
				});
				
				$limit = config('constants.TOP_SUPPLIERS_CHART_LIMIT') ?? 10;
				$topSuppliers = array_slice($supplierGroups, 0, $limit, true);
				
				$processedData = [];
				foreach($topSuppliers as $supName => $supData) {
					usort($supData['companies'], function($a, $b) {
						return $b['amount'] <=> $a['amount'];
					});
					
					$processedData[] = [
						'supplier' => $supName,
						'top1_company' => $supData['companies'][0]['name'] ?? 'None',
						'top1_amount' => $supData['companies'][0]['amount'] ?? 0,
						'top2_company' => $supData['companies'][1]['name'] ?? 'None',
						'top2_amount' => $supData['companies'][1]['amount'] ?? 0,
					];
				}
				
				$data['supplierDetails'] = $processedData;
				\Log::info('Chart view - Supplier details count:', ['count' => count($data['supplierDetails'] ?? [])]);
				
				return view( config('constants.AJAX_VIEW_FOLDER').'dashboard/dashboard-top-supplier-company' , $data  );
			}
			
			\Log::info('Rendering TABLE view for top suppliers');
			$whereData['group_by'] = [ 'gdm.i_buyer_company_id','type_of_pallet_box_final' ];
			$supplierDetails = $this->crudModel->getAverageSummaryDetails($whereData);
			
			$groupedResults = [];
			
			if(!empty($supplierDetails)){
				foreach ($supplierDetails as $supplierDetail) {
					if (!isset($groupedResults[$supplierDetail->v_company_name])) {
						$groupedResults[$supplierDetail->v_company_name] = [
								'total_units' => 0,
								'total_pallets' => 0,
								'total_boxes' => 0,
								'po_amount_with_vat_gbp' => 0,
						];
					}
					$groupedResults[$supplierDetail->v_company_name]['total_units'] += ( isset($supplierDetail->total_units) ? $supplierDetail->total_units : 0 );
					$groupedResults[$supplierDetail->v_company_name]['total_pallets'] += ( isset($supplierDetail->type_of_pallet_box_final) && ($supplierDetail->type_of_pallet_box_final) == config('constants.PALLET') ? $supplierDetail->total_pallets : 0 );
					$groupedResults[$supplierDetail->v_company_name]['total_boxes'] +=  ( isset($supplierDetail->type_of_pallet_box_final) && ($supplierDetail->type_of_pallet_box_final) == config('constants.BOX') ? $supplierDetail->total_boxes : 0  );
					$groupedResults[$supplierDetail->v_company_name]['po_amount_with_vat_gbp'] += $supplierDetail->po_amount_with_vat_gbp;
				}
						
			}
			
			$data['supplierDetails'] = $groupedResults;
			
			return view( config('constants.AJAX_VIEW_FOLDER').'dashboard/dashboard-supplier-company' , $data );
		}
	}
	
	public function setLogoutPopupStatus(Request $request){
		Session::put('showLogoutAlert', config('constants.SELECTION_YES') );
		$response = [];
		$response['status_code'] = 1;
		$response['message'] = trans('messages.success');
		return response()->json($response);
		
	}
	
	public function logout(Request $request){
		
		$getCurrentSessionToken = session()->get('_token');
		
		if(!empty($getCurrentSessionToken)){
			$this->BaseModel->updateTableData(config('constants.LOGIN_HISTORY_TABLE'), [ 'dt_logout_time' => config('constants.DATE_TIME') ], [ 'i_login_id' => session()->get('user_id') , 'i_session_id' => $getCurrentSessionToken ] );
		}
		
		Cookie::queue(Cookie::forget( $this->loginCookieName . '_process_email'));
		Cookie::queue(Cookie::forget( $this->loginCookieName . '_process_password'));
		
		$request->session()->flush();
		return redirect('login');
	}
	
	public function changePassword(){
		$data['pageTitle'] =  trans('messages.change-password');
		$data['user_id'] = session()->get('user_id');
		
		return view('admin/change-password' , $data);
	}
	
	public function updatePassword(Request $request ){
		
		$validator = Validator::make($request->all(), [
				'current_password' => 'required',
				'new_password' => 'required',
				'confirm_password' => 'required|same:new_password',
		],[
				'current_password.required' => __('messages.required-current-password') ,
				'new_password.required' => __('messages.required-new-password') ,
				'confirm_password.required' => __('messages.required-confirm-password') ,
		]
		);
	
		if ($validator->fails()) {
			return redirect::back()
			->withErrors($validator)
			->withInput();
		}
	
		$requestUserId =  (!empty(session()->get('user_id')) ? (int)session()->get('user_id') : 0 );
	
		$currentPassword  = $request->input('current_password');
		$newPassword  = $request->input('new_password');
		$confirmPassword  = $request->input('confirm_password');
	
		if( $requestUserId > 0 ){
				
			if(  $newPassword ==  $confirmPassword ){
	
				$masterUserData = Login::find ( $requestUserId );
	
				
				if (password_verify($currentPassword, $masterUserData->v_password) != true ) {
					Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.invalid-current-password' ) );
					return redirect::back();
				}
				
				$masterUserData->v_password = password_hash($newPassword, PASSWORD_DEFAULT);
					
				$updateUser = $masterUserData->save();
				
				if ($updateUser != false) {
					$request->session()->flush();
					Wild_tiger::setFlashMessage ( 'success', trans ( 'messages.success-update-password' ) );
					
					return redirect ( 'login' );
				}
	
				Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.error-update-password' ) );
				return redirect ( 'login' );
			}
				
			Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.confirm-password-not-match' ) );
			return redirect ( 'login' );
				
		}
	
		Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.system-error' ) );
		return redirect::back();
	}

	/**
	 * Get Goods Out Statistics for Dashboard using Stored Procedure
	 */
protected function getGoodsOutStatisticsGraphDetails($whereData = [])
{
    // STEP 1: Convert object to array
    if (is_object($whereData)) {
        $whereData = (array) $whereData;
    }

    $data = [];

    // STEP 2: Correct parameter mapping
    $location   = $whereData['location'] ?? 'EU';
    $recordType = $whereData['recordType'] ?? $whereData['status'] ?? config('constants.IN_TRANSIT_STATUS');
    $fromDate   = $whereData['fromDate'] ?? $whereData['from_date'] ?? null;
    $toDate     = $whereData['toDate'] ?? $whereData['to_date'] ?? null;

    \Log::info('Calling get_goods_out_statistics_data with parameters', [
        'location' => $location,
        'status'   => $recordType,
        'from'     => $fromDate,
        'to'       => $toDate,
    ]);

    // STEP 3: Call stored procedure
    $recordDetails = $this->CallRaw(
        'get_goods_out_statistics_data',
        [$location, $recordType, $fromDate, $toDate]
    );

    // STEP 4: Read result properly
     $result = null;
    if (!empty($recordDetails)) {
        if (isset($recordDetails[0])) {
            if (is_array($recordDetails[0])) {
                $result = $recordDetails[0][0] ?? null;
            } else {
                $result = $recordDetails[0];
            }
        }
    }

    if ($result) {
        $data['totalUnits']        = (int) ($result->Unit_Count ?? 0);
        $data['totalPallets']     = (int) ($result->{'Total Pallets'} ?? 0);
        $data['totalBoxes']       = (int) ($result->{'Total Boxes'} ?? 0);
        $data['shipmentValue']    = (float) ($result->ShipmentValue ?? 0);
        $data['totalTransactions']= (float) ($result->{'Total Transaction'} ?? 0);

    } else {
        // Defaults
        $data = [
            'totalUnits'        => 0,
            'totalPallets'     => 0,
            'totalBoxes'       => 0,
            'shipmentValue'    => 0,
            'totalTransactions'=> 0,
        ];
    }

    return $data;
}


	/**
	 * Get Goods Out Statistics for Dashboard (Legacy method for compatibility)
	 */
	public function getGoodsOutStatistics($filterData = [])
	{
		// Debug: Log initial page load call
		\Log::info('Goods Out Statistics - Initial page load call with filterData: ' . json_encode($filterData));
		
		return $this->getGoodsOutStatisticsGraphDetails($filterData);
	}

	/**
	 * Filter Goods Out Statistics via AJAX using Stored Procedure
	 */
	public function getGoodsOutStatisticsFilter(Request $request){
		$whereData = [];
		
		// Handle location parameter
		if($request->has('search_location')){
			$locationValue = $request->input('search_location');
			$whereData['location'] = $locationValue;
		} else {
			$whereData['location'] = 'EU';
		}
		
		// Handle status parameter
		if($request->has('search_status')){
			$statusValue = $request->input('search_status');
			$whereData['recordType'] = $statusValue;
		} else {
			$whereData['recordType'] = config('constants.IN_TRANSIT_STATUS');
		}
		
		// Handle date parameters
		if($request->has('search_goods_out_buyer_delivery_from_date') && !empty($request->input('search_goods_out_buyer_delivery_from_date'))){
			$fromDate = $request->input('search_goods_out_buyer_delivery_from_date');
			// Date comes in as DD-MM-YYYY, convert to Y-m-d
			$whereData['fromDate'] = \DateTime::createFromFormat('d-m-Y', $fromDate) 
				? \DateTime::createFromFormat('d-m-Y', $fromDate)->format('Y-m-d')
				: date('Y-m-d', strtotime($fromDate));
		}
		
		if($request->has('search_goods_out_buyer_delivery_to_date') && !empty($request->input('search_goods_out_buyer_delivery_to_date'))){
			$toDate = $request->input('search_goods_out_buyer_delivery_to_date');
			// Date comes in as DD-MM-YYYY, convert to Y-m-d
			$whereData['toDate'] = \DateTime::createFromFormat('d-m-Y', $toDate)
				? \DateTime::createFromFormat('d-m-Y', $toDate)->format('Y-m-d')
				: date('Y-m-d', strtotime($toDate));
		}
		
		if($request->has('search_goods_out_from_warehouse') && !empty($request->input('search_goods_out_from_warehouse'))){
			$searchWarehouses = explode(',' , $request->input('search_goods_out_from_warehouse'));
			$searchDecodeWarehouse = array_map(function ($searchWarehouse){
				return (int)Wild_tiger::decode($searchWarehouse);
			}, $searchWarehouses);
			$whereData['fromWarehouse'] = implode(',', $searchDecodeWarehouse);
		}

		$data['goodsOutStatistics'] = $this->getGoodsOutStatisticsGraphDetails($whereData);
		$data['recordType'] = $whereData['recordType'];
		
		return view( config('constants.AJAX_VIEW_FOLDER').'dashboard/dashboard-goods-out-statistics', $data);
	}

	/**
	 * Filter Goods Out Statistics via AJAX using Stored Procedure (JSON Response)
	 */
	public function filterGoodsOutStatistics(Request $request){
		$whereData = [];
		
		// Handle location parameter
		if($request->has('search_location')){
			$locationValue = $request->input('search_location');
			$whereData['location'] = $locationValue;
		} else {
			$whereData['location'] = 'EU';
		}
		
		// Handle status parameter
		if($request->has('search_status')){
			$statusValue = $request->input('search_status');
			$whereData['recordType'] = $statusValue;
		} else {
			$whereData['recordType'] = config('constants.IN_TRANSIT_STATUS');
		}
		
		// Handle date parameters
		if($request->has('search_goods_out_buyer_delivery_from_date') && !empty($request->input('search_goods_out_buyer_delivery_from_date'))){
			$fromDate = $request->input('search_goods_out_buyer_delivery_from_date');
			$whereData['fromDate'] = \DateTime::createFromFormat('d-m-Y', $fromDate) 
			? \DateTime::createFromFormat('d-m-Y', $fromDate)->format('Y-m-d')
			: date('Y-m-d', strtotime($fromDate));
		}
		
		if($request->has('search_goods_out_buyer_delivery_to_date') && !empty($request->input('search_goods_out_buyer_delivery_to_date'))){
			$toDate = $request->input('search_goods_out_buyer_delivery_to_date');
			$whereData['toDate'] = \DateTime::createFromFormat('d-m-Y', $toDate) 
			? \DateTime::createFromFormat('d-m-Y', $toDate)->format('Y-m-d')
			: date('Y-m-d', strtotime($toDate));
		}

		if($request->has('search_goods_out_from_warehouse') && !empty($request->input('search_goods_out_from_warehouse'))){
			$searchWarehouses = explode(',' , $request->input('search_goods_out_from_warehouse'));
			$searchDecodeWarehouse = array_map(function ($searchWarehouse){
				return (int)Wild_tiger::decode($searchWarehouse);
			}, $searchWarehouses);
			$whereData['fromWarehouse'] = implode(',', $searchDecodeWarehouse);
		}
		
		$statistics = $this->getGoodsOutStatisticsGraphDetails($whereData);

		// Also prepare top-N warehouses for filtered data
		$buyerDelivery = [];
		$warehouseLabels = $warehouseUnits = $warehouseValues = [];
		$top = 5;
		$whereForBuyer = [];
		if(isset($whereData['fromDate'])){
			$whereForBuyer['gdm.dt_delivery_date >='] = $whereData['fromDate'];
		}
		if(isset($whereData['toDate'])){
			$whereForBuyer['gdm.dt_delivery_date <='] = $whereData['toDate'];
		}
		$buyerDelivery = $this->crudModel->getBuyerDeliveryDetails($whereForBuyer);
		if(!empty($buyerDelivery)){
			$counter = 0;
			foreach($buyerDelivery as $row){
				if($counter >= $top) break;
				$label = (isset($row->v_warehouse_name) ? $row->v_warehouse_name : 'Unknown');
				if(isset($row->v_warehouse_code) && !empty($row->v_warehouse_code)){
					$label .= ' (' . $row->v_warehouse_code . ')';
				}
				$warehouseLabels[] = $label;
				$warehouseUnits[] = (int) ($row->total_units ?? 0);
				$warehouseValues[] = (float) ($row->po_amount_with_vat_gbp ?? 0);
				$counter++;
			}
		}

		return response()->json([
			'status_code' => 1,
			'data' => $statistics,
			'warehouseLabels' => $warehouseLabels,
			'warehouseUnits' => $warehouseUnits,
			'warehouseValues' => $warehouseValues,
		]);
	}

}
