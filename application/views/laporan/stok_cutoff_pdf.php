<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cut-off Stok <?= htmlspecialchars($periode) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        #printContent {
            background-color: white;
            padding: 40px;
            margin: 0 auto;
            width: 210mm;
            min-height: 297mm;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .report-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #323232;
            padding-bottom: 15px;
        }

        .report-header h1 { font-size: 16px; font-weight: bold; color: #323232; margin-bottom: 4px; }
        .report-header p  { font-size: 11px; color: #666; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
            font-size: 10px;
        }

        thead { background-color: #323232; color: white; }

        th, td {
            padding: 7px 6px;
            border: 1px solid #000;
        }

        th { text-align: center; font-weight: bold; }

        tbody tr:nth-child(even)  { background-color: #f5f5f5; }
        tbody tr:nth-child(odd)   { background-color: white; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .total-row { background-color: #e8e8e8 !important; font-weight: bold; }

        .report-footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: right;
        }

        .button-container { text-align: center; margin-bottom: 20px; }

        .btn {
            padding: 8px 18px;
            margin: 4px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            background-color: #323232;
            color: white;
        }

        @media print {
            body { background-color: white; padding: 0; }
            #printContent { width: 100%; padding: 10mm; box-shadow: none; min-height: auto; }
            .button-container { display: none; }
        }
    </style>
</head>
<body>
    <div class="button-container">
        <button class="btn" onclick="window.print()">&#128438; Print / Save as PDF</button>
        <button class="btn" onclick="window.history.back()">&#8592; Kembali</button>
    </div>

    <div id="printContent">
        <div class="report-header">
            <h1>LAPORAN CUT-OFF STOK BULANAN</h1>
            <h1>Periode : <?= htmlspecialchars($periode) ?></h1>
            <p>Snapshot stok seluruh barang pada akhir periode</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Merk</th>
                    <th class="text-right">Stok</th>
                    <th class="text-right">Harga Beli</th>
                    <th class="text-right">Harga Jual</th>
                    <th class="text-right">Nilai Stok (Beli)</th>
                    <th class="text-right">Nilai Stok (Jual)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no           = 1;
                $total_beli   = 0;
                $total_jual   = 0;
                foreach ($rows as $row):
                    $total_beli += $row->nilai_stok_beli;
                    $total_jual += $row->nilai_stok_jual;
                ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row->kode_barang) ?></td>
                    <td><?= htmlspecialchars($row->nama_barang) ?></td>
                    <td><?= htmlspecialchars($row->merk) ?></td>
                    <td class="text-right"><?= number_format($row->stok, 0, ',', '.') ?></td>
                    <td class="text-right"><?= number_format($row->harga_beli, 0, ',', '.') ?></td>
                    <td class="text-right"><?= number_format($row->harga_jual, 0, ',', '.') ?></td>
                    <td class="text-right"><?= number_format($row->nilai_stok_beli, 0, ',', '.') ?></td>
                    <td class="text-right"><?= number_format($row->nilai_stok_jual, 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="7" class="text-right">TOTAL</td>
                    <td class="text-right"><?= number_format($total_beli, 0, ',', '.') ?></td>
                    <td class="text-right"><?= number_format($total_jual, 0, ',', '.') ?></td>
                </tr>
            </tbody>
        </table>

        <div class="report-footer">
            Dicetak pada: <?= date('d-m-Y H:i:s') ?>
        </div>
    </div>
</body>
</html>
