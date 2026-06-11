// ===== COMMON FUNCTIONS FOR LOGISTIC MANAGEMENT SYSTEM =====

// Function to update record status
function updateRecordStatus(thisitem, moduleName) {
    var recordId = $(thisitem).attr("data-record-id");
    var hitURL = site_url + moduleName + "/updateStatus";
    var currentRow = $(thisitem);
    var status = $(thisitem).parents('.status-class').find('.record-status').text();

    // temper module name for lookup_module
    if (moduleName == "lookup") {
        moduleName = $(thisitem).attr("data-another-module-name");
    }
    status = $.trim(status);
    var confirm_update_msg = '';
    if (status.toLowerCase() == 'enable') {
        doStatus = 'disable';
        confirm_update_msg = "Are you sure you want to disable this record?";
    } else if (status.toLowerCase() == 'disable') {
        doStatus = 'enable';
        confirm_update_msg = "Are you sure you want to enable this record?";
    } else {
        alertifyMessage('error', 'System error occurred');
    }

    moduleName = moduleName.replace(/_/g, ' ');

    alertify.confirm('Update Status', confirm_update_msg, function() {
        // ajax request
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: hitURL,
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                'record_id': recordId,
                'current_status': status,
                'lookup_module_name': moduleName
            },
            beforeSend: function() {
                // block ui
                showLoader();
            },
            success: function(response) {
                hideLoader();
                if (response.status_code == 1) {
                    if ($(document).find('.filter-button').length > 0) {
                        filterData();
                    }
                    alertifyMessage('success', response.message);
                    $(thisitem).parents('.status-class').find('.record-status').text(response.data.update_status);
                } else if (response.status_code == 101) {
                    alertifyMessage('error', response.message);
                } else {
                    alertifyMessage('error', 'System error occurred');
                }
            },
            error: function() {

            }
        });
    }, function() {
        if (status.toLowerCase() == 'enable') {
            $(thisitem).prop('checked', true);
        } else if (status.toLowerCase() == 'disable') {
            $(thisitem).prop('checked', false);
        }
    });
}

// Function to delete record
function deleteRecord(thisitem) {
    var module_name = $.trim($(thisitem).attr('data-module-name'));

    alertify.confirm('Delete Record', 'Are you sure you want to delete this record?', function() {
        var record_id = $.trim($(thisitem).attr('data-record-id'));
        var module_name = $.trim($(thisitem).attr('data-module-name'));

        if (module_name != '' && module_name != null && record_id != null && record_id != "") {
            if (module_name == "lookup") {
                $("[name='delete_module_name']").val($.trim($(thisitem).attr('data-another-module-name')));
            } else {
                $("[name='delete_module_name']").val(module_name);
            }
            $("[name='delete_record_id']").val(record_id);

            var deleteUrl = site_url + module_name + "/delete/" + record_id;
            $("#delete-record-form").attr('action', deleteUrl);
            showLoader();
            $("#delete-record-form").submit();
        }
    }, function() {});
}

// Function to remove logistic table record
function removeLogisticTableRrecord(thisitem, tbody_class_name = null) {
    alertify.confirm('Delete Record', 'Are you sure you want to delete this record?', function() {
        if (tbody_class_name == null || tbody_class_name == '') {
            tbody_class_name = $(thisitem).parents('tbody').attr('class');
        }
        $(thisitem).parents('tr').remove();
        reindexTable(tbody_class_name)
    }, function() {});
}

// Function to reindex table
function reindexTable(tbody_class_name) {
    var table_index = 1;
    $('.' + tbody_class_name + ' tr').each(function() {
        $(this).find('.table-index').html(table_index);
        table_index++;
    })
}

// Function to remove uploaded file
function removeUploadedFile(thisitem) {
    var record_id = $.trim($(thisitem).attr("data-record-id"));
    var module_name = $.trim($(thisitem).attr("data-field-name"));
    var file_name = $.trim($(thisitem).attr("data-file-name"));
    var form_name = $.trim($(thisitem).parents('form').attr('id'));
    var field_name = 'remove_' + module_name + '_' + record_id;

    alertify.confirm('Delete File', 'Are you sure you want to delete this file?', function() {
        if (module_name != '' && module_name != null && record_id != null && record_id != "") {
            if ($("#" + form_name).find("[name='" + field_name + "']").length > 0) {
                var read_previous_value = $("#" + form_name).find("[name='" + field_name + "']").val();
                var updated_value = read_previous_value + ',' + file_name;
                $("#" + form_name).find("[name='" + field_name + "']").val(updated_value);
            } else {
                $("<input type='hidden'/>")
                    .attr("name", field_name)
                    .attr("value", file_name)
                    .appendTo("#" + form_name);
            }
            $(thisitem).parents('.download-link-items').remove();
        }
    }, function() {});
}

