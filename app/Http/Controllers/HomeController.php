<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BaseModel;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Helpers\Twt\Wild_tiger;
use Illuminate\Database\Eloquent\Model;



class HomeController extends MasterController
{
    //
    protected $perPage;
    public function __construct(){
    	$this->perPage = 10;
    }

	public function collection_details_form()
	{
		$fontdata = [
			'poppins-regular' => [
				'R' => 'Poppins-Regular.ttf',
			],
		];

		$fontdata = [
			'poppins-medium' => [
				'R' => 'Poppins-Medium.ttf',
			],
		];

		$fontdata = [
			'poppins-bold' => [
				'R' => 'Poppins-Bold.ttf',
			],
		];

		$defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();

		$fontDirs = $defaultConfig['fontDir'];
		$defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
		$fontDirs = $defaultConfig['fontDir'];
		$defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
		$fontData = $defaultFontConfig['fontdata'];
		$fontData += [
			'poppins-regular' => [
				'R' => 'Poppins-Regular.ttf',
			]
		];
		$fontData += [
			'poppins-medium' => [
				'R' => 'Poppins-Medium.ttf',
			]
		];
		$fontdata = [
			'poppins-bold' => [
				'R' => 'Poppins-Bold.ttf',
			],
		];
		$data = [];
		$html = view('admin/collection-details-form')->with($data);

		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'c',
			'format' => 'A4',
			'margin_left' => 3,
			'margin_right' => 3,
			'margin_top' => 3,
			'margin_bottom' => 3,
			'margin_header' => 3,
			'margin_footer' => 3,
			'fontDir' => array_merge($fontDirs, [
				dirname(dirname(__DIR__)) . '/assets/css/fonts/',
			]),
			'fontdata' => $fontData,
			'mode' => 'utf-8',
		]);

		$mpdf->SetWatermarkImage(
			('images/favicon.png'),
			0.1,
			'',
			//    array(160,10)
		);
		$mpdf->autoPageBreak = true;


		$header  = '';
		$header .= '<div class="main-page-border-outer vh100">';
		$header .= '<div class="px-20" style="padding:3px;">';
		$footer = '';
		// $footer = '<table cellpadding="20" cellspacing="0" style="width:100%; font-family: Poppins, sans-serif; vertical-align:top;">
		// 	<tbody>
			
		// 	<tr>
		// 			<td style="text-align: right;"><strong>For, ACORN UNIVERSAL CONSULTANCY LLP</strong></td>
		// 			<br><br>
		// 		</tr>
		// 		<tr>
		// 			<td style="text-align: right;">Authorised Signatory</td>
		// 			<br><br>s
		// 		</tr>
		// 		<tr>
		// 			<td style="text-align: center;">This is computer generated payslip signature not required.</td>
		// 			<br>
		// 		</tr>
		// 	</tbody>
		// </table></div>';
		// $footer .= '</div>';
		// $footer .= '</div>';

		$mpdf->SetHTMLHeader($header);
		$mpdf->SetHTMLFooter($footer);
		// echo $html;die;	
		// $mpdf->setFooter('{PAGENO}');
		$mpdf->showWatermarkImage = true;
		$mpdf->WriteHTML($html, 2);
		$mpdf->Output();
	}

	public function shipment_quotes_form()
	{
		$fontdata = [
			'poppins-regular' => [
				'R' => 'Poppins-Regular.ttf',
			],
		];

		$fontdata = [
			'poppins-medium' => [
				'R' => 'Poppins-Medium.ttf',
			],
		];

		$fontdata = [
			'poppins-bold' => [
				'R' => 'Poppins-Bold.ttf',
			],
		];

		$defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();

		$fontDirs = $defaultConfig['fontDir'];
		$defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
		$fontDirs = $defaultConfig['fontDir'];
		$defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
		$fontData = $defaultFontConfig['fontdata'];
		$fontData += [
			'poppins-regular' => [
				'R' => 'Poppins-Regular.ttf',
			]
		];
		$fontData += [
			'poppins-medium' => [
				'R' => 'Poppins-Medium.ttf',
			]
		];
		$fontdata = [
			'poppins-bold' => [
				'R' => 'Poppins-Bold.ttf',
			],
		];
		$data = [];
		$html = view('admin/shipment-quote-form')->with($data);

		$mpdf = new \Mpdf\Mpdf([
			'mode' => 'c',
			'format' => 'A4',
			'margin_left' => 3,
			'margin_right' => 3,
			'margin_top' => 3,
			'margin_bottom' => 3,
			'margin_header' => 3,
			'margin_footer' => 3,
			'fontDir' => array_merge($fontDirs, [
				dirname(dirname(__DIR__)) . '/assets/css/fonts/',
			]),
			'fontdata' => $fontData,
			'mode' => 'utf-8',
		]);

		$mpdf->SetWatermarkImage(
			('images/favicon.png'),
			0.1,
			'',
			//    array(160,10)
		);
		$mpdf->autoPageBreak = true;


		$header  = '';
		$header .= '<div class="main-page-border-outer vh100">';
		$header .= '<div class="px-20" style="padding:3px;">';
		$footer = '';

		$mpdf->SetHTMLHeader($header);
		$mpdf->SetHTMLFooter($footer);
		// echo $html;die;	
		// $mpdf->setFooter('{PAGENO}');
		$mpdf->showWatermarkImage = true;
		$mpdf->WriteHTML($html, 2);
		$mpdf->Output();
	}
	
    // public function collection_details_form(){
    // 	$fontdata = [
    // 			'poppins-regular' => [
    // 					'R' => 'Poppins-Regular.ttf',
    // 			],
    // 	];
    	
    // 	$fontdata = [
    // 			'poppins-medium' => [
    // 					'R' => 'Poppins-Medium.ttf',
    // 			],
    // 	];
    	
    // 	$fontdata = [
    // 			'poppins-bold' => [
    // 					'R' => 'Poppins-Bold.ttf',
    // 			],
    // 	];
    	
    // 	$defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
    	
    // 	$fontDirs = $defaultConfig['fontDir'];
    // 	$defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
    // 	$fontDirs = $defaultConfig['fontDir'];
    // 	$defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
    // 	$fontData = $defaultFontConfig['fontdata'];
    // 	$fontData += [
    // 			'poppins-regular' => [
    // 					'R' => 'Poppins-Regular.ttf',
    // 			]
    // 	];
    // 	$fontData += [
    // 			'poppins-medium' => [
    // 					'R' => 'Poppins-Medium.ttf',
    // 			]
    // 	];
    // 	$fontdata = [
    // 			'poppins-bold' => [
    // 					'R' => 'Poppins-Bold.ttf',
    // 			],
    // 	];
    // 	$data = [];
    // 	$html = view ( 'pdf/sample-pdf')->with ( $data );
    	
    // 	$mpdf = new \Mpdf\Mpdf([
    // 			'mode' => 'c',
    // 			'format' => 'A4',
    // 			'margin_left' => 0,
    // 			'margin_right' => 0,
    // 			'margin_top' => 0,
    // 			'margin_bottom' => 0,
    // 			'margin_header' => 0,
    // 			'margin_footer' => 0,
    // 			'fontDir' => array_merge($fontDirs, [
    // 					dirname(dirname(__DIR__)).'/assets/css/fonts/',
    // 			]),
    // 			'fontdata' => $fontData,
    // 			'mode' => 'utf-8',
    // 	]);
    	
    // 	$mpdf->WriteHTML($html,2);
    // 	$mpdf->Output();
    // }


    
    public function sampleForm(){
    	$data['pageTitle'] = trans ( 'messages.form');
    	return view( 'form/form')->with($data);
    }
    
    public function checkDbConnection(){
    	$this->dbObject = new BaseModel();
    	$userDetails =  $this->dbObject->selectData('users' , [ 'password' ]);
    	echo "<pre>";print_r($userDetails);die;
    }

	public function dashboard(){
		$data['pageTitle'] = trans('messages.dashboard');
        return view( 'admin/dashboard')->with($data);
    }
	public function login(){
        $data['pageTitle'] = trans('messages.login');
        return view( 'admin/login')->with($data);
    }
	public function verify_otp(){
        $data['pageTitle'] = trans('messages.verify-otp');
        return view( 'admin/design/verify-otp')->with($data);
    }
	public function changepassword(){
        $data['pageTitle'] = trans('messages.change-password');
        return view( 'admin/changepassword')->with($data);
    }
	public function good_in_buyer(){
        $data['pageTitle'] = trans('messages.good-in-buyer');
        return view( 'admin/design/good-in-buyer/good-in-buyer')->with($data);
    }
	public function add_good_in_buyer(){
        $data['pageTitle'] = trans('messages.add-buyer');
        return view( 'admin/design/good-in-buyer/add-good-in-buyer')->with($data);
    }

	public function good_in_logistic(){
        $data['pageTitle'] = trans('messages.good-in-logistic');
        return view( 'admin/design/good-in-logistic/good-in-logistic')->with($data);
    }
	public function add_good_in_logistic(){
        $data['pageTitle'] = trans('messages.add-logistic');
        return view( 'admin/design/good-in-logistic/add-good-in-logistic')->with($data);
    }

	public function tracking_goods_in(){
        $data['pageTitle'] = trans('messages.tracking-goods-in');
        return view( 'admin/design/tracking-goods-in/tracking-goods-in')->with($data);
    }
	public function uk_other_country_us_port(){
        $data['pageTitle'] = trans('messages.uk-other-country-us-port');
        return view( 'admin/design/uk-other-country-us-port/uk-other-country-us-port')->with($data);
    }

	public function add_uk_other_country_us_port(){
        $data['pageTitle'] = trans('messages.add-uk-other-country-us-port');
        return view( 'admin/design/uk-other-country-us-port/add-uk-other-country-us-port')->with($data);
    }

	public function us_port_to_agent_warehouse(){
        $data['pageTitle'] = trans('messages.us-port-to-agent-warehouse');
        return view( 'admin/design/us-port-to-agent-warehouse/us-port-to-agent-warehouse')->with($data);
    }

	public function add_us_port_to_agent_warehouse(){
        $data['pageTitle'] = trans('messages.add-us-port-to-agent-warehouse');
        return view( 'admin/design/us-port-to-agent-warehouse/add-us-port-to-agent-warehouse')->with($data);
    }

	public function view_fba_sheet(){
        $data['pageTitle'] = trans('messages.view-fba-sheet');
        return view( 'admin/design/uk-other-country-us-port/view-fba-sheet')->with($data);
    }

	public function agent_warehouse_to_amazon(){
        $data['pageTitle'] = trans('messages.agent-warehouse-to-amazon-warehouse-customer');
        return view( 'admin/design/agent-warehouse-to-amazon/agent-warehouse-to-amazon')->with($data);
    }

	public function add_agent_warehouse_to_amazon(){
        $data['pageTitle'] = trans('messages.add-agent-warehouse-to-amazon');
        return view( 'admin/design/agent-warehouse-to-amazon/add-agent-warehouse-to-amazon')->with($data);
    }

	public function add_us_warehouse_to_amazon_customer_uk_warehouse()
	{
		$data['pageTitle'] = trans('messages.add-us-warehouse-to-amazon-customer-uk-warehouse');
		return view('admin/design/us-warehouse-to-amazon/add-us-warehouse-to-amazon-customer-uk-warehouse')->with($data);
	}

	public function us_warehouse_to_amazon_customer_uk_warehouse()
	{
		$data['pageTitle'] = trans('messages.us-warehouse-to-amazon-customer-uk-warehouse');
		return view('admin/design/us-warehouse-to-amazon/us-warehouse-to-amazon-customer-uk-warehouse')->with($data);
	}

	public function to_amazon(){
        $data['pageTitle'] = trans('messages.to-amazon');
        return view( 'admin/design/to-amazon/to-amazon')->with($data);
    }
	
	public function add_to_amazon(){
        $data['pageTitle'] = trans('messages.add-to-amazon');
        return view( 'admin/design/to-amazon/add-to-amazon')->with($data);
    }

	public function category_list(){
        $data['pageTitle'] = trans('messages.category');
        return view( 'admin/category-list')->with($data);
    }
	
	public function add_category(){
        $data['pageTitle'] = trans('messages.category');
        return view( 'admin/add-category')->with($data);
    }
	public function login_history(){
        $data['pageTitle'] = trans('messages.category');
        return view( 'admin/login-history')->with($data);
    }
	public function update_category(){
        $data['pageTitle'] = trans('messages.category');
        return view( 'admin/update-category')->with($data);
    }
	public function document_type_master(){
        $data['pageTitle'] = trans('messages.document-type-master');
        return view( 'admin/design/document-type-master')->with($data);
    }
	public function logistic_partner_master(){
        $data['pageTitle'] = trans('messages.logistic-partner-master');
        return view( 'admin/design/logistic-partner-master')->with($data);
    }
	public function add_logistic_partner_master(){
        $data['pageTitle'] = trans('messages.add-logistic-partner');
        return view( 'admin/design/add-logistic-partner-master')->with($data);
    }

	
	public function country_master(){
        $data['pageTitle'] = trans('messages.country-master');
        return view( 'admin/design/country-master/country-master')->with($data);
    }
	public function internal_transfer(){
        $data['pageTitle'] = trans('messages.internal-transfer');
        return view( 'admin/design/internal-transfer/internal-transfer')->with($data);
    }
	public function add_internal_transfer(){
        $data['pageTitle'] = trans('messages.add-internal-transfer');
        return view( 'admin/design/internal-transfer/add-internal-transfer')->with($data);
    }

	public function dimension_master(){
        $data['pageTitle'] = trans('messages.dimension-master');
        return view( 'admin/design/dimension-master/dimension-master')->with($data);
    }
	public function status_master(){
        $data['pageTitle'] = trans('messages.status-master');
        return view( 'admin/design/status-master/status-master')->with($data);
    }
	public function company_master(){
        $data['pageTitle'] = trans('messages.company-master');
        return view( 'admin/design/company-master/company-master')->with($data);
    }
	public function currency_master(){
        $data['pageTitle'] = trans('messages.currency-master');
        return view( 'admin/design/currency-master/currency-master')->with($data);
    }
	
	public function warehouse_master(){
        $data['pageTitle'] = trans('messages.warehouse-master');
        return view( 'admin/design/warehouse-master/warehouse-master')->with($data);
    }
	public function supplier_master(){
        $data['pageTitle'] = trans('messages.supplier-master');
        return view( 'admin/design/supplier-master/supplier-master')->with($data);
    }
	public function add_supplier_master(){
        $data['pageTitle'] = trans('messages.add-supplier');
        return view( 'admin/design/supplier-master/add-supplier-master')->with($data);
    }
	public function add_us_container_clubbing(){
			$data['pageTitle'] = trans('messages.add-usa-container-clubbing');
			return view( 'admin/design/us-container-clubbing/add-us-container-clubbing')->with($data);
		}
	public function us_container_clubbing(){
			$data['pageTitle'] = trans('messages.usa-container-clubbing');
			return view( 'admin/design/us-container-clubbing/us-container-clubbing')->with($data);
		}
		public function design_dashboard(){
			$data['pageTitle'] = trans('messages.dashboard');
			return view( 'admin/design/dashboard')->with($data);
		}
		public function design_Warehouse_Pallet_Limit(){
			$data['pageTitle'] = trans('messages.warehouse-pallet-limit');
			return view( 'admin/design/warehouse-pallet-limit')->with($data);
		}

		
	// public function add_supplier_master(){
    //     $data['pageTitle'] = trans('messages.add-supplier');
    //     return view( 'admin/design/supplier-master/add-supplier-master')->with($data);
    // }
    public function demoCheck(){
    	//$data = WarehouseMasterModel::with(['wareHouseCompany'])->get();
    	//$data = Post::with(['postSate'])->get();
    	//$data = Post::with(['postSate'])->whereHas('postSate')->get();
    	//$data = Post::has('postSate')->get();
    	
    /* 	$value = 2;
    	$data = Post::with([ 'postSate' => function($q) use($value) {
    		// Query the name field in status table
    		$q->where('id', '=', '2');
    	}])
    	->get();
    	
    	$data = Post::with([ 'postSate'])->whereHas('postSate' , function($q){ $q->where('id' , 2 ); })->get(); */
    	
    	$data = Post::paginate($this->perPage,['*'],'page',1);
    	
    	$pageData['details'] = $data;
    	return view( 'home')->with($pageData);
    	
    	return view('home', compact('faqs'));
    	
    	
    	echo "dddddd = ". $data->links();die;
    	
    	
    	/* $data = Post::query()
    	->with(['postSate' => function ($query) {
    		$query->where('id', '=' , 1);
    	}])
    	->get(); */
    		echo "<pre>";print_r($data);die;
    		echo "demoCheck";die;
    }
    
    
    public function filterPageRequest(Request $request){
    	$page = (!empty($request->input('page_no')) ? $request->input('page_no') : 1 );
    	$data = Post::paginate($this->perPage,['*'],'page',$page);
    	$pageData['details'] = $data;
    	$html = view( 'home-list')->with($pageData)->render();
    	echo $html;die;
    	//echo "dddddd = ". $data->links();die;
    	
    }
    
    public function checkExcel(){
    
    	$start = microtime(true);
    	$importFileUpload = null;
    	
    	
    		$inputFileName = public_path("fba-sheet.xlsx");
    		$masterExcelData = [];
    		require_once 'vendor/autoload.php';
    		try {
    			
    				/* $objReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($inputFileName);
					$spreadsheet = $objReader->load($inputFileName);
					$spreadsheet->setActiveSheetIndex(0); */
					
					$spreadsheet = IOFactory::load($inputFileName);
					$sheet = $spreadsheet->getActiveSheet();
					
					echo 'before unmerge: ', count($sheet->getMergeCells()), PHP_EOL;
						
					$mitesh = [];
					foreach($sheet->getMergeCells() as $cells){
						$mitesh[] =  $cells;
						//echo "<pre>";print_r($cells);
						$sheet->unmergeCells($cells);
					}
					
					for($i=1; $i<count($mitesh);$i++){
						// explode merge cells range //
						$CellIndex = explode(":", $mitesh[$i]);
							
						// get main cell with value, ex N25:N27 , the value only stored in N25 //
						$CellValue = $spreadsheet->getActiveSheet()->getCell($CellIndex[0])->getValue();
							
						// starting index //
						$StartIndex = (int) substr($CellIndex[0],1,2);
							
						// ending index //
						$EndIndex = (int) substr($CellIndex[1],1,2);
							
						// column name example "A" ,"B" //
						$ColumnName = substr($CellIndex[0],0,1);
							
						// loop to copy the value from main cell, to other cell //
						for($j = $StartIndex;$j <= $EndIndex; $j++){
							$spreadsheet->getActiveSheet()->setCellValue($ColumnName.$j , $CellValue);
						}
							
					}
					
					/* $writer = new Xlsx($spreadsheet);
					$writer->save(public_path('unmerged.xlsx')); */
					
					//echo 'after unmerge: ', count($sheet->getMergeCells()), PHP_EOL;die;
					/* $allDataInSheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
					$DuplicatedSheetData = [ 'A' , 'B' , 'C' , 'D' , 'E' , 'F' , 'G' , 'H' , 'I' , 'J' , 'K' , 'L' , 'M' , 'N' ];
					for($i=1; $i<count($DuplicatedSheetData);$i++){
						// explode merge cells range //
						$CellIndex = explode(":", $DuplicatedSheetData[$i]);
					
						// get main cell with value, ex N25:N27 , the value only stored in N25 //
						$CellValue = $spreadsheet->getActiveSheet()->getCell($CellIndex[0])->getValue();
					
						// starting index //
						$StartIndex = (int) substr($CellIndex[0],1,2);
					
						// ending index //
						$EndIndex = (int) substr($CellIndex[1],1,2);
					
						// column name example "A" ,"B" //
						$ColumnName = substr($CellIndex[0],0,1);
					
						// loop to copy the value from main cell, to other cell //
						for($j = $StartIndex;$j <= $EndIndex; $j++){
							$spreadsheet->getActiveSheet()->setCellValue($ColumnName.$j , $CellValue);
						}
					
					} */
					
					$allDataInSheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
					//$DuplicatedSheetData = array_values(array_map('array_filter', $allDataInSheet));
					
					
					
					$flag = true;
					$finalWorldCardDetails = [];
					$attendanceColumnReach = false;
					$attendaceDate = null;
					$dataColumnReach = false;
					$skipRow = false;
					$masterExcelData = [];
					$excelKeys = [];
					$rowDetails = [];
						
					foreach ($allDataInSheet as $key => $value) {
							
						if( $key > 0 ){
								
							if( $key == 1 ){
								$excelKeys = array_values($value);
							} else {
	
								$rowDetail = [];
								$rowDetail = array_combine($excelKeys, $value);
								if(!empty($rowDetail)){
									$rowDetails[] = $rowDetail;
								}
							}
								
								
						}
					}
    	
    		}catch (Exception $e) {
    			die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME)
    					. '": ' .$e->getMessage());
    		}
    		//echo "<pre>";print_r($rowDetails);die;
    		if(!empty($rowDetails)){
    			$loopfbaNumber = "";
    			foreach ($rowDetails as $key=> $rowDetail){
    				//$rowExcelData = [];
    				foreach( $rowDetail as $rowKey => $rowValue){
    					$rowKey = strtolower( trim($rowKey) );
    					$rowKey = str_replace(" ", "_", $rowKey);
    					$rowValue = ( trim($rowValue) );
    					switch (trim($rowKey)){
    						case 'fba':
    							$rowExcelData['fba'] = (!empty($rowValue) ? $rowValue : null);
    							break;
    						case 'ref_id':
    							$rowExcelData['ref_id'] = (!empty($rowValue) ? $rowValue : null);
    							break;
    						case 'company':
    							$rowExcelData['company'] = (!empty($rowValue) ? $rowValue : null);
    							break;
    						case 'product_name':
    							$rowExcelData['product_name'] = (!empty($rowValue) ? $rowValue : null);
    							break;
    						case 'location':
    							$rowExcelData['location'] = (!empty($rowValue) ? $rowValue : null);
    							break;
    						case 'sku':
    							$rowExcelData['sku'] = (!empty($rowValue) ? $rowValue : null);
    							break;
    						case 'amazon_address':
    							$rowExcelData['amazon_address'] = (!empty($rowValue) ? $rowValue : null);
    							break;
    						case 'boxes(units)':
    							$rowExcelData['boxes_units'] = (!empty($rowValue) ? $rowValue : null);
    							break;
    						case 'boxes':
    							$rowExcelData['boxes'] = (!empty($rowValue) ? $rowValue : null);
    							break;
    						case 'pallet':
    							$rowExcelData['pallet'] = (!empty($rowValue) ? $rowValue : null);
    							break;
    						case 'total_no_of_pallets':
    							$rowExcelData['total_no_of_pallets'] = (!empty($rowValue) ? $rowValue : null);
    							break;
    						case 'pallet_dimension':
    							$rowExcelData['v'] = (!empty($rowValue) ? $rowValue : null);
    							break;
    						case 'pallet_weight_(kg)':
    							$rowExcelData['pallet_weight_kg'] = (!empty($rowValue) ? $rowValue : null);
    							break;
    						case '_pallet_number':
    							$rowExcelData['pallet_number'] = (!empty($rowValue) ? $rowValue : null);
    							break;
    						case 'pallet_#':
    							$rowExcelData['pallet_'] = (!empty($rowValue) ? $rowValue : null);
    							break;
    						case 'total_pallet_count':
    							$rowExcelData['total_pallet_count'] = (!empty($rowValue) ? $rowValue : null);
    							break;
    						case 'pallet_weight_(kg)':
    							$rowExcelData['pallet_weight_kg'] = (!empty($rowValue) ? $rowValue : null);
    							break;
    						case 'po_id':
    							$rowExcelData['po_id'] = (!empty($rowValue) ? $rowValue : null);
								break;
    					}
    		
    				}
    		
    				if((!empty(array_filter($rowExcelData)))){
    					if(count($masterExcelData) >= 50 ){
    						//var_dump($rowExcelData);die;
    					}
    					$masterExcelData[] = $rowExcelData;
    				}
    			}
    		
    		}
    		echo "<pre>";print_r($masterExcelData);die;
    	
    	
    }
    
    public function uploadFBASheet(Request $request){
    	//echo "<pre>";print_r($request->all());
    	$this->curdModel =  New BaseModel();
    	$parentRecordId = (!empty($request->input('parent_record_id')) ? (int) Wild_tiger::decode($request->input('parent_record_id')) : 0);
    	
    	$formValidation = [];
    	$formValidation['parent_record_id'] = 'required';
    	$formValidation['upload_excel'] = 'required|mimes:xls,xlsx';
    		
    	$validator = Validator::make ( $request->all (), $formValidation , [
    			'parent_record_id.required' => __ ( 'messages.required-record' ),
    			'upload_excel.mimes' => __ ( 'messages.only-excel-file-allowed' ),
    	] );
    	if ($validator->fails ()) {
    		echo "error = ".$validator->errors()->first();die;
    		$this->ajaxResponse(101, (!empty($validator->errors()->first()) ? $validator->errors()->first() :  trans ( 'messages.error-create', [ 'module' => trans('messages.fba-sheet') ] ) ) );
    	}
    	
    	$importFile = "";
    	if( !empty( $_FILES['upload_excel']['name'] ) ){
    		$importFile = $this->uploadFile($request , 'upload_excel');
    	}
    	
    	if(!empty($importFile)){
    		$uploadedFilePath = config('constants.FILE_STORAGE_FILE_PATH') . $importFile;
    		
    		$successMessage = trans('messages.success-file-data-imported',['module'=> trans('messages.fba-sheet') ]);
    		$errorMessages =  trans('messages.error-file-data-imported',['module'=> trans('messages.fba-sheet') ]);
    		
    		$inputFileName = public_path("fba-sheet.xlsx");
    		$masterExcelData = [];
    		require_once 'vendor/autoload.php';
    		try {
    			
    			$spreadsheet = IOFactory::load($uploadedFilePath);
    			$sheet = $spreadsheet->getActiveSheet();
    				
    			$allCellDetails = [];
    			foreach($sheet->getMergeCells() as $cells){
    				$allCellDetails[] =  $cells;
    				$sheet->unmergeCells($cells);
    			}
    				
    			for($i=1; $i<count($allCellDetails);$i++){
    				// explode merge cells range //
    				$CellIndex = explode(":", $allCellDetails[$i]);
    					
    				// get main cell with value, ex N25:N27 , the value only stored in N25 //
    				$CellValue = $spreadsheet->getActiveSheet()->getCell($CellIndex[0])->getValue();
    					
    				// starting index //
    				$StartIndex = (int) substr($CellIndex[0],1,2);
    					
    				// ending index //
    				$EndIndex = (int) substr($CellIndex[1],1,2);
    					
    				// column name example "A" ,"B" //
    				$ColumnName = substr($CellIndex[0],0,1);
    					
    				// loop to copy the value from main cell, to other cell //
    				for($j = $StartIndex;$j <= $EndIndex; $j++){
    					$spreadsheet->getActiveSheet()->setCellValue($ColumnName.$j , $CellValue);
    				}
    					
    			}
    				
    			$allDataInSheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
    			
    			$rowDetails = [];
    		
    			if(!empty($allDataInSheet)){
    				foreach ($allDataInSheet as $key => $value) {
    						
    					if( $key > 0 ){
    				
    						if( $key == 1 ){
    							$excelKeys = array_values($value);
    						} else {
    				
    							$rowDetail = [];
    							$rowDetail = array_combine($excelKeys, $value);
    							if(!empty($rowDetail)){
    								$rowDetails[] = $rowDetail;
    							}
    						}
    				
    				
    					}
    				}
    			}
    		}catch (Exception $e) {
    			$this->ajaxResponse(101, $e->getMessage());
    		}
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
    						case 'products':
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
    					}
    		
    				}
    		
    				if((!empty(array_filter($rowExcelData)))){
    					if(empty($rowExcelData['v_fba_po_no'])){
    						$allExcelErrors[] = trans ( 'messages.error-sheet-row-info', [ 'columnName' => trans('messages.fba-sheet')  , 'srNo' => $excelRecordNo ] ) ;
    					}
    					$masterExcelData[] = $rowExcelData;
    				}
    			}
    		
    		}
    		
    		if(!empty($masterExcelData)){
    			
    			if(!empty($allExcelErrors)){
    				$this->ajaxResponse(101, implode("<br><br>", $allExcelErrors));
    			}
    				
    			$fileInfo  = [];
    			$fileInfo['i_country_to_port_goods_out_master_id'] = $parentRecordId;
    			$fileInfo['v_file_name'] = (!empty($_FILES['upload_excel']['name'])  ? $_FILES['upload_excel']['name'] : "" );
    			$fileInfo['v_file_path'] = $importFile;
    				
    			$insertData = $this->curdModel->insertTableData(config('constants.IMPORT_SHEET_HISTORY_MASTER_TABLE') , $fileInfo );
    			if( $insertData > 0 ){
    				$result = true;
    			}
    				
    			if($result != false){
    				$this->ajaxResponse(1, $successMessage);
    			}else {
    				$this->ajaxResponse(101, $errorMessages);
    			}
    		}
    		$this->ajaxResponse(101, trans('messages.no-data-found-for-import'));
    	}
    	$this->ajaxResponse(101, trans('messages.error-file-upload'));
    	
    }
}
