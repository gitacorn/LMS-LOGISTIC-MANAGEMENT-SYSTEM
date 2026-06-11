
<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/collection-details-form/', 'App\Http\Controllers\HomeController@collection_details_form');
Route::get('/collection-details-form', 'App\Http\Controllers\HomeController@collection_details_form');
Route::get('/shipment-quote-form', 'App\Http\Controllers\HomeController@shipment_quotes_form');
Route::get('/sample-form/', 'App\Http\Controllers\HomeController@sampleForm');
Route::get('/check-dbconnection/', 'App\Http\Controllers\HomeController@checkDbConnection');
Route::get('/design-dashboard', 'App\Http\Controllers\HomeController@dashboard'); 
Route::get('/login', 'App\Http\Controllers\HomeController@login');
Route::get('/changepassword', 'App\Http\Controllers\HomeController@changepassword');
Route::get('/category-list', 'App\Http\Controllers\HomeController@category_list');
Route::get('/add-category', 'App\Http\Controllers\HomeController@add_category');
Route::get('/login-history', 'App\Http\Controllers\HomeController@login_history');
Route::get('/update-category', 'App\Http\Controllers\HomeController@update_category');

Route::get('/design-document-type-master', 'App\Http\Controllers\HomeController@document_type_master');
Route::get('/design-logistic-partner-master', 'App\Http\Controllers\HomeController@logistic_partner_master');
Route::get('/add-logistic-partner-master', 'App\Http\Controllers\HomeController@add_logistic_partner_master');
Route::get('/design-country-master', 'App\Http\Controllers\HomeController@country_master');
Route::get('/design-dimension-master', 'App\Http\Controllers\HomeController@dimension_master');
Route::get('/design-status-master', 'App\Http\Controllers\HomeController@status_master');
Route::get('/design-company-master', 'App\Http\Controllers\HomeController@company_master');


Route::get('/design-good-in-buyer', 'App\Http\Controllers\HomeController@good_in_buyer');
Route::get('/design-add-good-in-buyer', 'App\Http\Controllers\HomeController@add_good_in_buyer');
Route::get('/design-tracking-goods-in', 'App\Http\Controllers\HomeController@tracking_goods_in');

Route::get('/design-good-in-logistic', 'App\Http\Controllers\HomeController@good_in_logistic');
Route::get('/add-good-in-logistic', 'App\Http\Controllers\HomeController@add_good_in_logistic');

Route::get('users', 'App\Http\Controllers\UsersController@index');
Route::get('users/create', 'App\Http\Controllers\UsersController@create');
Route::post('users/filter','App\Http\Controllers\UsersController@filter');
Route::post('users/update','App\Http\Controllers\UsersController@update');
Route::post('users/add','App\Http\Controllers\UsersController@add');
Route::post('users/updateStatus','App\Http\Controllers\UsersController@updateStatus');
Route::get('users/edit/{id}','App\Http\Controllers\UsersController@edit')->name('user.edit');
Route::post('users/delete/{id}','App\Http\Controllers\UsersController@delete')->name('user.delete');
Route::post('checkUniqueUserEmail','App\Http\Controllers\GuestController@checkUniqueUserEmail');

Route::get('/', 'App\Http\Controllers\LoginController@showLoginForm');
Route::get('/login', 'App\Http\Controllers\LoginController@showLoginForm');
Route::post('login/checkLogin', 'App\Http\Controllers\LoginController@checkLogin');
Route::get('logout', 'App\Http\Controllers\DashboardController@logout');
Route::get('dashboard', 'App\Http\Controllers\DashboardController@index')->name('dashboard')->middleware('checklogin');
Route::get('change-password', 'App\Http\Controllers\DashboardController@changePassword')->name('change-password')->middleware('checklogin');
Route::post('dashboard/updatePassword','App\Http\Controllers\DashboardController@updatePassword');
Route::post('removeRecord','App\Http\Controllers\MasterController@removeRecord');

Route::get('login-history', 'App\Http\Controllers\LoginHistoryController@index');
Route::post('login-history/filter','App\Http\Controllers\LoginHistoryController@filter');

Route::get('location-master', 'App\Http\Controllers\LookupMasterController@index');
Route::get('type-master', 'App\Http\Controllers\LookupMasterController@index');
Route::post('lookup-master/filter','App\Http\Controllers\LookupMasterController@filter');
Route::post('lookup-master/delete/{id}','App\Http\Controllers\LookupMasterController@delete');
Route::post('lookup-master/updateStatus','App\Http\Controllers\LookupMasterController@updateStatus');


Route::post('add-lookup-master','App\Http\Controllers\LookupMasterController@addLookupMaster');
Route::post('lookup-master/getLookupRecordInfo','App\Http\Controllers\LookupMasterController@getLookupRecordInfo');
Route::post('lookup-master/delete/{id}','App\Http\Controllers\LookupMasterController@delete');
Route::get('/design-currency-master', 'App\Http\Controllers\HomeController@currency_master');

