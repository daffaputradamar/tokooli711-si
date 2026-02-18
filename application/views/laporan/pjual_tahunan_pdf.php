<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Laporan Penjualan <?= $tahun ?></title>
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}
		
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
		
		.report-header h1 {
			font-size: 18px;
			font-weight: bold;
			color: #323232;
			margin-bottom: 5px;
		}
		
		.report-header p {
			font-size: 12px;
			color: #666;
		}
		
		.report-table {
			width: 100%;
			border-collapse: collapse;
			margin: 20px 0;
		}
		
		.report-table thead {
			background-color: #323232;
			color: white;
		}
		
		.report-table th {
			padding: 12px 8px;
			border: 1px solid #000;
			text-align: center;
			font-weight: bold;
			font-size: 10px;
		}
		
		.report-table td {
			padding: 10px 8px;
			border: 1px solid #000;
			font-size: 11px;
		}
		
		.report-table tbody tr:nth-child(even) {
			background-color: #f5f5f5;
		}
		
		.report-table tbody tr:nth-child(odd) {
			background-color: white;
		}
		
		.report-table .text-right {
			text-align: right;
		}
		
		.report-table .total-row {
			background-color: #e8e8e8;
			font-weight: bold;
		}
		
		.report-footer {
			margin-top: 30px;
			padding-top: 20px;
			border-top: 1px solid #ddd;
			font-size: 10px;
			color: #666;
			text-align: right;
		}
		
		.button-container {
			text-align: center;
			margin-bottom: 20px;
			no-print: true;
		}
		
		.btn {
			padding: 10px 20px;
			margin: 5px;
			border: none;
			border-radius: 4px;
			cursor: pointer;
			font-size: 14px;
		}
		
		.btn-primary {
			background-color: #323232;
			color: white;
		}
		
		.btn-primary:hover {
			background-color: #1a1a1a;
		}
		
		@media print {
			body {
				background-color: white;
				padding: 0;
			}
			
			#printContent {
				width: 100%;
				padding: 0;
				box-shadow: none;
				min-height: auto;
			}
			
			.button-container {
				display: none;
			}
		}
	</style>
</head>
<body>
	<div class="button-container">
		<button class="btn btn-primary" onclick="window.print()">🖨️ Print / Save as PDF</button>
		<button class="btn btn-primary" onclick="window.history.back()">← Kembali</button>
	</div>

	<div id="printContent">
		<div class="report-header">
			<h1>LAPORAN PENJUALAN TAHUNAN <?= $tahun ?></h1>
			<p>Data Penjualan per Periode</p>
		</div>

		<table class="report-table">
			<thead>
				<tr>
					<th>Periode</th>
					<th class="text-right">Total Penjualan</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$total = 0;
				foreach ($data_report as $row):
					$total += $row->total_penjualan;
				?>
					<tr>
						<td><?= $row->periode ?></td>
						<td class="text-right"><?= number_format($row->total_penjualan, 0, ',', '.') ?></td>
					</tr>
				<?php endforeach; ?>
				<tr class="total-row">
					<td>TOTAL</td>
					<td class="text-right"><?= number_format($total, 0, ',', '.') ?></td>
				</tr>
			</tbody>
		</table>

		<div class="report-footer">
			Dicetak pada: <?= date('d-m-Y H:i:s') ?>
		</div>
	</div>
</body>
</html>
