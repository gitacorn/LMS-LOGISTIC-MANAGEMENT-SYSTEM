@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-md-flex border-navabr align-items-center">
        <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ trans("messages.warehouse-pallet-limit") }}</h1>
        <!-- <nav aria-label="breadcrumb" class="d-flex ml-auto mr-3">
            <ol class="breadcrumb bg-transparent p-0 mb-0 align-self-end">
                <li class="breadcrumb-item"><a href="{{ url('logistic-partner-master') }}" class="category-add-link">{{ trans("messages.logistic-partner-master") }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ trans("messages.warehouse-pallet-limit") }}</li>
            </ol>
        </nav> -->
    </div>

    <section class="inner-wrapper-common-sections main-listing-section">
        <div class="container-fluid">
            <div class="card card-body mb-3">
                <form method="post" id="warehouse-filter-form">
                    <div class="row">
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                            <label for="warehouse_name" class="control-label">{{ trans('messages.warehouse') }}<span class="text-danger">*</span></label>
                                <select class="form-control" name="warehouse_name">
                                    <option value="">{{ trans("messages.select") }}</option>
                                    <option value="warehouse 1">Warehouse 1</option>
                                    <option value="warehouse 2">Warehouse 2</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 form-group d-flex">
                            <button type="button" data-toggle="modal" data-target="#warehouse-pallet-limit-history" class="btn btn bg-theme text-white mt-auto" title="{{ trans('messages.view-history') }}">{{ trans("messages.view-history") }}</button>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-lg-6 mt-4">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered table-sm pb-4">
                                    <thead>
                                        <tr>
                                            <th class="sr-col">{{ trans("messages.sr-no") }}</th>
                                            <th style="max-width:100px;min-width:100px;">{{ trans("messages.date") }}</th>
                                            <th style="max-width:100px;min-width:100px;">{{ trans("messages.pallet-limit") }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center">1</td>
                                            <td style="max-width:100px;min-width:100px;">26-11-2024</td>
                                            <td style="max-width:100px;min-width:100px;"><input type="text" onchange="onlyNumber(this)" onkeyup="onlyNumber(this)" class="form-control" name="pallet_limit"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">2</td>
                                            <td style="max-width:100px;min-width:100px;">27-11-2024</td>
                                            <td style="max-width:100px;min-width:100px;"><input type="text" onchange="onlyNumber(this)" onkeyup="onlyNumber(this)" class="form-control" name="pallet_limit"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">3</td>
                                            <td style="max-width:100px;min-width:100px;">28-11-2024</td>
                                            <td style="max-width:100px;min-width:100px;"><input type="text" onchange="onlyNumber(this)" onkeyup="onlyNumber(this)" class="form-control" name="pallet_limit"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">4</td>
                                            <td style="max-width:100px;min-width:100px;">29-11-2024</td>
                                            <td style="max-width:100px;min-width:100px;"><input type="text" onchange="onlyNumber(this)" onkeyup="onlyNumber(this)" class="form-control" name="pallet_limit"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">5</td>
                                            <td style="max-width:100px;min-width:100px;">30-11-2024</td>
                                            <td style="max-width:100px;min-width:100px;"><input type="text" onchange="onlyNumber(this)" onkeyup="onlyNumber(this)" class="form-control" name="pallet_limit"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">6</td>
                                            <td style="max-width:100px;min-width:100px;">1-12-2024</td>
                                            <td style="max-width:100px;min-width:100px;"><input type="text" onchange="onlyNumber(this)" onkeyup="onlyNumber(this)" class="form-control" name="pallet_limit"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">7</td>
                                            <td style="max-width:100px;min-width:100px;">2-12-2024</td>
                                            <td style="max-width:100px;min-width:100px;"><input type="text" onchange="onlyNumber(this)" onkeyup="onlyNumber(this)" class="form-control" name="pallet_limit"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">8</td>
                                            <td style="max-width:100px;min-width:100px;">3-12-2024</td>
                                            <td style="max-width:100px;min-width:100px;"><input type="text" onchange="onlyNumber(this)" onkeyup="onlyNumber(this)" class="form-control" name="pallet_limit"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">9</td>
                                            <td style="max-width:100px;min-width:100px;">4-12-2024</td>
                                            <td style="max-width:100px;min-width:100px;"><input type="text" onchange="onlyNumber(this)" onkeyup="onlyNumber(this)" class="form-control" name="pallet_limit"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">10</td>
                                            <td style="max-width:100px;min-width:100px;">5-12-2024</td>
                                            <td style="max-width:100px;min-width:100px;"><input type="text" onchange="onlyNumber(this)" onkeyup="onlyNumber(this)" class="form-control" name="pallet_limit"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">11</td>
                                            <td style="max-width:100px;min-width:100px;">6-12-2024</td>
                                            <td style="max-width:100px;min-width:100px;"><input type="text" onchange="onlyNumber(this)" onkeyup="onlyNumber(this)" class="form-control" name="pallet_limit"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">12</td>
                                            <td style="max-width:100px;min-width:100px;">7-12-2024</td>
                                            <td style="max-width:100px;min-width:100px;"><input type="text" onchange="onlyNumber(this)" onkeyup="onlyNumber(this)" class="form-control" name="pallet_limit"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">13</td>
                                            <td style="max-width:100px;min-width:100px;">8-12-2024</td>
                                            <td style="max-width:100px;min-width:100px;"><input type="text" onchange="onlyNumber(this)" onkeyup="onlyNumber(this)" class="form-control" name="pallet_limit"></td>
                                        </tr>
                                    </tbody>
                                </table>                           
                            </div>
                        </div>
                        <div class="col-md-12 submit-sticky">
                            <button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.update') }}">{{ trans("messages.update") }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    
    <div class="modal fade bd-example-modal-lg" id="warehouse-pallet-limit-history" role="dialog" aria-labelledby="myLargeModalLabel" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">{{ trans('messages.warehouse-pallet-limit-history') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body add-lookup-modal-html">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="form-group">
                            <label for="search_warehouse" class="control-label">{{ trans('messages.warehouse') }}</label>
                            <select class="form-control" name="search_warehouse" disabled>
                                <option value="">{{ trans("messages.select") }}</option>
                                <option value="warehouse 1">Warehouse 1</option>
                                <option value="warehouse 2">Warehouse 2</option>
                            </select>
                            </div>
                        </div>
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label" for="search_month">{{ trans("messages.month") }}</label>
								<input type="text" name="search_month" class="form-control date-format" placeholder="{{ trans('messages.month') }}">
							</div>
						</div>
						<div class="col-xl-2 col-md-4 d-flex align-items-end gap pt-3">
							<button type="button" title="{{ trans('messages.search') }}" class="btn btn-theme text-white">{{ trans("messages.search") }}</button>
							<button type="button" title="{{ trans('messages.reset') }}" class="btn btn-outline-secondary reset-wild-tigers">{{ trans("messages.reset") }}</button>
						</div>
					</div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="table-responsive table-head-sticky table-height-sticky">
                                <table class="table table-hover table-bordered table-sm pb-4">
                                    <thead>
                                        <tr>
                                            <th class="sr-col">{{ trans("messages.sr-no") }}</th>
                                            <th style="max-width:100px;min-width:100px;">{{ trans("messages.date") }}</th>
                                            <th style="max-width:100px;min-width:100px;">{{ trans("messages.pallet-limit") }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center">1</td>
                                            <td style="max-width:100px;min-width:100px;">1-11-2024</td>
                                            <td style="max-width:100px;min-width:100px;">50</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">2</td>
                                            <td style="max-width:100px;min-width:100px;">2-11-2024</td>
                                            <td style="max-width:100px;min-width:100px;">500</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">3</td>
                                            <td style="max-width:100px;min-width:100px;">3-11-2024</td>
                                            <td style="max-width:100px;min-width:100px;">200</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">4</td>
                                            <td style="max-width:100px;min-width:100px;">4-11-2024</td>
                                            <td style="max-width:100px;min-width:100px;">100</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">5</td>
                                            <td style="max-width:100px;min-width:100px;">5-11-2024</td>
                                            <td style="max-width:100px;min-width:100px;">1000</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">6</td>
                                            <td style="max-width:100px;min-width:100px;">6-11-2024</td>
                                            <td style="max-width:100px;min-width:100px;">800</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">7</td>
                                            <td style="max-width:100px;min-width:100px;">7-11-2024</td>
                                            <td style="max-width:100px;min-width:100px;">80</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">8</td>
                                            <td style="max-width:100px;min-width:100px;">8-11-2024</td>
                                            <td style="max-width:100px;min-width:100px;">700</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">9</td>
                                            <td style="max-width:100px;min-width:100px;">9-11-2024</td>
                                            <td style="max-width:100px;min-width:100px;">1520</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">10</td>
                                            <td style="max-width:100px;min-width:100px;">10-11-2024</td>
                                            <td style="max-width:100px;min-width:100px;">1020</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">11</td>
                                            <td style="max-width:100px;min-width:100px;">11-11-2024</td>
                                            <td style="max-width:100px;min-width:100px;">5000</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">12</td>
                                            <td style="max-width:100px;min-width:100px;">12-11-2024</td>
                                            <td style="max-width:100px;min-width:100px;">1000</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">13</td>
                                            <td style="max-width:100px;min-width:100px;">13-11-2024</td>
                                            <td style="max-width:100px;min-width:100px;">5</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">14</td>
                                            <td style="max-width:100px;min-width:100px;">14-11-2024</td>
                                            <td style="max-width:100px;min-width:100px;">5</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">15</td>
                                            <td style="max-width:100px;min-width:100px;">15-11-2024</td>
                                            <td style="max-width:100px;min-width:100px;">5</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">16</td>
                                            <td style="max-width:100px;min-width:100px;">16-11-2024</td>
                                            <td style="max-width:100px;min-width:100px;">5</td>
                                        </tr>
                                    </tbody>
                                </table>                           
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $(".date-format").datetimepicker({
                useCurrent: false,
                viewMode: 'days',
                ignoreReadonly: true,
                widgetPositioning: {
                    vertical: 'bottom'
                },
                format: 'MM-YYYY',

            });
        });

        $("#warehouse-filter-form").validate({
            errorClass: "invalid-input",
            rules: {
                warehouse_name: {
                    required: true
                },
            },
            messages: {
                warehouse_name: {
                    required: "{{ trans('messages.require-select-warehouse-name') }}"
                },
            },
            submitHandler: function(form) {
                showLoader()
                form.submit();
            }
        });
    </script>
</main>
@endsection