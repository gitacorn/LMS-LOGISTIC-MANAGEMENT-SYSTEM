@php
    $status = isset($recordType) ? $recordType : config('constants.IN_TRANSIT_STATUS');
@endphp

<div class="statistics-wrapper col-12">

    <div class="row">
        <div class="col-6 d-flex">
            <div class="statistics-item w-100">
                <h5 class="statistics-title statistics-title-units">
                    @if($status == config('constants.DELIVERED_STATUS'))
                        {{ trans("messages.total-delivered-units") }}
                    @else
                        {{ trans("messages.total-in-transit-units") }}
                    @endif
                </h5>
                <h6 class="statistics-count">
                    {{ empty($goodsOutStatistics['totalUnits']) ? '-' : decimalAmount($goodsOutStatistics['totalUnits']) }}
                </h6>
            </div>
        </div>

        <div class="col-6 d-flex">
            <div class="statistics-item w-100">
                <h5 class="statistics-title statistics-title-boxes">
                    @if($status == config('constants.DELIVERED_STATUS'))
                        {{ trans("messages.total-delivered-boxes") }}
                    @else
                        {{ trans("messages.total-in-transit-boxes") }}
                    @endif
                </h5>
                <h6 class="statistics-count">
                    {{ empty($goodsOutStatistics['totalBoxes']) ? '-' : decimalAmount($goodsOutStatistics['totalBoxes']) }}
                </h6>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6 d-flex">
            <div class="statistics-item w-100">
                <h5 class="statistics-title statistics-title-pallets">
                    @if($status == config('constants.DELIVERED_STATUS'))
                        {{ trans("messages.total-delivered-pallets") }}
                    @else
                        {{ trans("messages.total-in-transit-pallets") }}
                    @endif
                </h5>
                <h6 class="statistics-count">
                   {{ empty($goodsOutStatistics['totalPallets']) ? '-' : decimalAmount($goodsOutStatistics['totalPallets']) }}
                </h6>
            </div>
        </div>

        <div class="col-6 d-flex">
            <div class="statistics-item totle w-100">
                <h5 class="statistics-title statistics-title-shipment-value">
                    @if($status == config('constants.DELIVERED_STATUS'))
                        {{ trans("messages.total-delivered-shipment-value") }}
                    @else
                        {{ trans("messages.total-in-transit-shipment-value") }}
                    @endif
                </h5>
                <h6 class="statistics-count">
                    {{ empty($goodsOutStatistics['shipmentValue']) ? '-' : '£' . decimalAmount($goodsOutStatistics['shipmentValue']) }}
                </h6>
            </div>
        </div>
    </div>

</div>

<style>
/* =========================================
   STATISTICS ALIGNMENT FIX
   ========================================= */

.statistics-wrapper .row {
    margin-left: -5px;
    margin-right: -5px;
}

.statistics-wrapper .col-6 {
    padding-left: 5px;
    padding-right: 5px;
    margin-bottom: 15px;
}

.statistics-item {
    min-height: 120px;              /* Equal height for all 4 */
    padding: 12px 10px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    word-break: normal !important; /* Fix arbitrary word breaking */
    overflow-wrap: break-word;
}

.statistics-title {
    margin-bottom: 8px;
    font-size: 13px;
    line-height: 1.2;
}

.statistics-count {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    word-break: break-word !important;
}
</style>