Route::get('/design-warehouse-master', 'App\Http\Controllers\HomeController@warehouse_master');
Route::get('/add-uk-other-country-us-port', 'App\Http\Controllers\HomeController@add_uk_other_country_us_port');
Route::get('/design-uk-other-country-us-port', 'App\Http\Controllers\HomeController@uk_other_country_us_port');
Route::get('/design-view-fba-sheet', 'App\Http\Controllers\HomeController@view_fba_sheet');
Route::get('/design-agent-warehouse-to-amazon', 'App\Http\Controllers\HomeController@agent_warehouse_to_amazon');
Route::get('/add-agent-warehouse-to-amazon', 'App\Http\Controllers\HomeController@add_agent_warehouse_to_amazon');
Route::get('/design-to-amazon', 'App\Http\Controllers\HomeController@to_amazon');
Route::get('/add-to-amazon', 'App\Http\Controllers\HomeController@add_to_amazon');
Route::get('/add-us-port-to-agent-warehouse', 'App\Http\Controllers\HomeController@add_us_port_to_agent_warehouse');
Route::get('/design-us-port-to-agent-warehouse', 'App\Http\Controllers\HomeController@us_port_to_agent_warehouse');
Route::get('/design-internal-transfer', 'App\Http\Controllers\HomeController@internal_transfer');
Route::get('/add-internal-transfer', 'App\Http\Controllers\HomeController@add_internal_transfer');
Route::get('/design-add-us-warehouse-to-amazon-customer-uk-warehouse', 'App\Http\Controllers\HomeController@add_us_warehouse_to_amazon_customer_uk_warehouse');
Route::get('/design-us-warehouse-to-amazon-customer-uk-warehouse', 'App\Http\Controllers\HomeController@us_warehouse_to_amazon_customer_uk_warehouse');

Route::get('/design-verify-otp', 'App\Http\Controllers\HomeController@verify_otp');

Route::get('us-container-clubbing/design-add-us-container-clubbing', 'App\Http\Controllers\HomeController@add_us_container_clubbing');
Route::get('us-container-clubbing/design-us-container-clubbing', 'App\Http\Controllers\HomeController@us_container_clubbing');

Route::get('/design-dashboard', 'App\Http\Controllers\HomeController@design_dashboard');
Route::get('/design-warehouse-pallet-limit', 'App\Http\Controllers\HomeController@design_Warehouse_Pallet_Limit');


// Warehouse Pallet Limit
Route::post('warehouse-pallet-limit/export', 'App\Http\Controllers\WarehousePalletMaster@exportExcel');



Route::get('country-master', 'App\Http\Controllers\CountryMasterController@index');
Route::post('country-master/add', 'App\Http\Controllers\CountryMasterController@add');
Route::post('country-master/edit','App\Http\Controllers\CountryMasterController@edit');
Route::post('country-master/filter','App\Http\Controllers\CountryMasterController@filter');
Route::post('country-master/updateStatus','App\Http\Controllers\CountryMasterController@updateStatus');
Route::post('country-master/delete/{id}','App\Http\Controllers\CountryMasterController@delete');

Route::post('country-master/checkUniqueCountryName','App\Http\Controllers\CountryMasterController@checkUniqueCountryName');

Route::post('manage-session-messages','App\Http\Controllers\MasterController@manageSessionMessages');

Route::get('dimension-master', 'App\Http\Controllers\DimensionMasterController@index');
Route::post('dimension-master/add', 'App\Http\Controllers\DimensionMasterController@add');
Route::post('dimension-master/edit','App\Http\Controllers\DimensionMasterController@edit');
Route::post('dimension-master/filter','App\Http\Controllers\DimensionMasterController@filter');
Route::post('dimension-master/updateStatus','App\Http\Controllers\DimensionMasterController@updateStatus');
Route::post('dimension-master/delete/{id}','App\Http\Controllers\DimensionMasterController@delete');

Route::get('document-type-master', 'App\Http\Controllers\Document_Type_Master_Controller@index');
Route::post('document-type-master/add', 'App\Http\Controllers\Document_Type_Master_Controller@add');
Route::post('document-type-master/edit','App\Http\Controllers\Document_Type_Master_Controller@edit');
Route::post('document-type-master/filter','App\Http\Controllers\Document_Type_Master_Controller@filter');
Route::post('document-type-master/updateStatus','App\Http\Controllers\Document_Type_Master_Controller@updateStatus');
Route::post('document-type-master/delete/{id}','App\Http\Controllers\Document_Type_Master_Controller@delete');

Route::get('status-master', 'App\Http\Controllers\StatusMasterController@index');
Route::post('status-master/add', 'App\Http\Controllers\StatusMasterController@add');
Route::post('status-master/edit','App\Http\Controllers\StatusMasterController@edit');
Route::post('status-master/filter','App\Http\Controllers\StatusMasterController@filter');
Route::post('status-master/updateStatus','App\Http\Controllers\StatusMasterController@updateStatus');
Route::post('status-master/delete/{id}','App\Http\Controllers\StatusMasterController@delete');

Route::get('currency-master', 'App\Http\Controllers\CurrencyMasterController@index');
Route::post('currency-master/add', 'App\Http\Controllers\CurrencyMasterController@add');
Route::post('currency-master/edit','App\Http\Controllers\CurrencyMasterController@edit');
Route::post('currency-master/filter','App\Http\Controllers\CurrencyMasterController@filter');
Route::post('currency-master/updateStatus','App\Http\Controllers\CurrencyMasterController@updateStatus');
Route::post('currency-master/delete/{id}','App\Http\Controllers\CurrencyMasterController@delete');

