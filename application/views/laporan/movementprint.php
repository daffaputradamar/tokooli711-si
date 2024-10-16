<link href="<?= base_url(); ?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<style>
	* {
		font-size: 12px;
	}

	@media print {
		@page {
			margin: 0;
		}

		.danger {
			color: lightcoral !important;
			-webkit-print-color-adjust: exact;
			print-color-adjust: exact;
		}

		.success {
			color: lightgreen !important;
			-webkit-print-color-adjust: exact;
			print-color-adjust: exact;
		}

		.info {
			color: darkblue !important;
			-webkit-print-color-adjust: exact;
			print-color-adjust: exact;
		}
	}
</style>
<script>
	window.print();
</script>
<center>
	<h4>Laporan Movement <br><?= $barang->nama_barang ?></h4>
</center>
<center>
	<h4> <?= $this->uri->segment(4) ?> <?= $this->uri->segment(5) ?> sampai <?= $this->uri->segment(6) ?> <?= $this->uri->segment(7) ?></h4>
</center>
<table class="table table-bordered table-hover table-striped">
	<tr>
		<th>No</th>
		<th>Kode Transaksi</th>
		<th>Jenis Transaksi</th>
		<th>Tanggal Transaksi</th>
		<th>Jumlah</th>
	</tr>
	<?php
	$no = 0;

	foreach ($listbarang as $row):
		if ($row->jenis_trans == 'Penjualan'):
	?>
			<tr>
				<td><?= ++$no ?></td>
				<td><?= $row->kode_trans ?></td>
				<td><?= $row->jenis_trans ?></td>
				<td><?= $row->tanggal_trans ?></td>
				<td class="danger"><?= number_format($row->jumlah, 2) ?></td>
			</tr>
		<?php elseif ($row->jenis_trans == 'Pembelian') : ?>
			<tr>
				<td><?= ++$no ?></td>
				<td><?= $row->kode_trans ?></td>
				<td><?= $row->jenis_trans ?></td>
				<td><?= $row->tanggal_trans ?></td>
				<td class="success"><?= number_format($row->jumlah, 2) ?></td>
			</tr>
		<?php else:
		?>
			<tr>
				<td colspan="4" class="text-center">Jumlah Kumulatif Penjualan</td>
				<td class="info"><?= number_format($row->jumlah, 2) ?></td>
			</tr>
	<?php
		endif;
	endforeach; ?>
</table>