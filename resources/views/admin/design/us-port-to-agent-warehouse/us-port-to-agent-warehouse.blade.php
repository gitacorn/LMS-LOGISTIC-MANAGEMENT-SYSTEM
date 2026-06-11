@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color"> 
<div class="breadcrumb-wrapper d-flex align-items-center border-navabr">  
    <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle"> {{ $pageTitle }}<span> (2)</span></h1>
    <div class=" ml-auto pt-sm-0 d-flex align-items-center">
        <a href="{{ url('add-us-port-to-agent-warehouse') }}" class="btn btn btn-theme text-white button-actions-top-bar  border btn-sm mr-2 d-flex align-items-center" title="{{ trans('messages.add-us-port-to-agent-warehouse') }}"><i class=" fas fa-plus mr-md-2"></i> <span>{{ trans("messages.add") }}</span></a>
        <button class="btn btn button-actions-top-bar filter-btn  border btn-sm d-flex align-items-center" data-toggle="collapse" data-target="#filter" title="Toggle Filter"><i class="fas fa-filter mr-md-1"></i> <span class="d-md-block d-none">{{ trans("messages.filter") }}</span></button>
    </div>
</div>


<section class="inner-wrapper-common-section main-listing-section">
    <div class="container-fluid">
        <div class="collapse" id="filter">
                <div class="card card-body mb-3">
                    <div class="row align-items-center">
                        <div class="col-xl-6 col-md-12">
                            <div class="form-group">
                                <label for="search_by_us-port-to-agent-warehouse" class="control-label">{{ trans("messages.search-by") }}</label>
                                <input type="text" class="form-control twt-enter-search custom-input" name="search_by_us-port-to-agent-warehouse" id="search_by_us-port-to-agent-warehouse" placeholder="{{ trans('messages.search-by') }}">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="way_of_transport">{{ trans("messages.way-of-transport") }}</label>
                                        <select name="way_of_transport" class="form-control">
                                            <option value="">{{ trans("messages.select") }}</option>
                                            <option value="Air">Air</option>    
                                            <option value="Sea">Sea</option>
                                            <option value="Truck">Truck</option>                                        
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="from_port_airport">{{ trans("messages.from-port-airport") }}</label>
                                        <select name="from_port_airport" class="form-control">
                                            <option value="">{{ trans("messages.select") }}</option>
                                            <option value="New York">New York</option>    
                                            <option value="US">US</option>                                                                           
                                        </select>
                                    </div>
                                </div>

                                <!-- <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="form-group">
                                            <label class="control-label" for="broker_custom_agent">{{ trans("messages.broker-custom-agent") }}</label>
                                            <input type="text" name="broker_custom_agent" class="form-control" placeholder="{{ trans('messages.broker-custom-agent') }}">
                                        </div>
                                    </div> -->

                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="logistic_partner">{{ trans("messages.logistic-partner") }}</label>
                                        <select name="logistic_partner" class="form-control">
                                            <option value="">{{ trans("messages.select") }}</option>
                                            <option value="FedEx">FedEx</option>    
                                            <option value="DHL">DHL</option>                                                                           
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="to_agent_location">{{ trans("messages.to-agent-location") }}</label>
                                        <select name="to_agent_location" class="form-control">
                                            <option value="">{{ trans("messages.select") }}</option>
                                            <option value="FedEx">FedEx - Mumbai</option>    
                                            <option value="DHL">DHL - Surat</option>                                                                           
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-sm-6">
                                        <div class="form-group">
                                            <label for="select_containers" class="control-label">{{ trans("messages.select-containers") }}</label>
                                            <select name="select_containers" class="form-control select2" multiple="">
                                                <option value="CON-001-21092022">CON-001-21092022</option>
                                                <option value="CON-001-21092022">CON-001-21092022</option>
                                            </select>
                                        </div>
                                    </div>

                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="book_by">{{ trans("messages.book-by") }}</label>
                                        <select name="book_by" class="form-control">
                                            <option value="">{{ trans("messages.select") }}</option>
                                            <option value="Ravi">Ravi</option>    
                                            <option value="Deep">Deep</option>                                                                           
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="container_discharged_from_date">{{ trans("messages.container-discharged-from-date") }}</label>
                                        <input type="text" name="container_discharged_from_date" class="form-control date-format" placeholder="{{ trans('messages.container-discharged-from-date') }}" value="{{old('container_discharged_date',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->dt_container_discharged_date))) ?  clientDate($recordInfo->goodInBuyerMaster->dt_container_discharged_date) : '' ) )}}">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="container_discharged_to_date">{{ trans("messages.container-discharged-to-date") }}</label>
                                        <input type="text" name="container_discharged_to_date" class="form-control date-format" placeholder="{{ trans('messages.container-discharged-to-date') }}" value="{{old('container_discharged_date',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->dt_container_discharged_date))) ?  clientDate($recordInfo->goodInBuyerMaster->dt_container_discharged_date) : '' ) )}}">
                                    </div>
                                </div>
                                                    
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="booking_from_date">{{ trans("messages.booking-from-date") }}</label>
                                        <input type="text" name="booking_from_date" class="form-control date-format" placeholder="{{ trans('messages.booking-from-date') }}" value="{{old('container_discharged_date',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->dt_container_discharged_date))) ?  clientDate($recordInfo->goodInBuyerMaster->dt_container_discharged_date) : '' ) )}}">
                                    </div>
                                </div>
                                                        
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="booking_to_date">{{ trans("messages.booking-to-date") }}</label>
                                        <input type="text" name="booking_to_date" class="form-control date-format" placeholder="{{ trans('messages.booking-to-date') }}" value="{{old('container_discharged_date',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->dt_container_discharged_date))) ?  clientDate($recordInfo->goodInBuyerMaster->dt_container_discharged_date) : '' ) )}}">
                                    </div>
                                </div>
                            
                            
                                
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="collection_from_date">{{ trans("messages.collection-from-date") }}</label>
                                        <input type="text" name="collection_from_date" class="form-control date-format" placeholder="{{ trans('messages.collection-from-date') }}" value="{{old('container_discharged_date',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->dt_container_discharged_date))) ?  clientDate($recordInfo->goodInBuyerMaster->dt_container_discharged_date) : '' ) )}}">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="collection_to_date">{{ trans("messages.collection-to-date") }}</label>
                                        <input type="text" name="collection_to_date" class="form-control date-format" placeholder="{{ trans('messages.collection-to-date') }}" value="{{old('container_discharged_date',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->dt_container_discharged_date))) ?  clientDate($recordInfo->goodInBuyerMaster->dt_container_discharged_date) : '' ) )}}">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="delivery_from_date">{{ trans("messages.delivery-from-date") }}</label>
                                        <input type="text" name="delivery_from_date" class="form-control date-format" placeholder="{{ trans('messages.delivery-from-date') }}" value="{{old('container_discharged_date',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->dt_container_discharged_date))) ?  clientDate($recordInfo->goodInBuyerMaster->dt_container_discharged_date) : '' ) )}}">
                                    </div>
                                </div> 
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="delivery_to_date">{{ trans("messages.delivery-to-date") }}</label>
                                        <input type="text" name="delivery_to_date" class="form-control date-format" placeholder="{{ trans('messages.delivery-to-date') }}" value="{{old('container_discharged_date',  ( (isset($recordInfo) && (!empty($recordInfo->goodInBuyerMaster->dt_container_discharged_date))) ?  clientDate($recordInfo->goodInBuyerMaster->dt_container_discharged_date) : '' ) )}}">
                                    </div>
                                </div> 
                                <div class="col-xl-2 col-md-4 d-flex align-items-end gap pt-md-3">
                                    <button type="button" title="{{ trans('messages.search') }}" class="btn btn-theme text-white">{{ trans("messages.search") }}</button>
                                    <button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers">{{ trans("messages.reset") }}</button>
                                </div>

                    </div>
                </div>    
        </div>

        <div class="filter-result-wrapper">
				<div class="card card-body">
					<div class="table-responsive">
						<table class="table table-sm table-bordered table-hover" id="user-table">
							<thead>
								<tr>
									<th class="sr-col">{{ trans("messages.sr-no") }}</th>
									<th class="text-left">{{ trans("messages.entry-no") }}</th>
									<th class="text-left">{{ trans("messages.way-of-transport") }} </th>
									<th class="text-left" style="max-width:200px;min-width:200px;">{{ trans("messages.from-port") }} <br> {{ trans("messages.broker-custom-agent") }} </th>
									<th class="text-left">{{ trans("messages.logistic-partner") }}</th>
									<th class="text-left" style="max-width:140px;min-width:140px;">{{ trans("messages.to-agent-location") }} </th>
									<th class="text-left" style="max-width:140px;min-width:140px;">{{ trans("messages.select-containers") }} </th>
									<th class="text-left" style="max-width:90px;min-width:90px;">{{ trans("messages.book-by") }}</th>
									<th class="text-left" style="max-width:120px;min-width:120px;">{{ trans("messages.booking-date") }} <br> {{ trans("messages.ref-no") }}</th>
									<th class="text-left">{{ trans("messages.tracking-no") }} <br> {{ trans("messages.tracking-link") }} </th>
									<th class="text-left" style="max-width:150px;min-width:150px;">{{ trans("messages.collection-date") }} <br> {{ trans("messages.delivery-date") }} </th>
									<th class="text-center" style="max-width:110px;min-width:110px;">{{ trans("messages.status") }}</th>
									<th style="max-width:100px;min-width:100px;">{{ trans("messages.actions") }}</th>
								</tr>
							</thead>

							<tbody>
								<tr>
									<td>1</td>
									<td class="text-left">PTA-001-22092022 </td>
									<td class="text-left">Road </td>
									<td class="text-left">NEW YORK <br> Broker -Smit</td>
									<td class="text-left">R&A</td>
									<td class="text-left"> Agent Warehouse</td>
									<td class="text-left">CON-001-21092022</td>
									<td class="text-left">Smit Patel</td>
									<td class="text-left">04-02-2022 <br> SA00749823</td>
                                    <td class="text-left">326125 <br> <a href="#"> https://www.google.com</a></td>
									<td class="text-center">04-02-2022 <br> 04-02-2022</td>
									<td class="text-center">Pending</td>
									<td>
										<a title="Edit Record" href="javascript:void(0);" class="btn btn-sm btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
										<button title="Delete" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>

									</td>
								</tr>
                                <tr>
									<td>2</td>
									<td class="text-left">PTA-002-22092022 </td>
									<td class="text-left">Road</td>
									<td class="text-left">NEW YORK-2<br> Broker -Ram-2</td>
									<td class="text-left">R&A</td>
									<td class="text-left"> Agent Warehouse</td>
									<td class="text-left">CON-002-21092022</td>
									<td class="text-left">Mohit Shah</td>
									<td class="text-left">05-02-2022 <br> SA00749824</td>
                                    <td class="text-left">326126 <br> <a href="#"> https://www.google.com</a></td>
									<td class="text-center">05-02-2022 <br> 05-02-2022</td>
									<td class="text-center">Failed<br><a href="#" class="btn failed-btn ml-2" title="" data-toggle="modal" data-target="#track-file-modal"><i class="fa fa-list"></i></a></td>
									<td>
										<a title="Edit Record" href="javascript:void(0);" class="btn btn-sm btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
										<button title="Delete" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>

									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

		</div>
    </div>