Route::get('company-master', 'App\Http\Controllers\CompanyMasterController@index');
Route::post('company-master/add', 'App\Http\Controllers\CompanyMasterController@add');
Route::post('company-master/edit','App\Http\Controllers\CompanyMasterController@edit');
Route::post('company-master/filter','App\Http\Controllers\CompanyMasterController@filter');
Route::post('company-master/updateStatus','App\Http\Controllers\CompanyMasterController@updateStatus');
Route::post('company-master/delete/{id}','App\Http\Controllers\CompanyMasterController@delete');

Route::get('warehouse-master', 'App\Http\Controllers\WarehouseMasterController@index');
Route::post('warehouse-master/add', 'App\Http\Controllers\WarehouseMasterController@add');
Route::post('warehouse-master/edit','App\Http\Controllers\WarehouseMasterController@edit');
Route::post('warehouse-master/filter','App\Http\Controllers\WarehouseMasterController@filter');
Route::post('warehouse-master/updateStatus','App\Http\Controllers\WarehouseMasterController@updateStatus');
Route::post('warehouse-master/delete/{id}','App\Http\Controllers\WarehouseMasterController@delete');

Route::get('/design-supplier-master', 'App\Http\Controllers\HomeController@supplier_master');
Route::get('/add-supplier-master', 'App\Http\Controllers\HomeController@add_supplier_master');

Route::get('logistic-partner-master', 'App\Http\Controllers\LogisticPartnerMasterController@index');
Route::get('logistic-partner-master/create', 'App\Http\Controllers\LogisticPartnerMasterController@create');
Route::post('logistic-partner-master/filter','App\Http\Controllers\LogisticPartnerMasterController@filter');
Route::post('logistic-partner-master/update','App\Http\Controllers\LogisticPartnerMasterController@update');
Route::post('logistic-partner-master/add','App\Http\Controllers\LogisticPartnerMasterController@add');
Route::post('logistic-partner-master/updateStatus','App\Http\Controllers\LogisticPartnerMasterController@updateStatus');
Route::get('logistic-partner-master/edit/{id}','App\Http\Controllers\LogisticPartnerMasterController@edit')->name('logistic-partner-master.edit');
Route::post('logistic-partner-master/delete/{id}','App\Http\Controllers\LogisticPartnerMasterController@delete')->name('logistic-partner-master.delete');


Route::get('demoCheck', 'App\Http\Controllers\HomeController@demoCheck');
Route::post('filterPageRequest', 'App\Http\Controllers\HomeController@filterPageRequest');

Route::get('supplier-master', 'App\Http\Controllers\SupplierMasterController@index');
Route::get('supplier-master/create', 'App\Http\Controllers\SupplierMasterController@create');
Route::post('supplier-master/filter','App\Http\Controllers\SupplierMasterController@filter');
Route::post('supplier-master/update','App\Http\Controllers\SupplierMasterController@update');
Route::post('supplier-master/add','App\Http\Controllers\SupplierMasterController@add');
Route::post('supplier-master/updateStatus','App\Http\Controllers\SupplierMasterController@updateStatus');
Route::get('supplier-master/edit/{id}','App\Http\Controllers\SupplierMasterController@edit')->name('supplier-master.edit');
Route::post('supplier-master/delete/{id}','App\Http\Controllers\SupplierMasterController@delete')->name('supplier-master.delete');

Route::get('check-goods', 'App\Http\Controllers\GoodsInBuyerController@check');

Route::get('location-master', 'App\Http\Controllers\WarehouseMasterController@index');
Route::post('location-master/add', 'App\Http\Controllers\WarehouseMasterController@add');
Route::post('location-master/edit','App\Http\Controllers\WarehouseMasterController@edit');
Route::post('location-master/filter','App\Http\Controllers\WarehouseMasterController@filter');
Route::post('location-master/updateStatus','App\Http\Controllers\WarehouseMasterController@updateStatus');
Route::post('location-master/delete/{id}','App\Http\Controllers\WarehouseMasterController@delete');

Route::get('good-in-buyer', 'App\Http\Controllers\GoodInBuyerMasterController@index');
Route::get('good-in-buyer/create', 'App\Http\Controllers\GoodInBuyerMasterController@create');
Route::post('good-in-buyer/filter','App\Http\Controllers\GoodInBuyerMasterController@filter');
Route::post('good-in-buyer/update','App\Http\Controllers\GoodInBuyerMasterController@update');
Route::post('good-in-buyer/add','App\Http\Controllers\GoodInBuyerMasterController@add');
Route::post('good-in-buyer/updateStatus','App\Http\Controllers\GoodInBuyerMasterController@updateStatus');
Route::get('good-in-buyer/edit/{id}','App\Http\Controllers\GoodInBuyerMasterController@edit')->name('good-in-buyer.edit');
Route::post('good-in-buyer/delete/{id}','App\Http\Controllers\GoodInBuyerMasterController@delete')->name('good-in-buyer.delete');

Route::get('check-excel', 'App\Http\Controllers\HomeController@checkExcel');

