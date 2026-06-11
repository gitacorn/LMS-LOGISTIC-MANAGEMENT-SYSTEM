<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GoodsInBuyerModel;
use App\GoodInBuyerDetailModel;
use Illuminate\Database\Eloquent\Model;
use App\GoodInBuyerMasterModel;


class GoodsInBuyerController extends Controller
{
    //
    
	public function __construct(){
		$this->model = new GoodInBuyerMasterModel();
	}
	
    public function check(){
    	//$where['company'] = 2;
    	//$where['document_type'] = 2;
    	$where['master_id'] = 1;
    	$where['edit_record'] = true;
    	
    	//$where['order_date'] = "2022-09-07";
    	$data = $this->model->getGoodsInBuyerDetails($where);
    	//$data = GoodsInBuyerModel::all();
    	echo "<pre>";print_r($data);die;
    	echo "welcome";die;
    }
	
}
