<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diamond Inventory - HeyDiamond</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --header-bg: #f1f5f9;
            --row-hover: #f8fafc;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
        }

        /* Navbar */
        .navbar {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }

        .navbar-brand {
            font-size: 20px;
            font-weight: 700;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
        }

        .logout-btn {
            background: rgba(239, 68, 68, 0.1);
            color: #b91c1c;
            border: 1px solid rgba(239, 68, 68, 0.2);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.2);
        }

        /* Main Content */
        .container {
            padding: 32px;
            max-width: 1600px;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
        }

        .fetch-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
        }

        .fetch-btn:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        /* Card */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        /* DataTables Customization */
        table.dataTable {
            border-collapse: collapse !important;
            width: 100% !important;
            color: var(--text-main);
        }

        table.dataTable thead th {
            background: var(--header-bg);
            color: var(--text-muted);
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 16px;
            border-bottom: 1px solid var(--border-color) !important;
        }

        table.dataTable tbody td {
            padding: 16px;
            font-size: 14px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        table.dataTable tbody tr {
            background-color: var(--card-bg) !important;
            transition: background-color 0.2s;
        }

        /* Fixed Columns Background */
        table.dataTable thead th.dtfc-fixed-left {
            background-color: var(--header-bg) !important;
        }

        table.dataTable tbody td.dtfc-fixed-left {
            background-color: var(--card-bg) !important;
        }

        table.dataTable tbody tr:hover,
        table.dataTable tbody tr:hover td.dtfc-fixed-left {
            background-color: var(--row-hover) !important;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_processing,
        .dataTables_wrapper .dataTables_paginate {
            color: var(--text-muted) !important;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .dataTables_wrapper .dataTables_filter input {
            background: var(--bg-color);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 8px 12px;
            color: var(--text-main);
            margin-left: 8px;
            outline: none;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--primary);
        }

        .dataTables_wrapper .dataTables_length select {
            background: var(--bg-color);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 6px 12px;
            color: var(--text-main);
            outline: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: var(--text-main) !important;
            border: 1px solid var(--border-color) !important;
            background: var(--bg-color) !important;
            border-radius: 8px;
            margin: 0 4px;
            padding: 6px 12px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: var(--primary) !important;
            color: white !important;
            border-color: var(--primary) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--row-hover) !important;
            color: white !important;
        }

        /* Status Badge */
        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 1000;
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        .toast-icon {
            color: #059669;
            font-size: 20px;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <a href="#" class="navbar-brand">HeyDiamond</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Diamond Inventory</h1>
            <button class="fetch-btn" id="fetchDataBtn">
                <i class="fas fa-sync-alt"></i> Sync Data
            </button>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table id="diamondsTable" class="display nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>stockNo</th>
                            <th>status</th>
                            <th>shape</th>
                            <th>Diamond_Type</th>
                            <th>weight</th>
                            <th>color</th>
                            <th>clarity</th>
                            <th>cut</th>
                            <th>polish</th>
                            <th>symmetry</th>
                            <th>fluorescenceIntensity</th>
                            <th>fluorescenceColor</th>
                            <th>measurements</th>
                            <th>shade</th>
                            <th>BGM</th>
                            <th>milky</th>
                            <th>eyeClean</th>
                            <th>lab</th>
                            <th>reportNo</th>
                            <th>reportDate</th>
                            <th>location</th>
                            <th>treatment</th>
                            <th>ratio</th>
                            <th>growthType</th>
                            <th>rapPricePerCt</th>
                            <th>depth</th>
                            <th>table</th>
                            <th>girdleThin</th>
                            <th>girdleThick</th>
                            <th>gridlePer</th>
                            <th>girdleCondition</th>
                            <th>culetSize</th>
                            <th>ciletCondition</th>
                            <th>crownHeight</th>
                            <th>crownAngle</th>
                            <th>pavilionDepth</th>
                            <th>pavilionAngle</th>
                            <th>starLength</th>
                            <th>inscription</th>
                            <th>laserInscription</th>
                            <th>certiComment</th>
                            <th>keyToSymbole</th>
                            <th>inclusion</th>
                            <th>whiteInclusion</th>
                            <th>blackInclusion</th>
                            <th>openInclusion</th>
                            <th>fancyColor</th>
                            <th>fancyColorIntensity</th>
                            <th>fancyColorOvertone</th>
                            <th>country</th>
                            <th>state</th>
                            <th>city</th>
                            <th>certiFile</th>
                            <th>videoLink</th>
                            <th>vdbVideo</th>
                            <th>imageLink</th>
                            <th>heartImage</th>
                            <th>arrowImage</th>
                            <th>assetImage</th>
                            <th>heartAndArrow</th>
                            <th>luster</th>
                            <th>brand</th>
                            <th>origin</th>
                            <th>COP</th>
                            <th>DNA</th>
                            <th>handyVideo</th>
                            <th>supplierComment</th>
                            <th>tinge</th>
                            <th>memberComment</th>
                            <th>isPair</th>
                            <th>pairStock</th>
                            <th>tweezerVideo</th>
                            <th>length</th>
                            <th>height</th>
                            <th>width</th>
                            <th>pricePerCt</th>
                            <th>totalPrice</th>
                            <th>discount</th>
                            <th>exclusive</th>
                            <th>Date And Time</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="toast" id="toast">
        <i class="fas fa-check-circle toast-icon"></i>
        <span id="toastMessage">Data synced successfully!</span>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>

    <script>
        $(document).ready(function () {
            // Initialize DataTable
            const table = $('#diamondsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('diamonds.data') }}",
                pageLength: 25,
                scrollX: true,
                fixedColumns: {
                    leftColumns: 4
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'stockNo',
                        name: 'stockNo',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            return `<span class="status-badge">${data}</span>`;
                        }
                    },
                    {
                        data: 'shape',
                        name: 'shape',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'Diamond_Type',
                        name: 'Diamond_Type',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'weight',
                        name: 'weight',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'color',
                        name: 'color',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'clarity',
                        name: 'clarity',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'cut',
                        name: 'cut',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'polish',
                        name: 'polish',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'symmetry',
                        name: 'symmetry',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'fluorescenceIntensity',
                        name: 'fluorescenceIntensity',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'fluorescenceColor',
                        name: 'fluorescenceColor',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'measurements',
                        name: 'measurements',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'shade',
                        name: 'shade',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'BGM',
                        name: 'BGM',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'milky',
                        name: 'milky',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'eyeClean',
                        name: 'eyeClean',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'lab',
                        name: 'lab',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'reportNo',
                        name: 'reportNo',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'reportDate',
                        name: 'reportDate',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'location',
                        name: 'location',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'treatment',
                        name: 'treatment',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'ratio',
                        name: 'ratio',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'growthType',
                        name: 'growthType',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'rapPricePerCt',
                        name: 'rapPricePerCt',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'depth',
                        name: 'depth',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'table',
                        name: 'table',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'girdleThin',
                        name: 'girdleThin',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'girdleThick',
                        name: 'girdleThick',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'gridlePer',
                        name: 'gridlePer',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'girdleCondition',
                        name: 'girdleCondition',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'culetSize',
                        name: 'culetSize',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'ciletCondition',
                        name: 'ciletCondition',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'crownHeight',
                        name: 'crownHeight',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'crownAngle',
                        name: 'crownAngle',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'pavilionDepth',
                        name: 'pavilionDepth',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'pavilionAngle',
                        name: 'pavilionAngle',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'starLength',
                        name: 'starLength',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'inscription',
                        name: 'inscription',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'laserInscription',
                        name: 'laserInscription',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'certiComment',
                        name: 'certiComment',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'keyToSymbole',
                        name: 'keyToSymbole',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'inclusion',
                        name: 'inclusion',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'whiteInclusion',
                        name: 'whiteInclusion',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'blackInclusion',
                        name: 'blackInclusion',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'openInclusion',
                        name: 'openInclusion',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'fancyColor',
                        name: 'fancyColor',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'fancyColorIntensity',
                        name: 'fancyColorIntensity',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'fancyColorOvertone',
                        name: 'fancyColorOvertone',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'country',
                        name: 'country',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'state',
                        name: 'state',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'city',
                        name: 'city',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'certiFile',
                        name: 'certiFile',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            return `<a href="${data}" target="_blank" style="color: #007bff; text-decoration: underline;">View</a>`;
                        }
                    },
                    {
                        data: 'videoLink',
                        name: 'videoLink',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'vdbVideo',
                        name: 'vdbVideo',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'imageLink',
                        name: 'imageLink',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'heartImage',
                        name: 'heartImage',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'arrowImage',
                        name: 'arrowImage',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'assetImage',
                        name: 'assetImage',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'heartAndArrow',
                        name: 'heartAndArrow',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'luster',
                        name: 'luster',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'brand',
                        name: 'brand',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'origin',
                        name: 'origin',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'COP',
                        name: 'COP',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'DNA',
                        name: 'DNA',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'handyVideo',
                        name: 'handyVideo',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'supplierComment',
                        name: 'supplierComment',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'tinge',
                        name: 'tinge',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'memberComment',
                        name: 'memberComment',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'isPair',
                        name: 'isPair',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'pairStock',
                        name: 'pairStock',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'tweezerVideo',
                        name: 'tweezerVideo',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'length',
                        name: 'length',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'height',
                        name: 'height',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'width',
                        name: 'width',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'pricePerCt',
                        name: 'pricePerCt',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'totalPrice',
                        name: 'totalPrice',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'discount',
                        name: 'discount',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'exclusive',
                        name: 'exclusive',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';
                            if (typeof data === 'string') {
                                data = data.replace(/\$/g, '');
                                data = data.replace(/^[\+\-]\s*(?=\d)/, '');
                            }
                            return data;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function (data) {
                            if (data === null || data === undefined || data === '') return '-';

                            // Parse date
                            const dateObj = new Date(data);
                            if (isNaN(dateObj.getTime())) return data;

                            // Format Date: DD/MM/YYYY
                            const day = String(dateObj.getDate()).padStart(2, '0');
                            const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                            const year = dateObj.getFullYear();
                            const formattedDate = `${day}/${month}/${year}`;

                            // Format Time: hh:mmA
                            let hours = dateObj.getHours();
                            const minutes = String(dateObj.getMinutes()).padStart(2, '0');
                            const ampm = hours >= 12 ? 'PM' : 'AM';
                            hours = hours % 12;
                            hours = hours ? hours : 12; // the hour '0' should be '12'
                            const formattedHours = String(hours).padStart(2, '0');
                            const formattedTime = `${formattedHours}:${minutes}${ampm}`;

                            return `<div><i class="fas fa-calendar-alt" style="margin-right: 5px;"></i>${formattedDate}</div>
                                    <div style="margin-top: 4px;"><i class="fas fa-clock" style="margin-right: 5px;"></i>${formattedTime}</div>`;
                        }
                    },
                ],
                order: [[0, 'desc']],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search diamonds..."
                }
            });

            // Fetch Data Button
            $('#fetchDataBtn').on('click', function () {
                const btn = $(this);
                const originalText = btn.html();

                btn.html('<i class="fas fa-spinner fa-spin"></i> Syncing...');
                btn.prop('disabled', true);

                $.ajax({
                    url: '/fetch-diamonds',
                    type: 'GET',
                    success: function (response) {
                        if (response.success) {
                            table.ajax.reload();
                            showToast(response.message);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function (xhr) {
                        alert('An error occurred while fetching data.');
                    },
                    complete: function () {
                        btn.html(originalText);
                        btn.prop('disabled', false);
                    }
                });
            });

            function showToast(message) {
                $('#toastMessage').text(message);
                $('#toast').addClass('show');
                setTimeout(() => {
                    $('#toast').removeClass('show');
                }, 3000);
            }
        });
    </script>
</body>

</html>