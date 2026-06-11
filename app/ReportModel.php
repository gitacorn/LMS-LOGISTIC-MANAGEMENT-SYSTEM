<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\BaseModel;
use DB;
use App\Traits\MySoftDeletes;
use Illuminate\Support\Facades\Config;
use App\CountrytoPortEuropeDetailModel;

class ReportModel extends BaseModel
{
	public function getAverageSummaryDetails( $whereData = [] , $likeData = [] , $additionalData = [] ){
		
		$defaultWhere = $selectData = $joinData = [];
		
		if(isset($whereData['supplierDetails']) && ( $whereData['supplierDetails'] != false ) ){
			unset($whereData['supplierDetails']);
			$defaultWhere = [
					'gbd.t_is_deleted !=' => 1,
					'order_by' => ['po_amount_with_vat_gbp' => 'desc'],
					'group_by' => 'gdm.i_buyer_company_id',
			];
			
			// Handle status filter - if not explicitly set, default to DELIVERED
			if(isset($whereData['status_filter']) && $whereData['status_filter'] == 'IN_TRANSIT'){
				$defaultWhere['gbd.t_is_all_delivered_cancelled_ststus'] = 0; // Not delivered
				unset($whereData['status_filter']);
			} else {
				// Default to DELIVERED
				$defaultWhere['glm.i_status_id'] = config('constants.DELIVERED_STATUS_ID');
			}
			
			$selectData = [
					'cm.v_company_name as v_company_name',
					'sm.v_supplier_name as v_supplier_name',
					'po_currency.d_gbp_conversation_rate',
					DB::raw('SUM(gdm.i_total_units) as total_units'),
					DB::raw("SUM(
					    CASE
					        WHEN gdm.e_collection_type = '". config('constants.DELIVERY') ."' THEN
					            CASE
					                WHEN glm.e_dimension = '". config('constants.PALLET') ."' THEN glm.i_no_of_pallet_box
					                ELSE 
					                    CASE WHEN gdm.e_pallet_box_type = '". config('constants.PALLET') ."' THEN gdm.i_no_of_pallet_box ELSE 0 END
					            END
					        ELSE
					            CASE
					                WHEN gdm.e_pallet_box_type = '". config('constants.PALLET') ."' THEN gdm.i_no_of_pallet_box
					                ELSE 0
					            END
					    END
					) AS total_pallets"),
					
					### Total Boxes (for "Box" type only):
					DB::raw("SUM(
					    CASE
					        WHEN gdm.e_collection_type = '". config('constants.DELIVERY') ."' THEN
					            CASE
					                WHEN glm.e_dimension = '". config('constants.BOX') ."' THEN glm.i_no_of_pallet_box
					                ELSE 
					                    CASE WHEN gdm.e_pallet_box_type = '". config('constants.BOX') ."' THEN gdm.i_no_of_pallet_box ELSE 0 END
					            END
					        ELSE
					            CASE
					                WHEN gdm.e_pallet_box_type = '". config('constants.BOX') ."' THEN gdm.i_no_of_pallet_box
					                ELSE 0
					            END
					    END
					) AS total_boxes"),
					
					### Type of Pallet Box (Final Type, but not used in GROUP BY):
					DB::raw('
					    CASE 
					        WHEN gdm.e_collection_type = "'. config('constants.DELIVERY') .'" THEN 
					            CASE WHEN glm.e_dimension != "" THEN glm.e_dimension ELSE gdm.e_pallet_box_type END 
					        ELSE gdm.e_pallet_box_type 
					    END AS type_of_pallet_box_final'
					),
					
					### Final Number of Pallet Boxes:
					DB::raw('
					    SUM(
					        CASE 
					            WHEN gdm.e_collection_type = "'. config('constants.DELIVERY') .'" THEN 
					                CASE WHEN glm.i_no_of_pallet_box > 0 THEN glm.i_no_of_pallet_box ELSE gdm.i_no_of_pallet_box END 
					            ELSE gdm.i_no_of_pallet_box 
					        END
					    ) AS no_of_pallet_box_final'
					),
					
					### PO Amount Calculation (unchanged):
					DB::raw(
					    "SUM(
					        CASE 
					            WHEN `gdm`.`d_po_amount_with_vat` IS NOT NULL 
					                AND `gdm`.`d_po_amount_with_vat` > 0 
					                AND `po_currency`.`d_gbp_conversation_rate` > 0 
					            THEN `gdm`.`d_po_amount_with_vat` * `po_currency`.`d_gbp_conversation_rate`
					            ELSE 0
					        END
					    ) AS po_amount_with_vat_gbp"
					),
			];
			
			$joinData = [
					[
							'tableName' =>	config('constants.GOODS_IN_BUYER_MASTER_TABLE') . ' as gdm',
							'condition' =>	"gdm.i_id = gbd.i_goods_in_buyer_master_id",
					],
					[
							'tableName' =>	config('constants.GOODS_IN_LOGISTIC_MASTER_TABLE') . ' as glm',
							'condition' => [ 'custom' =>  "find_in_set(gbd.i_id , glm.i_goods_in_buyer_detail_id) and glm.t_is_deleted = 0"],
							'type' => 'left',
					],
					[
							'tableName' =>	config('constants.SUPPLIER_DETAIL_TABLE') . ' as sd',
							'condition' =>	"sd.i_id = gbd.i_goods_in_buyer_supplier_id",
					],
					[
							'tableName' =>	config('constants.SUPPLIER_MASTER_TABLE') . ' as sm',
							'condition' =>	"sm.i_id = sd.i_supplier_id",
					],
					[
							'tableName' =>	config('constants.COMPANY_MASTER_TABLE') . ' as cm',
							'condition' =>	"cm.i_id = gdm.i_buyer_company_id",
							'type' => 'left',
					],
					[
							'tableName' =>	config('constants.CURRENCY_MASTER_TABLE') . ' as po_currency',
							'condition' =>	"po_currency.i_id = gdm.i_po_currency_id",
					],
					[
							'tableName' =>	config('constants.COUNTRY_MASTER_TABLE') . ' as scm',
							'condition' =>	"scm.i_id = sd.i_country_id and scm.t_is_deleted = 0",
							'type' => 'inner',
					],
			];
			
		} else {
			$defaultWhere = [
					'gbd.t_is_deleted != ' => 1,
					'order_by' => [ 'total_pallets' => 'desc' ],
					'group_by' =>  'gdm.i_delivery_location_id',
					'glm.i_status_id' => config('constants.DELIVERED_STATUS_ID'),
					'gdm.e_collection_type' => config('constants.COLLECTION'),
					'gdm.e_pallet_box_type' => config('constants.PALLET')
			];
			
			$selectData = [
					'wh.v_warehouse_name',
					'wh.v_warehouse_code',
					DB::raw('SUM(gdm.i_no_of_pallet_box) as total_pallets'),
					DB::raw('SUM(glm.d_invoice_total) AS total_invoice_amount'),
					DB::raw("CASE
		        WHEN SUM(gdm.i_no_of_pallet_box) > 0 THEN
		            SUM(glm.d_invoice_total) / SUM(gdm.i_no_of_pallet_box)
		        ELSE 0
		    END AS invoice_per_pallet"),
					DB::raw('SUM(
		        DATEDIFF(
		            IFNULL(glm.dt_delivery_date, CURDATE()),
		            IFNULL(glm.dt_collection_date, CURDATE())
		        )
		    ) AS total_date_diff'),
			];
			
			$joinData = [
					[
							'tableName' =>	config('constants.GOODS_IN_BUYER_MASTER_TABLE') . ' as gdm',
							'condition' =>	"gdm.i_id = gbd.i_goods_in_buyer_master_id",
					],
					[
							'tableName' =>	config('constants.GOODS_IN_LOGISTIC_MASTER_TABLE') . ' as glm',
							'condition' => [ 'custom' =>  "find_in_set(gbd.i_id , glm.i_goods_in_buyer_detail_id) and glm.t_is_deleted = 0"],
							'type' => 'left',
					],
					[
							'tableName' =>	config('constants.WAREHOUSE_MASTER_TABLE') . ' as wh',
							'condition' =>	"wh.i_id = gdm.i_delivery_location_id and wh.t_is_deleted = 0",
							'type' => 'left',
					],
					[
							'tableName' =>	config('constants.SUPPLIER_DETAIL_TABLE') . ' as sd',
							'condition' =>	"sd.i_id = gbd.i_goods_in_buyer_supplier_id",
					],
					[
						'tableName' =>	config('constants.COUNTRY_MASTER_TABLE') . ' as scm',
						'condition' =>	"scm.i_id = sd.i_country_id and scm.t_is_deleted = 0",
						'type' => 'inner',
					],
			];
			
		}
		
		
		$whereData = (!empty($whereData) ? array_merge( $defaultWhere , $whereData) : $defaultWhere );
		
		// Handle searchCountry and searchWareHouse filters using custom_function
		if(isset($whereData['searchCountry']) && !empty($whereData['searchCountry'])){
			$whereData['custom_function'][] = "(sd.i_country_id in (" . $whereData['searchCountry'] . "))";
			unset($whereData['searchCountry']);
		}
		
		if(isset($whereData['searchWareHouse']) && !empty($whereData['searchWareHouse'])){
			$whereData['custom_function'][] = "(gdm.i_delivery_location_id in (" . $whereData['searchWareHouse'] . "))";
			unset($whereData['searchWareHouse']);
		}
		
		$tableName = config('constants.GOODS_IN_BUYER_DETAIL_TABLE'). ' as gbd';
		$data = $this->selectJoinData( $tableName, $selectData, $joinData, $whereData, $likeData, $additionalData );
		
		return $data;
		
	}
	
