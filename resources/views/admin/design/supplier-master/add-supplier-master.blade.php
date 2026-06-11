@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-md-flex border-navabr align-items-center">
        <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ trans("messages.add-supplier") }}</h1>
        <nav aria-label="breadcrumb" class="d-flex ml-auto mr-3">
            <ol class="breadcrumb bg-transparent p-0 mb-0 align-self-end">
                <li class="breadcrumb-item"><a href="{{ url('supplier-master') }}" class="category-add-link">{{ trans("messages.supplier-master") }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ trans("messages.add-supplier") }}</li>
            </ol>
        </nav>
    </div>

    <section class="inner-wrapper-common-sections main-listing-section">
        <div class="container-fluid">
            <div class="card card-body mb-3">
                <form method="post">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="supplier_name">{{ trans("messages.supplier-partner-name") }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control"  name="supplier_name" placeholder="{{ trans('messages.supplier-partner-name') }}" autofocus="">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group pb-3 pt-3">
                                <div class="card shadow-none border">
                                    <div class="card-header">
                                        <span class="partner-tilte">
                                            {{ trans("messages.add-multi-address") }}
                                        </span>

                                    </div>
                                    <div class="card-body supplier">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered table-sm pb-4">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>{{ trans("messages.sr-no") }}</th>
                                                        <th>{{ trans("messages.supplier-code") }} <span class="star">*</span></th>
                                                        <th>{{ trans("messages.supplier-address") }} <span class="star">*</span></th>
                                                        <th>{{ trans("messages.supplier-country") }}<span class="star">*</span></th>
                                                        <th>{{ trans("messages.action") }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-center">1</td>
                                                        <td><input type="text" class="form-control" name="supplier_code"></td>
                                                        <td><textarea class="form-control" name="supplier_address" rows="1"></textarea></td>
                                                        <td>
                                                            <select name="supplier_country" class="form-control">
                                                                <option value="">Select</option>
                                                                <option value="">India</option>
                                                                <option value="">Canada</option>
                                                                <option value="">Lo ndon</option>
                                                            </select>
                                                        </td>
                                                        <td></td>
                                                    <tr>
                                                        <td class="text-center">2</td>
                                                        <td><input type="text" class="form-control" name="supplier_code"></td>
                                                        <td><textarea class="form-control" name="supplier_address" rows="1"></textarea></td>
                                                        <td>
                                                            <select name="supplier_country" class="form-control">
                                                                <option value="">Select</option>
                                                                <option value="">India</option>
                                                                <option value="">Canada</option>
                                                                <option value="">London</option>
                                                            </select>
                                                        </td>
                                                        <td><button type="button" class="btn btn-sm btn-danger m-auto d-table"><i class="fa fa-trash fa-fw"></i></button></td>
                                                    </tr>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <button type="button" class="btn btn-sm bg-theme text-white add-new-row" title="{{ trans('messages.add-new') }}"><i class="fa fa-plus fa-fw"></i>{{ trans("messages.add-new") }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 submit-sticky">
                            <button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.submit') }}">{{ trans("messages.submit") }}</button>
                            <a href="{{ url('supplier-master') }}" class="btn btn-outline-secondary shadow-sm btn-wide" title="{{ trans('messages.cancel') }}">{{ trans("messages.cancel") }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>
<script>
    $("form").validate({
        errorClass: "invalid-input",
        rules: {
            supplier_name: {
                required: true
            },
            supplier_code: {
                required: true
            },
            supplier_address: {
                required: true
            },
            supplier_country: {
                required: true
            },
        },
        messages: {
            supplier_name: {
                required: "{{ trans('messages.require-supplier-partner-name') }}"
            },
            supplier_code: {
                required: "{{ trans('messages.require-supplier-code') }}"
            },
            supplier_address: {
                required: "{{ trans('messages.require-supplier-address') }}"
            },
            supplier_country: {
                required: "{{ trans('messages.require-supplier-country') }}"
            },
        },
        submitHandler: function(form) {
            showLoader()
            form.submit();
        }
    });
</script>
@endsection