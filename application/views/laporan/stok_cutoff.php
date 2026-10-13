<?php
$last_month = date('Y-m', strtotime(date('Y-m-01') . ' -1 month'));
$done = array();
foreach ($cutoff_list as $row) {
	$done[$row->periode] = TRUE;
}
$latest = empty($cutoff_list) ? NULL : $cutoff_list[0];
?>
<div class="rpt-head">
	<div>
		<div class="rpt-crumb"><a href="<?= site_url('laporan') ?>">Laporan</a> / Cut-off stok</div>
		<h1 class="rpt-title">Cut-off Stok Bulanan</h1>
		<p class="rpt-sub">Simpan snapshot stok dan nilainya di akhir bulan, lalu export kapan saja.</p>
	</div>
</div>

<?php if ($message) { ?>
<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?= $message ?></div>
<?php } ?>
<?php if ($error) { ?>
<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?= $error ?></div>
<?php } ?>

<div class="rpt-grid">
	<section class="rpt-card">
		<div class="rpt-card-head">
			<span class="rpt-icon"><i class="fa fa-scissors"></i></span>
			<div>
				<h2 class="rpt-card-title">Buat cut-off</h2>
				<p class="rpt-card-desc">Mengambil stok, harga beli, dan harga jual semua barang saat ini untuk periode yang dipilih.</p>
			</div>
		</div>
		<?= form_open('laporan/stok_cutoff'); ?>
			<label class="rpt-field">
				<span class="rpt-label">Periode</span>
				<input type="month" name="periode" class="form-control" value="<?= $last_month ?>" max="<?= date('Y-m') ?>" required>
			</label>
			<?php if (isset($done[$last_month])) { ?>
			<p class="rpt-note"><i class="fa fa-check-circle rpt-pos"></i> Cut-off <?= lap_tanggal($last_month . '-01', TRUE) ?> sudah dibuat.</p>
			<?php } else { ?>
			<p class="rpt-note"><i class="fa fa-exclamation-triangle rpt-warn-text"></i> Cut-off <?= lap_tanggal($last_month . '-01', TRUE) ?> belum dibuat.</p>
			<?php } ?>
			<div class="rpt-form-foot">
				<button type="submit" name="action" value="cutoff" class="btn btn-primary"><i class="fa fa-scissors"></i> Proses cut-off</button>
			</div>
		</form>
	</section>

	<section class="rpt-card">
		<div class="rpt-card-head">
			<span class="rpt-icon is-info"><i class="fa fa-info-circle"></i></span>
			<div>
				<h2 class="rpt-card-title">Cut-off terakhir</h2>
				<p class="rpt-card-desc">
					<?php if ($latest) { ?>
					<?= lap_tanggal($latest->periode . '-01', TRUE) ?> &middot; <?= number_format($latest->jumlah_barang, 0, ',', '.') ?> barang
					<?php } else { ?>
					Belum ada cut-off.
					<?php } ?>
				</p>
			</div>
		</div>
		<?php if ($latest) { ?>
		<ul class="rpt-lines">
			<li class="rpt-line"><span class="rpt-line-name">Nilai stok (harga beli)</span><span class="rpt-line-total"><?= lap_rp($latest->total_nilai_beli) ?></span><span class="rpt-line-sub">&nbsp;</span></li>
			<li class="rpt-line"><span class="rpt-line-name">Nilai stok (harga jual)</span><span class="rpt-line-total"><?= lap_rp($latest->total_nilai_jual) ?></span><span class="rpt-line-sub">&nbsp;</span></li>
		</ul>
		<?php } else { ?>
		<p class="rpt-note">Buat cut-off pertama di samping. Biasanya dilakukan di awal bulan untuk bulan sebelumnya.</p>
		<?php } ?>
	</section>

	<section class="rpt-card rpt-span-2">
		<div class="rpt-card-head">
			<span class="rpt-icon is-ok"><i class="fa fa-history"></i></span>
			<div>
				<h2 class="rpt-card-title">Riwayat cut-off</h2>
				<p class="rpt-card-desc">PDF terbuka di tab baru; CSV bisa dibuka di Excel.</p>
			</div>
		</div>
		<?php if (empty($cutoff_list)) { ?>
		<div class="rpt-empty"><i class="fa fa-inbox"></i>Belum ada data cut-off.</div>
		<?php } else { ?>
		<div class="rpt-table-wrap rpt-cards">
			<table class="rpt-table">
				<thead>
					<tr>
						<th>Periode</th>
						<th class="num">Barang</th>
						<th class="num">Nilai (beli)</th>
						<th class="num">Nilai (jual)</th>
						<th>Dibuat</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($cutoff_list as $row) { ?>
					<tr>
						<td class="rpt-cell-title"><?= lap_tanggal($row->periode . '-01', TRUE) ?></td>
						<td class="num rpt-cell-hero" data-label="Barang"><?= number_format($row->jumlah_barang, 0, ',', '.') ?></td>
						<td class="num" data-label="Nilai beli"><?= lap_rp($row->total_nilai_beli) ?></td>
						<td class="num" data-label="Nilai jual"><?= lap_rp($row->total_nilai_jual) ?></td>
						<td class="muted" data-label="Dibuat">
							<?= date('d-m-Y H:i', strtotime($row->created_at)) ?>
							<div class="rpt-line-sub"><?= htmlspecialchars($row->created_by) ?></div>
						</td>
						<td class="rpt-cell-actions">
							<a href="<?= site_url('laporan/export_cutoff_pdf/' . urlencode($row->periode)) ?>" target="_blank" class="btn btn-sm btn-default"><i class="fa fa-file-pdf-o"></i> PDF</a>
							<a href="<?= site_url('laporan/export_cutoff_csv/' . urlencode($row->periode)) ?>" class="btn btn-sm btn-default"><i class="fa fa-file-excel-o"></i> CSV</a>
							<?= form_open('laporan/stok_cutoff', 'class="rpt-inline-form"'); ?>
								<input type="hidden" name="action" value="delete">
								<input type="hidden" name="periode" value="<?= htmlspecialchars($row->periode) ?>">
								<button type="submit" class="btn btn-sm btn-default rpt-danger-ghost" title="Hapus cut-off <?= htmlspecialchars($row->periode) ?>"
									onclick="return confirm('Hapus cut-off <?= htmlspecialchars(lap_tanggal($row->periode . '-01', TRUE)) ?>? Data snapshot periode ini akan hilang.')">
									<i class="fa fa-trash-o"></i><span class="sr-only">Hapus</span>
								</button>
							</form>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<?php } ?>
	</section>
</div>
