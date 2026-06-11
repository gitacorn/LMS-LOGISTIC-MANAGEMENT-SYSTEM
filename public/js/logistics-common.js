/**
 * Logistics Common Functions
 * Contains all common JavaScript functions used across the Laravel LMS application
 */

// Global variables
var site_url = window.LaravelApp ? window.LaravelApp.siteUrl : (window.site_url || '/');

/**
 * Delete Record Function
 * @param {string} deleteUrl - The URL to delete the record
 * @param {string} deleteRecordId - The ID of the record to delete
 * @param {string} deleteModuleName - The module name
 */
function deleteRecord(deleteUrl, deleteRecordId, deleteModuleName) {
    if (confirm(window.LaravelApp.messages.confirmDelete || 'Are you sure you want to delete this record?')) {
        // Set form values
        $("input[name='delete_record_id']").val(deleteRecordId);
        $("input[name='delete_module_name']").val(deleteModuleName);
        
        // Set form action and submit
        $("#delete-record-form").attr('action', deleteUrl);
        $("#delete-record-form").submit();
    }
}

/**
 * Update Status Function
 * @param {string} statusUrl - The URL to update status
 * @param {string} recordId - The ID of the record
 * @param {string} currentStatus - Current status value
 * @param {string} moduleName - The module name
 */
function updateStatus(statusUrl, recordId, currentStatus, moduleName) {
    if (confirm(window.LaravelApp.messages.confirmStatusChange || 'Are you sure you want to change the status?')) {
        var newStatus = (currentStatus == window.LaravelApp.constants.statusActive) ? 
                       window.LaravelApp.constants.statusInactive : 
                       window.LaravelApp.constants.statusActive;
        
        $.ajax({
            type: "POST",
            url: statusUrl,
            headers: {
                'X-CSRF-TOKEN': window.LaravelApp.csrfToken
            },
            data: {
                "_token": window.LaravelApp.csrfToken,
                'record_id': recordId,
                'status': newStatus,
                'module_name': moduleName
            },
            beforeSend: function() {
                showLoader();
            },
            success: function(response) {
                hideLoader();
                if (response.status_code == 1) {
                    showSuccessMessage(response.message);
                    // Reload the page or update the UI
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showErrorMessage(response.message);
                }
            },
            error: function(xhr, status, error) {
                hideLoader();
                showErrorMessage('An error occurred while updating the status.');
            }
        });
    }
}

/**
 * Manage Session Messages
 * @param {string} redirectUrl - URL to redirect to
 * @param {string} moduleName - Module name
 * @param {string} action - Action type (add, edit, etc.)
 */
function manageSessionMessages(redirectUrl, moduleName, action) {
    $("input[name='session_redirect_module_url']").val(redirectUrl);
    $("input[name='session_redirect_module_name']").val(moduleName);
    $("input[name='session_redirect_module_action']").val(action || 'add');
    
    $("#manage-session-messages-form").submit();
}

/**
 * Show Success Message
 * @param {string} message - Success message to display
 */
function showSuccessMessage(message) {
    if (typeof alertify !== 'undefined') {
        alertify.success(message);
    } else {
        alert(message);
    }
}

/**
 * Show Error Message
 * @param {string} message - Error message to display
 */
function showErrorMessage(message) {
    if (typeof alertify !== 'undefined') {
        alertify.error(message);
    } else {
        alert(message);
    }
}

/**
 * Show Warning Message
 * @param {string} message - Warning message to display
 */
function showWarningMessage(message) {
    if (typeof alertify !== 'undefined') {
        alertify.warning(message);
    } else {
        alert(message);
    }
}

/**
 * Show Info Message
 * @param {string} message - Info message to display
 */
function showInfoMessage(message) {
    if (typeof alertify !== 'undefined') {
        alertify.message(message);
    } else {
        alert(message);
    }
}

/**
 * Show Loader
 */
function showLoader() {
    if ($('#loader').length === 0) {
        $('body').append('<div id="loader" class="loader-overlay"><div class="loader-spinner"></div></div>');
    }
    $('#loader').show();
}

/**
 * Hide Loader
 */
function hideLoader() {
    $('#loader').hide();
}

