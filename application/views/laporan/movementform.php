<?php
$today_iso = date('Y-m-d');
$month_start_iso = date('Y-m-01');
?>
<div class="rpt-head">
	<div>
		<div class="rpt-crumb"><a href="<?= site_url('laporan') ?>">Laporan</a> / Movement barang</div>
		<h1 class="rpt-title">Movement Barang</h1>
		<p class="rpt-sub">Urutan pembelian dan penjualan satu barang, dengan jumlah kumulatif penjualan di antara setiap pembelian.</p>
	</div>
</div>

<div class="rpt-grid">
	<section class="rpt-card">
		<div class="rpt-card-head">
			<span class="rpt-icon is-warn"><i class="fa fa-exchange"></i></span>
			<div>
				<h2 class="rpt-card-title">Pilih barang &amp; rentang waktu</h2>
				<p class="rpt-card-desc">Hasil terbuka di tab baru, siap dicetak.</p>
			</div>
		</div>
		<?= form_open('laporan/movement', 'target="_blank"'); ?>
			<label class="rpt-field">
				<span class="rpt-label">Barang</span>
				<select name="kode_barang" class="form-control selectpicker" data-live-search="true" title="Pilih barang…" data-size="8" required>
					<?php foreach ($barang as $komp) { ?>
					<option value="<?= htmlspecialchars($komp->kode_barang) ?>"><?= htmlspecialchars(trim($komp->nama_barang)) ?> [<?= htmlspecialchars($komp->merk) ?>]</option>
					<?php } ?>
				</select>
			</label>
			<div class="rpt-chips" aria-label="Rentang cepat">
				<button type="button" class="rpt-chip" data-preset="7d">7 hari</button>
				<button type="button" class="rpt-chip" data-preset="30d">30 hari</button>
				<button type="button" class="rpt-chip is-active" data-preset="this-month">Bulan ini</button>
				<button type="button" class="rpt-chip" data-preset="last-month">Bulan lalu</button>
			</div>
			<div>
				<div class="rpt-field">
					<span class="rpt-label">Dari</span>
					<div class="rpt-dt">
						<input type="date" class="form-control" name="tgl_awal" value="<?= $month_start_iso ?>" required aria-label="Tanggal awal">
						<input type="time" class="form-control" name="wkt_awal" value="00:00" required aria-label="Jam awal">
					</div>
				</div>
				<div class="rpt-field">
					<span class="rpt-label">Sampai</span>
					<div class="rpt-dt">
						<input type="date" class="form-control" name="tgl_akhir" value="<?= $today_iso ?>" required aria-label="Tanggal akhir">
						<input type="time" class="form-control" name="wkt_akhir" value="23:59" required aria-label="Jam akhir">
					</div>
				</div>
			</div>
			<div class="rpt-form-foot">
				<button type="submit" class="btn btn-primary"><i class="fa fa-external-link"></i> Buka laporan</button>
			</div>
		</form>
	</section>

	<section class="rpt-card">
		<div class="rpt-card-head">
			<span class="rpt-icon is-info"><i class="fa fa-lightbulb-o"></i></span>
			<div>
				<h2 class="rpt-card-title">Cara membaca laporan</h2>
				<p class="rpt-card-desc">Berguna untuk mengecek selisih stok dan pola penjualan.</p>
			</div>
		</div>
		<ul class="rpt-lines">
			<li class="rpt-line"><span class="rpt-line-name">Pembelian</span><span class="rpt-line-sub">Stok masuk dari supplier.</span></li>
			<li class="rpt-line"><span class="rpt-line-name">Penjualan</span><span class="rpt-line-sub">Stok keluar per transaksi.</span></li>
			<li class="rpt-line"><span class="rpt-line-name">Jumlah kumulatif penjualan</span><span class="rpt-line-sub">Total terjual sejak pembelian sebelumnya.</span></li>
		</ul>
	</section>
</div>
