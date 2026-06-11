# Codebase Context: Logistic Management System (LMS)

This document provides a comprehensive technical overview of the LMS codebase to establish a deep understanding of its architecture, data models, business logic flows, and optimization strategies.

---

## 1. Core Stack & Environment
*   **Framework:** Laravel 8.75
*   **PHP Version compatibility:** `^7.3` or `^8.0`
*   **Key Dependencies:**
    *   `phpoffice/phpspreadsheet`: Used extensively for importing and exporting logistics manifests, Amazon FBA sheets, and warehouse schedules.
    *   `razorpay/razorpay`: Payment gateway integration.
    *   `ghanuz/relations`: Custom relational utilities.
    *   `laravelcollective/html`: Form building helpers.
    *   `mpdf/mpdf`: PDF generation for shipment quotes and invoice documentation.
*   **Development OS:** Windows (Powershell environment).

---

## 2. Directory Layout & Key Files

```
LMS-LOGISTIC-MANAGEMENT-SYSTEM/
├── app/
│   ├── BaseModel.php                 # Core Eloquent wrapper with unified query helpers
│   ├── helpers.php                   # Global utility helper functions (e.g., date formats, smtp)
│   ├── User.php                      # Application User Model
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── MasterController.php  # Base controller (inherits GuestController), houses CallRaw()
│   │   │   ├── DashboardController.php # Dashboard views, JSON metrics, and filter endpoints
│   │   │   ├── ReportController.php  # Tabular and graphical reports for Goods In/Out
│   │   │   └── ...                   # Inbound/Outbound logistics controllers
│   │   └── Middleware/
│   │       └── CheckLogin.php        # Session-based authentication check
│   └── Models/                       # Additional models (e.g., State, WarehousePalletMasterModel)
├── config/
│   ├── constants.php                 # Application-wide status codes, role names, and file paths
│   └── permission_constants.php      # User role permissions
├── database/
│   └── migrations/                   # Full table schemas (2014 - 2026)
├── routes/
│   └── web.php                       # Web & AJAX routes (contains ~500 lines of routing logic)
└── [Root SQL Files]
    ├── get_goods_out_statistics_procedure.sql
    └── update_goods_out_procedure_with_location.sql
```

---

## 3. Database Schema & Logistics Domain Architecture

The database is divided into three primary functional areas:

### A. Master Metadata Tables
*   **`country_master`**: Geographic entities.
*   **`warehouse_master`**: Locations holding inventory. Can be **Own** or **Agent** warehouse types.
*   **`status_master`**: Logistics status states (e.g., *Registered*, *Collection*, *In House*, *In Transit*, *Delivered*, *Cancelled*).
*   **`company_master`**: Buyer and seller organizational profiles.
*   **`currency_master`**: Active currencies with GBP conversion exchange rates.
*   **`warehouse_pallet_master`**: Logs maximum daily pallet capacities/limits for specific warehouses to prevent overbooking.

### B. Inbound Logistics (Goods-In)
*   **`goods_in_buyer_master` & `goods_in_buyer_detail`**: Logs orders placed by buyers, containing purchase details, dimensions, and unit counts.
*   **`goods_in_logistic_master` & `goods_in_logistic_detail`**: Tracks the transportation process, tracking numbers, entry numbers, and delivery/collection scheduling.

### C. Outbound Logistics (Goods-Out)
*   **`country_to_port_goods_out_master` & `detail`**: Exports from warehouse/country origin to transit ports.
*   **`port_to_agent_goods_out_master`**: Transfers from port to agent warehouses.
*   **`agent_to_warehouse_goods_out_master`**: Transfers from agent warehouses to final destination warehouses.
*   **`country_to_port_europe_goods_out_master`**: European logistics routes.
*   **`us_warehouse_to_amazon_master` & `us_warehouse_to_amazon_details`**: Inbound shipments from US warehouse locations to Amazon FBA.
*   **`goods_out_fba_sheet_master` & `goods_out_fba_sheet_detail`**: Imported manifest worksheets mapping items to Amazon shipment IDs.
*   **`usa_container_clubbing_master`**: Groups individual cargo details into shipping containers.

---

## 4. Key Programming Design Patterns

### B. Calling Stored Procedures (`MasterController.php`)
Heavy query logic and dashboard reports use stored procedures to ensure optimal performance. In `MasterController.php`:
*   `CallRaw($procName, $parameters = [], $isExecute = false)`: Prepares a PDO statement and handles multi-rowset queries, parsing the results into standard PHP objects.
    ```php
    $recordDetails = $this->CallRaw('get_dashboard_statistics_data', [
        $recordType, $whereFromDate, $whereToDate, $whereFromCountry, $whereToWareHouse
    ]);
    ```

### C. Query Optimization Techniques
1.  **Stored Procedures**: Stored procedures like `get_goods_out_statistics_data` aggregate large datasets (counting box counts, pallet counts, value sums) directly on the DB server instead of pulling records into PHP memory.
2.  **Custom JSON-Table Left Joins**: 
    To optimize legacy `FIND_IN_SET` left-joins, a custom optimizer method can be used in `BaseModel`:
    ```php
    protected function applyCustomJoinWithJsonTable($query, $joinInfo, $customJoin) { ... }
    ```
    This method intercepts calls that use `FIND_IN_SET` inside join conditions and transforms them to use MySQL's native `JSON_TABLE` joins. This enables index-based lookups and solves the typical `O(N^2)` scanning issues of `FIND_IN_SET`.

---

## 5. Directory Variations & Active Workspaces
Two folders currently exist on the user system with distinct git profiles:
1.  **Workspace Directory:** `C:\Projects\LMS-LOGISTIC MANAGEMENT SYSTEM`
    *   **Repository:** `gitacorn/LMS-LOGISTIC-MANAGEMENT-SYSTEM.git`
    *   **Git Status:** Clean `main` branch.
2.  **Alternate Reference Directory:** `C:\Projects\Logistic-Management-System`
    *   **Repository:** `PriyalAcorn/Logistic-Management-System.git`
    *   **Git Status:** Contains modified files with query optimizations (such as `applyCustomJoinWithJsonTable` in `BaseModel.php` and changes in `DashboardController.php`).

---

## 6. Key Dashboard Endpoints (in `DashboardController.php`)
*   `index()`: The primary entry point for rendering the admin dashboard page.
*   `getStatisticsGraphFilter(Request $request)`: AJAX filter returning statistics for units, boxes, pallets, and value. Calls stored procedure `get_dashboard_statistics_data`.
*   `getDonutChartFilter(Request $request)`: Returns a JSON payload showing counts of delivery vs collection types.
*   `getGoodsOutStatisticsFilter(Request $request)`: AJAX filter return statistics for outbound shipments using stored procedure `get_goods_out_statistics_data`.
*   `topSuppliersCompanyFilter(Request $request)`: Fetches top supplier counts and renders tabular/chart reports of transaction counts and volumes.
*   `buyerDelivery(Request $request)`: Renders warehouse pallet scheduling limits across a range of calendar dates.