// Function to get port to agent container details
function getPortToagentContainerDetails(thisitem) {
    var logistic_parner_detail_id = $.trim($("[name='logistic_partner_name']").val());
    var data_record_filter_id = $.trim($(thisitem).attr("data-record-id"));

    if (logistic_parner_detail_id != "" && logistic_parner_detail_id != null) {
        $.ajax({
            type: "POST",
            url: site_url + 'agent-warehouse-to-amazon/getPortToagentContainerDetails',
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                'logistic_parner_detail_id': logistic_parner_detail_id,
                'data_record_filter_id': data_record_filter_id,
            },
            beforeSend: function() {
                // block ui
                showLoader();
            },
            success: function(response) {
                hideLoader();
                $(thisitem).parents('.warehouse-amazon-list').find('.container-list').html('');
                if (response != "" && response != null) {
                    $(thisitem).parents('.warehouse-amazon-list').find('.container-list').html(response);
                }
            },
            error: function() {
                hideLoader();
            }
        });
    }
}

// Function to add new invoice row
var agent_warehouse_transporter_count = 2;
function addNewInvoiceRow(thisitem) {
    agent_warehouse_transporter_count++;
    var html = "";
    html += '<tr>';
    html += '<td class="table-index">' + agent_warehouse_transporter_count + '</td>';
    html += '<td class="text-left">';
    html += '<select name="name_' + agent_warehouse_transporter_count + '" class="form-control agent-warehouse-transporter-name select2">';
    html += '<option value="">Select</option>';
    // Note: This would need to be populated with actual data from the server
    html += '</select>';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<input type="text" class="form-control agent-warehouse-transporter-inv-no" name="inv_no_' + agent_warehouse_transporter_count + '" placeholder="Invoice No">';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<input type="text" class="form-control agent-to-warehouse-freight" name="freight_' + agent_warehouse_transporter_count + '" placeholder="Freight" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<input type="text" class="form-control agent-to-warehouse-custom" name="custom_' + agent_warehouse_transporter_count + '" placeholder="Custom" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<input type="text" class="form-control agent-to-warehouse-duty" name="duty_' + agent_warehouse_transporter_count + '" placeholder="Duty" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<input type="text" class="form-control agent-to-warehouse-other" name="other_' + agent_warehouse_transporter_count + '" placeholder="Other" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<input type="text" class="form-control agent-to-warehouse-vat" name="vat_' + agent_warehouse_transporter_count + '" placeholder="VAT" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<div class="input-group align-items-center flex-nowrap">';
    html += '<label class="mb-0" for=""><span class="agent-warehouse-total-value"></span></label>';
    html += '<div class="input-group-prepend">';
    html += '<select class="form-control ml-2" name="currency_id_' + agent_warehouse_transporter_count + '" onchange="getTotalNumberOfValue(this)">';
    html += '<option value="">Currency</option>';
    html += '</select>';
    html += '</div>';
    html += '</div>';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<input type="text" class="form-control agent-to-warehouse-con-rate" name="cov_rate_' + agent_warehouse_transporter_count + '" placeholder="Conversion Rate" onkeyup="onlyDecimal(this),getTotalNumberOfValue(this)">';
    html += '</td>';
    html += '<td class="text-left"><span class="agent-warehouse-final-rate"></span></td>';
    html += '<td class="text-left">';
    html += '<div class="custom-file">';
    html += '<input type="file" class="custom-file-input" id="invoice_' + agent_warehouse_transporter_count + '" name="invoice_file_' + agent_warehouse_transporter_count + '[]" multiple onchange="validFile(this,\'pdf_doc_jpg_png_jpeg_xls\')">';
    html += '<label class="custom-file-label" for="invoice_' + agent_warehouse_transporter_count + '">Choose file</label>';
    html += '</div>';
    html += '</td>';
    html += '<td class="actions-col">';
    html += '</td>';
    html += '</tr>';
    
    if ($('.agent-to-warehouse-transport-tbody').find('tr').length > 0) {
        $(html).insertAfter($('.agent-to-warehouse-transport-tbody').find('tr:last'));
    } else {
        $('.agent-to-warehouse-transport-tbody').html(html);
    }
    reindexTable('agent-to-warehouse-transport-tbody');

    $(function() {
        $('.select2').select2();
    })
}