Route::post('good-in-buyer/getSupplierLocation','App\Http\Controllers\GoodInBuyerMasterController@getSupplierLocation');
Route::get('page-not-found', 'App\Http\Controllers\GuestController@customErrorPage');

Route::post('good-in-buyer/getSupplierLocationDetails','App\Http\Controllers\GoodInBuyerMasterController@getSupplierLocationDetails');

Route::get('good-in-logistic', 'App\Http\Controllers\GoodInLogisticMasterController@index');
Route::get('good-in-logistic/create', 'App\Http\Controllers\GoodInLogisticMasterController@create');
Route::post('good-in-logistic/filter','App\Http\Controllers\GoodInLogisticMasterController@filter');
Route::post('good-in-logistic/update','App\Http\Controllers\GoodInLogisticMasterController@update');
Route::post('good-in-logistic/add','App\Http\Controllers\GoodInLogisticMasterController@add');
Route::post('good-in-logistic/updateStatus','App\Http\Controllers\GoodInLogisticMasterController@updateStatus');
Route::get('good-in-logistic/edit/{id}','App\Http\Controllers\GoodInLogisticMasterController@edit')->name('good-in-logistic.edit');
Route::post('good-in-logistic/delete/{id}','App\Http\Controllers\GoodInLogisticMasterController@delete')->name('good-in-logistic.delete');

Route::post('good-in-logistic/getGoodInBuyerDetails','App\Http\Controllers\GoodInLogisticMasterController@getGoodInBuyerDetails');


Route::post('currency-master/checkUniqueCurrencyCode','App\Http\Controllers\CurrencyMasterController@checkUniqueCurrencyCode');
Route::post('document-type-master/checkUniqueDocumentName','App\Http\Controllers\Document_Type_Master_Controller@checkUniqueDocumentName');
Route::post('dimension-master/checkUniqueDimensionName','App\Http\Controllers\DimensionMasterController@checkUniqueDimensionName');
Route::post('dimension-master/checkUniqueDimensionSize','App\Http\Controllers\DimensionMasterController@checkUniqueDimensionSize');
Route::post('status-master/checkUniqueStatus','App\Http\Controllers\StatusMasterController@checkUniqueStatus');
Route::post('company-master/checkUniqueCompanyName','App\Http\Controllers\CompanyMasterController@checkUniqueCompanyName');
Route::post('company-master/checkUniqueCompanyCode','App\Http\Controllers\CompanyMasterController@checkUniqueCompanyCode');
Route::post('company-master/checkUniqueCompanyShortCode','App\Http\Controllers\CompanyMasterController@checkUniqueCompanyShortCode');
Route::post('warehouse-master/checkUniqueWarehouseName','App\Http\Controllers\WarehouseMasterController@checkUniqueWarehouseName');
Route::post('warehouse-master/checkUniqueWarehouseCode','App\Http\Controllers\WarehouseMasterController@checkUniqueWarehouseCode');


Route::get('port-master', 'App\Http\Controllers\WarehouseMasterController@index');
Route::post('port-master/add', 'App\Http\Controllers\WarehouseMasterController@add');
Route::post('port-master/edit','App\Http\Controllers\WarehouseMasterController@edit');
Route::post('port-master/filter','App\Http\Controllers\WarehouseMasterController@filter');
Route::post('port-master/updateStatus','App\Http\Controllers\WarehouseMasterController@updateStatus');
Route::post('port-master/delete/{id}','App\Http\Controllers\WarehouseMasterController@delete');

Route::get('customer-master', 'App\Http\Controllers\CustomerMasterController@index');
Route::get('customer-master/create', 'App\Http\Controllers\CustomerMasterController@create');
Route::post('customer-master/filter','App\Http\Controllers\CustomerMasterController@filter');
Route::post('customer-master/update','App\Http\Controllers\CustomerMasterController@update');
Route::post('customer-master/add','App\Http\Controllers\CustomerMasterController@add');
Route::post('customer-master/updateStatus','App\Http\Controllers\CustomerMasterController@updateStatus');
Route::get('customer-master/edit/{id}','App\Http\Controllers\CustomerMasterController@edit')->name('customer-master.edit');
Route::post('customer-master/delete/{id}','App\Http\Controllers\CustomerMasterController@delete')->name('customer-master.delete');















//Route::post('uk-other-country-us-port/getGoodInBuyerDetails','App\Http\Controllers\CountryToPortGoodsOutController@getCountryToPostGoodOutDetails');



Route::get('uk-other-country-us-port', 'App\Http\Controllers\CountryToPortGoodsOutController@index');
Route::get('uk-other-country-us-port/create', 'App\Http\Controllers\CountryToPortGoodsOutController@create');
Route::post('uk-other-country-us-port/filter','App\Http\Controllers\CountryToPortGoodsOutController@filter');
Route::post('uk-other-country-us-port/update','App\Http\Controllers\CountryToPortGoodsOutController@update');
Route::post('uk-other-country-us-port/add','App\Http\Controllers\CountryToPortGoodsOutController@add');
Route::post('uk-other-country-us-port/updateStatus','App\Http\Controllers\CountryToPortGoodsOutController@updateStatus');
Route::get('uk-other-country-us-port/edit/{id}','App\Http\Controllers\CountryToPortGoodsOutController@edit')->name('uk-other-country-us-port.edit');
Route::post('uk-other-country-us-port/delete/{id}','App\Http\Controllers\CountryToPortGoodsOutController@delete')->name('uk-other-country-us-port.delete');