/**
 * Open Bootstrap Modal
 * @param {string} modalId - ID of the modal to open
 */
function openBootstrapModal(modalId) {
    $('#' + modalId).modal('show');
}

/**
 * Close Bootstrap Modal
 * @param {string} modalId - ID of the modal to close
 */
function closeBootstrapModal(modalId) {
    $('#' + modalId).modal('hide');
}

/**
 * Validate Form
 * @param {string} formId - ID of the form to validate
 * @param {object} rules - Validation rules
 * @param {object} messages - Custom validation messages
 */
function validateForm(formId, rules, messages) {
    if (typeof $.validator !== 'undefined') {
        return $('#' + formId).validate({
            rules: rules || {},
            messages: messages || {},
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    }
    return null;
}

/**
 * Reset Form
 * @param {string} formId - ID of the form to reset
 */
function resetForm(formId) {
    $('#' + formId)[0].reset();
    $('#' + formId).find('.is-invalid').removeClass('is-invalid');
    $('#' + formId).find('.invalid-feedback').remove();
}

/**
 * Serialize Form Data
 * @param {string} formId - ID of the form
 * @returns {object} Serialized form data
 */
function serializeFormData(formId) {
    var formData = {};
    $('#' + formId).serializeArray().forEach(function(item) {
        formData[item.name] = item.value;
    });
    return formData;
}

/**
 * Make AJAX Request
 * @param {object} options - AJAX options
 */
function makeAjaxRequest(options) {
    var defaultOptions = {
        type: 'POST',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': window.LaravelApp.csrfToken
        },
        beforeSend: function() {
            if (options.showLoader !== false) {
                showLoader();
            }
        },
        complete: function() {
            if (options.showLoader !== false) {
                hideLoader();
            }
        },
        error: function(xhr, status, error) {
            showErrorMessage('An error occurred: ' + error);
        }
    };
    
    $.ajax($.extend(defaultOptions, options));
}

/**
 * Format Date
 * @param {Date|string} date - Date to format
 * @param {string} format - Date format (default: 'DD/MM/YYYY')
 * @returns {string} Formatted date
 */
function formatDate(date, format) {
    if (typeof moment !== 'undefined') {
        return moment(date).format(format || 'DD/MM/YYYY');
    }
    return new Date(date).toLocaleDateString();
}

/**
 * Initialize Select2
 * @param {string} selector - CSS selector for select elements
 * @param {object} options - Select2 options
 */
function initializeSelect2(selector, options) {
    if (typeof $.fn.select2 !== 'undefined') {
        $(selector).select2(options || {});
    }
}

/**
 * Initialize Date Picker
 * @param {string} selector - CSS selector for date inputs
 * @param {object} options - Date picker options
 */
function initializeDatePicker(selector, options) {
    if (typeof $.fn.datetimepicker !== 'undefined') {
        var defaultOptions = {
            format: 'DD/MM/YYYY',
            showTodayButton: true,
            showClear: true,
            showClose: true
        };
        $(selector).datetimepicker($.extend(defaultOptions, options || {}));
    }
}

/**
 * Initialize Data Table
 * @param {string} selector - CSS selector for table
 * @param {object} options - DataTable options
 */
function initializeDataTable(selector, options) {
    if (typeof $.fn.DataTable !== 'undefined') {
        var defaultOptions = {
            responsive: true,
            processing: true,
            serverSide: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                processing: window.LaravelApp.messages.processing || 'Processing...',
                search: 'Search:',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                infoEmpty: 'Showing 0 to 0 of 0 entries',
                infoFiltered: '(filtered from _MAX_ total entries)',
                paginate: {
                    first: 'First',
                    last: 'Last',
                    next: 'Next',
                    previous: 'Previous'
                }
            }
        };
        return $(selector).DataTable($.extend(defaultOptions, options || {}));
    }
    return null;
}

/**
 * Export Data
 * @param {string} url - Export URL
 * @param {object} data - Data to export
 * @param {string} filename - Export filename
 */
