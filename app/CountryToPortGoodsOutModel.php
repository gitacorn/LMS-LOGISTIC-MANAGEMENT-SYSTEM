<?php

namespace App;
use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MySoftDeletes;
use App\WarehouseMasterModel;
use App\Login;
use App\LogisticPartnerDetailModel;
use App\CurrencyMasterModel;
use App\StatusMasterModel;
use App\CountryToPortGoodsOutDocumentModel;
use App\CountryToPortGoodsOutInvoiceModel;
use App\Helpers\Twt\Wild_tiger;
use App\ImportFileHistoryModel;
use App\Models\PortToContainerInfoModel;
use App\Models\CountryToPortGoodsOutShipmentModel;

class CountryToPortGoodsOutModel extends BaseModel
{
    use HasFactory,MySoftDeletes;
    protected  $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];
    
    public function __construct(){
    	parent::__construct();
    	$this->table = config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_TABLE');
    	$this->perPage = config ( 'constants.PER_PAGE' );
    }
    
    public function fromPortInfo(){
    	return $this->belongsTo(WarehouseMasterModel::class,'i_transport_from_id');
    }
    
    public function toPortInfo(){
    	return $this->belongsTo(WarehouseMasterModel::class,'i_transport_to_id');
    }
    
    public function bookEmployeeInfo(){
    	return $this->belongsTo(Login::class,'i_book_by_employee_id');
    }
    
    /* public function logisticPartnerDetail(){
    	return $this->belongsTo(LogisticPartnerDetailModel::class,'i_logistic_partner_detail_id');
    } */
    
    public function currencyInfo(){
    	return $this->belongsTo(CurrencyMasterModel::class,'i_goods_out_currency_id');
    }
    public function statusInfo(){
    	return $this->belongsTo(StatusMasterModel::class,'i_status_id');
    }
    public function shipmentInfo(){
    	return $this->hasMany(CountryToPortGoodsOutShipmentModel::class,'i_country_to_port_goods_out_master_id');
    }
    public function documentInfo(){
    	return $this->hasMany(CountryToPortGoodsOutDocumentModel::class,'i_country_to_port_goods_out_master_id');
    }
    public function invoiceInfo(){
    	return $this->hasMany(CountryToPortGoodsOutInvoiceModel::class,'i_country_to_port_goods_out_master_id');
    }
    public function logisticPartnerMaster(){
    	return $this->belongsTo(LogisticPartnerMasterModel::class,'i_logistic_partner_detail_id');
    }
    public function uploadFBASheetInfo(){
    	return $this->belongsTo(ImportFileHistoryModel::class,'i_lastet_import_file_id');
    }
    public function fbaSheetMaster(){
    	return $this->hasMany(FBASheetMasterModel::class,'i_country_to_port_goods_out_master_id');
    }
    public function portToAgentaContainerInfo(){
    	return $this->hasOne(PortToContainerInfoModel::class,'i_container_id');
    }
    public function fromWarehouseCountry(){
    	return $this->belongsTo(CountryMasterModel::class,'i_from_warehouse_country_id');
    }
    public function warehouseInfo(){
    	return $this->belongsTo(WarehouseMasterModel::class,'i_warehouse_id');
    }
    public function getRecordDetails( $where = [] , $likeData = [] ){
    	
    	$query = CountryToPortGoodsOutModel::with( [
    			'portToAgentaContainerInfo.countryToPortInfo','documentInfo','invoiceInfo','fromPortInfo','toPortInfo','bookEmployeeInfo','currencyInfo','statusInfo','logisticPartnerMaster','uploadFBASheetInfo', 'fbaSheetMaster'
    	]);
    	
    	if(isset($where['master_id']) && (!empty($where['master_id'])) ){
    		if(is_array($where['master_id'])){
    			$query->whereIn('i_id',$where['master_id']);
    		} else {
    			$masterRecordId = $where['master_id'];
    			$query->where('i_id','=',$masterRecordId);
    		}
    		
    		
    	}
    	
    	if( isset($where['edit_record']) && ( ($where['edit_record']) == true ) ){
    		$query->groupBy('i_id');
    	}
    	
    	if(isset($where['supplier_detail']) && (!empty($where['supplier_detail'])) ){
    		$supplierDetailId = $where['supplier_detail'];
    		$query->where('i_supplier_id',$supplierDetailId);
    	}
    	
    	if(isset($where['transport_way']) && (!empty($where['transport_way'])) ){
    		$collectionType = $where['transport_way'];
    		$query->where('e_transport_way','=',$collectionType);
    	}
    	
    	if(isset($where['from_port_airport']) && (!empty($where['from_port_airport'])) ){
    		$fromPortId = (int)($where['from_port_airport']);
    		$query->where('i_transport_from_id',$fromPortId);
    	}
    	
    	if(isset($where['to_port_airport']) && (!empty($where['to_port_airport'])) ){
    		$toPortId = (int)($where['to_port_airport']);
    		$query->where('i_transport_to_id',$toPortId);
    	}
    	
    	if(isset($where['book_by']) && (!empty($where['book_by'])) ){
    		$bookEmployeeId = $where['book_by'];
    		$query->where('i_book_by_employee_id',$bookEmployeeId);
    	}
    	
    	if(isset($where['logistic_partner_uk']) && (!empty($where['logistic_partner_uk'])) ){
    		$logisticPartnerId = $where['logistic_partner_uk'];
    		$query->where('i_logistic_partner_detail_id',$logisticPartnerId);
    	}
    	
    	if(isset($where['etd_dispatch_from_date']) && (!empty($where['etd_dispatch_from_date'])) ){
    		$estimateDispatchFromDate = dbDate( $where['etd_dispatch_from_date'] );
    		$query->where('dt_est_dispatch_date','>=',$estimateDispatchFromDate);
    	}
    	
    	if(isset($where['etd_dispatch_to_date']) && (!empty($where['etd_dispatch_to_date'])) ){
    		$estimateDispatchToDate = dbDate( $where['etd_dispatch_to_date'] );
    		$query->where('dt_est_dispatch_date','<=',$estimateDispatchToDate);
    	}
    	
    	if(isset($where['eta_arrival_from_date']) && (!empty($where['eta_arrival_from_date'])) ){
    		$estimateArrivalFromDate = dbDate( $where['eta_arrival_from_date'] );
    		$query->where('dt_est_port_arrival_date','>=',$estimateArrivalFromDate);
    	}
    	
    	if(isset($where['eta_arrival_to_date']) && (!empty($where['eta_arrival_to_date'])) ){
    		$estimateArrivalToDate = dbDate( $where['eta_arrival_to_date'] );
    		$query->where('dt_est_port_arrival_date','<=',$estimateArrivalToDate);
    	}
    	
    	if(isset($where['dangerous_goods']) && (!empty($where['dangerous_goods'])) ){
    		$insuranceStatus = $where['dangerous_goods'];
    		$query->where('e_dangerous_goods','=',$insuranceStatus);
    	}
    	
    	if(isset($where['insurance_status']) && (!empty($where['insurance_status'])) ){
    		$insuranceStatus = $where['insurance_status'];
    		$query->where('e_insurance_status','=',$insuranceStatus);
    	}
    	if(isset($where['t_is_active']) && (!empty($where['t_is_active'])) ){
    		$query->where('t_is_active',$where['t_is_active']);
    	}
    	$query->whereHas('statusInfo',function ($q){
    		$q->where('t_is_deleted',0);
    	});
    	if(isset($where['status']) && (!empty($where['status'])) ){
    		$statusId = $where['status'];
    		$query->whereIn('i_status_id',$statusId);
    	
    	}
    	if(isset($where['default_status']) && (!empty($where['default_status'])) ){
    		$statusIds = $where['default_status'];
    		$query->whereNotIn('i_status_id',$statusIds);
    	
    	}
    	if(isset($where['process_status']) && (!empty($where['process_status'])) ){
    		$processStatus = ($where['process_status']);
    		$query->where('e_process_status',$processStatus);
    	}
    	
    	if(isset($where['from_warehouse_country']) && (!empty($where['from_warehouse_country'])) ){
    		$query->where('i_from_warehouse_country_id',$where['from_warehouse_country']);
    	}
    	
    	if(isset($where['warehouse']) && (!empty($where['warehouse'])) ){
    		$query->where('i_warehouse_id',$where['warehouse']);
    	}
    	
    	if(isset($where['pick_up_from_date_from_warehouse']) && (!empty($where['pick_up_from_date_from_warehouse'])) ){
    		$query->where('dt_pick_up_date_from_warehouse','>=',dbDate( $where['pick_up_from_date_from_warehouse'] ));
    	}
    	 
    	if(isset($where['pick_up_to_date_from_warehouse']) && (!empty($where['pick_up_to_date_from_warehouse'])) ){
    		$query->where('dt_pick_up_date_from_warehouse','<=',dbDate( $where['pick_up_to_date_from_warehouse'] ));
    	}
    	
    	if(isset($where['fba_status']) && (!empty($where['fba_status'])) ){
    		$fbaStatus = ($where['fba_status']);
    		if( $fbaStatus ==  config('constants.NOT_UPLOADLED_STATUS')){
    			$query->where('i_lastet_import_file_id',null);
    		} else {
    			$query->whereHas('uploadFBASheetInfo' , function($query) use($fbaStatus) {
    				$query->where('e_status',$fbaStatus);
    			});
    		}
    	}
    	if(isset($where['delivery_from_date']) && (!empty($where['delivery_from_date'])) ){
    		$deliveryFromDate = dbDate( $where['delivery_from_date'] );
    		$query->where('dt_delivery_date','>=',$deliveryFromDate);
    	}
    	
    	if(isset($where['delivery_to_date']) && (!empty($where['delivery_to_date'])) ){
    		$deliveryToDate = dbDate( $where['delivery_to_date'] );
    		$query->where('dt_delivery_date','<=',$deliveryToDate);
    	}
    	if(isset($where['container_process_status']) && (!empty($where['container_process_status']))){
    		$processStatus = $where['container_process_status'];
    		$query->whereHas('portToAgentaContainerInfo' , function($query) use($processStatus) {
    			$query->where('e_container_status',$processStatus);
    		});
    	}
    	if(isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
    	
    		$searchString = ( $likeData['searchBy'] );
    			
    		$allLikeColumns = [ 'v_seal_house_waybill_no' , 'v_container_air_waybill_no' , 'v_tracking_no','v_country_to_port_record_no', 'v_personal_ref' ];
    	
    		$query->where(function($q) use ($allLikeColumns,$searchString){
    			$q->orWhereHas('fbaSheetMaster.fbaSheetDetail', function($q1) use($searchString) {
    				$allOtherLikeColumns = [ 'v_fba_po_no' ];
    					
    				$q1->where(function($q2) use ($allOtherLikeColumns,$searchString){
    					foreach($allOtherLikeColumns as $key => $allLikeColumn){
    						$q2->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
    					}
    				});
    			});
    			
    			foreach($allLikeColumns as $key => $allLikeColumn){
    				$q->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
    			}
    		});
    	}
    	$query->orderBy('i_id', "DESC" ) ;
    	$pageNo = ( ( isset($where['page']) && (!empty($where['page'])) ) ? $where['page'] : 1 );
    	
    	if( isset($where['count_record']) && ( ($where['count_record']) == true ) ){
    		$data = $query->get( );
    	} else {
    		$data = $query->paginate( $this->perPage,['*'],'page', $pageNo );
    	}
    	return $data;
    	
    }
    public function getFbaSheetRecordDetails( $whereData = [] , $likeData = [] , $additionalData = [] ){
    	
    
    	$defaultWhere = [];
    	$defaultWhere['t_is_deleted != ' ] = 1;
    	$defaultWhere['order_by'] = ['i_id'=>'desc'];
    
    	$tableName = config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE');
    		
    	$selectData = [
    			'i_id',
    			'v_fba_po_no',
    			'v_ref_id',
    			'i_company_id',
    			'e_destination',
    			'v_units',
    			'v_company_code',
    			'v_location_code',
    			'i_warehouse_location_id',
    			'v_product',
    			'v_sku',
    			'v_amazon_address',
    			'v_boxes',
    			'i_boxes_units',
    			'e_status',
    			'i_pallet_no',
    			'v_pallet_weight',
    			'v_pallet',
    			'v_pallet_dimension',
    			'i_total_no_of_pallets',
    			'i_fba_sheet_master_id',
    			'v_fba_value',
    	];
    
    	$whereData = (!empty($whereData) ? array_merge( $defaultWhere , $whereData) : $defaultWhere );
    		
    	$data = $this->getSingleRecordById( $tableName, $selectData,  $whereData, $likeData, $additionalData );
    	
    	return $data;
    		
    }
   
}
