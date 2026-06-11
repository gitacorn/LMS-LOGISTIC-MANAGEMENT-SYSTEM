<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WarehousePalletMasterModel;
use App\WarehouseMasterModel;
use App\Helpers\Twt\Wild_tiger;
use App\Login;
use App\ReportModel;
use App\Models\WarehousePalletForecastLock;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;

class WarehousePalletMaster extends MasterController
{
    public function __construct(){
    	parent::__construct();
    	$this->tableName = config('constants.WAREHOUSE_PALLET_MASTER_TABLE');
    	$this->moduleName = trans('messages.warehouse-pallet-limit');
    	$this->crudModel = new WarehousePalletMasterModel();
    	$this->folderName = config('constants.ADMIN_FOLDER').'/warehouse-pallet-limit/';
    	$this->redirectUrl = config('constants.WAREHOUSE_PALLET_MASTER_URL');
    }
    
    public function index(){
    	if(  strtolower(session()->get('role')) == strtolower(config('constants.ROLE_ADMIN')) || ( strtolower(session()->get('role')) == strtolower(config('constants.ROLE_USER')) && ( checkUserRoleBase(session()->get('user_id')) )   )  ){
    		$data = [];
    		$data['pageTitle'] = $this->moduleName;
    		$data['warehouseDetails'] = WarehouseMasterModel::where('e_record_type',config('constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
    		if( strtolower(session()->get('role')) == strtolower(config('constants.ROLE_USER')) ){
    			$wareHouseId = Login::where([
    					['t_is_active', '=', 1],
    					['i_id','=',session()->get('user_id')],
    			])->first();
	    		$data['wareHouseId'] = $wareHouseId->i_warehouse_id;
			}
    		return view($this->folderName.'warehouse-pallet-limit')->with($data);
    	}
    	return redirect('access-denied');
    }
    
    public function filter(Request $request){
    	$whereData = $likeData = [];
    	$data = [];
    	if(!empty($request->post('search_warehouse_name'))){
    		$whereData['i_warehouse_id'] = (int)Wild_tiger::decode($request->post('search_warehouse_name'));
    	    $recordDetails = $this->crudModel->getRecordDetails($whereData);
    	    $data['recordDetails'] = $recordDetails;
    	    $data['allDates'] = allFifteenDates();

            // Build Pallet Forecast map per date from Dashboard Buyer Delivery source
            $palletForecastMap = [];
            try{
                $reportModel = new ReportModel();
                // Fetch upcoming buyer delivery details (defaults to today..+6 days)
                $buyerDetails = $reportModel->getBuyerDeliveryDetails([
                    'gdm.i_delivery_location_id' => $whereData['i_warehouse_id']
                ]);
                if(!empty($buyerDetails)){
                    foreach($buyerDetails as $bd){
                        $deliveryDate = clientDate($bd->dt_delivery_date);
                        // Only count pallets
                        $pallets = (isset($bd->type_of_pallet_box) && $bd->type_of_pallet_box == config('constants.PALLET')) ? (int)$bd->total_pallets : 0;
                        if(!isset($palletForecastMap[$deliveryDate])){
                            $palletForecastMap[$deliveryDate] = 0;
                        }
                        $palletForecastMap[$deliveryDate] += $pallets;
                    }
                }

                // Overlay locked forecasts for the displayed dates (if any)
                if(!empty($data['allDates'])){
                    $dbDates = [];
                    foreach($data['allDates'] as $d){
                        $dt = \DateTime::createFromFormat('d-m-Y', $d);
                        $dbDates[] = (!empty($dt) ? $dt->format('Y-m-d') : null);
                    }
                    $dbDates = array_filter($dbDates);
                    if(!empty($dbDates)){
                        $locks = WarehousePalletForecastLock::where('t_is_deleted',0)
                            ->where('t_is_active',1)
                            ->where('i_warehouse_id', $whereData['i_warehouse_id'])
                            ->whereIn('dt_forecast_date', $dbDates)
                            ->get();
                        foreach($locks as $lock){
                            $clientD = clientDate($lock->dt_forecast_date);
                            $palletForecastMap[$clientD] = (int)$lock->i_pallet_forecast;
                        }
                    }
                }
            } catch(\Exception $e){
                // Fail silently; view will handle missing forecast
            }
            $data['palletForecastMap'] = $palletForecastMap;

            // Build Pallet Received map for these dates (up to yesterday in app timezone)
            try{
                $palletReceivedMap = [];
                if(!empty($data['allDates'])){
                    $tz = config('app.timezone');
                    $yesterday = Carbon::now($tz)->subDay()->startOfDay();
                    $maxClientDate = end($data['allDates']); reset($data['allDates']);
                    $maxList = \Carbon\Carbon::createFromFormat('d-m-Y', $maxClientDate, $tz)->startOfDay();
                    $rangeEnd = $yesterday->lt($maxList) ? $yesterday : $maxList;
                    $monthStart = Carbon::now($tz)->startOfMonth();
                    if($rangeEnd->gte($monthStart)){
                        $start = $monthStart->format('Y-m-d');
                        $end = $rangeEnd->format('Y-m-d');
                        $rows = DB::table('goods_in_buyer_detail as gbd')
                            ->join('goods_in_buyer_master as gdm', 'gdm.i_id', '=', 'gbd.i_goods_in_buyer_master_id')
                            ->leftJoin('goods_in_logistic_master as glm', function($join){
                                $join->whereRaw('find_in_set(gbd.i_id , glm.i_goods_in_buyer_detail_id)');
                                $join->where('glm.t_is_deleted', 0);
                            })
                            ->where('gdm.i_delivery_location_id', $whereData['i_warehouse_id'])
                            ->whereNotNull('glm.dt_delivery_date')
                            ->whereBetween('glm.dt_delivery_date', [$start, $end])
                            ->where('glm.i_status_id', config('constants.DELIVERED_STATUS_ID'))
                            ->select('glm.dt_delivery_date', DB::raw("SUM(
                                CASE
                                    WHEN gdm.e_collection_type = '". config('constants.DELIVERY') ."' THEN
                                        CASE 
                                            WHEN glm.e_dimension = '". config('constants.PALLET') ."' THEN COALESCE(glm.i_no_of_pallet_box,0)
                                            ELSE CASE WHEN gdm.e_pallet_box_type = '". config('constants.PALLET') ."' THEN COALESCE(gdm.i_no_of_pallet_box,0) ELSE 0 END
                                        END
                                    ELSE
                                        CASE WHEN gdm.e_pallet_box_type = '". config('constants.PALLET') ."' THEN COALESCE(gdm.i_no_of_pallet_box,0) ELSE 0 END
                                END
                            ) as total_pallets"))
                            ->groupBy('glm.dt_delivery_date')
                            ->get();
                        foreach($rows as $r){
                            $clientD = clientDate($r->dt_delivery_date);
                            $palletReceivedMap[$clientD] = (int)$r->total_pallets;
                        }
                    }
                }
                $data['palletReceivedMap'] = $palletReceivedMap;
            } catch(\Exception $e){
                $data['palletReceivedMap'] = [];
            }
    	}
    	$html = view (config('constants.AJAX_VIEW_FOLDER') . 'warehouse-pallet-limit/warehouse-pallet-limit-list')->with($data)->render();
    	return $html;
    }

    /**
     * Export Warehouse Pallet data to Excel (multi-sheet by warehouse)
     * Inputs: warehouse_name_export (encoded, optional), from_date (d-m-Y), to_date (d-m-Y)
     */
    public function exportExcel(Request $request){
        $encodedWarehouse = trim($request->post('warehouse_name_export'));
        $fromDateStr = trim($request->post('from_date'));
        $toDateStr = trim($request->post('to_date'));

        if(empty($fromDateStr) || empty($toDateStr)){
            Wild_tiger::setFlashMessage('error', 'Please select From and To dates.');
            return redirect($this->redirectUrl);
        }

        $fromDate = \DateTime::createFromFormat('d-m-Y', $fromDateStr);
        $toDate = \DateTime::createFromFormat('d-m-Y', $toDateStr);
        if(!$fromDate || !$toDate || $toDate < $fromDate){
            Wild_tiger::setFlashMessage('error', 'Invalid date range provided.');
            return redirect($this->redirectUrl);
        }

        // Build list of warehouses
        $warehousesQuery = WarehouseMasterModel::where('e_record_type',config('constants.WAREHOUSE'))->orderBy('v_warehouse_name','ASC');
        if(!empty($encodedWarehouse)){
            $id = (int)Wild_tiger::decode($encodedWarehouse);
            if($id > 0){
                $warehousesQuery->where('i_id',$id);
            }
        }
        $warehouses = $warehousesQuery->get();
        if($warehouses->isEmpty()){
            Wild_tiger::setFlashMessage('error', 'No warehouse found for export.');
            return redirect($this->redirectUrl);
        }

        // Prepare date arrays and DB range
        $dbFrom = $fromDate->format('Y-m-d');
        $dbTo = $toDate->format('Y-m-d');
        $dateList = [];
        $cursor = clone $fromDate;
        while($cursor <= $toDate){
            $dateList[] = $cursor->format('d-m-Y');
            $cursor->modify('+1 day');
        }

        $spreadsheet = new Spreadsheet();
        $sheetIndex = 0;
        $usedTitles = [];

        foreach($warehouses as $wh){
            if($sheetIndex === 0){
                $sheet = $spreadsheet->getActiveSheet();
            } else {
                $sheet = $spreadsheet->createSheet($sheetIndex);
            }
            // Build safe sheet title: remove invalid characters : \ / ? * [ ] and limit to 31 chars, ensure uniqueness
            $rawTitle = trim(($wh->v_warehouse_name ?: 'Warehouse').' '.($wh->v_warehouse_code ?: ''));
            $invalid = [':', '\\', '/', '?', '*', '[', ']'];
            $safeTitle = str_replace($invalid, '-', $rawTitle);
            $safeTitle = trim($safeTitle);
            if($safeTitle === ''){ $safeTitle = 'Warehouse'; }
            // limit to 31
            $safeTitle = mb_substr($safeTitle, 0, 31);
            // ensure unique within workbook
            $baseTitle = $safeTitle;
            $suffix = 1;
            while(isset($usedTitles[$safeTitle])){
                $suffix++;
                $append = ' ('.$suffix.')';
                $safeTitle = mb_substr($baseTitle, 0, max(0, 31 - mb_strlen($append))) . $append;
            }
            $usedTitles[$safeTitle] = true;
            $sheet->setTitle($safeTitle);

            // Headers
            $sheet->setCellValue('A1', 'Date');
            $sheet->setCellValue('B1', 'Pallet Limit');
            $sheet->setCellValue('C1', 'Pallet Forecasted');
            $sheet->setCellValue('D1', 'Pallet Received');

            // Fetch Pallet Limit for range
            $limitRows = DB::table(config('constants.WAREHOUSE_PALLET_MASTER_TABLE'))
                ->select('dt_pallet_date','i_pallet_limit')
                ->where('i_warehouse_id', $wh->i_id)
                ->whereBetween('dt_pallet_date', [$dbFrom, $dbTo])
                ->get();
            $limitMap = [];
            foreach($limitRows as $r){
                $limitMap[ clientDate($r->dt_pallet_date) ] = $r->i_pallet_limit;
            }

            // Fetch Forecast (locked) for range
            $forecastRows = WarehousePalletForecastLock::where('t_is_deleted',0)
                ->where('t_is_active',1)
                ->where('i_warehouse_id',$wh->i_id)
                ->whereBetween('dt_forecast_date', [$dbFrom, $dbTo])
                ->get();
            $forecastMap = [];
            foreach($forecastRows as $r){
                $forecastMap[ clientDate($r->dt_forecast_date) ] = (int)$r->i_pallet_forecast;
            }

            // Fetch Received using glm.dt_delivery_date (up to min(toDate, yesterday))
            $yesterday = new \DateTime(date('Y-m-d'));
            $yesterday->modify('-1 day');
            $receivedTo = ($yesterday < $toDate) ? $yesterday : $toDate;
            $receivedMap = [];
            if($receivedTo >= $fromDate){
                $rcvFrom = $fromDate->format('Y-m-d');
                $rcvTo = $receivedTo->format('Y-m-d');
                $receivedRows = DB::table('goods_in_buyer_detail as gbd')
                    ->join('goods_in_buyer_master as gdm', 'gdm.i_id', '=', 'gbd.i_goods_in_buyer_master_id')
                    ->leftJoin('goods_in_logistic_master as glm', function($join){
                        $join->whereRaw('find_in_set(gbd.i_id , glm.i_goods_in_buyer_detail_id)');
                        $join->where('glm.t_is_deleted', 0);
                    })
                    ->where('gdm.i_delivery_location_id', $wh->i_id)
                    ->whereNotNull('glm.dt_delivery_date')
                    ->whereBetween('glm.dt_delivery_date', [$rcvFrom, $rcvTo])
                    ->where('glm.i_status_id', config('constants.DELIVERED_STATUS_ID'))
                    ->select('glm.dt_delivery_date', DB::raw("SUM(
                        CASE
                            WHEN gdm.e_collection_type = '". config('constants.DELIVERY') ."' THEN
                                CASE 
                                    WHEN glm.e_dimension = '". config('constants.PALLET') ."' THEN COALESCE(glm.i_no_of_pallet_box,0)
                                    ELSE CASE WHEN gdm.e_pallet_box_type = '". config('constants.PALLET') ."' THEN COALESCE(gdm.i_no_of_pallet_box,0) ELSE 0 END
                                END
                            ELSE
                                CASE WHEN gdm.e_pallet_box_type = '". config('constants.PALLET') ."' THEN COALESCE(gdm.i_no_of_pallet_box,0) ELSE 0 END
                        END
                    ) as total_pallets"))
                    ->groupBy('glm.dt_delivery_date')
                    ->get();
                foreach($receivedRows as $r){
                    $receivedMap[ clientDate($r->dt_delivery_date) ] = (int)$r->total_pallets;
                }
            }

            // Fill rows
            $row = 2;
            foreach($dateList as $d){
                $sheet->setCellValue("A{$row}", $d);
                $sheet->setCellValue("B{$row}", isset($limitMap[$d]) && $limitMap[$d] !== null && $limitMap[$d] !== '' ? (int)$limitMap[$d] : '-');
                $sheet->setCellValue("C{$row}", isset($forecastMap[$d]) ? (int)$forecastMap[$d] : '-');
                $sheet->setCellValue("D{$row}", isset($receivedMap[$d]) ? (int)$receivedMap[$d] : '-');
                $row++;
            }

            $sheetIndex++;
        }

        $fileName = 'warehouse-pallet-limit-'. $fromDate->format('Ymd') .'-'. $toDate->format('Ymd') .'.xlsx';
        $writer = new Xlsx($spreadsheet);
        // Write to a temporary file and use BinaryFileResponse to avoid stream corruption
        $tempPath = tempnam(sys_get_temp_dir(), 'wpl_');
        // Ensure .xlsx extension for proper content-type inference by some clients
        $tempXlsx = $tempPath . '.xlsx';
        @rename($tempPath, $tempXlsx);
        $writer->save($tempXlsx);

        if (ob_get_length()) { @ob_end_clean(); }
        @ini_set('zlib.output_compression', 'Off');
        return response()->download($tempXlsx, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ])->deleteFileAfterSend(true);
    }

    
    public function add(Request $request){
    	if(!empty($request->post())){
    		
    		$successMessage = trans('messages.success-update',['module'=>trans('messages.warehouse-pallet-limit')]);
    		$wareHouseId = ( !empty($request->post('warehouse_name')) ?  (int)Wild_tiger::decode($request->post('warehouse_name')) : '' );
    		$recordData = [];
    		
    		if(strtolower( session()->get('role') ) == strtolower(config('constants.ROLE_USER'))){
    			$userId = session()->get('user_id');
    			$userDetails = Login::where([
					['t_is_active', '=', 1],
					['i_id','=',$userId]])->first();
    			
    			$wareHouseId = (!empty($userDetails) ? $userDetails->i_warehouse_id : 0);
    		}
    		
    		if(!empty($wareHouseId) && ($wareHouseId > 0)){
    			$recordData['i_warehouse_id'] = $wareHouseId;
    			$palletLimit = (!empty( $request->post('pallet_limit') ) ? ($request->post('pallet_limit')) : [] );
    			$palletLimit = (is_array($palletLimit) ? array_filter($palletLimit) : [] );
    			$editPalletLimit = (!empty($request->post('edit_pallet_limit')) ? ($request->post('edit_pallet_limit')) : [] );
    			if(!empty($editPalletLimit)){
    				foreach ($editPalletLimit as $key => $limit){
    					$whereData['i_id'] = (int)Wild_tiger::decode($key);
    					$recordData['i_pallet_limit'] = $limit;
	    				$this->crudModel->updateTableData(config('constants.WAREHOUSE_PALLET_MASTER_TABLE'), $recordData, $whereData);
    				}
    			}
    			
    			if(!empty($palletLimit)){
    				foreach ($palletLimit as $key => $limit){
    					if(!empty($limit) && ($limit > 0)){
	    					$recordData['dt_pallet_date'] = dbDate($key);
	    					$recordData['i_pallet_limit'] = $limit;
    					}
    					$this->crudModel->insertTableData(config('constants.WAREHOUSE_PALLET_MASTER_TABLE'), $recordData);
    				}
    			}
	    		Wild_tiger::setFlashMessage ( 'success', $successMessage  );
    			return redirect ( $this->redirectUrl );
    		}
    	}
    	Wild_tiger::setFlashMessage('error', trans('messages.system-error'));
    	return redirect ( $this->redirectUrl );
    }
    
    public function showHistoryModal(Request $request){
        $data = [];
        $wareHouseId = ( !empty($request->post('warehouse_name')) ?  (int)Wild_tiger::decode($request->post('warehouse_name'))  : 0 );
        $data['warehouseDetails'] = WarehouseMasterModel::where('e_record_type',config('constants.WAREHOUSE'))->orderBy('v_warehouse_name', 'ASC')->get();
        $data['wareHouseId'] = $wareHouseId;
        
        if($wareHouseId > 0){
            $whereData['i_warehouse_id'] = $wareHouseId;
            $recordDetails = $this->crudModel->getRecordDetails($whereData);
            $data['recordDetails'] = $recordDetails;
            $data['allDates'] = getAllDatesOfMonth(date('m-Y'));

            // Build Pallet Forecast (locked) map for current month
            try{
                $palletForecastMap = [];
                if(!empty($data['allDates'])){
                    $dbDates = [];
                    foreach($data['allDates'] as $d){
                        $dt = \DateTime::createFromFormat('d-m-Y', $d);
                        $dbDates[] = (!empty($dt) ? $dt->format('Y-m-d') : null);
                    }
                    $dbDates = array_filter($dbDates);
                    if(!empty($dbDates)){
                        $locks = WarehousePalletForecastLock::where('t_is_deleted',0)
                            ->where('t_is_active',1)
                            ->where('i_warehouse_id', $wareHouseId)
                            ->whereIn('dt_forecast_date', $dbDates)
                            ->get();
                        foreach($locks as $lock){
                            $clientD = clientDate($lock->dt_forecast_date);
                            $palletForecastMap[$clientD] = (int)$lock->i_pallet_forecast;
                        }
                    }
                }
                $data['palletForecastMap'] = $palletForecastMap;
            } catch(\Exception $e){
                $data['palletForecastMap'] = [];
            }

            // Build Pallet Received map (sum of gdm.i_no_of_pallet_box per day) up to yesterday, grouped by glm.dt_delivery_date
            try{
                $palletReceivedMap = [];
                if(!empty($data['allDates'])){
                    // Calculate month range limited to yesterday using app timezone
                    $tz = config('app.timezone');
                    $monthStart = Carbon::now($tz)->startOfMonth();
                    $yesterday = Carbon::now($tz)->subDay()->startOfDay();
                    // If yesterday is before month start, no data
                    if($yesterday->gte($monthStart)){
                        $start = $monthStart->format('Y-m-d');
                        $end = $yesterday->format('Y-m-d');
                        $rows = DB::table('goods_in_buyer_detail as gbd')
                            ->join('goods_in_buyer_master as gdm', 'gdm.i_id', '=', 'gbd.i_goods_in_buyer_master_id')
                            ->leftJoin('goods_in_logistic_master as glm', function($join){
                                $join->whereRaw('find_in_set(gbd.i_id , glm.i_goods_in_buyer_detail_id)');
                                $join->where('glm.t_is_deleted', 0);
                            })
                            ->where('gdm.i_delivery_location_id', $wareHouseId)
                            ->whereNotNull('glm.dt_delivery_date')
                            ->whereBetween('glm.dt_delivery_date', [$start, $end])
                            ->where('glm.i_status_id', config('constants.DELIVERED_STATUS_ID'))
                            ->select('glm.dt_delivery_date', DB::raw("SUM(
                                CASE
                                    WHEN gdm.e_collection_type = '". config('constants.DELIVERY') ."' THEN
                                        CASE 
                                            WHEN glm.e_dimension = '". config('constants.PALLET') ."' THEN COALESCE(glm.i_no_of_pallet_box,0)
                                            ELSE CASE WHEN gdm.e_pallet_box_type = '". config('constants.PALLET') ."' THEN COALESCE(gdm.i_no_of_pallet_box,0) ELSE 0 END
                                        END
                                    ELSE
                                        CASE WHEN gdm.e_pallet_box_type = '". config('constants.PALLET') ."' THEN COALESCE(gdm.i_no_of_pallet_box,0) ELSE 0 END
                                END
                            ) as total_pallets"))
                            ->groupBy('glm.dt_delivery_date')
                            ->get();
                        foreach($rows as $r){
                            $clientD = clientDate($r->dt_delivery_date);
                            $palletReceivedMap[$clientD] = (int)$r->total_pallets;
                        }
                    }
                }
                $data['palletReceivedMap'] = $palletReceivedMap;
            } catch(\Exception $e){
                $data['palletReceivedMap'] = [];
            }
        }
        $html = view (config('constants.AJAX_VIEW_FOLDER') . 'warehouse-pallet-limit/warehouse-pallet-history-modal')->with($data)->render();
        return $html;
    }
    
    public function historyModalFilter(Request $request){
    	$whereData = $likeData = [];
    	$data = [];
    	
    	$searchMonth = (!empty($request->post('search_month')) ? trim($request->post('search_month')) : date('m-Y') );
    	if(!empty($searchMonth)){
    		list($searchMonthNumber, $searchYear) = explode('-', $searchMonth);
    		$startDate = new \DateTime("$searchYear-$searchMonthNumber-01");
		    $endDate = new \DateTime("$searchYear-$searchMonthNumber-01");
		    $endDate->modify('first day of next month'); 
			$startDateFormatted = $startDate->format('Y-m-d');
		    $endDateFormatted = $endDate->format('Y-m-d');
    		
    		$whereData['dt_pallet_date >='] = $startDateFormatted;
    		$whereData['dt_pallet_date <'] = $endDateFormatted;
    		
    	}
        if(!empty($request->post('search_warehouse_name'))){
            $whereData['i_warehouse_id'] = (int)Wild_tiger::decode($request->post('search_warehouse_name'));
            $recordDetails = $this->crudModel->getRecordDetails($whereData);
            $data['recordDetails'] = $recordDetails;
            $data['allDates'] = getAllDatesOfMonth($searchMonth);

            // Build Pallet Forecast (locked) map for selected month
            try{
                $palletForecastMap = [];
                if(!empty($data['allDates'])){
                    $dbDates = [];
                    foreach($data['allDates'] as $d){
                        $dt = \DateTime::createFromFormat('d-m-Y', $d);
                        $dbDates[] = (!empty($dt) ? $dt->format('Y-m-d') : null);
                    }
                    $dbDates = array_filter($dbDates);
                    if(!empty($dbDates)){
                        $locks = WarehousePalletForecastLock::where('t_is_deleted',0)
                            ->where('t_is_active',1)
                            ->where('i_warehouse_id', $whereData['i_warehouse_id'])
                            ->whereIn('dt_forecast_date', $dbDates)
                            ->get();
                        foreach($locks as $lock){
                            $clientD = clientDate($lock->dt_forecast_date);
                            $palletForecastMap[$clientD] = (int)$lock->i_pallet_forecast;
                        }
                    }
                }
                $data['palletForecastMap'] = $palletForecastMap;
            } catch(\Exception $e){
                $data['palletForecastMap'] = [];
            }

            // Build Pallet Received map (sum of gdm.i_no_of_pallet_box per day) up to yesterday for selected month, grouped by glm.dt_delivery_date
            try{
                $palletReceivedMap = [];
                if(!empty($data['allDates'])){
                    // Determine selected month boundaries
                    list($m, $Y) = explode('-', $searchMonth);
                    $tz = config('app.timezone');
                    $monthStart = Carbon::createFromFormat('Y-m-d', $Y.'-'.$m.'-01', $tz)->startOfDay();
                    $monthEnd = $monthStart->copy()->endOfMonth()->startOfDay();
                    $yesterday = Carbon::now($tz)->subDay()->startOfDay();
                    // End date is the min of monthEnd and yesterday; ensure after monthStart
                    $rangeEnd = $yesterday->lt($monthStart) ? null : ( $yesterday->lt($monthEnd) ? $yesterday : $monthEnd );
                    if($rangeEnd){
                        $start = $monthStart->format('Y-m-d');
                        $end = $rangeEnd->format('Y-m-d');
                        $rows = DB::table('goods_in_buyer_detail as gbd')
                            ->join('goods_in_buyer_master as gdm', 'gdm.i_id', '=', 'gbd.i_goods_in_buyer_master_id')
                            ->leftJoin('goods_in_logistic_master as glm', function($join){
                                $join->whereRaw('find_in_set(gbd.i_id , glm.i_goods_in_buyer_detail_id)');
                                $join->where('glm.t_is_deleted', 0);
                            })
                            ->where('gdm.i_delivery_location_id', $whereData['i_warehouse_id'])
                            ->whereNotNull('glm.dt_delivery_date')
                            ->whereBetween('glm.dt_delivery_date', [$start, $end])
                            ->where('glm.i_status_id', config('constants.DELIVERED_STATUS_ID'))
                            ->select('glm.dt_delivery_date', DB::raw("SUM(
                                CASE
                                    WHEN gdm.e_collection_type = '". config('constants.DELIVERY') ."' THEN
                                        CASE 
                                            WHEN glm.e_dimension = '". config('constants.PALLET') ."' THEN COALESCE(glm.i_no_of_pallet_box,0)
                                            ELSE CASE WHEN gdm.e_pallet_box_type = '". config('constants.PALLET') ."' THEN COALESCE(gdm.i_no_of_pallet_box,0) ELSE 0 END
                                        END
                                    ELSE
                                        CASE WHEN gdm.e_pallet_box_type = '". config('constants.PALLET') ."' THEN COALESCE(gdm.i_no_of_pallet_box,0) ELSE 0 END
                                END
                            ) as total_pallets"))
                            ->groupBy('glm.dt_delivery_date')
                            ->get();
                        foreach($rows as $r){
                            $clientD = clientDate($r->dt_delivery_date);
                            $palletReceivedMap[$clientD] = (int)$r->total_pallets;
                        }
                    }
                }
                $data['palletReceivedMap'] = $palletReceivedMap;
            } catch(\Exception $e){
                $data['palletReceivedMap'] = [];
            }
        }
        $html = view (config('constants.AJAX_VIEW_FOLDER') . 'warehouse-pallet-limit/warehouse-pallet-history-list')->with($data)->render();
        return $html;
    }
}