// Function to add new document row
var agent_warehouse_document_type_count = 2;
function addNewDocumentRow(thisitem) {
    agent_warehouse_document_type_count++;
    var html = "";
    html += '<tr>';
    html += '<td class="table-index text-center" style="width:70px;min-width:70px;">' + agent_warehouse_document_type_count + '</td>';
    html += '<td class="text-left">';
    html += '<select name="type_' + agent_warehouse_document_type_count + '" class="form-control warehouse-document-type">';
    html += '<option value="">Select</option>';
    html += '</select>';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<div class="custom-file">';
    html += '<input type="file" class="custom-file-input warehouse-document-file" id="document_' + agent_warehouse_document_type_count + '" name="file_' + agent_warehouse_document_type_count + '[]" multiple onchange="validFile(this,\'pdf_doc_jpg_png_jpeg_xls\')">';
    html += '<label class="custom-file-label" for="document_' + agent_warehouse_document_type_count + '">Choose file</label>';
    html += '</div>';
    html += '</td>';
    html += '<td class="text-left">';
    html += '<input type="text" class="form-control" name="remarks_' + agent_warehouse_document_type_count + '">';
    html += '</td>';
    html += '<td class="actions-col">';
    html += '</td>';
    html += '<td style="width:70px;min-width:70px;">';
    html += '<button type="button" class="btn btn-sm btn-danger m-auto d-table" onclick="removeLogisticTableRrecord(this)"><i class="fa fa-trash fa-fw"></i></button>';
    html += '</td>';
    html += '</tr>';
    
    if ($('.agent-to-warehouse-document-tbody').find('tr').length > 0) {
        $(html).insertAfter($('.agent-to-warehouse-document-tbody').find('tr:last'));
    } else {
        $('.agent-to-warehouse-document-tbody').html(html);
    }
    reindexTable('agent-to-warehouse-document-tbody');
}

// Function to get total number of value
function getTotalNumberOfValue(thisitem) {
    var agent_to_warehouse_freight = $.trim($(thisitem).parents('tr').find('.agent-to-warehouse-freight').val());
    var agent_to_warehouse_custom = $.trim($(thisitem).parents('tr').find('.agent-to-warehouse-custom').val());
    var agent_to_warehouse_duty = $.trim($(thisitem).parents('tr').find('.agent-to-warehouse-duty').val());
    var agent_to_warehouse_other = $.trim($(thisitem).parents('tr').find('.agent-to-warehouse-other').val());
    var agent_to_warehouse_vat = $.trim($(thisitem).parents('tr').find('.agent-to-warehouse-vat').val());
    var agent_to_warehouse_con_rate = $.trim($(thisitem).parents('tr').find('.agent-to-warehouse-con-rate').val());

    agent_to_warehouse_freight = (parseFloat(agent_to_warehouse_freight) > 0.00 ? parseFloat(agent_to_warehouse_freight).toFixed(2) : 0.00);
    agent_to_warehouse_custom = (parseFloat(agent_to_warehouse_custom) > 0.00 ? parseFloat(agent_to_warehouse_custom).toFixed(2) : 0.00);
    agent_to_warehouse_duty = (parseFloat(agent_to_warehouse_duty) > 0.00 ? parseFloat(agent_to_warehouse_duty).toFixed(2) : 0.00);
    agent_to_warehouse_other = (parseFloat(agent_to_warehouse_other) > 0.00 ? parseFloat(agent_to_warehouse_other).toFixed(2) : 0.00);
    agent_to_warehouse_vat = (parseFloat(agent_to_warehouse_vat) > 0.00 ? parseFloat(agent_to_warehouse_vat).toFixed(2) : 0.00);
    agent_to_warehouse_con_rate = (parseFloat(agent_to_warehouse_con_rate) > 0.00 ? parseFloat(agent_to_warehouse_con_rate).toFixed(4) : 0.00);

    var total_value = (parseFloat(agent_to_warehouse_freight) + parseFloat(agent_to_warehouse_custom) + parseFloat(agent_to_warehouse_duty) + parseFloat(agent_to_warehouse_other) + parseFloat(agent_to_warehouse_vat));
    var total_con_rate = (parseFloat(total_value) * parseFloat(agent_to_warehouse_con_rate));

    total_value = (parseFloat(total_value) > 0.00 ? parseFloat(total_value) : 0.00);
    total_value = (parseFloat(total_value) > 0.00 ? total_value.toFixed(2) : 0.00);
    total_con_rate = (parseFloat(total_con_rate) > 0.00 ? parseFloat(total_con_rate) : 0.00);
    total_con_rate = (parseFloat(total_con_rate) > 0.00 ? total_con_rate.toFixed(2) : 0.00);

    $(thisitem).parents('tr').find('.agent-warehouse-total-value').html(total_value);
    $(thisitem).parents('tr').find('.agent-warehouse-final-rate').html(total_con_rate);

    if ($('[name="logistic_cost_usd"]').length > 0) {
        calculateTotalTransportCharge()
    }
}

