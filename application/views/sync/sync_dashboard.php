<?php
$target_host = $sync_target_url ? parse_url($sync_target_url, PHP_URL_HOST) : '';
if ($target_host && parse_url($sync_target_url, PHP_URL_PORT)) {
	$target_host .= ':' . parse_url($sync_target_url, PHP_URL_PORT);
}
$can_use = ($sync_enabled && $sync_target_url);
$config = array(
	'base' => base_url(),
	'target' => rtrim((string) $sync_target_url, '/'),
	'targetHost' => $target_host,
	'sendEnabled' => (bool) $sync_enabled,
	'receiveEnabled' => (bool) $sync_receive_enabled,
	'syncKey' => (string) $this->config->item('sync_api_key')
);
?>
<script type="application/json" id="syncConfig"><?= str_replace('<', '<', json_encode($config)) ?></script>

<div class="rpt-head">
	<div>
		<h1 class="rpt-title">Sinkronisasi Data</h1>
		<p class="rpt-sub">Pastikan transaksi, stok, dan harga di server ini sama dengan server tujuan.</p>
	</div>
</div>

<!-- Connection -->
<section class="rpt-card sync-conn">
	<div class="sync-conn-main">
		<span class="rpt-icon <?= $can_use ? '' : 'is-warn' ?>"><i class="fa fa-server"></i></span>
		<div class="sync-conn-text">
			<div class="sync-conn-label">Server tujuan</div>
			<div class="sync-conn-host"><?= $target_host ? htmlspecialchars($target_host) : '<span class="rpt-muted">Belum diatur</span>' ?></div>
		</div>
		<span class="sync-pill is-idle" id="connPill"><i class="fa fa-circle"></i> <span><?= $can_use ? 'Memeriksa koneksi…' : 'Nonaktif' ?></span></span>
	</div>
	<div class="sync-conn-flags">
		<span class="sync-flag <?= $sync_enabled ? 'is-on' : 'is-off' ?>">
			<i class="fa <?= $sync_enabled ? 'fa-check' : 'fa-times' ?>"></i> Kirim data <?= $sync_enabled ? 'aktif' : 'nonaktif' ?>
		</span>
		<span class="sync-flag <?= $sync_receive_enabled ? 'is-on' : 'is-off' ?>">
			<i class="fa <?= $sync_receive_enabled ? 'fa-check' : 'fa-times' ?>"></i> Terima data <?= $sync_receive_enabled ? 'aktif' : 'nonaktif' ?>
		</span>
		<?php if ($can_use) { ?>
		<button type="button" class="btn btn-default btn-sm" id="btnPing"><i class="fa fa-plug"></i> Tes koneksi</button>
		<?php } ?>
	</div>
	<?php if (!$can_use) { ?>
	<div class="alert alert-warning sync-off">
		<i class="fa fa-info-circle"></i>
		Sinkronisasi dimatikan di server ini, jadi pengecekan dan pengiriman tidak tersedia.
		Aktifkan <code>sync_enabled</code> dan isi <code>sync_target_url</code> di <code>application/config/config.php</code>.
	</div>
	<?php } ?>
</section>

<!-- Tabs -->
<div class="sync-tabs" role="tablist" aria-label="Jenis sinkronisasi">
	<button type="button" role="tab" class="sync-tab is-active" id="tab-trx" aria-controls="panel-trx" aria-selected="true" data-tab="trx">
		<i class="fa fa-exchange"></i> Transaksi <span class="sync-tab-badge" id="badge-trx" hidden></span>
	</button>
	<button type="button" role="tab" class="sync-tab" id="tab-stok" aria-controls="panel-stok" aria-selected="false" data-tab="stok">
		<i class="fa fa-cubes"></i> Stok <span class="sync-tab-badge" id="badge-stok" hidden></span>
	</button>
	<button type="button" role="tab" class="sync-tab" id="tab-harga" aria-controls="panel-harga" aria-selected="false" data-tab="harga">
		<i class="fa fa-tags"></i> Harga <span class="sync-tab-badge" id="badge-harga" hidden></span>
	</button>
</div>