Route::get('port-to-agent-warehouse', 'App\Http\Controllers\PortToAgentGoodsOutController@index');
Route::get('port-to-agent-warehouse/create', 'App\Http\Controllers\PortToAgentGoodsOutController@create');
Route::post('port-to-agent-warehouse/filter','App\Http\Controllers\PortToAgentGoodsOutController@filter');
Route::post('port-to-agent-warehouse/update','App\Http\Controllers\PortToAgentGoodsOutController@update');
Route::post('port-to-agent-warehouse/add','App\Http\Controllers\PortToAgentGoodsOutController@add');
Route::post('port-to-agent-warehouse/updateStatus','App\Http\Controllers\PortToAgentGoodsOutController@updateStatus');
Route::get('port-to-agent-warehouse/edit/{id}','App\Http\Controllers\PortToAgentGoodsOutController@edit')->name('port-to-agent-warehouse.edit');
Route::post('port-to-agent-warehouse/delete/{id}','App\Http\Controllers\PortToAgentGoodsOutController@delete')->name('port-to-agent-warehouse.delete');

Route::post('upload-fba-sheet','App\Http\Controllers\HomeController@uploadFBASheet');
Route::get('process-fba-sheet','App\Http\Controllers\CronController@manageFBASheet');

Route::post('logistic-partner-master/checkUniqueLogisticPartnerName','App\Http\Controllers\LogisticPartnerMasterController@checkUniqueLogisticPartnerName');
Route::post('supplier-master/checkUniqueSupplierName','App\Http\Controllers\SupplierMasterController@checkUniqueSupplierName');

Route::post('customer-master/checkUniqueCustomerName','App\Http\Controllers\CustomerMasterController@checkUniqueCustomerName');
Route::post('country-master/checkUniqueCountryCode','App\Http\Controllers\CountryMasterController@checkUniqueCountryCode');


Route::get('good-in-buyer/view/{id}','App\Http\Controllers\GoodInBuyerMasterController@edit')->name('good-in-buyer.view');
Route::get('access-denied','App\Http\Controllers\GuestController@accessDenidePage')->name('access-denied-page');
Route::get('good-in-logistic/view/{id}','App\Http\Controllers\GoodInLogisticMasterController@edit')->name('good-in-logistic.view');
Route::post('good-in-buyer/updateDetailCancelledStatus','App\Http\Controllers\GoodInBuyerMasterController@updateDetailCancelledStatus');

Route::get('uk-other-country-us-port/view/{id}','App\Http\Controllers\CountryToPortGoodsOutController@edit')->name('uk-other-country-us-port.view');

Route::get('agent-warehouse-to-amazon', 'App\Http\Controllers\AgentWarehouseToAmazonController@index');
Route::get('agent-warehouse-to-amazon/create', 'App\Http\Controllers\AgentWarehouseToAmazonController@create');
Route::post('agent-warehouse-to-amazon/filter','App\Http\Controllers\AgentWarehouseToAmazonController@filter');
Route::post('agent-warehouse-to-amazon/update','App\Http\Controllers\AgentWarehouseToAmazonController@update');
Route::post('agent-warehouse-to-amazon/add','App\Http\Controllers\AgentWarehouseToAmazonController@add');
Route::get('agent-warehouse-to-amazon/edit/{id}','App\Http\Controllers\AgentWarehouseToAmazonController@edit')->name('agent-warehouse-to-amazon.edit');
Route::post('agent-warehouse-to-amazon/delete/{id}','App\Http\Controllers\AgentWarehouseToAmazonController@delete')->name('agent-warehouse-to-amazon.delete');

Route::post('agent-warehouse-to-amazon/getPortToagentContainerDetails','App\Http\Controllers\AgentWarehouseToAmazonController@getPortToagentContainerDetails');

Route::post('agent-warehouse-to-amazon/getFbaRecordDetails','App\Http\Controllers\AgentWarehouseToAmazonController@getFbaRecordDetails');

Route::get('europe-to-amazon', 'App\Http\Controllers\EuropeToAmazonController@index');
Route::get('europe-to-amazon/create', 'App\Http\Controllers\EuropeToAmazonController@create');
Route::post('europe-to-amazon/filter','App\Http\Controllers\EuropeToAmazonController@filter');
Route::post('europe-to-amazon/update','App\Http\Controllers\EuropeToAmazonController@update');
Route::post('europe-to-amazon/add','App\Http\Controllers\EuropeToAmazonController@add');
Route::get('europe-to-amazon/edit/{id}','App\Http\Controllers\EuropeToAmazonController@edit')->name('europe-to-amazon.edit');
Route::post('europe-to-amazon/delete/{id}','App\Http\Controllers\EuropeToAmazonController@delete')->name('europe-to-amazon.delete');

