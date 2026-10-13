<?php
$mulai = $this->uri->segment(3);
$monthly = ($this->uri->segment(4) == 1);
$rad = $monthly ? 1 : 0;
$ts = strtotime($mulai);

// Previous / next period, never stepping past today
if ($monthly) {
	$first = strtotime(date('Y-m-01', $ts));
	$prev = date('d-m-Y', strtotime('-1 month', $first));
	$next_ts = strtotime('+1 month', $first);
}
else {
	$prev = date('d-m-Y', strtotime('-1 day', $ts));
	$next_ts = strtotime('+1 day', $ts);
}
$next = ($next_ts <= time()) ? date('d-m-Y', $next_ts) : '';
$period_label = lap_tanggal($mulai, $monthly);

$lines_by_tx = array();
foreach ($listdetail as $d) {
	$lines_by_tx[$d->kode_jual][] = $d;
}
$tx_count = count($listjual);
?>
<div class="rpt-head">
	<div>
		<div class="rpt-crumb"><a href="<?= site_url('laporan') ?>">Laporan</a> / <a href="<?= site_url('laporan/jual') ?>">Penjualan</a></div>
		<h1 class="rpt-title">Penjualan <?= $monthly ? 'bulan ' : '' ?><?= $period_label ?></h1>
		<p class="rpt-sub"><?= $monthly ? 'Laporan bulanan' : 'Laporan harian' ?> &middot; <?= number_format($tx_count, 0, ',', '.') ?> transaksi</p>
	</div>
	<div class="rpt-actions">
		<div class="rpt-stepper">
			<a class="btn btn-default" href="<?= site_url('laporan/pjual/' . $prev . '/' . $rad) ?>" title="<?= $monthly ? 'Bulan' : 'Hari' ?> sebelumnya" aria-label="Periode sebelumnya"><i class="fa fa-chevron-left"></i></a>
			<?php if ($next) { ?>
			<a class="btn btn-default" href="<?= site_url('laporan/pjual/' . $next . '/' . $rad) ?>" title="<?= $monthly ? 'Bulan' : 'Hari' ?> berikutnya" aria-label="Periode berikutnya"><i class="fa fa-chevron-right"></i></a>
			<?php } else { ?>
			<span class="btn btn-default is-disabled" aria-disabled="true"><i class="fa fa-chevron-right"></i></span>
			<?php } ?>
		</div>
		<a class="btn btn-default" href="<?= site_url('laporan/jual') ?>"><i class="fa fa-sliders"></i> Ganti periode</a>
		<a class="btn btn-primary" href="<?= site_url('laporan/pjualprint/' . $mulai . '/' . $rad) ?>" target="_blank"><i class="fa fa-print"></i> Cetak</a>
	</div>
</div>

<div class="rpt-kpis">
	<div class="rpt-kpi is-hero">
		<div class="rpt-kpi-label">Total transaksi</div>
		<div class="rpt-kpi-value"><?= lap_rp($hjual + $hgaji) ?></div>
		<div class="rpt-kpi-hint"><?= number_format($tx_count, 0, ',', '.') ?> transaksi</div>
	</div>
	<div class="rpt-kpi">
		<div class="rpt-kpi-label">Penjualan barang</div>
		<div class="rpt-kpi-value"><?= lap_rp($hjual) ?></div>
	</div>
	<div class="rpt-kpi">
		<div class="rpt-kpi-label">Bea lain-lain</div>
		<div class="rpt-kpi-value"><?= lap_rp($hgaji) ?></div>
	</div>
	<div class="rpt-kpi">
		<div class="rpt-kpi-label">Laba kotor (omset)</div>
		<div class="rpt-kpi-value"><?= lap_rp($byTgl) ?></div>
		<div class="rpt-kpi-hint">Harga jual &minus; harga beli</div>
	</div>
</div>