function exportData(url, data, filename) {
    var form = $('<form>', {
        method: 'POST',
        action: url,
        style: 'display: none;'
    });
    
    // Add CSRF token
    form.append($('<input>', {
        type: 'hidden',
        name: '_token',
        value: window.LaravelApp.csrfToken
    }));
    
    // Add data fields
    if (data) {
        $.each(data, function(key, value) {
            form.append($('<input>', {
                type: 'hidden',
                name: key,
                value: value
            }));
        });
    }
    
    // Add filename if provided
    if (filename) {
        form.append($('<input>', {
            type: 'hidden',
            name: 'filename',
            value: filename
        }));
    }
    
    $('body').append(form);
    form.submit();
    form.remove();
}

/**
 * Print Page
 */
function printPage() {
    window.print();
}

/**
 * Go Back to Previous Page
 */
function goBack() {
    window.history.back();
}

/**
 * Redirect to URL
 * @param {string} url - URL to redirect to
 */
function redirectTo(url) {
    window.location.href = url;
}

/**
 * Refresh Page
 */
function refreshPage() {
    location.reload();
}

/**
 * Check if Unique Shipment ID
 * @param {string} shipmentId - Shipment ID to check
 * @returns {boolean} True if unique
 */
function checkUniqueShipmentId(shipmentId) {
    if (typeof unique_shipment_array !== 'undefined') {
        return unique_shipment_array.indexOf(shipmentId) === -1;
    }
    return true;
}

/**
 * Add to Unique Shipment Array
 * @param {string} shipmentId - Shipment ID to add
 */
function addToUniqueShipmentArray(shipmentId) {
    if (typeof unique_shipment_array !== 'undefined') {
        if (unique_shipment_array.indexOf(shipmentId) === -1) {
            unique_shipment_array.push(shipmentId);
        }
    }
}

/**
 * Remove from Unique Shipment Array
 * @param {string} shipmentId - Shipment ID to remove
 */
function removeFromUniqueShipmentArray(shipmentId) {
    if (typeof unique_shipment_array !== 'undefined') {
        var index = unique_shipment_array.indexOf(shipmentId);
        if (index > -1) {
            unique_shipment_array.splice(index, 1);
        }
    }
}

/**
 * Initialize Common Functions on Document Ready
 */
$(document).ready(function() {
    // Initialize tooltips
    if (typeof $.fn.tooltip !== 'undefined') {
        $('[data-toggle="tooltip"]').tooltip();
    }
    
    // Initialize popovers
    if (typeof $.fn.popover !== 'undefined') {
        $('[data-toggle="popover"]').popover();
    }
    
    // Set autocomplete off for all text inputs
    $('input[type="text"]').attr('autocomplete', 'off');
    
    // Handle AJAX success to set autocomplete off
    $(document).ajaxSuccess(function() {
        $('input[type="text"]').attr('autocomplete', 'off');
    });
    
    // Add loading state to buttons on form submit
    $('form').on('submit', function() {
        $(this).find('button[type="submit"]').prop('disabled', true).addClass('loading');
    });
    
    // Remove loading state on page unload
    $(window).on('beforeunload', function() {
        $('button.loading').prop('disabled', false).removeClass('loading');
    });
});

// Export functions for global access
window.LogisticsCommon = {
    deleteRecord: deleteRecord,
    updateStatus: updateStatus,
    manageSessionMessages: manageSessionMessages,
    showSuccessMessage: showSuccessMessage,
    showErrorMessage: showErrorMessage,
    showWarningMessage: showWarningMessage,
    showInfoMessage: showInfoMessage,
    showLoader: showLoader,
    hideLoader: hideLoader,
    openBootstrapModal: openBootstrapModal,
    closeBootstrapModal: closeBootstrapModal,
    validateForm: validateForm,
    resetForm: resetForm,
    serializeFormData: serializeFormData,
    makeAjaxRequest: makeAjaxRequest,
    formatDate: formatDate,
    initializeSelect2: initializeSelect2,
    initializeDatePicker: initializeDatePicker,
    initializeDataTable: initializeDataTable,
    exportData: exportData,
    printPage: printPage,
    goBack: goBack,
    redirectTo: redirectTo,
    refreshPage: refreshPage,
    checkUniqueShipmentId: checkUniqueShipmentId,
    addToUniqueShipmentArray: addToUniqueShipmentArray,
    removeFromUniqueShipmentArray: removeFromUniqueShipmentArray
};