Route::get('europe-internal-transfer', 'App\Http\Controllers\EuropeInternalTransferController@index');
Route::get('europe-internal-transfer/create', 'App\Http\Controllers\EuropeInternalTransferController@create');
Route::post('europe-internal-transfer/filter','App\Http\Controllers\EuropeInternalTransferController@filter');
Route::post('europe-internal-transfer/update','App\Http\Controllers\EuropeInternalTransferController@update');
Route::post('europe-internal-transfer/add','App\Http\Controllers\EuropeInternalTransferController@add');
Route::get('europe-internal-transfer/edit/{id}','App\Http\Controllers\EuropeInternalTransferController@edit')->name('europe-internal-transfer.edit');
Route::post('europe-internal-transfer/delete/{id}','App\Http\Controllers\EuropeInternalTransferController@delete')->name('europe-internal-transfer.delete');

//Route::post('europe-to-amazon/checkUniqueShipmentId','App\Http\Controllers\EuropeToAmazonController@checkUniqueShipmentId');


Route::post('port-to-agent-warehouse/getContainerRecordDetails','App\Http\Controllers\PortToAgentGoodsOutController@getContainerRecordDetails');

Route::post('uk-other-country-us-port/uploadFBASheet', 'App\Http\Controllers\CountryToPortGoodsOutController@uploadFBASheet');
Route::post('logistic-partner-master/checkUniqueLogisticPartnerCode','App\Http\Controllers\LogisticPartnerMasterController@checkUniqueLogisticPartnerCode');

Route::post('customer-master/checkUniqueCustomerCode','App\Http\Controllers\CustomerMasterController@checkUniqueCustomerCode');

Route::post('supplier-master/checkUniqueSupplierCode','App\Http\Controllers\SupplierMasterController@checkUniqueSupplierCode');

Route::get('/tracking-goods-in', 'App\Http\Controllers\ReportController@trackingGoodsIn');
Route::post('tracking-goods-in/filter','App\Http\Controllers\ReportController@filter');

Route::post('uk-other-country-us-port/get-failed-sheet-info','App\Http\Controllers\CountryToPortGoodsOutController@getUploadedSheedFailedData');
Route::get('uk-other-country-us-port/view-fba-sheet/{record_id}', 'App\Http\Controllers\CountryToPortGoodsOutController@showFbaSheetRecordDetails')->name('country-to-port.view-fba-sheet');

Route::get('port-to-agent-warehouse/view/{id}','App\Http\Controllers\PortToAgentGoodsOutController@edit')->name('port-to-agent-warehouse.view');
Route::get('agent-warehouse-to-amazon/view/{id}','App\Http\Controllers\AgentWarehouseToAmazonController@edit')->name('agent-warehouse-to-amazon.view');
Route::get('europe-to-amazon/view/{id}','App\Http\Controllers\EuropeToAmazonController@edit')->name('europe-to-amazon.view');
Route::get('europe-internal-transfer/view/{id}','App\Http\Controllers\EuropeInternalTransferController@edit')->name('europe-internal-transfer.view');


Route::post('uk-other-country-us-port/editFBASheetModel','App\Http\Controllers\CountryToPortGoodsOutController@editFBASheetModel');
Route::post('uk-other-country-us-port/addFBASheetModelDetails','App\Http\Controllers\CountryToPortGoodsOutController@addFBASheetModelDetails');
Route::post('uk-other-country-us-port/deleteFBARecord','App\Http\Controllers\CountryToPortGoodsOutController@deleteFBARecord');

Route::get('uk-other-country-us-port/view-fba-sheet/{record_id}/{agent_warehouse_id}', 'App\Http\Controllers\CountryToPortGoodsOutController@showFbaSheetRecordDetails')->name('port-to-agent.view-fba-sheet');

Route::post('uk-other-country-us-port/checkUniqueFBAInvoiceNo','App\Http\Controllers\CountryToPortGoodsOutController@checkUniqueFBAInvoiceNo');
Route::post('manage-session-messages','App\Http\Controllers\MasterController@manageSessionMessages');


Route::get('us-warehouse-to-amazon', 'App\Http\Controllers\UsWarehouseToAmazonController@index');
Route::get('us-warehouse-to-amazon/create', 'App\Http\Controllers\UsWarehouseToAmazonController@create');
Route::post('us-warehouse-to-amazon/filter','App\Http\Controllers\UsWarehouseToAmazonController@filter');
Route::post('us-warehouse-to-amazon/update','App\Http\Controllers\UsWarehouseToAmazonController@update');
Route::post('us-warehouse-to-amazon/add','App\Http\Controllers\UsWarehouseToAmazonController@add');
Route::get('us-warehouse-to-amazon/edit/{id}','App\Http\Controllers\UsWarehouseToAmazonController@edit')->name('us-warehouse-to-amazon.edit');
Route::post('us-warehouse-to-amazon/delete/{id}','App\Http\Controllers\UsWarehouseToAmazonController@delete')->name('us-warehouse-to-amazon.delete');

Route::get('us-warehouse-to-amazon/view/{id}','App\Http\Controllers\UsWarehouseToAmazonController@edit')->name('us-warehouse-to-amazon.view');
Route::post('europe-to-amazon/checkUniqueShipmentId','App\Http\Controllers\MasterController@checkUniqueShipmentId');
Route::post('uk-other-country-us-port/getDestinationTypeDetails', 'App\Http\Controllers\CountryToPortGoodsOutController@getDestinationTypeDetails');

