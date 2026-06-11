<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\MySoftDeletes;
use App\Http\Controllers\WarehouseMasterController;
use GhanuZ\FindInSet\FindInSetRelationTrait;
use DB;
use App\Models\ShipmentInfoModel;

class UsWarehouseToAmazonDetailsModel extends BaseModel
{
	use MySoftDeletes,FindInSetRelationTrait;
	protected  $table = '';
	protected $primaryKey = 'i_id';
	protected $dates = ['dt_deleted_at'];
	
	public function __construct(){
		parent::__construct();
		$this->table = config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE');
	}
	public function usWarehouseToAmazonMaster(){
		return $this->belongsTo(UsWarehouseToAmazonMasterModel::class,'i_us_warehouse_to_amazon_master_id');
	}
	public function accountComapnyInfo(){
		return $this->belongsTo(CompanyMasterModel::class,'i_account_company_id');
	}
	public function amazonFromWarehouseInfo(){
		return $this->belongsTo(WarehouseMasterModel::class,'i_amazon_from_warehouse_id');
	}
	public function toAmazonLocationInfo(){
		return $this->belongsTo(WarehouseMasterModel::class,'i_to_amazon_location_id');
	}
	public function customerInfo(){
		return $this->belongsTo(CustomerMasterModel::class,'i_customer_id');
	}
	public function fromWarehouseInfo(){
		return $this->belongsTo(WarehouseMasterModel::class,'i_customer_from_warehouse_id');
	}
	/* public function toCustomerLocationInfo(){
		return $this->belongsTo(WarehouseMasterModel::class,'i_to_customer_id');
	} */
	public function toCustomerLocationInfo(){
		return $this->belongsTo(CustomerMasterModel::class,'i_to_customer_id');
	}
	public function ukAccountCompanyInfo(){
		return $this->belongsTo(CompanyMasterModel::class,'i_uk_account_id');
	}
	public function ukFromWarehouseInfo(){
		return $this->belongsTo(WarehouseMasterModel::class,'i_uk_from_warehouse_id');
	}
	public function ukToWarehouseInfo(){
		return $this->belongsTo(WarehouseMasterModel::class,'i_uk_to_warehouse_id');
	}
	public function shipmentRecordIfo(){
		return $this->hasOne(ShipmentInfoModel::class , 'i_ref_table_id');
	}
	public function ukAccountCompnyMaster(){
		return $this->FindInSetMany('App\CompanyMasterModel', 'v_uk_account_ids', 'i_id');
	}
	
	public function usaContainerDetailInfo(){
		return $this->hasOne(UsaContainerClubbingDetailModel::class , 'i_fba_sheet_detail_id')->where('e_record_type', config('constants.USA_CONTAINER_CLUBBING_NOT_FBA_RECORD'));
	}
	
	public function getRecordDetails( $where = [] , $likeData = [] ){
			
		$query = UsWarehouseToAmazonDetailsModel::with( [ 'shipmentRecordIfo',
				'usWarehouseToAmazonMaster.usWarehouseToAmazonDetails','usWarehouseToAmazonMaster.bookByEmployee','accountComapnyInfo','amazonFromWarehouseInfo','toAmazonLocationInfo','customerInfo','fromWarehouseInfo','toCustomerLocationInfo','ukAccountCompanyInfo','ukFromWarehouseInfo',
				'ukToWarehouseInfo','ukAccountCompnyMaster','usWarehouseToAmazonMaster.logisticPartnerMasterInfo','usWarehouseToAmazonMaster.statusInfo'
		]);
		if( isset($where['fba_po_no']) && ( ($where['fba_po_no']) == true ) ){
			$query->select( [ '*' , DB::raw('CASE WHEN v_shipment_id is not null THEN v_shipment_id WHEN v_invoice_no_ref_no  is not null THEN v_invoice_no_ref_no WHEN v_shipment_invoice_no  is not null THEN v_shipment_invoice_no ELSE "" END as fba_report_no') ]);
			$query->orderBy('fba_report_no', "asc" );
		} else{
			$query->orderBy('i_id', "DESC");
		}
		if(isset($where['master_id']) && (!empty($where['master_id'])) ){
			$masterRecordId = $where['master_id'];
			$query->whereHas('usWarehouseToAmazonMaster' , function($query) use($masterRecordId) {
				$query->where('i_us_warehouse_to_amazon_master_id',$masterRecordId);
					
			});
		}
		if(isset($where['ref_record_type']) && (!empty($where['ref_record_type'])) ){
			$refRecordType = $where['ref_record_type'];
			$query->whereHas('shipmentRecordIfo' , function($query) use($refRecordType) {
				$query->where('v_ref_record_type',$refRecordType);
					
			});
		}
		if( isset($where['custom_where']) && (!empty($where['custom_where'])) ){
			$searchFBANo = ( $where['custom_where'] );
			$query->whereRaw($where['custom_where']);
			
		}
		
		$pageNo = ( ( isset($where['page']) && (!empty($where['page'])) ) ? $where['page'] : 1 );
			
		if(isset($where['singleRecord']) && ( $where['singleRecord'] != false )  ){
			$data = $query->first();
		} else if(isset($where['count_record']) && ( ($where['count_record']) == true )){
			$data = $query->get();
		} else{
			$data = $query->paginate( $this->perPage,['*'],'page', $pageNo );
		}
		return $data;
			
	}
}