	public function getBuyerDeliveryDetails( $whereData = [], $likeData = [], $additionalData = [] ){
		
		$defaultWhere = $selectData = $joinData = [];
		
		$defaultWhere = [
			'gbd.t_is_deleted != ' => 1,
			'gbd.t_is_all_delivered_cancelled_ststus' => 0,
			'order_by' => [ 'gdm.dt_delivery_date' => 'asc', 'po_amount_with_vat_gbp' => 'desc' ],
			'group_by' => [ 'gdm.dt_delivery_date','gdm.i_delivery_location_id','type_of_pallet_box' ],
			'gdm.dt_delivery_date >='  => date('Y-m-d'),
			'gdm.dt_delivery_date <=' =>  date('Y-m-d', strtotime('+6 days'))
		];
		
		$selectData = [
			'wh.v_warehouse_name',
			'wh.v_warehouse_code',
			'wh.i_id as i_warehouse_id',
			DB::raw("SUM(
			    CASE
			        WHEN gdm.e_collection_type = '". config('constants.DELIVERY') ."' THEN
			            CASE
			                WHEN glm.e_dimension = '". config('constants.PALLET') ."' THEN glm.i_no_of_pallet_box
			                ELSE 
			                    CASE WHEN gdm.e_pallet_box_type = '". config('constants.PALLET') ."' THEN gdm.i_no_of_pallet_box ELSE 0 END
			            END
			        ELSE
			            CASE
			                WHEN gdm.e_pallet_box_type = '". config('constants.PALLET') ."' THEN gdm.i_no_of_pallet_box
			                ELSE 0
			            END
			    END
			) AS total_pallets"),
			
			### Total Boxes (for "Box" type only):
			DB::raw("SUM(
			    CASE
			        WHEN gdm.e_collection_type = '". config('constants.DELIVERY') ."' THEN
			            CASE
			                WHEN glm.e_dimension = '". config('constants.BOX') ."' THEN glm.i_no_of_pallet_box
			                ELSE 
			                    CASE WHEN gdm.e_pallet_box_type = '". config('constants.BOX') ."' THEN gdm.i_no_of_pallet_box ELSE 0 END
			            END
			        ELSE
			            CASE
			                WHEN gdm.e_pallet_box_type = '". config('constants.BOX') ."' THEN gdm.i_no_of_pallet_box
			                ELSE 0
			            END
			    END
			) AS total_boxes"),
			DB::raw("SUM(gdm.i_total_units) AS total_units"),
			DB::raw('CASE WHEN gdm.e_collection_type = "'. config('constants.DELIVERY') .'" THEN (CASE WHEN glm.e_dimension != "" THEN glm.e_dimension ELSE gdm.e_pallet_box_type END) ELSE gdm.e_pallet_box_type END AS type_of_pallet_box'),
			DB::raw("
			  SUM(
        	  	CASE 
					WHEN `gdm`.`d_po_amount_with_vat` IS NOT NULL AND `gdm`.`d_po_amount_with_vat` > 0 AND `po_currency`.`d_gbp_conversation_rate` > 0 THEN `gdm`.`d_po_amount_with_vat` * `po_currency`.`d_gbp_conversation_rate` ELSE 0
    		  	END
				) AS po_amount_with_vat_gbp"),
			'gdm.dt_delivery_date'	
		];
		
		$joinData = [
			[
				'tableName' =>	config('constants.GOODS_IN_BUYER_MASTER_TABLE') . ' as gdm',
				'condition' =>	"gdm.i_id = gbd.i_goods_in_buyer_master_id",
			],
			[
				'tableName' =>	config('constants.GOODS_IN_LOGISTIC_MASTER_TABLE') . ' as glm',
				'condition' => [ 'custom' =>  "find_in_set(gbd.i_id , glm.i_goods_in_buyer_detail_id) and glm.t_is_deleted = 0"],
				'type' => 'left',
			],
			[
				'tableName' =>	config('constants.WAREHOUSE_MASTER_TABLE') . ' as wh',
				'condition' =>	"wh.i_id = gdm.i_delivery_location_id and wh.t_is_deleted = 0",
				'type' => 'left',
			],
			[
				'tableName' =>	config('constants.CURRENCY_MASTER_TABLE') . ' as po_currency',
				'condition' =>	"po_currency.i_id = gdm.i_po_currency_id",
			],
		];
		
		$whereData = (!empty($whereData) ? array_merge($whereData,$defaultWhere) : $defaultWhere );
		
		$tableName = config('constants.GOODS_IN_BUYER_DETAIL_TABLE'). ' as gbd';
		$data = $this->selectJoinData( $tableName, $selectData, $joinData, $whereData, $likeData, $additionalData );
		
		return $data;
	}
	
    public function getTrackingGoodInDetail( $whereData = [] , $likeData = [] , $additionalData = [] ){
    
    	if(isset($whereData['singleRecord'])){
    		$this->singleRecord = true;
    		unset($whereData['singleRecord']);
    	}
    		
    	$defaultWhere = [];
    	$defaultWhere['gbd.t_is_deleted != ' ] = 1 ;
    	$defaultWhere['order_by'] = [ 'gbd.i_id' => 'desc' ];
    	//$defaultWhere['group_by'] = [ 'gbd.i_id' ];
    	//$defaultWhere['lm.t_is_deleted != ' ] = 1 ;
    	
    	$selectData = $joinData = [];
    	
    	if( isset($whereData['dashboardType']) && ($whereData['dashboardType'] != false) ){
    		unset($whereData['dashboardType']);
    		
    		$selectData = [
    			'gdm.e_collection_type',
    			'gdm.i_total_units',
    			'gdm.i_no_of_pallet_box',
    			'gdm.e_pallet_box_type',
    			'glm.e_dimension',
    			'glm.i_no_of_pallet_box as logistic_i_no_of_pallet_box',
    			'gdm.d_po_amount_with_vat',
    			'po_currency.d_gbp_conversation_rate as po_gbp_conversation_rate'
    		];
    		
    		$joinData = [
    			[
    				'tableName' =>	config('constants.GOODS_IN_BUYER_MASTER_TABLE') . ' as gdm',
    				'condition' =>	"gdm.i_id = gbd.i_goods_in_buyer_master_id",
    			],
    			[
    				'tableName' =>	config('constants.GOODS_IN_LOGISTIC_MASTER_TABLE') . ' as glm',
    				'condition' => [ 'custom' =>  "find_in_set(gbd.i_id , glm.i_goods_in_buyer_detail_id) and glm.t_is_deleted = 0"],
    				'type' => 'left',
    			],
    			[
    				'tableName' =>	config('constants.SUPPLIER_DETAIL_TABLE') . ' as sd',
    				'condition' =>	"sd.i_id = gbd.i_goods_in_buyer_supplier_id",
    			],
    			[
    				'tableName' =>	config('constants.COUNTRY_MASTER_TABLE') . ' as scm',
    				'condition' =>	"scm.i_id = sd.i_country_id and scm.t_is_deleted = 0",
    				'type' => 'inner',
    			],
				[
    				'tableName' =>	config('constants.CURRENCY_MASTER_TABLE') . ' as po_currency',
    				'condition' =>	"po_currency.i_id = gdm.i_po_currency_id",
    				'type' => 'left',
    			],
    		];
    		
    	} else {
    		$selectData = [
    				'gbd.i_id as buyer_id',
    				'gdm.dt_order_date',
    				'gdm.dt_invoice_date',
    				'gbd.v_goods_in_buyer_detail_no',
    				'cm.v_company_name',
    				//'goods_buyer_name.v_name as goods_buyer_name',
    				'sd.v_supplier_code',
    				'sm.v_supplier_name',
    				'gdm.v_po_sales_invoice_no',
    				//'gdm.e_payment_status',
    				'gdm.dt_delivery_date as gib_delivery_date',
    				'gdm.d_po_amount',
    				'po_currency.v_currency_name as po_currency_name',
    				'po_currency.v_currency_code as po_currency_code',
    				'po_currency.d_gbp_conversation_rate as po_gbp_conversation_rate',
    				'payment_currency.v_currency_name as payment_currency_name',
    				'payment_currency.v_currency_code as payment_currency_code',
    				'payment_currency.d_gbp_conversation_rate as payment_currency_gbp_conversation_rate',
    				'gdm.e_collection_type',
    				'gdm.e_mode_of_transport',
    				//'gdm.e_delivery_type',
    				'glm.v_goods_in_logistic_master_no',
    				'glm.d_invoice_total',
    				'logistic_book_by.v_name as logistic_book_by_name',
    				'logistic_partner_master.v_logistic_partner_name',
    				'logistic_partner.v_logistic_partner_code',
    				'glm.dt_collection_date',
    				'glm.v_tracking_no',
    				'glm.dt_delivery_date',
    				'glm.dt_goods_in_date',
    				'glm.v_tracking_link',
    				'logistic_status.v_status',
    				DB::raw("(SELECT group_concat(buyer_user_company.v_company_name SEPARATOR  ', ' )  FROM ".config('constants.COMPANY_MASTER_TABLE')." as buyer_user_company WHERE find_in_set(buyer_user_company.i_id , gdm.v_user_company_ids ) and buyer_user_company.t_is_deleted = 0 ) as buyer_user_company_name"),
    				DB::raw("(SELECT group_concat(buyer_employee_names.v_name SEPARATOR  ', ' )  FROM ".config('constants.LOGIN_MASTER_TABLE')." as buyer_employee_names WHERE find_in_set(buyer_employee_names.i_id , gdm.v_buyer_employee_ids ) and buyer_employee_names.t_is_deleted = 0 ) as goods_buyer_name"),
    				 
    				DB::raw("(SELECT group_concat(user_buyer_names.v_name SEPARATOR  ', ' )  FROM ".config('constants.LOGIN_MASTER_TABLE')." as user_buyer_names WHERE find_in_set(user_buyer_names.i_id , gdm.v_user_buyer_ids ) and user_buyer_names.t_is_deleted = 0 ) as user_buyer_name"),
    				 
    				//DB::raw("GROUP_CONCAT(buyer_user_company.v_company_name)  AS buyer_user_company_name"),
    				'sd.v_supplier_address',
    				//'gdm.d_payment_amount',
    				'gld.e_collection_delivery_type',
    				'wh.v_warehouse_name',
    				'wh.v_warehouse_code',
    				'scm.v_country_name',
    				'scm.v_country_code',
    				'glm.i_logistic_partner_id',
    				'gdm.e_ready_for_collection_status',
    				'sd.e_record_status',
    				'glm.i_id as logistic_record_id',
    				'wh.i_id',
    				'gbd.t_is_all_delivered_cancelled_ststus',
    				'glm.i_status_id',
    				'gdm.v_brand',
    				//'gdm.v_goods_remarks',
    				//'gdm.e_dangerous_goods',
    				//'gdm.e_customer_procedure_export',
    				//'gdm.e_customer_procedure_import',
    				//'gdm.v_payment_remark',
    				'gdm.dt_payment_date',
    				'gdm.i_no_of_pallet_box',
    				'glm.i_no_of_pallet_box as logistic_i_no_of_pallet_box',
    				'gdm.e_pallet_box_type',
    				'glm.e_dimension',
    				'gdm.v_vendor_number',
    				'gdm.v_invoice_no',
    				'gdm.dt_po_creation_date',
    				'gdm.d_po_amount_with_vat',
    				//'gdm.i_user_buyer_id',
    				'gdm.e_customs_procedure',
    				//'user_buyer.v_name as user_buyer_name',
    				'dangerous_goods.v_value as dangerous_goods_value',
    				'payment_term.v_value as payment_term_value',
    				 
    				DB::raw("(SELECT group_concat(goods_remark.v_value SEPARATOR  ', ' )  FROM ".config('constants.LOOKUP_MASTER_TABLE')." as goods_remark WHERE find_in_set(goods_remark.i_id , gdm.v_goods_remark_ids ) and goods_remark.t_is_deleted = 0 ) as goods_remark_value"),
    				 
    				DB::raw("(SELECT group_concat(concat(ilp.v_logistic_partner_name , ' - ' , gi.d_final_charge) SEPARATOR  ', ' )  FROM ".config('constants.GOODS_IN_LOGISTIC_INVOICE_TABLE')." as gi join ".config('constants.LOGISTIC_PARTNER_MASTER_TABLE')." as ilp on ilp.i_id = gi.i_logistic_partner_master_id  WHERE gi.i_goods_in_logistic_master_id = glm.i_id and gi.t_is_deleted = 0 ) as invoice_details"),
    				 
    				'gdm.i_total_units',
    				'gdm.v_buyer_comments',
    				'glm.v_status_comment',
    				'gdm.dt_actual_payment_date',
    				 
    				DB::raw('CASE WHEN gdm.e_collection_type = "'. config('constants.DELIVERY') .'" THEN (CASE WHEN glm.i_no_of_pallet_box > 0 THEN glm.i_no_of_pallet_box ELSE gdm.i_no_of_pallet_box END) ELSE gdm.i_no_of_pallet_box END AS no_of_pallet_box_final'),
    				DB::raw('CASE WHEN gdm.e_collection_type = "'. config('constants.DELIVERY') .'" THEN (CASE WHEN glm.e_dimension != "" THEN glm.e_dimension ELSE gdm.e_pallet_box_type END) ELSE gdm.e_pallet_box_type END AS type_of_pallet_box_final')
    		];
    		
    		$joinData = [
    				[
    						'tableName' =>	config('constants.GOODS_IN_BUYER_MASTER_TABLE') . ' as gdm',
    						'condition' =>	"gdm.i_id = gbd.i_goods_in_buyer_master_id",
    				],
    				[
    						'tableName' =>	config('constants.COMPANY_MASTER_TABLE') . ' as cm',
    						'condition' =>	"cm.i_id = gdm.i_buyer_company_id",
    				],
    				/* [
    				 'tableName' =>	config('constants.LOGIN_MASTER_TABLE') . ' as goods_buyer_name',
    						'condition' =>	"goods_buyer_name.i_id = gdm.i_buyer_employee_id",
    				],
    		*/
    				/* [
    				 'tableName' =>	config('constants.LOGIN_MASTER_TABLE') . ' as goods_buyer_name',
    						'condition' => [ 'custom' =>  "find_in_set(goods_buyer_name.i_id , gdm.v_buyer_employee_ids) and goods_buyer_name.t_is_deleted = 0"],
    				], */
    				[
    						'tableName' =>	config('constants.SUPPLIER_DETAIL_TABLE') . ' as sd',
    						'condition' =>	"sd.i_id = gbd.i_goods_in_buyer_supplier_id",
    				],
    				[
    						'tableName' =>	config('constants.SUPPLIER_MASTER_TABLE') . ' as sm',
    						'condition' =>	"sm.i_id = sd.i_supplier_id",
    				],
    				[
    						'tableName' =>	config('constants.CURRENCY_MASTER_TABLE') . ' as po_currency',
    						'condition' =>	"po_currency.i_id = gdm.i_po_currency_id",
    				],
    				[
    						'tableName' =>	config('constants.CURRENCY_MASTER_TABLE') . ' as payment_currency',
    						'condition' =>	"payment_currency.i_id = gdm.i_payment_currency_id and gdm.t_is_deleted = 0",
    						'type' => 'left'
    				],
    				[
    						'tableName' =>	config('constants.GOODS_IN_LOGISTIC_MASTER_TABLE') . ' as glm',
    						'condition' => [ 'custom' =>  "find_in_set(gbd.i_id , glm.i_goods_in_buyer_detail_id) and glm.t_is_deleted = 0"],
    						'type' => 'left',
    				],
    				[
    						'tableName' =>	config('constants.GOODS_IN_LOGISTIC_COLLECTION_TABLE') . ' as gld',
    						'condition' =>	"glm.i_id = gld.i_goods_in_logistic_master_id and gld.i_goods_in_buyer_detail_id = gbd.i_id and gld.t_is_deleted = 0",
    						'type' => 'left',
    				],
    				[
    						'tableName' =>	config('constants.LOGIN_MASTER_TABLE') . ' as logistic_book_by',
    						'condition' =>	"logistic_book_by.i_id = glm.i_book_employee_id and glm.t_is_deleted = 0",
    						'type' => 'left',
    				],
    				[
    						'tableName' =>	config('constants.LOGISTIC_PARTNER_DETAIL_TABLE') . ' as logistic_partner',
    						'condition' =>	"logistic_partner.i_id = glm.i_logistic_partner_id and logistic_partner.t_is_deleted = 0",
    						'type' => 'left',
    				],
    				[
    						'tableName' =>	config('constants.LOGISTIC_PARTNER_MASTER_TABLE') . ' as logistic_partner_master',
    						'condition' =>	"logistic_partner_master.i_id = logistic_partner.i_logictic_partner_id and logistic_partner_master.t_is_deleted = 0",
    						'type' => 'left',
    				],
    				[
    						'tableName' =>	config('constants.STATUS_MASTER_TABLE') . ' as logistic_status',
    						'condition' =>	"logistic_status.i_id = glm.i_status_id and logistic_status.t_is_deleted = 0",
    						'type' => 'left',
    				],
    				[
    						'tableName' =>	config('constants.WAREHOUSE_MASTER_TABLE') . ' as wh',
    						'condition' =>	"wh.i_id = gdm.i_delivery_location_id and wh.t_is_deleted = 0",
    						'type' => 'left',
    				],
    				[
    						'tableName' =>	config('constants.COUNTRY_MASTER_TABLE') . ' as scm',
    						'condition' =>	"scm.i_id = sd.i_country_id and scm.t_is_deleted = 0",
    						'type' => 'inner',
    				],
    				/* [
    				 'tableName' =>	config('constants.LOGIN_MASTER_TABLE') . ' as user_buyer',
    						'condition' => "user_buyer.i_id = gdm.i_user_buyer_id and gdm.t_is_deleted = 0 and user_buyer.t_is_deleted = 0",
    						//'condition' => [ 'custom' => "find_in_set(user_buyer.i_id , gdm.v_user_buyer_ids) and gdm.t_is_deleted = 0 and user_buyer.t_is_deleted = 0" ],
    						'type' => 'left',
    				], */
    				[
    						'tableName' =>	config('constants.LOOKUP_MASTER_TABLE') . ' as dangerous_goods',
    						'condition' =>	"dangerous_goods.i_id = gdm.i_dangerous_goods_id and gdm.t_is_deleted = 0 and dangerous_goods.t_is_deleted = 0 and dangerous_goods.v_module_name = '" . config('constants.DANGEROUS_GOODS_LOOKUP') . "'",
    						'type' => 'left',
    				],
    				[
    						'tableName' =>	config('constants.LOOKUP_MASTER_TABLE') . ' as payment_term',
    						'condition' =>	"payment_term.i_id = gdm.i_payment_terms_id and gdm.t_is_deleted = 0 and payment_term.t_is_deleted = 0 and payment_term.v_module_name = '" . config('constants.PAYMENT_TERMS_LOOKUP') . "'",
    						'type' => 'left',
    				],
    		];
    	}
    	
    	
    	$tableName = config('constants.GOODS_IN_BUYER_DETAIL_TABLE'). ' as gbd';
    		
    	$whereData = (!empty($whereData) ? array_merge( $defaultWhere , $whereData) : $defaultWhere );
    		
    		
    	//dd($tableName);
    	//DB::enableQueryLog();
    	if( $this->singleRecord == true ){
    		$data = $this->getSingleRecordWithJoinById( $tableName, $selectData, $joinData, $whereData, $likeData, $additionalData );
    	} else {
    		$data = $this->selectJoinData( $tableName, $selectData, $joinData, $whereData, $likeData, $additionalData );
    	}
    	
    	//$query = DB::getQueryLog();
    	//$query = end($query);
    	///print_r($query);die;

    	return $data;
    	
    }
    public function getFbaDetails( $whereData = [] , $likeData = [] , $additionalData = [] ){
    	$defaultWhere = [];
    	$defaultWhere['fbad.t_is_deleted != ' ] = 1 ;
    	$defaultWhere['order_by'] = [ 'fbad.i_id' => 'desc' ];
    	$defaultWhere['fbam.t_is_deleted != ' ] = 1 ;
    	 
    	 
    	$tableName = config('constants.GOODS_OUT_FBA_SHEET_DETAIL_TABLE'). ' as fbad';
    	
    	$selectData = [
    			'fbad.i_id',
    			'fbad.v_fba_po_no',
    			'fbad.e_destination',
    			'fbad.v_ref_id',
    			'fbad.v_company_code',
    			'fbad.v_location_code',
    			'fbad.v_product',
    			'fbad.v_sku',
    			'fbad.v_units',
    			'fbad.v_amazon_address',
    
    	];
    	
    	$joinData = [
    			[
    					'tableName' =>	config('constants.GOODS_OUT_FBA_SHEET_MASTER_TABLE') . ' as fbam',
    					'condition' =>	"fbam.i_id = fbad.i_fba_sheet_master_id",
    			],
    			[
    					'tableName' =>	config('constants.COUNTRY_TO_PORT_GOODS_OUT_MASTER_TABLE') . ' as ctpgom',
    					'condition' =>	"ctpgom.i_id = fbam.i_country_to_port_goods_out_master_id",
    			],
    		
    	];
    	
    	$whereData = (!empty($whereData) ? array_merge( $defaultWhere , $whereData) : $defaultWhere );
    	
    	$data = $this->selectJoinData( $tableName, $selectData, $joinData, $whereData, $likeData, $additionalData );
    	
    	return $data;
    }
    
    public function getShimentInfo($whereData = [] , $likeData = [] , $additionalData = [] ){
    	$defaultWhere = [];
    	$defaultWhere['sn.t_is_deleted != ' ] = 1 ;
    	$defaultWhere['order_by'] = [ 'sn.i_id' => 'desc' ];
    	
    	
    	$tableName = config('constants.SHIPMENT_NO_INFO_TABLE'). ' as sn';
    	 
    	$selectData = [
    			'sn.i_id',
    			'sn.v_shipment_no',
    			'sn.v_ref_record_type',
    			'ctpe.v_shipment_id',
    			'ctpe.v_ref_id',
    			'goetd.v_invoice_ref_no',
    			'goetd.v_units',
    			'uswt.v_shipment_id',
    			'uswt.v_ref_id',
    			'uswt.v_shipment_invoice_no',
    			'uswt.v_shipment_invoice_no',
    	];
    	 
    	$joinData = [
    			[
    					'tableName' =>	config('constants.COUNTRY_TO_PORT_EUROPE_GOODS_OUT_DETAIL_TABLE') . ' as ctpe',
    					'condition' =>	"ctpe.i_id = sn.i_ref_table_id and ctpe.t_is_deleted = 0 and sn.v_ref_record_type = '".config('constants.WAREHOUSE_TO_AMAZON')."'",
    					'type' => 'left'
    			],
    			[
    					'tableName' =>	config('constants.GOODS_OUT_EUROPE_TRANSFER_DETAIL_TABLE') . ' as goetd',
    					'condition' =>	"goetd.i_id = sn.i_ref_table_id and goetd.t_is_deleted = 0 and sn.v_ref_record_type = '".config('constants.INTERNAL_WAREHOUSE_TRANSFER')."'",
    					'type' => 'left'
    			],
    			[
    					'tableName' =>	config('constants.US_WAREHOUSE_TO_AMAZON_DETAILS_TABLE') . ' as uswt',
    					'condition' =>	"uswt.i_id = sn.i_ref_table_id and uswt.t_is_deleted = 0 and sn.v_ref_record_type = '".config('constants.US_WAREHOUSE_TO_AMAZON')."'",
    					'type' => 'left'
    			],
    			
    	
    	];
    	 
    	$whereData = (!empty($whereData) ? array_merge( $defaultWhere , $whereData) : $defaultWhere );
    	 
    	$data = $this->selectJoinData( $tableName, $selectData, $joinData, $whereData, $likeData, $additionalData );
    	 
    	return $data;
    }
    
    public function getTrackingGoodOutDetail( $whereData = [] , $likeData = [] , $additionalData = [] ){
    
    	$query = CountrytoPortEuropeDetailModel::with( [
    			'countryToPortEurope', 'countryToPortEurope.bookEmployeeInfo', 'countryToPortEurope.logisticPartnerDetail', 'countryToPortEurope.logisticPartnerDetail.logisticPartnerMaster', 'countryToPortEurope.invoiceInfo', 'countryToPortEurope.detailInfo', 'accountCompany', 'warehouse', 'location', 'country'
    	]);
    
    	if(isset($whereData['way_of_transport']) && (!empty($whereData['way_of_transport'])) ){
    		$transportWay = $whereData['way_of_transport'];
    		$query->whereHas('countryToPortEurope' , function($query) use($transportWay) {
    			$query->where('e_transport_way',$transportWay);
    		});
    	}
    	
    	if(isset($whereData['book_by']) && (!empty($whereData['book_by'])) ){
    		$bookEmployeeId = $whereData['book_by'];
    		$query->whereHas('countryToPortEurope' , function($query) use($bookEmployeeId) {
    			$query->where('i_book_by_employee_id',$bookEmployeeId);
    		});
    	}
    	
    	if(isset($whereData['logistic_partner']) && (!empty($whereData['logistic_partner'])) ){
    		$logisticPartnerId = $whereData['logistic_partner'];
    		$query->whereHas('countryToPortEurope' , function($query) use($logisticPartnerId) {
    			$query->where('i_logistic_partner_detail_id',$logisticPartnerId);
    		});
    	}
    	
    	if(isset($whereData['from_warehouse']) && (!empty($whereData['from_warehouse'])) ){
    		$fromWarehouse = $whereData['from_warehouse'];
    		$query->where('i_warehouse_id',$fromWarehouse);
    	}
    	
    	if(isset($whereData['to_amazon_location']) && (!empty($whereData['to_amazon_location'])) ){
    		$toAmazonLocation = $whereData['to_amazon_location'];
    		$query->where('i_location_id',$toAmazonLocation);
    	}
    	
    	if(isset($whereData['status']) && (!empty($whereData['status'])) ){
    		$statusId = ($whereData['status']);
    		$query->whereHas('countryToPortEurope' , function($query) use($statusId) {
    			$query->whereIn('i_status_id',$statusId);
    		});
    	}
    	if(isset($whereData['account_company']) && (!empty($whereData['account_company'])) ){
    		$accountCompnay = $whereData['account_company'];
    		$query->where('i_account_company_id','=',$accountCompnay);
    	}
    	
    	if(isset($whereData['booking_form_date']) && (!empty($whereData['booking_form_date'])) ){
    		$bookingFromDate = dbDate( $whereData['booking_form_date'] );
    		$query->where('dt_booking_date','>=',$bookingFromDate);
    	}
    	
    	if(isset($whereData['booking_to_date']) && (!empty($whereData['booking_to_date'])) ){
    		$bookingToDate = dbDate( $whereData['booking_to_date'] );
    		$query->where('dt_booking_date','<=',$bookingToDate);
    	}
    	
    	if(isset($whereData['collection_form_date']) && (!empty($whereData['collection_form_date'])) ){
    		$collectionFromDate = dbDate( $whereData['collection_form_date'] );
    		$query->where('dt_collection_date','>=',$collectionFromDate);
    	}
    	
    	if(isset($whereData['collection_to_date']) && (!empty($whereData['collection_to_date'])) ){
    		$collectionToDate = dbDate( $whereData['collection_to_date'] );
    		$query->where('dt_collection_date','<=',$collectionToDate);
    	}
    	
    	if(isset($whereData['appointment_from_date']) && (!empty($whereData['appointment_from_date'])) ){
    		$appointmentFromDate = dbDate( $whereData['appointment_from_date'] );
    		$query->where('dt_amazon_shipment_date','>=',$appointmentFromDate);
    	}
    	
    	if(isset($whereData['appointment_to_date']) && (!empty($whereData['appointment_to_date'])) ){
    		$appointmentToDate = dbDate( $whereData['appointment_to_date'] );
    		$query->where('dt_amazon_shipment_date','<=',$appointmentToDate);
    	}
    	
    	if(isset($whereData['delivery_from_date']) && (!empty($whereData['delivery_from_date'])) ){
    		$deliveryFromDate = dbDate( $whereData['delivery_from_date'] );
    		$query->where('dt_delivery_date','>=',$deliveryFromDate);
    	
    	}
    	
    	if(isset($whereData['delivery_to_date']) && (!empty($whereData['delivery_to_date'])) ){
    		$deliveryToDate = dbDate( $whereData['delivery_to_date'] );
    		$query->where('dt_delivery_date','<=',$deliveryToDate);
    	}
    	
    	if( isset($likeData['searchBy']) && (!empty($likeData['searchBy'])) ){
    		$searchString = ( $likeData['searchBy'] );
    	
    		$query->where(function($q) use ($searchString){
    			$q->orWhereHas('countryToPortEurope' , function($q1) use($searchString) {
    				$allLikeDetailInfoColumns = [ 'v_tracking_no' , 'v_tracking_link', 'v_country_to_port_europe_record_no' ];
    	
    				$q1->where(function($q2) use ($allLikeDetailInfoColumns,$searchString){
    					foreach($allLikeDetailInfoColumns as $key => $allLikeColumn){
    						$q2->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
    					}
    				});
    			});
    	
    				$allLikeColumns = [ 'v_workflow_id' , 'v_shipment_id' ];
    				foreach($allLikeColumns as $key => $allLikeColumn){
    					$q->orWhere($allLikeColumn, 'like', "%" .$searchString . "%");
    				}
    	
    		});
    	
    	}
    	
    	if(isset($whereData['offset']) && (!empty($whereData['offset'])) ){
    		$query->skip($whereData['offset']);
    	}
    	
    	if(isset($whereData['limit']) && (!empty($whereData['limit'])) ){
    		$query->take($whereData['limit']);
    	}
    	
    	if(isset($whereData['order_by']) && (!empty($whereData['order_by']) && is_array($whereData['order_by'])) ){
    		foreach ($whereData['order_by'] as $order_col => $order_dir){
    			$query->orderBy($order_col, $order_dir) ;
    		}
    	} else {
    		$query->orderBy('i_id', "DESC" ) ;
    	}
    	
    	
    	if(isset($where['singleRecord']) && ( $where['singleRecord'] != false )  ){
    		$data = $query->first();
    	} else{
    		$data = $query->get();
    	}
    	return $data;
    	 
    }
}