// Function to calculate total transport charge
function calculateTotalTransportCharge() {
    let total_charge = 0;
    if ($('.agent-to-warehouse-transport-tbody tr').length > 0) {
        $('.agent-to-warehouse-transport-tbody tr').each(function() {
            let get_final_total = parseFloat($(this).find('.agent-warehouse-final-rate').text());
            if (get_final_total > 0) {
                total_charge += get_final_total;
            }
        })
    }
    $('[name="logistic_cost_usd"]').val(total_charge.toFixed(2));
}

// Function to validate file
function validFile(thisitem, allowed_file_type = 'image') {
    var filedId = $(thisitem).attr("id");
    var validImageTypes = [];
    var validExtensions = [];
    var message = '';

    switch (allowed_file_type) {
        case 'pdf_doc':
            validExtensions = ['pdf', 'doc', 'docx'];
            message = 'Invalid file format. Please select a valid file.';
            break;
        case 'excel':
            validExtensions = ['xls', 'xlsx'];
            message = 'Invalid file format. Please select a valid file.';
            break;
        case 'pdf_doc_ppt_html_xls':
            validExtensions = ['pdf', 'doc', 'docx', 'html', 'ppt', 'pptx', 'xls', 'xlsx'];
            message = 'Invalid file format. Please select a valid file.';
            break;
        case 'pdf_doc_jpg_png_jpeg_xls':
            validExtensions = ['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg', 'xls', 'xlsx'];
            message = 'Invalid file format. Please select a valid file.';
            break;
        case 'pdf_doc_ppt_html':
            validExtensions = ['pdf', 'doc', 'docx', 'html', 'ppt', 'pptx'];
            message = 'Invalid file format. Please select a valid file.';
            break;
        case 'image':
            validImageTypes = ['image/jpg', 'image/jpeg', 'image/png'];
            validExtensions = ['jpg', 'jpeg', 'png'];
            message = 'Invalid image format. Please select a valid image.';
            break;
        case 'image_pdf':
            validImageTypes = ['image/jpg', 'image/jpeg', 'image/png', 'application/pdf'];
            validExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
            message = 'Invalid file format. Please select a valid image or PDF.';
            break;
        case 'cdr':
            validImageTypes = ['application/octet-stream', 'application/coreldraw', 'application/x-coreldraw', 'application/vnd.corel-draw', 'application/cdr', 'application/x-cdr', 'image/cdr', 'image/x-cdr', 'zz-application/zz-winassoc-cdr'];
            validExtensions = ['cdr'];
            message = 'Invalid CDR file format.';
            break;
        case 'pdf_cdr':
            validImageTypes = ['application/pdf', 'application/cdr', 'application/x-cdr', 'image/cdr', 'image/x-cdr', 'zz-application/zz-winassoc-cdr', 'application/pdf'];
            validExtensions = ['pdf', 'cdr'];
            message = 'Invalid file format. Please select PDF or CDR file.';
            break;
        case 'pdf_cdr_jpg':
            validImageTypes = ['image/jpg', 'image/jpeg', 'application/pdf', 'application/cdr', 'application/x-cdr', 'image/cdr', 'image/x-cdr', 'zz-application/zz-winassoc-cdr', 'application/pdf'];
            validExtensions = ['pdf', 'cdr', 'jpg'];
            message = 'Invalid file format. Please select PDF, CDR or JPG file.';
            break;
        case 'pdf_cdr_jpg_png_jpeg':
            validImageTypes = ['image/jpg', 'image/jpeg', 'image/png', 'application/pdf', 'application/cdr', 'application/x-cdr', 'image/cdr', 'image/x-cdr', 'zz-application/zz-winassoc-cdr', 'application/pdf'];
            validExtensions = ['pdf', 'cdr', 'jpg', 'png', 'jpeg'];
            message = 'Invalid file format. Please select a valid file.';
            break;
        case 'csv':
            validExtensions = ['csv'];
            message = 'Invalid file format. Please select a CSV file.';
            break;
    }

    var input = this;

    if (thisitem.files && thisitem.files[0]) {
        var filesAmount = thisitem.files.length;

        for (i = 0; i < filesAmount; i++) {
            var fileName = $.trim(thisitem.files[i].name);
            var fileNameExt = fileName.substr(fileName.lastIndexOf('.') + 1);
            fileNameExt = ((fileNameExt != "" && fileNameExt != null) ? fileNameExt.toLowerCase() : '');
            var fileType = thisitem.files[i]["type"];

            var reader = new FileReader();

            if ($.inArray(fileNameExt, validExtensions) == -1) {
                alertifyMessage("error", message);
                $("." + filedId + "-preview-div").hide();
                $("." + filedId + "-preview").attr("src", "");
                $(thisitem).attr('data-has-file', 'no');
                $(thisitem).attr('data-valid-file', 'no');
                $(thisitem).siblings(".custom-file-label").html("Choose file");
                $(thisitem).blur();
                $(thisitem).val("");
                return false;
            }

            var invalidImageTypes = ['application/pdf', 'application/octet-stream', 'application/coreldraw', 'application/x-coreldraw', 'application/vnd.corel-draw', 'application/cdr', 'application/x-cdr', 'image/cdr', 'image/x-cdr', 'zz-application/zz-winassoc-cdr'];

            if ($.inArray(fileNameExt, validExtensions) != -1) {
                $("." + filedId + "-preview-div").parent('div').show();
                $("." + filedId + "-preview-div").show();
                $("." + filedId + "-preview").show();
                $("." + filedId + "-preview").attr("src", "");

                if (allowed_file_type == 'image') {
                    reader.onload = function(e) {
                        $("." + filedId + "-preview").attr("src", e.target.result);
                    }
                    reader.readAsDataURL(thisitem.files[i]);
                    $('.submit-button').prop('disabled', false);
                }
                $(thisitem).attr('data-has-file', 'yes');
                $(thisitem).attr('data-valid-file', 'yes');

                if ($(thisitem).attr("multiple") == "multiple") {
                    if (thisitem.files.length > 1) {
                        $(thisitem).siblings(".custom-file-label").html("Multiple Files Selected");
                    } else {
                        $(thisitem).siblings(".custom-file-label").html(thisitem.files[0]["name"]);
                    }
                } else {
                    $(thisitem).siblings(".custom-file-label").html(thisitem.files[0]["name"]);
                }
            }
        }
    }
}

