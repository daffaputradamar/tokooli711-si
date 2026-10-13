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

// Only the purchases in this report need their lines
$in_report = array();
foreach ($listbeli as $row) {
	$in_report[$row->kode_beli] = TRUE;
}
$lines_by_tx = array();
foreach ($listdetail as $d) {
	if (isset($in_report[$d->kode_beli])) {
		$lines_by_tx[$d->kode_beli][] = $d;
	}
}
$tx_count = count($listbeli);
?>
<div class="rpt-head">
	<div>
		<div class="rpt-crumb"><a href="<?= site_url('laporan') ?>">Laporan</a> / <a href="<?= site_url('laporan/beli') ?>">Pembelian</a></div>
		<h1 class="rpt-title">Pembelian <?= $monthly ? 'bulan ' : '' ?><?= $period_label ?></h1>
		<p class="rpt-sub"><?= $monthly ? 'Laporan bulanan' : 'Laporan harian' ?> &middot; <?= number_format($tx_count, 0, ',', '.') ?> pembelian</p>
	</div>
	<div class="rpt-actions">
		<div class="rpt-stepper">
			<a class="btn btn-default" href="<?= site_url('laporan/pbeli/' . $prev . '/' . $rad) ?>" title="<?= $monthly ? 'Bulan' : 'Hari' ?> sebelumnya" aria-label="Periode sebelumnya"><i class="fa fa-chevron-left"></i></a>
			<?php if ($next) { ?>
			<a class="btn btn-default" href="<?= site_url('laporan/pbeli/' . $next . '/' . $rad) ?>" title="<?= $monthly ? 'Bulan' : 'Hari' ?> berikutnya" aria-label="Periode berikutnya"><i class="fa fa-chevron-right"></i></a>
			<?php } else { ?>
			<span class="btn btn-default is-disabled" aria-disabled="true"><i class="fa fa-chevron-right"></i></span>
			<?php } ?>
		</div>
		<a class="btn btn-default" href="<?= site_url('laporan/beli') ?>"><i class="fa fa-sliders"></i> Ganti periode</a>
		<a class="btn btn-primary" href="<?= site_url('laporan/pbeliprint/' . $mulai . '/' . $rad) ?>" target="_blank"><i class="fa fa-print"></i> Cetak</a>
	</div>
</div>

<div class="rpt-kpis">
	<div class="rpt-kpi is-hero">
		<div class="rpt-kpi-label">Total pembelian</div>
		<div class="rpt-kpi-value"><?= lap_rp($hbeli) ?></div>
		<div class="rpt-kpi-hint"><?= $period_label ?></div>
	</div>
	<div class="rpt-kpi">
		<div class="rpt-kpi-label">Jumlah pembelian</div>
		<div class="rpt-kpi-value"><?= number_format($tx_count, 0, ',', '.') ?></div>
		<div class="rpt-kpi-hint">faktur</div>
	</div>
	<div class="rpt-kpi">
		<div class="rpt-kpi-label">Rata-rata per pembelian</div>
		<div class="rpt-kpi-value"><?= lap_rp($tx_count ? $hbeli / $tx_count : 0) ?></div>
	</div>
	<div class="rpt-kpi">
		<div class="rpt-kpi-label">Baris barang</div>
		<div class="rpt-kpi-value"><?= number_format(array_sum(array_map('count', $lines_by_tx)), 0, ',', '.') ?></div>
		<div class="rpt-kpi-hint">item dibeli</div>
	</div>
</div>

<section class="rpt-card">
	<?php if (!$tx_count) { ?>
	<div class="rpt-empty">
		<i class="fa fa-inbox"></i>
		Tidak ada pembelian pada <?= $period_label ?>.
	</div>
	<?php } else { ?>
	<div class="rpt-toolbar">
		<div class="rpt-search">
			<i class="fa fa-search"></i>
			<input type="search" class="form-control" placeholder="Cari kode, no. faktur, atau barang…"
				data-filter-rows="#txList" data-filter-count="#txCount" aria-label="Cari pembelian">
		</div>
		<button type="button" class="btn btn-default" data-expand-all="#txList" aria-expanded="false">
			<i class="fa fa-arrows-v"></i> <span>Buka semua</span>
		</button>
		<span class="rpt-count" id="txCount"></span>
	</div>

	<div id="txList" data-page-size="50" data-noun="pembelian">
		<?php
		$no = 0;
		foreach ($listbeli as $row) {
			$lines = isset($lines_by_tx[$row->kode_beli]) ? $lines_by_tx[$row->kode_beli] : array();
			$faktur = trim($row->no_faktur);
			$jam = $row->waktu_beli ? date('H:i', strtotime($row->waktu_beli)) : '';
			$search = array($row->kode_beli, $faktur);
			foreach ($lines as $line) {
				$search[] = $line->nama_barang;
			}
			$meta = array();
			if ($monthly) {
				$meta[] = date('j', strtotime($row->tanggal_beli)) . ' ' . substr(lap_bulan(date('n', strtotime($row->tanggal_beli))), 0, 3) . ($jam ? ', ' . $jam : '');
			}
			elseif ($jam) {
				$meta[] = $jam;
			}
			if ($faktur !== '') {
				$meta[] = 'Faktur ' . $faktur;
			}
			$meta[] = count($lines) . ' barang';
		?>
		<details class="rpt-tx" data-search="<?= htmlspecialchars(strtolower(implode(' ', $search))) ?>">
			<summary>
				<span class="rpt-tx-no"><?= ++$no ?></span>
				<span>
					<span class="rpt-tx-code"><?= htmlspecialchars($row->kode_beli) ?></span>
					<span class="rpt-tx-meta"><?= htmlspecialchars(implode(' · ', $meta)) ?></span>
				</span>
				<span class="rpt-tx-extra"></span>
				<span class="rpt-tx-total"><?= lap_rp($row->total) ?></span>
				<i class="fa fa-chevron-right rpt-tx-caret" aria-hidden="true"></i>
			</summary>
			<div class="rpt-tx-body">
				<?php if (empty($lines)) { ?>
				<p class="rpt-muted">Tidak ada rincian barang.</p>
				<?php } else { ?>
				<ul class="rpt-lines">
					<?php foreach ($lines as $line) { ?>
					<li class="rpt-line">
						<span class="rpt-line-name"><?= htmlspecialchars($line->nama_barang) ?></span>
						<span class="rpt-line-total"><?= lap_rp($line->subtotal) ?></span>
						<span class="rpt-line-sub"><?= lap_num($line->jumlah) ?> &times; <?= lap_rp($line->harga_beli) ?></span>
					</li>
					<?php } ?>
				</ul>
				<?php } ?>
			</div>
		</details>
		<?php } ?>
	</div>
	<div class="rpt-empty rpt-hidden" data-filter-empty="#txList"><i class="fa fa-search"></i>Tidak ada pembelian yang cocok.</div>
	<button type="button" class="btn btn-default rpt-more rpt-hidden" data-show-more="#txList"><i class="fa fa-angle-down"></i> <span></span></button>
	<?php } ?>
</section>
