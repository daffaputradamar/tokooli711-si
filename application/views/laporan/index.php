<?php
$bulan_ini = lap_tanggal($today, TRUE);
$reports = array(
	array('jual', 'fa-line-chart', '', 'Laporan Penjualan', 'Harian, bulanan, per barang, dan export tahunan.'),
	array('beli', 'fa-truck', 'is-info', 'Laporan Pembelian', 'Pembelian harian, bulanan, per barang, dan per supplier.'),
	array('laporan_barang', 'fa-cubes', 'is-ok', 'Laporan Barang', 'Nilai stok, harga, dan potensi keuntungan semua barang.'),
	array('movement', 'fa-exchange', 'is-warn', 'Movement Barang', 'Keluar-masuk satu barang dalam rentang waktu.'),
	array('history_stok', 'fa-history', '', 'History Stok Barang', 'Riwayat perubahan stok satu barang.'),
	array('stok_cutoff', 'fa-calendar-check-o', 'is-info', 'Cut-off Stok Bulanan', 'Simpan snapshot stok akhir bulan dan export.')
);
?>
<div class="rpt-head">
	<div>
		<h1 class="rpt-title">Laporan</h1>
		<p class="rpt-sub">Ringkasan toko dan semua laporan di satu tempat.</p>
	</div>
</div>

<div class="rpt-kpis">
	<div class="rpt-kpi is-hero">
		<div class="rpt-kpi-label">Penjualan hari ini</div>
		<div class="rpt-kpi-value"><?= lap_rp($jual_hari_ini) ?></div>
		<div class="rpt-kpi-hint"><?= lap_tanggal($today) ?></div>
	</div>
	<div class="rpt-kpi">
		<div class="rpt-kpi-label">Penjualan bulan ini</div>
		<div class="rpt-kpi-value"><?= lap_rp($jual_bulan_ini) ?></div>
		<div class="rpt-kpi-hint">Termasuk bea lain-lain</div>
	</div>
	<div class="rpt-kpi">
		<div class="rpt-kpi-label">Laba kotor bulan ini</div>
		<div class="rpt-kpi-value"><?= lap_rp($laba_bulan_ini) ?></div>
		<div class="rpt-kpi-hint">Harga jual &minus; harga beli</div>
	</div>
	<div class="rpt-kpi">
		<div class="rpt-kpi-label">Pembelian bulan ini</div>
		<div class="rpt-kpi-value"><?= lap_rp($beli_bulan_ini) ?></div>
		<div class="rpt-kpi-hint"><?= $bulan_ini ?></div>
	</div>
</div>

<h2 class="rpt-section-title">Buka cepat</h2>
<div class="rpt-quick">
	<a class="btn btn-default" href="<?= site_url('laporan/pjual/' . $today . '/0') ?>"><i class="fa fa-calendar-o"></i> Penjualan hari ini</a>
	<a class="btn btn-default" href="<?= site_url('laporan/pjual/' . $today . '/1') ?>"><i class="fa fa-calendar"></i> Penjualan <?= $bulan_ini ?></a>
	<a class="btn btn-default" href="<?= site_url('laporan/pbeli/' . $today . '/1') ?>"><i class="fa fa-truck"></i> Pembelian <?= $bulan_ini ?></a>
	<a class="btn btn-default" href="<?= site_url('laporan/laporan_barang') ?>"><i class="fa fa-cubes"></i> Nilai stok</a>
</div>

<h2 class="rpt-section-title">Semua laporan</h2>
<div class="rpt-links">
	<?php foreach ($reports as $r) { ?>
	<a class="rpt-link" href="<?= site_url('laporan/' . $r[0]) ?>">
		<span class="rpt-icon <?= $r[2] ?>"><i class="fa <?= $r[1] ?>"></i></span>
		<span>
			<span class="rpt-link-title"><?= $r[3] ?></span>
			<span class="rpt-link-desc"><?= $r[4] ?></span>
		</span>
	</a>
	<?php } ?>
</div>