// Function to export data into Excel
function dataExportIntoExcel(export_info) {
    if (export_info != "" && export_info != null) {
        var paginationUrl = export_info.url;
        var searchData = export_info.searchData;
        searchData.custom_export_action = 'export';

        $.ajax({
            url: paginationUrl,
            type: 'post',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: searchData,
            beforeSend: function() {
                // block ui
                showLoader();
            },
            success: function(response) {
                hideLoader();
                if (response.status_code == 1) {
                    var opResult = response;
                    var $a = $("<a>");
                    $a.attr("href", opResult.data);
                    $("body").append($a);
                    $a.attr("download", response.file_name);
                    $a[0].click();
                    $a.remove();
                } else if (response.status_code == 101) {
                    alertifyMessage('error', 'No records available to export');
                }
            }
        });
    } else {
        alertifyMessage('error', 'No records available to export');
    }
}

// Function to check unique shipment ID
function checkUniqueShipmentId(thisitem) {
    var shipment_no = $.trim($(thisitem).parents('tr').find('.amazon-shipment-id-status').val());
    var shipment_record_id = $.trim($(thisitem).parents('tr').find('.amazon-shipment-id-status').attr('data-shipment-id'));
    var shipment_record_type = $.trim($(thisitem).parents('tr').find('.amazon-shipment-id-status').attr('data-record-type'));

    if (shipment_no != "" && shipment_no != null) {
        $.ajax({
            type: "POST",
            async: false,
            url: site_url + 'europe-to-amazon/checkUniqueShipmentId',
            dataType: "json",
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                shipment_record_type: shipment_record_type,
                shipment_record_id: shipment_record_id,
                shipment_no: shipment_no,
                'record_id': ($.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null)
            },
            beforeSend: function() {

            },
            success: function(response) {
                if (response.status_code == 101) {
                    $(thisitem).val("");
                    unique_shipment_id = false;
                    alertifyMessage("error", "This shipment ID already exists. Please use a unique shipment ID.");
                } else {
                    unique_shipment_id = true;
                }
            }
        });
    }
}

