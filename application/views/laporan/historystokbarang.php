<?php
$tgl_awal = $tgl_awal !== '' ? $tgl_awal : date('Y-m-01');
$tgl_akhir = $tgl_akhir !== '' ? $tgl_akhir : date('Y-m-d');
$selected_name = '';
foreach ($barang as $komp) {
	if ($kode_barang !== '' && $kode_barang == $komp->kode_barang) {
		$selected_name = trim($komp->nama_barang);
	}
}
$back_query = $this->input->get();
unset($back_query['saved']);
$back_url = site_url('laporan/history_stok') . '?' . http_build_query($back_query);
?>
<div class="rpt-head">
	<div>
		<div class="rpt-crumb"><a href="<?= site_url('laporan') ?>">Laporan</a> / History stok</div>
		<h1 class="rpt-title">History Stok Barang</h1>
		<p class="rpt-sub">Setiap perubahan stok satu barang, terbaru di atas.</p>
	</div>
</div>

<?php if ($this->input->get('saved')) { ?>
<div class="alert alert-success"><i class="fa fa-check-circle"></i> Perubahan stok disimpan.</div>
<?php } ?>

<div class="rpt-grid">
	<section class="rpt-card">
		<div class="rpt-card-head">
			<span class="rpt-icon"><i class="fa fa-filter"></i></span>
			<div>
				<h2 class="rpt-card-title">Filter</h2>
				<p class="rpt-card-desc">Pilih barang dan rentang waktu.</p>
			</div>
		</div>
		<form action="<?= site_url('laporan/history_stok') ?>" method="get">
			<label class="rpt-field">
				<span class="rpt-label">Barang</span>
				<select name="kode_barang" class="form-control selectpicker" data-live-search="true" title="Pilih barang…" data-size="8" required>
					<?php foreach ($barang as $komp) { ?>
					<option value="<?= htmlspecialchars($komp->kode_barang) ?>"<?= $kode_barang == $komp->kode_barang ? ' selected' : '' ?>><?= htmlspecialchars(trim($komp->nama_barang)) ?> [<?= htmlspecialchars($komp->merk) ?>]</option>
					<?php } ?>
				</select>
			</label>
			<div class="rpt-chips" aria-label="Rentang cepat">
				<button type="button" class="rpt-chip" data-preset="7d">7 hari</button>
				<button type="button" class="rpt-chip" data-preset="30d">30 hari</button>
				<button type="button" class="rpt-chip" data-preset="this-month">Bulan ini</button>
				<button type="button" class="rpt-chip" data-preset="last-month">Bulan lalu</button>
			</div>
			<div class="rpt-field">
				<span class="rpt-label">Dari</span>
				<div class="rpt-dt">
					<input type="date" class="form-control" name="tgl_awal" value="<?= htmlspecialchars($tgl_awal) ?>" required aria-label="Tanggal awal">
					<input type="time" class="form-control" name="wkt_awal" value="<?= htmlspecialchars($wkt_awal) ?>" required aria-label="Jam awal">
				</div>
			</div>
			<div class="rpt-field">
				<span class="rpt-label">Sampai</span>
				<div class="rpt-dt">
					<input type="date" class="form-control" name="tgl_akhir" value="<?= htmlspecialchars($tgl_akhir) ?>" required aria-label="Tanggal akhir">
					<input type="time" class="form-control" name="wkt_akhir" value="<?= htmlspecialchars($wkt_akhir) ?>" required aria-label="Jam akhir">
				</div>
			</div>
			<div class="rpt-form-foot">
				<button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Tampilkan</button>
			</div>
		</form>
	</section>

	<section class="rpt-card">
		<?php if (!$has_filter) { ?>
		<div class="rpt-empty">
			<i class="fa fa-history"></i>
			Pilih barang dan rentang waktu untuk melihat riwayat stoknya.
		</div>
		<?php } elseif (empty($history)) { ?>
		<div class="rpt-empty">
			<i class="fa fa-inbox"></i>
			Tidak ada perubahan stok <?= htmlspecialchars($selected_name) ?> pada rentang ini.
		</div>
		<?php } else { ?>
		<div class="rpt-card-head">
			<span class="rpt-icon is-ok"><i class="fa fa-cube"></i></span>
			<div>
				<h2 class="rpt-card-title"><?= htmlspecialchars($selected_name) ?></h2>
				<p class="rpt-card-desc">
					<?= number_format($total_rows, 0, ',', '.') ?> perubahan &middot;
					<?= lap_tanggal($tgl_awal) ?> &ndash; <?= lap_tanggal($tgl_akhir) ?>
				</p>
			</div>
		</div>
		<div class="rpt-table-wrap rpt-cards">
			<table class="rpt-table">
				<thead>
					<tr><th>#</th><th>Waktu perubahan</th><th class="num">Stok</th><th></th></tr>
				</thead>
				<tbody>
					<?php foreach ($history as $hist) {
						$ts = strtotime($hist->createddate);
					?>
					<tr>
						<td class="muted rpt-cell-hide-sm"><?= ++$start ?></td>
						<td class="rpt-cell-title">
							<?= lap_tanggal($hist->createddate) ?>
							<div class="rpt-line-sub"><?= date('H:i:s', $ts) ?></div>
						</td>
						<td class="num rpt-cell-hero" data-label="Stok"><?= lap_num($hist->stok) ?></td>
						<td class="rpt-cell-actions">
							<a class="btn btn-default btn-sm" href="<?= site_url('laporan/history_stok/' . $hist->id . '/update') ?>?back=<?= urlencode($back_url) ?>">
								<i class="fa fa-pencil"></i> Ubah
							</a>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<?php if ($pagination) { ?>
		<div class="rpt-pager"><?= $pagination ?></div>
		<?php } ?>
		<?php } ?>
	</section>
</div>
