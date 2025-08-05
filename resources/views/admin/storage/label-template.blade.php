<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Label Box Arsip</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 10px;
            font-size: 12px;
        }

        .label-container {
            margin-bottom: 15px;
            page-break-inside: avoid;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 200px;
            width: 100%;
        }

        .label-table {
            width: 60%;
            border-collapse: collapse;
            border: 2px solid #000;
            margin: 0 auto;
        }

        .header-row {
            background-color: #ffffff;
        }

        .header-cell {
            padding: 10px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            border-bottom: 2px solid #000;
            vertical-align: middle;
        }

        .header-title {
            font-size: 12px;
            margin: 2px 0;
            line-height: 1.2;
        }

        .content-row {
            height: 60px;
        }

        .content-cell {
            padding: 15px 8px;
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            border-right: 2px solid #000;
        }

        .content-cell:last-child {
            border-right: none;
        }

        .nomor-berkas-cell {
            width: 70%;
        }

        .no-boks-cell {
            width: 30%;
        }

        .year-text {
            margin-top: 10px;
            font-size: 11px;
        }

        .cutting-line {
            border-top: 1px dashed #999;
            margin: 20px 0;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    @foreach($paginatedLabels as $pageIndex => $pageLabels)
        @foreach($pageLabels as $labelIndex => $label)
            <div class="label-container">
                <table class="label-table">
                    <!-- Header Row - Merged -->
                    <tr class="header-row">
                        <td class="header-cell" colspan="2">
                            <div class="header-title">DINAS PENANAMAN MODAL DAN PTSP</div>
                            <div class="header-title">PROVINSI JAWA TIMUR</div>
                        </td>
                    </tr>

                    <!-- Content Row -->
                    <tr class="content-row">
                        <td class="content-cell nomor-berkas-cell">
                            <div>NOMOR BERKAS</div>
                            <br>
                            {{-- ISILAH DENGAN TAHUN, NO. ARSIP DENGAN FORMAT (TAHUN X NO. ARSIP X-X) SESUAI PADA CONTROLLER --}}
                            @foreach($label['ranges'] as $range)
                                <div>{{ $range }}</div>
                            @endforeach
                        </td>
                        <td class="content-cell no-boks-cell">
                            <div>NO. BOKS</div>
                            <br>
                            <div>{{ $label['box_number'] }}</div>
                        </td>

                    </tr>
                </table>
            </div>

            @if($labelIndex < count($pageLabels) - 1)
                <div class="cutting-line"></div>
            @endif
        @endforeach

        @if($pageIndex < count($paginatedLabels) - 1)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