// Modal reset function
$(function() {
    $('.modal').on('hidden.bs.modal', function() {
        if ($(this).find('form').length > 0) {
            $(this).find('form').validate().resetForm();
            $(this).find('form').trigger("reset");
            $(this).find('form .custom-file-label').html("Choose file");
        }
    });
});

// Function to show collection delivery data
function showCollectionDeliveryData(thisitem) {
    var search_collection_delivery = $.trim($('[name="search_collection_delivery"]').val());
    if (search_collection_delivery != "" && search_collection_delivery != null) {
        if (search_collection_delivery == 'collection') {
            $('.delivery-collection-row').hide();
            $('.delivery-collection-location').show();
        } else {
            $('.delivery-collection-row').show();
            $('.delivery-collection-location').hide();
        }
    } else {
        $('.delivery-collection-row').hide();
        $('.delivery-collection-location').hide();
    }
}

// Function to get supplier details
function getSupplierDetails(thisitem) {
    var supplier_country_id = $.trim($('[name="search_supplier_country"]').val());

    $.ajax({
        type: 'post',
        data: {
            "_token": $('meta[name="csrf-token"]').attr('content'),
            'supplier_country_id': supplier_country_id
        },
        url: site_url + 'goods-in-buyer/getSupplierDetails',
        beforeSend: function() {
            // block ui
            showLoader();
        },
        success: function(response) {
            hideLoader();
            if (response != "" && response != null) {
                $('.supplier-name-list').html(response);
            } else {
                $('.supplier-name-list').html("");
                $('.supplier-location-list').html("");
            }
            filterData();
        },
        error: function() {
            hideLoader();
        }
    });
}

// Function to get dimension info
function getDimensionInfo(thisitem, filterRecord = false) {
    var pallet_box_type = $.trim($(thisitem).val());

    $.ajax({
        type: 'post',
        data: {
            "_token": $('meta[name="csrf-token"]').attr('content'),
            'pallet_box_type': pallet_box_type
        },
        url: site_url + 'goods-in-buyer/get-dimension-details',
        beforeSend: function() {
            showLoader();
        },
        success: function(response) {
            hideLoader();
            if (response != "" && response != null) {
                $(thisitem).parents('.dependent-div-class').find('.pallet-box-dimension-div').html(response);
            }
            if (filterRecord != false) {
                filterData();
            }
        },
        error: function() {
            hideLoader();
        }
    });
}

// Function to get related warehouse by warehouse country
function relatedWarehouseByWarehouseCountry(thisitem, filter_request = false) {
    var from_warehouse_country = $.trim($(thisitem).val());
    var record_id = $.trim($('[name="record_id"]').val());

    $.ajax({
        type: 'post',
        data: {
            'from_warehouse_country': from_warehouse_country,
            'record_id': record_id,
            'filter_request': filter_request
        },
        url: site_url + 'country-to-port-goods-out/related-warehouse-by-warehouse-country',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            showLoader();
        },
        success: function(response) {
            hideLoader();
            $('.warehouse-name-list').html("");
            if (response != "" && response != null) {
                $(thisitem).parents('.dependent-field-div').find('.warehouse-name-list').html(response);
            }
            if (filter_request != false) {
                filterData();
            }
        },
        error: function() {
            hideLoader();
        }
    });
}

// Function to show warehouse type wise location
function warehouseTypeWiseLocation(thisItem, filter_request = false) {
    $('.own-warehouse-location').hide();
    $('.agent-warehouse-location').hide();
    if ($(thisItem).val() === 'own_warehouse') {
        $('.own-warehouse-location').show();
    }
    if ($(thisItem).val() === 'agent_warehouse') {
        $('.agent-warehouse-location').show();
    }
    if (filter_request != false) {
        $('[name="search_to_own_location"]').val('');
        $('[name="to_agent_location"]').val('');
        $('[name="search_to_own_location"]').trigger('change');
        $('[name="to_agent_location"]').trigger('change');
        filterData();
    }
}