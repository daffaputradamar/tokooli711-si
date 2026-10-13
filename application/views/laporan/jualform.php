<?php
$today_iso = date('Y-m-d');
$month_start_iso = date('Y-m-01');
$this_year = date('Y');
$default_year = '';
foreach ($tahun_jual_list as $t) {
	if ($t->tahun == $this_year) {
		$default_year = $this_year;
	}
}
?>
<div class="rpt-head">
	<div>
		<div class="rpt-crumb"><a href="<?= site_url('laporan') ?>">Laporan</a> / Penjualan</div>
		<h1 class="rpt-title">Laporan Penjualan</h1>
		<p class="rpt-sub">Pilih jenis laporan di bawah. Laporan per barang terbuka di tab baru, siap dicetak.</p>
	</div>
</div>

<div class="rpt-grid">
	<!-- Per periode -->
	<section class="rpt-card">
		<div class="rpt-card-head">
			<span class="rpt-icon"><i class="fa fa-calendar"></i></span>
			<div>
				<h2 class="rpt-card-title">Penjualan per periode</h2>
				<p class="rpt-card-desc">Semua transaksi beserta rincian barang dan total, untuk satu hari atau satu bulan.</p>
			</div>
		</div>
		<?= form_open('laporan/jual', 'data-period-form'); ?>
			<div class="rpt-chips" aria-label="Pilihan cepat">
				<button type="button" class="rpt-chip" data-preset="today" data-submit>Hari ini</button>
				<button type="button" class="rpt-chip" data-preset="yesterday" data-submit>Kemarin</button>
				<button type="button" class="rpt-chip" data-preset="this-month" data-submit>Bulan ini</button>
				<button type="button" class="rpt-chip" data-preset="last-month" data-submit>Bulan lalu</button>
			</div>
			<div class="rpt-field">
				<span class="rpt-label">Jenis</span>
				<div class="rpt-seg" role="radiogroup" aria-label="Jenis laporan">
					<label><input type="radio" name="rad" value="0" checked><span>Harian</span></label>
					<label><input type="radio" name="rad" value="1"><span>Bulanan</span></label>
				</div>
			</div>
			<label class="rpt-field">
				<span class="rpt-label">Tanggal</span>
				<input type="date" class="form-control" id="mulai" name="mulai" value="<?= $today_iso ?>" required>
			</label>
			<div class="rpt-form-foot">
				<button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Tampilkan laporan</button>
			</div>
		</form>
	</section>

	<!-- Per barang -->
	<section class="rpt-card">
		<div class="rpt-card-head">
			<span class="rpt-icon is-ok"><i class="fa fa-cube"></i></span>
			<div>
				<h2 class="rpt-card-title">Penjualan per barang</h2>
				<p class="rpt-card-desc">Setiap penjualan satu barang dalam rentang tanggal.</p>
			</div>
		</div>
		<?= form_open('laporan/jual', 'target="_blank"'); ?>
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
			<div class="rpt-pair">
				<label class="rpt-field">
					<span class="rpt-label">Dari</span>
					<input type="date" class="form-control" name="tgl_awal" value="<?= $month_start_iso ?>" required>
				</label>
				<label class="rpt-field">
					<span class="rpt-label">Sampai</span>
					<input type="date" class="form-control" name="tgl_akhir" value="<?= $today_iso ?>" required>
				</label>
			</div>
			<div class="rpt-form-foot">
				<button type="submit" class="btn btn-primary"><i class="fa fa-external-link"></i> Buka laporan</button>
			</div>
		</form>
	</section>

	<!-- Export tahunan -->
	<section class="rpt-card" data-export-scope>
		<div class="rpt-card-head">
			<span class="rpt-icon is-info"><i class="fa fa-download"></i></span>
			<div>
				<h2 class="rpt-card-title">Export tahunan</h2>
				<p class="rpt-card-desc">Total per bulan untuk satu tahun, sebagai Excel (CSV) atau PDF.</p>
			</div>
		</div>
		<?= form_open('laporan/export_jual_tahunan', 'data-no-busy'); ?>
			<label class="rpt-field">
				<span class="rpt-label">Tahun</span>
				<select name="tahun" class="form-control" data-enable-on-value required>
					<option value="">Pilih tahun…</option>
					<?php foreach ($tahun_jual_list as $t) { ?>
					<option value="<?= html_escape((string) $t->tahun) ?>"<?= $t->tahun == $default_year ? ' selected' : '' ?>><?= html_escape((string) $t->tahun) ?></option>
					<?php } ?>
				</select>
			</label>
			<div class="rpt-field">
				<span class="rpt-label">Nilai yang dihitung</span>
				<div class="rpt-seg" role="radiogroup" aria-label="Jenis harga">
					<label><input type="radio" name="tipe_harga" value="jual" checked><span>Harga jual</span></label>
					<label><input type="radio" name="tipe_harga" value="hpp"><span>HPP (harga beli)</span></label>
				</div>
			</div>
			<div class="rpt-export">
				<button type="submit" class="btn btn-success" data-needs-year<?= $default_year ? '' : ' disabled' ?>>
					<i class="fa fa-file-excel-o"></i> Excel
				</button>
				<button type="submit" class="btn btn-danger" data-needs-year<?= $default_year ? '' : ' disabled' ?>
					formaction="<?= site_url('laporan/export_jual_tahunan_pdf') ?>" formtarget="_blank">
					<i class="fa fa-file-pdf-o"></i> PDF
				</button>
			</div>
			<p class="rpt-note"><i class="fa fa-info-circle"></i> Total per bulan dibulatkan ke kelipatan 500.</p>
		</form>
	</section>

	<!-- Bea lain-lain -->
	<section class="rpt-card">
		<div class="rpt-card-head">
			<span class="rpt-icon is-warn"><i class="fa fa-users"></i></span>
			<div>
				<h2 class="rpt-card-title">Bea lain-lain bulan ini</h2>
				<p class="rpt-card-desc">Bea servis &amp; lain-lain per karyawan, <?= lap_tanggal(date('Y-m-d'), TRUE) ?>.</p>
			</div>
		</div>
		<?php
		$total_bea = 0;
		$rows = array();
		foreach ($listgaji as $gaji) {
			if (!is_null($gaji->nama_karyawan)) {
				$rows[] = $gaji;
				$total_bea += $gaji->gaji;
			}
		}
		?>
		<?php if (empty($rows)) { ?>
		<div class="rpt-empty"><i class="fa fa-inbox"></i>Belum ada bea lain-lain bulan ini.</div>
		<?php } else { ?>
		<div class="rpt-table-wrap rpt-cards">
			<table class="rpt-table">
				<thead>
					<tr><th>Karyawan</th><th>No. telp</th><th class="num">Bea lain-lain</th></tr>
				</thead>
				<tbody>
					<?php foreach ($rows as $gaji) { ?>
					<tr>
						<td class="rpt-cell-title"><?= htmlspecialchars($gaji->nama_karyawan) ?></td>
						<td class="muted rpt-cell-wide" data-label="No. telp"><?= htmlspecialchars($gaji->telp_karyawan) ?></td>
						<td class="num rpt-cell-hero" data-label="Bea"><?= lap_rp($gaji->gaji) ?></td>
					</tr>
					<?php } ?>
				</tbody>
				<tfoot>
					<tr><td colspan="2">Total bulan ini</td><td class="num"><?= lap_rp($total_bea) ?></td></tr>
				</tfoot>
			</table>
		</div>
		<?php } ?>
	</section>
</div>