Route::get('login/verifyOtp/{id}', 'App\Http\Controllers\LoginController@verifyOtp');
Route::post('login/checkOtp', 'App\Http\Controllers\LoginController@checkOtp');
Route::get('force-logout', 'App\Http\Controllers\CronController@logoutEntry');
Route::post('set-logout-status', 'App\Http\Controllers\DashboardController@setLogoutPopupStatus');
Route::post('tracking-goods-in/getSupplierLocationDetails','App\Http\Controllers\ReportController@getSupplierLocationDetails');
Route::post('tracking-goods-in/getSupplierCountry','App\Http\Controllers\ReportController@getSupplierCountry');

Route::get('good-in-logistic/create/{id}/{delivery}', 'App\Http\Controllers\GoodInLogisticMasterController@create');

Route::get('/fba-report', 'App\Http\Controllers\ReportController@fbaReportIndex');
Route::post('fba-report/fbaReportFilter','App\Http\Controllers\ReportController@fbaReportFilter');

Route::post('us-warehouse-to-amazon/uploadCSVFile','App\Http\Controllers\UsWarehouseToAmazonController@uploadCSVFile');

Route::get('good-in-logistic/{entryNo}', 'App\Http\Controllers\GoodInLogisticMasterController@index');

Route::post('supplier-master/checkUniqueSupplierType','App\Http\Controllers\SupplierMasterController@checkUniqueSupplierType');


Route::get('/us-warehouse-to-fba-report', 'App\Http\Controllers\ReportController@usWarehouseFBAReportIndex');
Route::post('us-warehouse-to-fba-report/usWarehouseFBAReportFilter','App\Http\Controllers\ReportController@usWarehouseFBAReportFilter');

Route::get('/europe-to-amazon-report', 'App\Http\Controllers\ReportController@europeToAmazonReportIndex');
Route::post('europe-to-amazon-report/europeToAmazonFilter','App\Http\Controllers\ReportController@europeToAmazonFilter');


Route::get('shipment-quote-form/{id}', 'App\Http\Controllers\GoodInBuyerMasterController@shipmentQuotePdf');

Route::post('good-in-buyer/getSupplierDetails','App\Http\Controllers\GoodInBuyerMasterController@getSupplierDetails');

Route::post('tracking-goods-in/viewDocumentDetails','App\Http\Controllers\ReportController@viewDocumentDetails');
Route::post('good-in-logistic/getSupplierInfo','App\Http\Controllers\GoodInLogisticMasterController@getSupplierInfo');

Route::get('update-existing-buyer-status-info','App\Http\Controllers\CronController@managExistingBuyerRecord');

Route::get('process-uswarehouse-to-amazon','App\Http\Controllers\CronController@usWarehouseToAmazonFBADetails');
Route::get('process-to-amazon','App\Http\Controllers\CronController@toAmazonFBADetails');

Route::post('good-in-buyer/checkUniquePoSalesInvoiceNumber', 'App\Http\Controllers\GoodInBuyerMasterController@checkUniquePoSalesInvoiceNumber');

Route::get('update-internal-transfer-warehouse','App\Http\Controllers\CronController@updateEuropeTransferFormToWarehouse');

Route::post('europe-internal-transfer/getFromWarehouseDetails', 'App\Http\Controllers\EuropeInternalTransferController@getFromWarehouseDetails');

Route::get('get-buyer-detail-id','App\Http\Controllers\CronController@getBuyerDetailsId');

Route::get('delete-good-in-buyer-master-base-on-detail-table','App\Http\Controllers\CronController@deleteGoodInBuyerMasterDataBasedOnDetailTable');

Route::post('good-in-buyer/import-excel', 'App\Http\Controllers\GoodInBuyerMasterController@importExcel');

Route::get('/tracking-goods-in-old', 'App\Http\Controllers\ReportController@trackingGoodsInOld');
Route::post('tracking-goods-in-old/filter','App\Http\Controllers\ReportController@filterOld');

Route::get('/tracking-goods-out', 'App\Http\Controllers\ReportGoodOutController@trackingGoodsOut');
Route::post('tracking-goods-out/filter','App\Http\Controllers\ReportGoodOutController@filter');

Route::get('payment-terms', 'App\Http\Controllers\LookupMasterController@index');
Route::get('goods-remark', 'App\Http\Controllers\LookupMasterController@index');
Route::get('dangerous-goods', 'App\Http\Controllers\LookupMasterController@index');

Route::post('good-in-buyer/get-dimension-details', 'App\Http\Controllers\GoodInBuyerMasterController@getDimensionDetails');

Route::get('update-new-master-info-goodin','App\Http\Controllers\CronController@updateNewMasterInfoGoodIn');

