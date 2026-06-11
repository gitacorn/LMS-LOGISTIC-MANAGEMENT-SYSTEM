@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<style>
    :root {
        --primary-light: color-mix(in srgb, var(--primary-color) 8%, white);
        --primary-gradient: linear-gradient(135deg, var(--primary-color), color-mix(in srgb, var(--primary-color) 80%, black));
        
        --success-hsl: 145, 65%, 42%;
        --success-color: hsl(var(--success-hsl));
        --success-light: hsl(145, 65%, 95%);
        --success-gradient: linear-gradient(135deg, hsl(145, 70%, 48%), hsl(155, 75%, 35%));

        --warning-hsl: 35, 85%, 52%;
        --warning-color: hsl(var(--warning-hsl));
        --warning-light: hsl(35, 85%, 95%);
        
        --danger-hsl: 355, 75%, 55%;
        --danger-color: hsl(var(--danger-hsl));
        --danger-light: hsl(355, 75%, 96%);

        --info-hsl: 195, 70%, 45%;
        --info-color: hsl(var(--info-hsl));
        --info-light: hsl(195, 70%, 95%);
        
        --muted-color: #6c757d;
        --border-radius-lg: 16px;
        --border-radius-md: 10px;
        --transition-base: all 0.3s ease;
    }

    .lms-container {
        padding: 24px;
        font-family: 'Montserrat', sans-serif;
    }

    /* Stats Section */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: var(--border-radius-lg);
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        border: 1px solid rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        transition: var(--transition-base);
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.07);
    }

    .stat-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--primary-gradient);
        opacity: 0.8;
    }

    .stat-card.success-theme::after { background: var(--success-gradient); }
    .stat-card.warning-theme::after { background: linear-gradient(135deg, #ffc107, #ff9800); }
    .stat-card.info-theme::after { background: linear-gradient(135deg, #17a2b8, #117a8b); }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-right: 16px;
        background: var(--primary-light);
        color: var(--primary-color);
    }

    .success-theme .stat-icon { background: var(--success-light); color: var(--success-color); }
    .warning-theme .stat-icon { background: var(--warning-light); color: var(--warning-color); }
    .info-theme .stat-icon { background: var(--info-light); color: var(--info-color); }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 2px;
        line-height: 1;
    }

    .stat-label {
        font-size: 12px;
        font-weight: 500;
        color: var(--muted-color);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Actions Bar */
    .actions-bar {
        background: white;
        border-radius: var(--border-radius-md);
        padding: 16px 20px;
        margin-bottom: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        border: 1px solid rgba(0,0,0,0.05);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .search-input-wrapper {
        position: relative;
        flex: 1;
        max-width: 400px;
    }

    .search-input-wrapper i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--muted-color);
    }

    .search-control {
        width: 100%;
        padding: 10px 16px 10px 40px;
        border-radius: 20px;
        border: 1px solid #ced4da;
        font-size: 14px;
        transition: var(--transition-base);
    }

    .search-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(0,99,193,0.15);
        outline: none;
    }

    /* Campaign Cards */
    .campaign-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .campaign-card {
        background: white;
        border-radius: var(--border-radius-lg);
        border: 1px solid rgba(0,0,0,0.06);
        box-shadow: 0 6px 20px rgba(0,0,0,0.02);
        padding: 24px;
        transition: var(--transition-base);
        display: flex;
        flex-direction: column;
        gap: 20px;
        position: relative;
    }

    .campaign-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        border-color: rgba(0,99,193,0.15);
    }

    .campaign-header {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        border-bottom: 1px solid #f1f3f5;
        padding-bottom: 16px;
    }

    .campaign-meta-info {
        display: flex;
        flex-direction: column;
        gap: 6px;
        flex: 1;
        min-width: 250px;
    }

    .campaign-title-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .campaign-title {
        font-size: 16px;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }

    .campaign-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 12px;
        text-transform: uppercase;
    }

    .badge-active { background: var(--success-light); color: var(--success-color); }
    .badge-upcoming { background: var(--info-light); color: var(--info-color); }
    .badge-inactive { background: #f8f9fa; color: #868e96; border: 1px solid #dee2e6; }

    .warehouse-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 4px;
    }

    .warehouse-tag {
        background: #f1f3f5;
        color: #495057;
        font-size: 11px;
        font-weight: 500;
        padding: 3px 8px;
        border-radius: 4px;
        border: 1px solid #e9ecef;
    }

    .countdown-badge-wrapper {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        text-align: right;
    }

    .countdown-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 13px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.04);
    }

    .countdown-positive {
        background: linear-gradient(135deg, #6610f2, #4b0082);
        color: white;
    }

    .countdown-today {
        background: var(--success-gradient);
        color: white;
        animation: pulse-glow 2s infinite;
    }

    .countdown-ended {
        background: #e9ecef;
        color: #6c757d;
    }

    @keyframes pulse-glow {
        0% { box-shadow: 0 0 0 0 rgba(40,167,69,0.4); }
        70% { box-shadow: 0 0 0 8px rgba(40,167,69,0); }
        100% { box-shadow: 0 0 0 0 rgba(40,167,69,0); }
    }

    .date-label {
        font-size: 11px;
        color: var(--muted-color);
        margin-top: 4px;
    }

    /* Campaign Content / Body */
    .campaign-body {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
    }

    @media(min-width: 992px) {
        .campaign-body {
            grid-template-columns: 3fr 2fr;
            align-items: center;
        }
    }

    .announcement-text-section {
        background: #f8f9fa;
        border-radius: var(--border-radius-md);
        padding: 16px;
        border-left: 4px solid var(--primary-color);
        font-size: 14px;
        color: #495057;
        line-height: 1.5;
    }

    .campaign-tracker-section {
        background: #fdfdfd;
        border-radius: var(--border-radius-md);
        padding: 16px;
        border: 1px solid #f1f3f5;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .tracker-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        font-weight: 700;
        color: #495057;
    }

    .tracker-progress-wrapper {
        height: 6px;
        background: #e9ecef;
        border-radius: 3px;
        overflow: hidden;
    }

    .tracker-progress-bar {
        height: 100%;
        background: var(--success-gradient);
        border-radius: 3px;
        transition: width 0.6s ease;
    }

    /* Stepper Schedule Timeline */
    .stepper-timeline {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin-top: 8px;
        padding-bottom: 8px;
    }

    .stepper-timeline::before {
        content: '';
        position: absolute;
        top: 10px;
        left: 0;
        width: 100%;
        height: 2px;
        background: #e9ecef;
        z-index: 1;
    }

    .step-item {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
    }

    .step-node {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: white;
        border: 2px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 9px;
        font-weight: 700;
        color: #adb5bd;
        transition: var(--transition-base);
        cursor: pointer;
    }

    .step-item.Pending .step-node {
        border-color: var(--primary-color);
        color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(0,99,193,0.1);
    }

    .step-item.Sent .step-node {
        background: var(--success-color);
        border-color: var(--success-color);
        color: white;
    }

    .step-item.Skipped .step-node {
        background: #e9ecef;
        border-color: #ced4da;
        color: #868e96;
    }

    .step-item.Failed .step-node {
        background: var(--danger-color);
        border-color: var(--danger-color);
        color: white;
    }

    .step-label {
        font-size: 10px;
        font-weight: 600;
        margin-top: 6px;
        color: #868e96;
        text-align: center;
    }

    .step-item.Sent .step-label { color: var(--success-color); font-weight: 700; }
    .step-item.Failed .step-label { color: var(--danger-color); font-weight: 700; }

    /* Action Buttons Area */
    .campaign-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        border-top: 1px solid #f1f3f5;
        padding-top: 16px;
    }

    .btn-action-outline {
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        padding: 6px 16px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: var(--transition-base);
        background: white;
    }

    .btn-action-outline-edit {
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
    }

    .btn-action-outline-edit:hover {
        background: var(--primary-color);
        color: white;
        text-decoration: none;
    }

    .btn-action-outline-danger {
        border: 1px solid var(--danger-color);
        color: var(--danger-color);
    }

    .btn-action-outline-danger:hover {
        background: var(--danger-color);
        color: white;
        border-color: var(--danger-color);
    }

    /* Custom Tooltip Styling */
    .tooltip-inner {
        font-family: 'Montserrat', sans-serif;
        font-size: 11px;
        border-radius: 6px;
        padding: 6px 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
</style>

@php
    $totalCampaigns = $announcements->count();
    $activeEvents = $announcements->where('status', 'Active')->count() + $announcements->where('status', 'Upcoming')->count();
    
    // Count sent emails and pending emails across all campaigns
    $totalSent = 0;
    $totalPending = 0;
    foreach ($announcements as $ann) {
        if ($ann->email_enabled && is_array($ann->email_schedule)) {
            foreach ($ann->email_schedule as $cp) {
                $status = $cp['status'] ?? '';
                if ($status === 'Sent') {
                    $totalSent++;
                } elseif ($status === 'Pending') {
                    $totalPending++;
                }
            }
        }
    }
@endphp

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-navabr">
        <h1 class="h3 mb-lg-0 mr-3 header-title" id="pageTitle">{{ trans('Announcements & Email Campaigns') }}</h1>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
        	@if(checkPermission(config('permission_constants.ADD_ANNOUNCEMENT')) || strtolower(session()->get('role')) == strtolower(config('constants.ROLE_ADMIN')))
                <a href="{{ url('announcement/create') }}" class="btn btn-theme text-white button-actions-top-bar d-flex align-items-center border btn-sm" title="{{ trans('Add Announcement') }}">
                    <i class="fas fa-plus mr-md-2"></i><span class="d-md-block d-none">{{ trans("Add Announcement") }}</span>
                </a>
            @endif
        </div>
    </div>

    <div class="lms-container">
        <!-- Success Alert -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px; border-left: 5px solid var(--success-color);">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <i class="fas fa-check-circle mr-2 text-success"></i> {{ session('success') }}
            </div>
            @endif

        <!-- Stats Grid Section -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-bullhorn"></i></div>
                <div>
                    <div class="stat-value">{{ $totalCampaigns }}</div>
                    <div class="stat-label">Total Campaigns</div>
                </div>
            </div>
            <div class="stat-card success-theme">
                <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                <div>
                    <div class="stat-value">{{ $activeEvents }}</div>
                    <div class="stat-label">Active / Upcoming</div>
                </div>
            </div>
            <div class="stat-card info-theme">
                <div class="stat-icon"><i class="fas fa-paper-plane"></i></div>
                <div>
                    <div class="stat-value">{{ $totalSent }}</div>
                    <div class="stat-label">Campaign Emails Sent</div>
                </div>
            </div>
            <div class="stat-card warning-theme">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div>
                    <div class="stat-value">{{ $totalPending }}</div>
                    <div class="stat-label">Pending Send</div>
                </div>
            </div>
        </div>

        <!-- Filter Actions Bar -->
        <div class="actions-bar">
            <div class="search-input-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" id="campaignSearch" class="search-control" placeholder="Search announcement text, warehouses, status...">
            </div>
            <div class="filter-count text-muted font-weight-bold" style="font-size: 13px;">
                Showing <span id="visibleCount">{{ $totalCampaigns }}</span> of {{ $totalCampaigns }} campaigns
            </div>
        </div>

        <!-- Campaigns Panels List -->
        <div class="campaign-list" id="campaignList">
                    @if($announcements->count() > 0)
                    @foreach($announcements as $announcement)
                    @php
                    // Resolve warehouses
                                                        $warehouseNames = [];
                                                        $warehouseIds = is_array($announcement->warehouse_ids) ? $announcement->warehouse_ids : json_decode($announcement->warehouse_ids, true);
                                                        if ($warehouseIds && is_array($warehouseIds)) {
                                                            foreach ($warehouseIds as $encodedId) {
                                                                $decodedId = Wild_tiger::decode($encodedId);
                                                                $warehouse = \DB::table('warehouse_master')
                                                                    ->where('i_id', $decodedId)
                                                                    ->where('e_record_type', 'Warehouse')
                                                                    ->first();
                                                                if ($warehouse) {
                                                                $warehouseNames[] = $warehouse->v_warehouse_name;
                                }
                            }
                        }
                        $warehouseStr = implode(', ', $warehouseNames);

                        // Calculate Event Countdown
                        $expiryDate = \Carbon\Carbon::parse($announcement->dt_expiry_date)->startOfDay();
                        $today = \Carbon\Carbon::now()->startOfDay();
                        $daysLeft = $today->diffInDays($expiryDate, false);

                        // Calculate email schedule progress
                        $sentCount = 0;
                        $skippedCount = 0;
                        $failedCount = 0;
                        $totalScheduleCount = 0;
                        $progressPercent = 0;
                        
                        if ($announcement->email_enabled && is_array($announcement->email_schedule)) {
                            $totalScheduleCount = count($announcement->email_schedule);
                            foreach ($announcement->email_schedule as $cp) {
                                $status = $cp['status'] ?? '';
                                if ($status === 'Sent') {
                                    $sentCount++;
                                } elseif ($status === 'Skipped') {
                                    $skippedCount++;
                                } elseif ($status === 'Failed') {
                                    $failedCount++;
                                }
                            }
                            if ($totalScheduleCount > 0) {
                                $progressPercent = round((($sentCount + $skippedCount) / $totalScheduleCount) * 100);
                            }
                        }
                    @endphp

                    <div class="campaign-card" data-search-content="{{ strtolower($announcement->v_announcement_text) }} {{ strtolower($warehouseStr) }} {{ strtolower($announcement->status) }}">
                        <!-- Header Area -->
                        <div class="campaign-header">
                            <div class="campaign-meta-info">
                                <div class="campaign-title-row">
                                    <h5 class="campaign-title">
                                        <i class="far fa-bell text-primary mr-1"></i> Event Announcement #{{ $announcement->id }}
                                    </h5>
                                    
                                    @if($announcement->status == 'Active')
                                        <span class="campaign-badge badge-active">Active</span>
                                    @elseif($announcement->status == 'Inactive')
                                        <span class="campaign-badge badge-inactive">Inactive</span>
                                    @else
                                        <span class="campaign-badge badge-upcoming">Upcoming</span>
                                    @endif
                                </div>
                                <div class="warehouse-tags">
                                    @if(!empty($warehouseNames))
                                        @foreach($warehouseNames as $name)
                                            <span class="warehouse-tag"><i class="fas fa-warehouse mr-1 text-muted"></i>{{ $name }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted text-xs">No warehouses assigned</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Countdown Indicator -->
                            <div class="countdown-badge-wrapper">
                                @if($daysLeft > 0)
                                    <div class="countdown-pill countdown-positive">
                                        <i class="far fa-hourglass mr-1"></i> {{ $daysLeft }} {{ $daysLeft == 1 ? 'Day' : 'Days' }} Left
                                    </div>
                                @elseif($daysLeft == 0)
                                    <div class="countdown-pill countdown-today">
                                        <i class="fas fa-star mr-1"></i> Happening Today!
                                    </div>
                                @else
                                    <div class="countdown-pill countdown-ended">
                                        <i class="fas fa-calendar-check mr-1"></i> Event Ended
                                    </div>
                                @endif
                                <div class="date-label">
                                    Event: <strong>{{ date('d-m-Y', strtotime($announcement->dt_expiry_date)) }}</strong> 
                                    @if(!empty($announcement->dt_event_start_date))
                                        (Marquee: {{ date('d-m-Y', strtotime($announcement->dt_event_start_date)) }})
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Card Content Body -->
                        <div class="campaign-body">
                            <!-- Left: Announcement Text -->
                            <div class="announcement-text-section">
                                <div class="font-weight-bold text-dark mb-1" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Announcement Marquee Text</div>
                                {{ $announcement->v_announcement_text }}
                            </div>

                            <!-- Right: Campaign Email Schedule -->
                            <div class="campaign-tracker-section">
                                @if($announcement->email_enabled && is_array($announcement->email_schedule))
                                    <div class="tracker-header">
                                        <span>Automated Email Progress</span>
                                        <span class="text-success font-weight-bold">{{ $progressPercent }}% Complete</span>
                                    </div>
                                    <div class="tracker-progress-wrapper">
                                        <div class="tracker-progress-bar" style="width: {{ $progressPercent }}%;"></div>
                                    </div>

                                    <!-- Step Indicators -->
                                    <div class="stepper-timeline">
                                        @foreach($announcement->email_schedule as $checkpoint)
                                            @php
                                                $daysVal = $checkpoint['days_before'];
                                                $statusVal = $checkpoint['status'] ?? 'Pending';
                                                $dateVal = $checkpoint['send_date'] ?? '';
                                                $sentAtVal = $checkpoint['sent_at'] ?? '';
                                                
                                                // Create descriptive tooltip
                                                $tooltip = "{$daysVal} Days Before Event: {$statusVal}";
                                                if ($dateVal) {
                                                    $formattedSendDate = date('d-m-Y', strtotime($dateVal));
                                                    if ($statusVal === 'Sent' && $sentAtVal) {
                                                        $formattedSentAt = date('d-m-Y h:i A', strtotime($sentAtVal));
                                                        $tooltip .= " on {$formattedSentAt}";
                                                    } else {
                                                        $tooltip .= " (Scheduled: {$formattedSendDate})";
                                                    }
                                                }
                                            @endphp
                                            <div class="step-item {{ $statusVal }}" data-toggle="tooltip" data-placement="top" title="{{ $tooltip }}">
                                                <div class="step-node">
                                                    @if($statusVal === 'Sent')
                                                        <i class="fas fa-check"></i>
                                                    @elseif($statusVal === 'Failed')
                                                        <i class="fas fa-exclamation"></i>
                                                    @elseif($statusVal === 'Skipped')
                                                        <i class="fas fa-forward"></i>
                                                    @else
                                                        {{ $daysVal }}
                                                    @endif     
                                                         
                                                </div>
                                                <div class="step-label">{{ $daysVal }}d</div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="d-flex align-items-center justify-content-center py-4 text-muted" style="font-size: 13px; font-style: italic;">
                                        <i class="far fa-envelope-open mr-2 text-secondary"></i> Email campaigns are disabled for this announcement
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Card Action buttons footer -->
                        <div class="campaign-actions">
                            @if(checkPermission(config('permission_constants.EDIT_ANNOUNCEMENT')) || strtolower(session()->get('role')) == strtolower(config('constants.ROLE_ADMIN')))
                                <a href="{{ url('announcement/'.$announcement->id.'/edit') }}" class="btn-action-outline btn-action-outline-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @endif
                                                
                                                @if(checkPermission(config('permission_constants.DELETE_ANNOUNCEMENT')) || strtolower(session()->get('role')) == strtolower(config('constants.ROLE_ADMIN')))
                                                    <form method="POST" action="{{ url('announcement/'.$announcement->id) }}" style="display: inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn-action-outline btn-action-outline-danger" onclick="return confirm('Are you sure you want to delete this event announcement?')">
                                                        <i class="fas fa-trash-alt"></i> Delete
                                                        </button>
                                                    </form>
                                                @endif
                        </div>
                        </div>
                        @endforeach
                    @else
                    <div class="text-center py-5 bg-white border rounded" style="border-radius: var(--border-radius-lg); box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                    <i class="fa fa-bullhorn fa-4x text-muted mb-3"></i>
                    <h4 class="font-weight-bold text-dark">No announcements found</h4>
                    <p class="text-muted mb-4">Create your first automated announcement to start scheduling email campaigns.</p>
                    @if(checkPermission(config('permission_constants.ADD_ANNOUNCEMENT')) || strtolower(session()->get('role')) == strtolower(config('constants.ROLE_ADMIN')))
                        <a href="{{ url('announcement/create') }}" class="btn btn-theme text-white border btn-md px-4" style="border-radius: 20px;">
                            <i class="fas fa-plus mr-2"></i> Create
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</main>

@section('scripts')
<script>
$(document).ready(function() {
    // Enable Bootstrap Tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Instant Client-Side Filtering
    $('#campaignSearch').on('input', function() {
        var query = $(this).val().toLowerCase().trim();
        var cards = $('.campaign-card');
        var visibleCount = 0;

        cards.each(function() {
            var searchData = $(this).attr('data-search-content');
            if (searchData.indexOf(query) !== -1) {
                $(this).fadeIn(200);
                visibleCount++;
            } else {
                $(this).fadeOut(150);
            }
        });

        $('#visibleCount').text(visibleCount);
    });
});
</script>
@endsection

@endsection