<section class="rpt-card">
	<?php if (!$tx_count) { ?>
	<div class="rpt-empty">
		<i class="fa fa-inbox"></i>
		Tidak ada penjualan pada <?= $period_label ?>.
	</div>
	<?php } else { ?>
	<div class="rpt-toolbar">
		<div class="rpt-search">
			<i class="fa fa-search"></i>
			<input type="search" class="form-control" placeholder="Cari kode, pelanggan, nopol, atau barang…"
				data-filter-rows="#txList" data-filter-count="#txCount" aria-label="Cari transaksi">
		</div>
		<button type="button" class="btn btn-default" data-expand-all="#txList" aria-expanded="false">
			<i class="fa fa-arrows-v"></i> <span>Buka semua</span>
		</button>
		<span class="rpt-count" id="txCount"></span>
	</div>

	<div id="txList" data-page-size="50" data-noun="transaksi">
		<?php
		$no = 0;
		foreach ($listjual as $row) {
			$lines = isset($lines_by_tx[$row->kode_jual]) ? $lines_by_tx[$row->kode_jual] : array();
			$pelanggan = trim($row->pelanggan);
			$nopol = trim($row->nomor_polisi);
			$jam = $row->waktu_jual ? date('H:i', strtotime($row->waktu_jual)) : '';
			$search = array($row->kode_jual, $pelanggan, $nopol);
			foreach ($lines as $line) {
				$search[] = $line->nama_barang;
			}
			$meta = array();
			if (!$monthly) {
				if ($jam) {
					$meta[] = $jam;
				}
			}
			else {
				$meta[] = date('j', strtotime($row->tanggal_jual)) . ' ' . substr(lap_bulan(date('n', strtotime($row->tanggal_jual))), 0, 3) . ($jam ? ', ' . $jam : '');
			}
			if ($pelanggan !== '') {
				$meta[] = $pelanggan;
			}
			if ($nopol !== '') {
				$meta[] = $nopol;
			}
			$meta[] = count($lines) . ' barang';
		?>
		<details class="rpt-tx" data-search="<?= htmlspecialchars(strtolower(implode(' ', $search))) ?>">
			<summary>
				<span class="rpt-tx-no"><?= ++$no ?></span>
				<span>
					<span class="rpt-tx-code"><?= htmlspecialchars($row->kode_jual) ?></span>
					<span class="rpt-tx-meta"><?= htmlspecialchars(implode(' · ', $meta)) ?></span>
				</span>
				<span class="rpt-tx-extra"><?= $row->ongkos_karyawan > 0 ? 'Bea ' . lap_rp($row->ongkos_karyawan) : '' ?></span>
				<span class="rpt-tx-total"><?= lap_rp($row->total) ?></span>
				<i class="fa fa-chevron-right rpt-tx-caret" aria-hidden="true"></i>
			</summary>
			<div class="rpt-tx-body">
				<ul class="rpt-lines">
					<?php foreach ($lines as $line) { ?>
					<li class="rpt-line">
						<span class="rpt-line-name"><?= htmlspecialchars($line->nama_barang) ?></span>
						<span class="rpt-line-total"><?= lap_rp($line->subtotal) ?></span>
						<span class="rpt-line-sub"><?= lap_num($line->jumlah) ?> &times; <?= lap_rp($line->harga_jual) ?> &middot; beli <?= lap_rp($line->harga_beli) ?></span>
					</li>
					<?php } ?>
					<?php if ($row->ongkos_karyawan > 0) { ?>
					<li class="rpt-line">
						<span class="rpt-line-name">Bea servis / lain-lain</span>
						<span class="rpt-line-total"><?= lap_rp($row->ongkos_karyawan) ?></span>
						<span class="rpt-line-sub"><?= htmlspecialchars(trim($row->keterangan)) ?></span>
					</li>
					<?php } ?>
				</ul>
			</div>
		</details>
		<?php } ?>
	</div>
	<div class="rpt-empty rpt-hidden" data-filter-empty="#txList"><i class="fa fa-search"></i>Tidak ada transaksi yang cocok.</div>
	<button type="button" class="btn btn-default rpt-more rpt-hidden" data-show-more="#txList"><i class="fa fa-angle-down"></i> <span></span></button>
	<?php } ?>
</section>
