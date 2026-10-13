<?php
// Only accept a "back" link that points at the history list on this site
$back = (string) $this->input->get('back');
if ($back === '' || strpos($back, site_url('laporan/history_stok')) !== 0) {
	$back = site_url('laporan/history_stok');
}
?>
<div class="rpt-head">
	<div>
		<div class="rpt-crumb"><a href="<?= site_url('laporan') ?>">Laporan</a> / <a href="<?= htmlspecialchars($back) ?>">History stok</a></div>
		<h1 class="rpt-title">Ubah catatan stok</h1>
		<p class="rpt-sub">Koreksi angka stok pada satu titik riwayat. Stok barang saat ini tidak ikut berubah.</p>
	</div>
</div>

<div class="rpt-grid">
	<section class="rpt-card">
		<div class="rpt-card-head">
			<span class="rpt-icon is-ok"><i class="fa fa-cube"></i></span>
			<div>
				<h2 class="rpt-card-title"><?= htmlspecialchars(trim($history->nama_barang)) ?></h2>
				<p class="rpt-card-desc"><?= htmlspecialchars($history->kode_barang) ?> &middot; dicatat <?= lap_tanggal($history->createddate) ?>, <?= date('H:i:s', strtotime($history->createddate)) ?></p>
			</div>
		</div>

		<?= form_open('laporan/history_stok/' . $history->id . '/update'); ?>
			<input type="hidden" name="id" value="<?= htmlspecialchars($history->id) ?>"/>
			<input type="hidden" name="back" value="<?= htmlspecialchars($back) ?>"/>
			<label class="rpt-field">
				<span class="rpt-label">Stok</span>
				<input type="number" step="any" inputmode="decimal" class="form-control" name="stok" id="stok"
					value="<?= htmlspecialchars($history->stok) ?>" required autofocus/>
			</label>
			<div class="rpt-form-foot">
				<button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Simpan</button>
				<a href="<?= htmlspecialchars($back) ?>" class="btn btn-default">Batal</a>
			</div>
		</form>
	</section>
</div>