Route::get('remove-import-good-in-buyer-permission','App\Http\Controllers\CronController@removeImportGoodInBuyerPermission');
Route::get('clubbing-summary','App\Http\Controllers\CountryToPortGoodsOutController@clubbingSummary');
Route::get('booking-portal', 'App\Http\Controllers\LookupMasterController@index');
Route::post('uk-other-country-us-port/related-warehouse-by-warehouse-country','App\Http\Controllers\CountryToPortGoodsOutController@relatedWarehouseByWarehouseCountry');
Route::post('uk-other-country-us-port/check-unique-personal-ref-number','App\Http\Controllers\CountryToPortGoodsOutController@checkUniquePersonalReferenceNumber');
Route::post('port-to-agent-warehouse/container-wise-from-warehouse-country-and-warehouse','App\Http\Controllers\PortToAgentGoodsOutController@containerWiseFromWarehouseCountryAndWarehouse');
Route::post('check-unique-lookup-value','App\Http\Controllers\LookupMasterController@checkUniqueLookupValue');
Route::post('port-to-agent-warehouse/container-wise-from-warehouse-country-and-warehouse','App\Http\Controllers\PortToAgentGoodsOutController@containerWiseFromWarehouseCountryAndWarehouse');

Route::get('us-container-clubbing', 'App\Http\Controllers\UsaContainerClubbing@index');
Route::post('us-container-clubbing/filter', 'App\Http\Controllers\UsaContainerClubbing@filter');
Route::get('us-container-clubbing/create', 'App\Http\Controllers\UsaContainerClubbing@create');
Route::get('us-container-clubbing/edit/{id}', 'App\Http\Controllers\UsaContainerClubbing@edit');
Route::get('us-container-clubbing/view/{id}','App\Http\Controllers\UsaContainerClubbing@edit')->name('usa-container-clubbing.view');
Route::post('us-container-clubbing/add', 'App\Http\Controllers\UsaContainerClubbing@add');
Route::post('us-container-clubbing/delete/{id}', 'App\Http\Controllers\UsaContainerClubbing@delete');
Route::post('us-container-clubbing/getFbaRecordDetails','App\Http\Controllers\UsaContainerClubbing@getFbaRecordDetails');
Route::get('daily-mail', 'App\Http\Controllers\LookupMasterController@index');
Route::get('process-send-mail-to-pending-delivery-of-buyer', 'App\Http\Controllers\CronController@sendMailToPendingDeliveryOfBuyer');

Route::get('warehouse-pallet-limit', 'App\Http\Controllers\WarehousePalletMaster@index');
Route::post('warehouse-pallet-limit/filter','App\Http\Controllers\WarehousePalletMaster@filter');
Route::post('warehouse-pallet-limit/add','App\Http\Controllers\WarehousePalletMaster@add');
Route::post('warehouse-pallet-limit/showHistoryModal','App\Http\Controllers\WarehousePalletMaster@showHistoryModal');
Route::post('warehouse-pallet-limit/historyFilter','App\Http\Controllers\WarehousePalletMaster@historyModalFilter');
Route::post('dashboard/get-statistics-filter', 'App\Http\Controllers\DashboardController@getStatisticsGraphFilter');
Route::post('dashboard/get-donut-chart-filter', 'App\Http\Controllers\DashboardController@getDonutChartFilter');
Route::post('dashboard/get-goods-out-statistics-filter', 'App\Http\Controllers\DashboardController@getGoodsOutStatisticsFilter');
Route::post('dashboard/filter-goods-out-statistics', 'App\Http\Controllers\DashboardController@filterGoodsOutStatistics');
Route::post('dashboard/goods-out-statistics-filter', 'App\Http\Controllers\DashboardController@filterGoodsOutStatistics');
Route::post('dashboard/dashboard-avg-days', 'App\Http\Controllers\DashboardController@getAvgDaysCostSummaryFilter');
Route::post('dashboard/dashboard-supplier-company', 'App\Http\Controllers\DashboardController@topSuppliersCompanyFilter');
Route::post('dashboard/dashboard-buyer-delivery', 'App\Http\Controllers\DashboardController@buyerDelivery');
Route::post('good-in-buyer/checkPalletLimit', 'App\Http\Controllers\GoodInBuyerMasterController@checkPalletLimit');

// Chat Routes
Route::get('chat', 'App\Http\Controllers\ChatController@index');
Route::post('chat/search-users', 'App\Http\Controllers\ChatController@searchUsers');
Route::post('chat/get-messages', 'App\Http\Controllers\ChatController@getMessages');
Route::post('chat/send-message', 'App\Http\Controllers\ChatController@sendMessage');
Route::post('chat/get-unread-count', 'App\Http\Controllers\ChatController@getUnreadCount');
Route::post('chat/mark-as-read', 'App\Http\Controllers\ChatController@markAsRead');
Route::post('chat/delete-message', 'App\Http\Controllers\ChatController@deleteMessage');

// Announcement Routes
Route::get('announcement', 'App\Http\Controllers\AnnouncementController@index');
Route::get('announcement/create', 'App\Http\Controllers\AnnouncementController@create');
Route::post('announcement', 'App\Http\Controllers\AnnouncementController@store');
Route::get('announcement/{id}/edit', 'App\Http\Controllers\AnnouncementController@edit');
Route::put('announcement/{id}', 'App\Http\Controllers\AnnouncementController@update');
Route::delete('announcement/{id}', 'App\Http\Controllers\AnnouncementController@destroy');
Route::get('announcement/get-active', 'App\Http\Controllers\AnnouncementController@getActiveAnnouncements');