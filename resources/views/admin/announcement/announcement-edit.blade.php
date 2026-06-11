@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<link rel="stylesheet" href="{{ asset ('css/select2.css') }}">
<link rel="stylesheet" href="{{ asset ('css/select2-bootstrap4.min.css') }}">
<script type="text/javascript" src="{{ asset ('js/select2.js') }}"></script>

<style>
    :root {
        --primary-light: color-mix(in srgb, var(--primary-color) 8%, white);
        --primary-gradient: linear-gradient(135deg, var(--primary-color), color-mix(in srgb, var(--primary-color) 80%, black));
        
        --success-hsl: 145, 65%, 42%;
        --success-color: hsl(var(--success-hsl));
        --success-light: hsl(145, 65%, 95%);

        --warning-hsl: 35, 85%, 52%;
        --warning-color: hsl(var(--warning-hsl));
        --warning-light: hsl(35, 85%, 95%);

        --danger-hsl: 355, 75%, 55%;
        --danger-color: hsl(var(--danger-hsl));

        --muted-color: #6c757d;
        --border-radius-lg: 16px;
        --border-radius-md: 10px;
        --transition-base: all 0.3s ease;
    }

    .form-wrapper {
        padding: 24px;
        font-family: 'Montserrat', sans-serif;
    }

    .lms-card {
        background: white;
        border-radius: var(--border-radius-lg);
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid rgba(0,0,0,0.05);
        padding: 30px;
        margin-bottom: 30px;
    }

    .form-section-title {
        font-size: 15px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--primary-light);
        padding-bottom: 8px;
    }

    .custom-form-control {
        border-radius: 8px;
        border: 1px solid #ced4da;
        padding: 10px 14px;
        font-size: 14px;
        transition: var(--transition-base);
    }

    .custom-form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(0,99,193,0.15);
        outline: none;
    }

    /* Toggle Switch Component */
    .switch-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #f8f9fa;
        padding: 14px 20px;
        border-radius: var(--border-radius-md);
        border: 1px solid #e9ecef;
        margin-bottom: 20px;
    }

    .switch-label {
        font-weight: 700;
        font-size: 14px;
        color: #343a40;
        margin: 0;
        cursor: pointer;
    }

    .switch-desc {
        font-size: 12px;
        color: var(--muted-color);
        margin: 0;
    }

    .custom-switch-btn {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 26px;
    }

    .custom-switch-btn input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .switch-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ced4da;
        transition: .4s;
        border-radius: 34px;
    }

    .switch-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .switch-slider {
        background-color: var(--primary-color);
    }

    input:checked + .switch-slider:before {
        transform: translateX(24px);
    }

    /* Live Campaign Summary Panel */
    .campaign-summary-panel {
        background: #fdfdfd;
        border-radius: var(--border-radius-lg);
        border: 1.5px dashed var(--primary-color);
        padding: 24px;
        display: none;
        margin-top: 15px;
        animation: slideDown 0.3s ease forwards;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .summary-title {
        font-weight: 700;
        font-size: 14px;
        color: var(--primary-color);
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 15px;
    }

    .summary-steps-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .summary-step-item {
        background: white;
        border-radius: 8px;
        padding: 10px 14px;
        border: 1px solid #f1f3f5;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        transition: var(--transition-base);
    }

    .summary-step-item:hover {
        border-color: #dee2e6;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }

    .step-pill {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 12px;
        text-transform: uppercase;
    }

    .pill-pending { background: var(--primary-light); color: var(--primary-color); }
    .pill-sent { background: var(--success-light); color: var(--success-color); }
    .pill-skipped { background: #f8f9fa; color: #868e96; border: 1px solid #dee2e6; }
    .pill-failed { background: #fff5f5; color: var(--danger-color); border: 1px solid #ffc9c9; }
    .pill-warning { background: var(--warning-light); color: var(--warning-color); border: 1px solid #ffe8cc; }

    /* Button Layout */
    .btn-submit {
        background: var(--primary-gradient);
        border: none;
        color: white;
        font-weight: 700;
        border-radius: 20px;
        padding: 10px 24px;
        transition: var(--transition-base);
    }

    .btn-submit:hover {
        box-shadow: 0 4px 15px rgba(0,99,193,0.3);
        transform: translateY(-1px);
        color: white;
    }

    .btn-cancel {
        background: #f1f3f5;
        border: 1px solid #dee2e6;
        color: #495057;
        font-weight: 700;
        border-radius: 20px;
        padding: 10px 24px;
        transition: var(--transition-base);
    }

    .btn-cancel:hover {
        background: #e9ecef;
        color: #212529;
        text-decoration: none;
    }
</style>

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-navabr">
        <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ $pageTitle }}</h1>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            <button type="button" class="btn btn-theme text-white button-actions-top-bar d-flex align-items-center border btn-sm mr-2" data-toggle="modal" data-target="#emailFormatModal">
                <i class="fas fa-envelope mr-md-2"></i><span class="d-md-block d-none"> Edit Email Format</span>
            </button>
             <a href="{{ url('announcement') }}" class="btn btn-theme text-white button-actions-top-bar d-flex align-items-center border btn-sm" title="{{ trans('messages.back') }}">
                <i class="fas fa-arrow-left mr-md-2"></i><span class="d-md-block d-none"> {{ trans("messages.back") }}</span>
            </a>
        </div>
    </div>

    <div class="form-wrapper">
        <div class="container-fluid">
            <!-- Errors Alert -->
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible" style="border-radius: var(--border-radius-md);">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5 class="font-weight-bold"><i class="icon fas fa-ban mr-2"></i> Please fix the errors below:</h5>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-8 col-md-12">
                    <div class="lms-card">
                        <form method="POST" action="{{ url('announcement/'.$announcement->id) }}" id="announcementForm">
                        @csrf
                        @method('PUT')
                         <div class="form-section-title">Announcement Details</div>
                                <div class="form-group">
                                <label for="announcement_template" class="control-label">Announcement Template <span class="text-danger">*</span></label>
                                <select id="announcement_template" class="form-control" style="border-radius: 8px;">
                                    <option value="" disabled>-- Select a Predefined Template --</option>
                                    <option value="Holiday Closure: Please note that our warehouse operations will be temporarily suspended for holiday closure.">Holiday Closure Announcement</option>
                                    <option value="Inventory Audit: The warehouse will be closed for a scheduled physical inventory count and audit.">Inventory Audit Notice</option>
                                    <option value="System Maintenance: Operations will be paused due to scheduled system upgrade and maintenance.">System Maintenance Notice</option>
                                    <option value="Severe Weather Alert: Warehouse dispatch operations may be delayed due to extreme weather conditions.">Severe Weather Notice</option>
                                    <option value="custom">Custom Message...</option>
                                </select>
                                <small class="text-muted">Choose a structured template or select Custom Message to edit/write your own.</small>
                            </div>
                                <div class="form-group" id="announcement_text_wrapper" style="display: none;">
                                <label for="v_announcement_text" class="control-label">Announcement Message <span class="text-danger">*</span></label>
                                <textarea name="v_announcement_text" id="v_announcement_text" class="form-control" rows="3" required maxlength="1000" placeholder="Type the scroll announcement text displayable in the marquee bar...">{{ old('v_announcement_text', $announcement->v_announcement_text) }}</textarea>
                                <small class="text-muted">Maximum 1000 characters. You can edit the template text here.</small>
                        </div>

                        {{-- Marquee Start Date: hidden, auto-set to Event Date --}}
                            <input type="hidden" name="dt_event_start_date" id="dt_event_start_date" value="{{ old('dt_event_start_date', !empty($announcement->dt_event_start_date) ? date('Y-m-d', strtotime($announcement->dt_event_start_date)) : date('Y-m-d', strtotime($announcement->dt_expiry_date))) }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                        <label for="dt_expiry_date" class="control-label">Event Date <span class="text-danger">*</span></label>
                                        <input type="date" name="dt_expiry_date" id="dt_expiry_date" class="form-control" value="{{ old('dt_expiry_date', date('Y-m-d', strtotime($announcement->dt_expiry_date))) }}" required min="{{ date('Y-m-d') }}">
                                        <small class="text-muted">The event date — marquee disappears and email schedule is calculated 31 days before this.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                        <label class="control-label">Affected Warehouses <span class="text-danger">*</span></label>
                  
                                    <select class="form-control select2" name="warehouse_ids[]" id="warehouse_ids" multiple required>
                                        
                                        @if(!empty($wareHouseDetails))
                                            @foreach($wareHouseDetails as $wareHouseDetail)
                                                @php
                                                    $encodedId = trim(Wild_tiger::encode($wareHouseDetail->i_id));
                                                    $selected = false;
                                                    if (!empty($announcement->warehouse_ids)) {
                                                        $warehouseIds = is_array($announcement->warehouse_ids) ? $announcement->warehouse_ids : json_decode($announcement->warehouse_ids, true);
                                                        if ($warehouseIds && is_array($warehouseIds)) {
                                                            foreach ($warehouseIds as $storedEncodedId) {
                                                                $decodedId = Wild_tiger::decode($storedEncodedId);
                                                                if ($decodedId == $wareHouseDetail->i_id) { $selected = true; break; }
                                                            }
                                                        }
                                                    }
                                                @endphp	
                                                <option value="{{ $encodedId }}" {{ $selected ? 'selected' : '' }}>{{(!empty($wareHouseDetail->v_warehouse_name) ? $wareHouseDetail->v_warehouse_name .(!empty($wareHouseDetail->v_warehouse_code) ? ' (' .$wareHouseDetail->v_warehouse_code .')' : '' ): '' )}}</option>
                                            @endforeach
                                        @endif	
                                    </select>
                                    <small class="text-muted">Select warehouses linked to this event closure.</small>
                                                                        </div>
                                </div>
                            </div>

                            <div class="row align-items-center mt-2 mb-3">
                                <div class="col-md-12">
                                    <div class="custom-control custom-switch d-flex align-items-center">
                                        <input type="checkbox" class="custom-control-input" id="email_enabled" name="email_enabled" value="1" {{ old('email_enabled', $announcement->email_enabled) ? 'checked' : '' }}>
                                        <label class="custom-control-label ml-1" for="email_enabled"><strong>Enable Automated Email Notifications</strong></label>
                                    </div>
                                    <small class="text-muted ml-4">Weekly reminder emails starting exactly 31 days prior to the event.</small>
                                </div>
                            </div>

                            <!-- Email Campaign Schedule Preview -->
                            <div id="campaignSummary" style="display:none;" class="card shadow-none border mb-3">
                                <div class="card-header bg-light py-2">
                                    <strong><i class="fas fa-calendar-check mr-1"></i> Email Campaign Schedule</strong>
                                </div>
                                <div class="card-body p-2">
                                    <div id="summaryList"></div>
                            </div>
                        </div>

                            <div class="col-md-12 submit-sticky mt-3">
                                <button type="submit" class="btn btn bg-theme text-white btn-wide">
                                    <i class="fas fa-save mr-1"></i> Update
                                </button>
                                <a href="{{ url('announcement') }}" class="btn btn-outline-secondary shadow-sm btn-wide">Cancel</a>
                            </div>
                        </form>
                        </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Email Format Modal -->
<div class="modal fade" id="emailFormatModal" tabindex="-1" role="dialog" aria-labelledby="emailFormatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: var(--border-radius-lg);">
            <div class="modal-header bg-light">
                <h4 class="modal-title font-weight-bold text-dark" id="emailFormatModalLabel"><i class="fas fa-envelope-open-text text-primary mr-2"></i> Edit Email Format Template</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="email_subject" class="font-weight-bold text-dark">Email Subject Template</label>
                    <input type="text" id="email_subject" name="email_subject" class="form-control custom-form-control" value="{{ old('email_subject', $announcement->email_subject ?? 'Warehouse Closure Update - [Eventdate] - [Day]') }}">
                    <small class="form-text text-muted">Use <strong>[Eventdate]</strong> and <strong>[Day]</strong> placeholders.</small>
                </div>
                <div class="form-group">
                    <label for="email_message" class="font-weight-bold text-dark">Email Message Body Template</label>
                    <textarea id="email_message" name="email_message" class="form-control custom-form-control" rows="12">{{ old('email_message', $announcement->email_message ?? '') }}</textarea>


                    <small class="form-text text-muted">Use <strong>[Warehouse Name]</strong>, <strong>[Event Date]</strong>, and <strong>[Day(s)]</strong> placeholders.</small>
                </div>
            </div>
            <div class="modal-footer bg-light" style="border-bottom-left-radius: var(--border-radius-lg); border-bottom-right-radius: var(--border-radius-lg);">
                <button type="button" class="btn btn-secondary font-weight-bold" style="border-radius: 20px;" data-dismiss="modal">Close & Save Template</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
// Render database schedule info to JS
var originalEventDate = '{{ date('Y-m-d', strtotime($announcement->dt_expiry_date)) }}';
var dbSchedule = {!! json_encode($announcement->email_schedule ?? []) !!};

    $('#warehouse_ids').select2({
        placeholder: 'Select affected warehouses',
        allowClear: false,
        theme: 'bootstrap4',
        width: '100%'
    });

    // Handle template dropdown selection
    $('#announcement_template').on('change', function() {
        var val = $(this).val();
        if (val === 'custom') {
            $('#v_announcement_text').val('');
            $('#announcement_text_wrapper').slideDown(200);
            $('#v_announcement_text').focus();
        } else {
            $('#v_announcement_text').val(val);
            $('#announcement_text_wrapper').slideDown(200);
        }
    });

    // Prefill and set template selector state based on existing value
    var existingText = $('#v_announcement_text').val();
    var foundTemplate = false;
    if (existingText) {
        $('#announcement_template option').each(function() {
            if ($(this).val() === existingText) {
                $('#announcement_template').val(existingText);
                foundTemplate = true;
            }
        });
        if (!foundTemplate) {
            $('#announcement_template').val('custom');
        }
        $('#announcement_text_wrapper').show();
    }

    // Form append hidden fields for modal settings
    $('#announcementForm').append('<input type="hidden" name="email_subject" id="hidden_email_subject">');
    $('#announcementForm').append('<input type="hidden" name="email_message" id="hidden_email_message">');

    // Initialize hidden values
    $('#hidden_email_subject').val($('#email_subject').val());
    $('#hidden_email_message').val($('#email_message').val());

    // Sync values from modal fields
    $('#email_subject').on('input change', function() {
        $('#hidden_email_subject').val($(this).val());
    });

    $('#email_message').on('input change', function() {
        $('#hidden_email_message').val($(this).val());
    });

    // Live Email Campaign schedule calculator with status preservation
    function updateCampaignSchedule() {
        var eventDateVal = $('#dt_expiry_date').val();
        var emailEnabled = $('#email_enabled').is(':checked');
        var container = $('#campaignSummary');
        var list = $('#summaryList');
        
        if (!emailEnabled || !eventDateVal) {
            container.slideUp(200);
            return;
        }

        var eventDate = moment(eventDateVal).startOf('day');
        var today = moment().startOf('day');
        var offsets = [31, 24, 17, 10, 3];
        var isDateChanged = (eventDateVal !== originalEventDate);
        
        list.empty();
        
        offsets.forEach(function(days) {
            var sendDate = eventDate.clone().subtract(days, 'days');
            var sendDateStr = sendDate.format('DD-MM-YYYY');
            
            var statusLabel = 'Pending';
            var statusClass = 'pill-pending';
            
            // Check if we can preserve original database status
            var preserved = false;
            if (!isDateChanged && Array.isArray(dbSchedule)) {
                for (var i = 0; i < dbSchedule.length; i++) {
                    if (dbSchedule[i].days_before == days) {
                        var dbStatus = dbSchedule[i].status || 'Pending';
                        var dbSentAt = dbSchedule[i].sent_at;
                        
                        statusLabel = dbStatus;
                        if (dbStatus === 'Sent') {
                            statusClass = 'pill-sent';
                            if (dbSentAt) {
                                statusLabel += ' (' + moment(dbSentAt).format('DD-MM-YYYY hh:mm A') + ')';
                            }
                        } else if (dbStatus === 'Skipped') {
                            statusClass = 'pill-skipped';
                        } else if (dbStatus === 'Failed') {
                            statusClass = 'pill-failed';
                        } else {
                            statusClass = 'pill-pending';
                        }
                        preserved = true;
                        break;
                    }
                }
            }
            
            if (!preserved) {
                var isPast = sendDate.isBefore(today);
                statusLabel = isPast ? 'Skipped (Date has passed)' : 'Pending (Scheduled)';
                statusClass = isPast ? 'pill-skipped' : 'pill-pending';
            }
            
            // Add a badge indicating rescheduling if event date changed
            var rescheduleNotice = '';
            if (isDateChanged) {
                rescheduleNotice = ' <span class="step-pill pill-warning ml-1"><i class="fas fa-redo mr-1"></i>Recalculated</span>';
            }
            
            var html = '<div class="summary-step-item">' +
                       '  <div>' +
                       '    <strong class="text-dark">' + days + ' Days Before Event</strong>' +
                       '    <div class="text-muted text-xs mt-1"><i class="far fa-clock mr-1"></i>Send Date: ' + sendDateStr + '</div>' +
                       '  </div>' +
                       '  <div>' +
                       '    <span class="step-pill ' + statusClass + '">' + statusLabel + '</span>' +
                       rescheduleNotice +
                       '  </div>' +
                       '</div>';
            list.append(html);
        });
        
        container.slideDown(250);
    }

    // Bind events for schedule updating
    $('#dt_expiry_date').on('change input', updateCampaignSchedule);
    $('#email_enabled').on('change', updateCampaignSchedule);

    // Initial run
    updateCampaignSchedule();

    // Sync dt_event_start_date hidden field to event date
    $('#dt_expiry_date').on('change', function() {
        $('#dt_event_start_date').val($(this).val());
        updateCampaignSchedule();
    });
    $('#email_enabled').on('change', updateCampaignSchedule);
    updateCampaignSchedule();

    // Submit validator
    $('#announcementForm').submit(function(e) {
        var expiry = $('#dt_expiry_date').val();
        if (!expiry) {
            e.preventDefault();
            alert('Please select an Event Date.');
            return false;
        }
        var warehouses = $('#warehouse_ids').val();
        if (!warehouses || warehouses.length === 0) {
            e.preventDefault();
            alert('Please select at least one warehouse.');
            return false;
        }
    });
});
</script>
@endsection
