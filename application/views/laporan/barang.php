<?php
$cari = isset($_GET['cari']) ? $_GET['cari'] : '';
$total_beli = 0;
$total_jual = 0;
$in_stock = 0;
foreach ($listbarang as $row) {
	// Same totals as the printed report (laporan_barangprint), so screen and paper agree
	$total_beli += $row->harga_beli * $row->stok;
	$total_jual += $row->harga_jual * $row->stok;
	if ($row->stok > 0) {
		$in_stock++;
	}
}
$count = count($listbarang);
$print_url = site_url('laporan/laporan_barangprint') . ($cari !== '' ? '?cari=' . urlencode($cari) : '');
?>
<div class="rpt-head">
	<div>
		<div class="rpt-crumb"><a href="<?= site_url('laporan') ?>">Laporan</a> / Barang</div>
		<h1 class="rpt-title">Laporan Barang</h1>
		<p class="rpt-sub">Nilai stok saat ini, harga, dan potensi keuntungan<?= $cari !== '' ? ' &middot; hasil pencarian &ldquo;' . htmlspecialchars($cari) . '&rdquo;' : '' ?>.</p>
	</div>
	<div class="rpt-actions">
		<?php if ($cari !== '') { ?>
		<a class="btn btn-default" href="<?= site_url('laporan/laporan_barang') ?>"><i class="fa fa-times"></i> Hapus pencarian</a>
		<?php } ?>
		<a class="btn btn-primary" href="<?= $print_url ?>" target="_blank"><i class="fa fa-print"></i> Cetak</a>
	</div>
</div>

<div class="rpt-kpis">
	<div class="rpt-kpi is-hero">
		<div class="rpt-kpi-label">Nilai aset (harga beli)</div>
		<div class="rpt-kpi-value"><?= lap_rp($total_beli) ?></div>
		<div class="rpt-kpi-hint">Stok &times; harga beli</div>
	</div>
	<div class="rpt-kpi">
		<div class="rpt-kpi-label">Nilai jika terjual</div>
		<div class="rpt-kpi-value"><?= lap_rp($total_jual) ?></div>
		<div class="rpt-kpi-hint">Stok &times; harga jual</div>
	</div>
	<div class="rpt-kpi">
		<div class="rpt-kpi-label">Potensi keuntungan</div>
		<div class="rpt-kpi-value"><?= lap_rp($total_jual - $total_beli) ?></div>
		<div class="rpt-kpi-hint"><?= $total_beli > 0 ? number_format(($total_jual - $total_beli) / $total_beli * 100, 1, ',', '.') . '% dari nilai aset' : '&nbsp;' ?></div>
	</div>
	<div class="rpt-kpi">
		<div class="rpt-kpi-label">Barang</div>
		<div class="rpt-kpi-value"><?= number_format($count, 0, ',', '.') ?></div>
		<div class="rpt-kpi-hint"><?= number_format($in_stock, 0, ',', '.') ?> ada stok &middot; <?= number_format($count - $in_stock, 0, ',', '.') ?> habis</div>
	</div>
</div>

<section class="rpt-card">
	<div class="rpt-toolbar">
		<form class="rpt-search" action="<?= site_url('laporan/laporan_barang') ?>" method="get" role="search">
			<i class="fa fa-search"></i>
			<input type="search" class="form-control" name="cari" value="<?= htmlspecialchars($cari) ?>"
				placeholder="Cari nama, merk, atau kode…" aria-label="Cari barang"
				data-filter-rows="#barangRows" data-filter-count="#barangCount">
		</form>
		<div class="rpt-seg" role="group" aria-label="Filter stok">
			<label><input type="radio" name="scope" checked data-row-scope="all" data-filter-for="#barangRows" class="is-active"><span>Semua</span></label>
			<label><input type="radio" name="scope" data-row-scope="in" data-filter-for="#barangRows"><span>Ada stok</span></label>
			<label><input type="radio" name="scope" data-row-scope="out" data-filter-for="#barangRows"><span>Habis</span></label>
		</div>
		<span class="rpt-count" id="barangCount"></span>
	</div>

	<?php if (!$count) { ?>
	<div class="rpt-empty"><i class="fa fa-inbox"></i>Tidak ada barang<?= $cari !== '' ? ' yang cocok dengan &ldquo;' . htmlspecialchars($cari) . '&rdquo;' : '' ?>.</div>
	<?php } else { ?>
	<div class="rpt-table-wrap rpt-cards">
		<table class="rpt-table">
			<thead>
				<tr>
					<th>Barang</th>
					<th class="num">Harga beli</th>
					<th class="num">Harga jual</th>
					<th class="num">Margin</th>
					<th class="num">Stok</th>
					<th class="num">Nilai stok</th>
				</tr>
			</thead>
			<tbody id="barangRows" data-page-size="100" data-noun="barang">
				<?php foreach ($listbarang as $row) {
					$margin = $row->harga_jual - $row->harga_beli;
					$pct = $row->harga_beli > 0 ? $margin / $row->harga_beli * 100 : 0;
					$stok = (float) $row->stok;
					$stock_class = $stok <= 0 ? 'is-out' : ($stok <= 3 ? 'is-warn' : 'is-ok');
					$search = strtolower($row->nama_barang . ' ' . $row->merk . ' ' . $row->kode_barang);
				?>
				<tr data-search="<?= htmlspecialchars($search) ?>" data-scope="<?= $stok > 0 ? 'in' : 'out' ?>">
					<td class="rpt-cell-title">
						<?= htmlspecialchars(trim($row->nama_barang)) ?>
						<div class="rpt-line-sub"><?= htmlspecialchars($row->merk) ?> &middot; <?= htmlspecialchars($row->kode_barang) ?></div>
					</td>
					<td class="num" data-label="Beli"><?= lap_rp($row->harga_beli) ?></td>
					<td class="num" data-label="Jual"><?= lap_rp($row->harga_jual) ?></td>
					<td class="num <?= $margin < 0 ? 'rpt-neg' : '' ?>" data-label="Margin">
						<?= lap_rp($margin) ?>
						<div class="rpt-line-sub"><?= number_format($pct, 1, ',', '.') ?>%</div>
					</td>
					<td class="num rpt-cell-hero" data-label="Stok"><span class="rpt-badge <?= $stock_class ?>"><?= lap_num($stok) ?></span></td>
					<td class="num rpt-cell-hide-sm" data-label="Nilai stok"><?= $stok != 0 ? lap_rp($stok * $row->harga_beli) : '<span class="rpt-muted">&ndash;</span>' ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="rpt-empty rpt-hidden" data-filter-empty="#barangRows"><i class="fa fa-search"></i>Tidak ada barang yang cocok.</div>
	<button type="button" class="btn btn-default rpt-more rpt-hidden" data-show-more="#barangRows"><i class="fa fa-angle-down"></i> <span></span></button>
	<?php } ?>
</section>
