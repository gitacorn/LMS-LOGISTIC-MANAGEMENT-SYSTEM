<?php

namespace App\Http\Controllers;

use App\Http\Controllers\MasterController;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\LoginHistory;

class LoginHistoryController extends MasterController
{
    //
	public function __construct(){
		//parent::__construct();
		$this->middleware('checklogin');
		$this->crudModel = new LoginHistory();
		$this->perPageRecord = 20;
	}
	
	public function index(){
		 
		$data['pageTitle'] =  trans('messages.login-history');;
		
		$where = [];
		
		$paginationData = [];
		
		$pageNo = config('constants.DEFAULT_PAGE_INDEX');
		
		if ($pageNo == config('constants.DEFAULT_PAGE_INDEX') ){
			
			$total = count($this->crudModel->getRecordDetail($where));
				
			$lastpage = ceil($total/$this->perPageRecord);
				
			$paginationData['current_page'] = config('constants.DEFAULT_PAGE_INDEX');
				
			$paginationData['per_page'] = $this->perPageRecord;
				
			$paginationData['last_page'] = $lastpage;
			
		}
		
		$where['limit'] = $this->perPageRecord;
		
		$data['page_no'] = $pageNo;
			
		$data['perPageRecord'] = $this->perPageRecord;
			
		$data['recordDetails'] = $this->crudModel->getRecordDetail ( $where );;
		
		$data['pagination'] = $paginationData;
		
		$data['totalRecordCount'] = $total;
		
		return view('admin/login-history' , $data);
	}
	
	public function filter(Request $request) {
		if ($request->ajax ()) {
			$whereData = $likeData  = $additionalData =  [];
				
			$pageNo = (! empty ( $request->input ( 'page' ) )) ? ( int ) $request->input ( 'page' ) : 1;
			
			$paginationData = [];
			
			if ((! empty ( $request->input ( 'search_status' ) )) && ($request->input ( 'search_status' ) != 'all')) {
				$whereData ['lh.t_is_active'] = strtolower($request->input ( 'search_status' ) == config('constants.ENABLE_STATUS') ? 1 : 0);
			}
				
			if (! empty ( $request->post ( 'search_start_date' ) )) {
				$startDate = dbDate( $request->input ( 'search_start_date' ) );
				$whereData['custom_function'][] =  "date(lh.dt_created_at) >=  '".$startDate."'";
			}
			
			if (! empty ( $request->post ( 'search_end_date' ) )) {
				$endDate = dbDate( $request->input ( 'search_end_date' ) );
				$whereData['custom_function'][] =  "date(lh.dt_created_at) <=  '".$endDate."'";
			}
				
			if (! empty ( $request->post ( 'search_by' ) )) {
				$searchValue = trim($request->input ( 'search_by' ));
				$likeData ['lm.v_name'] = $searchValue;
				$likeData ['lm.v_email'] = $searchValue;
			}
			
			if ($pageNo == config('constants.DEFAULT_PAGE_INDEX') ){
					
				$total = count($this->crudModel->getRecordDetail($whereData , $likeData ));
			
				$lastpage = ceil($total/$this->perPageRecord);
			
				$paginationData['current_page'] = config('constants.DEFAULT_PAGE_INDEX');
			
				$paginationData['per_page'] = $this->perPageRecord;
			
				$paginationData['last_page'] = $lastpage;
					
			}
			
			if ($pageNo == config('constants.DEFAULT_PAGE_INDEX')) {
				$whereData['offset'] = 0;
				$whereData['limit'] = $this->perPageRecord;
			} else if ($pageNo > config('constants.DEFAULT_PAGE_INDEX')) {
				$whereData['offset'] = ($pageNo - 1) * $this->perPageRecord;
				$whereData['limit'] = $this->perPageRecord;
			}
				
			$recordDetails = $this->crudModel->getRecordDetail ( $whereData, $likeData );
				
			$data = [];
			
			$data['page_no'] = $pageNo;
			
			$data['perPageRecord'] = $this->perPageRecord;
			
			$data['recordDetails'] = $recordDetails;
			
			$data['pagination'] = $paginationData;
			
			if(isset($total)){
				$data['totalRecordCount'] = $total;
			}
			
			$html = view ( config('constants.AJAX_VIEW_FOLDER') . 'login-history/login-history-list' )->with ( $data )->render();
			
			return response ( $html );
		}
	}
}
