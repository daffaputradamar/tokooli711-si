<link href="<?=base_url();?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<style>
    *{
        font-size: 12px;
    }
</style>
<script>window.print();</script>
<center><h4>Laporan Pembelian <br><?=isset($listjual[0]->nama_barang)?$listjual[0]->nama_barang:""?></h4></center>
<center><h4><?= $this->uri->segment(3)?>  -  <?= $this->uri->segment(4)?></h4></center>
<table class="table table-bordered table-hover table-striped">

<tr>
		<th>No</th>
		<th>Kode Jual</th>
		<th>Tgl Transaksi</th>
		<th>Jumlah</th>
		<th>Harga Beli</th>
		<th>subtotal</th>

	</tr>
	<?php 
$totalbeli=0;
$totaljual=0;
$no=0;foreach ($listjual as $row) {
	?>
	
	
	<tr>
		<td><?= ++$no ?></td>
		<td><?= $row->kode_jual ?></td>
		<td><?= $row->tanggal_jual ?></td>
		<td><?= $row->jumlah ?></td>
		<td><?= $row->harga_beli ?></td>
		<td><?= $row->subtotal ?></td>
	</tr>
	<?php
	} ?>
</table>