<!-- Transaksi -->
<section class="sync-panel" id="panel-trx" role="tabpanel" aria-labelledby="tab-trx">
	<div class="rpt-card">
		<div class="rpt-card-head">
			<span class="rpt-icon"><i class="fa fa-search"></i></span>
			<div>
				<h2 class="rpt-card-title">Cek transaksi yang belum terkirim</h2>
				<p class="rpt-card-desc">Membandingkan kode transaksi di kedua server. Transaksi yang sudah ada di server tujuan tidak akan dikirim ulang.</p>
			</div>
		</div>
		<div class="sync-period">
			<div class="rpt-chips" aria-label="Periode">
				<button type="button" class="rpt-chip" data-days="1">Hari ini</button>
				<button type="button" class="rpt-chip is-active" data-days="3">3 hari terakhir</button>
				<button type="button" class="rpt-chip" data-days="7">7 hari terakhir</button>
			</div>
			<label class="sync-date">
				<span>atau tanggal</span>
				<input type="date" class="form-control" id="trxDate" max="<?= date('Y-m-d') ?>" aria-label="Cek tanggal tertentu">
			</label>
			<button type="button" class="btn btn-primary" id="btnCheckTrx"<?= $can_use ? '' : ' disabled' ?>><i class="fa fa-search"></i> Cek sekarang</button>
		</div>
	</div>

	<div class="rpt-grid sync-trx-grid">
		<?php foreach (array('penjualan' => array('Penjualan', 'fa-shopping-cart'), 'pembelian' => array('Pembelian', 'fa-truck')) as $kind => $meta) { ?>
		<div class="rpt-card sync-trx" id="trx-<?= $kind ?>" data-state="idle">
			<div class="sync-trx-head">
				<h3 class="rpt-card-title"><i class="fa <?= $meta[1] ?>"></i> <?= $meta[0] ?></h3>
				<span class="sync-pill is-idle" data-role="status"><span>Belum dicek</span></span>
			</div>
			<div class="sync-counts" data-role="counts" hidden>
				<div><span class="sync-count-num" data-role="local">0</span><span class="sync-count-label">di server ini</span></div>
				<div><span class="sync-count-num" data-role="remote">0</span><span class="sync-count-label">di server tujuan</span></div>
				<div class="is-missing"><span class="sync-count-num" data-role="missing">0</span><span class="sync-count-label">belum terkirim</span></div>
			</div>
			<p class="rpt-note" data-role="hint">Pilih periode lalu klik <strong>Cek sekarang</strong>.</p>
			<div data-role="missing-box" hidden>
				<div class="sync-codes" data-role="codes"></div>
				<div class="sync-progress" data-role="progress" hidden>
					<div class="sync-progress-bar"><span data-role="bar"></span></div>
					<span class="sync-progress-text" data-role="progress-text"></span>
				</div>
				<button type="button" class="btn btn-primary btn-block" data-role="send">
					<i class="fa fa-cloud-upload"></i> <span>Kirim ke server tujuan</span>
				</button>
			</div>
		</div>
		<?php } ?>
	</div>
</section>

<?php
$item_panels = array(
	'stok' => array(
		'title' => 'Bandingkan stok barang',
		'desc' => 'Cari barang yang stoknya berbeda, lalu pilih arah penyamaan.',
		'icon' => 'fa-cubes',
		'note' => 'Hanya barang yang dicentang yang diubah. Stok di sisi penerima ditimpa dengan angka dari sisi pengirim.'
	),
	'harga' => array(
		'title' => 'Bandingkan harga barang',
		'desc' => 'Cari barang yang harga beli atau harga jualnya berbeda.',
		'icon' => 'fa-tags',
		'note' => 'Yang dikirim adalah harga beli. Penerima menghitung ulang harga jual = harga beli + 10%, dibulatkan ke ratusan terdekat.'
	)
);
foreach ($item_panels as $kind => $p) { ?>
<section class="sync-panel" id="panel-<?= $kind ?>" role="tabpanel" aria-labelledby="tab-<?= $kind ?>" hidden>
	<div class="rpt-card" id="items-<?= $kind ?>" data-kind="<?= $kind ?>">
		<div class="rpt-card-head">
			<span class="rpt-icon"><i class="fa <?= $p['icon'] ?>"></i></span>
			<div>
				<h2 class="rpt-card-title"><?= $p['title'] ?></h2>
				<p class="rpt-card-desc"><?= $p['desc'] ?></p>
			</div>
			<button type="button" class="btn btn-primary sync-compare" data-role="compare"<?= $can_use ? '' : ' disabled' ?>>
				<i class="fa fa-search"></i> <span>Bandingkan</span>
			</button>
		</div>

		<p class="rpt-note" data-role="hint"><i class="fa fa-info-circle"></i> <?= $p['note'] ?></p>

		<div data-role="result" hidden>
			<div class="sync-summary" data-role="summary"></div>

			<div data-role="diff" hidden>
				<div class="rpt-toolbar sync-toolbar">
					<div class="rpt-search">
						<i class="fa fa-search"></i>
						<input type="search" class="form-control" placeholder="Cari barang…" data-role="filter" aria-label="Cari barang">
					</div>
					<label class="rpt-check sync-select-all">
						<input type="checkbox" data-role="select-all" checked> <span>Pilih semua</span>
					</label>
				</div>
				<div class="rpt-table-wrap rpt-cards sync-table">
					<table class="rpt-table">
						<thead data-role="thead"></thead>
						<tbody data-role="rows"></tbody>
					</table>
				</div>
				<div class="sync-actions">
					<span class="sync-selected" data-role="selected"></span>
					<button type="button" class="btn btn-default" data-role="pull"<?= $sync_receive_enabled ? '' : ' disabled title="Server ini tidak menerima data"' ?>>
						<i class="fa fa-cloud-download"></i> <span>Ambil dari server tujuan</span>
					</button>
					<button type="button" class="btn btn-primary" data-role="push">
						<i class="fa fa-cloud-upload"></i> <span>Kirim ke server tujuan</span>
					</button>
				</div>
			</div>
		</div>
	</div>
</section>
<?php } ?>

<!-- Activity -->
<details class="rpt-card sync-log-card" id="logCard">
	<summary>
		<span><i class="fa fa-history"></i> Riwayat aktivitas</span>
		<span class="sync-log-count" id="logCount">0</span>
	</summary>
	<ol class="sync-log" id="syncLog">
		<li class="sync-log-empty">Belum ada aktivitas.</li>
	</ol>
	<button type="button" class="btn btn-default btn-sm" id="btnClearLog">Bersihkan riwayat</button>
</details>