</section>
<div class="modal fade bd-example-modal-lg" id="track-file-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-lg">
			<div class="modal-content">
				<form>
					<div class="modal-header">
						<h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">Error List <span> - FBA_1664255873_021022.xlsx - 15-10-2022</span></h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
					</div>
					<div class="modal-body">
						<div class="table-responsive">
							<table class="table table-sm table-bordered table-hover mb-0">
								<thead>
									<tr>
										<th class="sr-col" width="5%">{{ trans("messages.sr-no") }}</th>
										<th class="text-left">{{ trans("messages.description") }}</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="sr-col">1</td>
										<td class="text-left">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</td>
									</tr>
									<tr>
										<td class="sr-col">2</td>
										<td class="text-left">It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</td>
									</tr>
									<tr>
										<td class="sr-col">3</td>
										<td class="text-left">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</td>
									</tr>

								</tbody>
							</table>
						</div>
						<div class="notes-class pt-2">
							<p class="track-notes-title mb-0"><b style="color: red;">Note :</b> <span>Please Correct Above Errors and Upload Again.</span></p>
						</div>
					</div>
					<div class="modal-footer justify-content-center">
						<button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>

</main>
<script>
	$("form").validate({
		errorClass: "invalid-input",
		rules: {
			upload_file: {
				required: true
			}

		},
		messages: {
			upload_file: {
				required: "{{ trans('messages.require-upload-file') }}"
			}
		},
		submitHandler: function(form) {
			showLoader()
			form.submit();
		}
	});
</script>

<script>
	$(document).ready(function() {

		//init date time picker
		$(".date-format").datetimepicker({
			useCurrent: false,
			viewMode: 'days',
			ignoreReadonly: true,
			widgetPositioning: {
				vertical: 'bottom'
			},
			// minDate: moment().startOf('d'),
			format: '{{ config("constants.DEFAULT_DATE_FORMAT") }}',

		});

	});
</script>
@endsection