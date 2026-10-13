<?php
$kode_user = $_SESSION['kode'];
$is_admin = ($_SESSION['level'] == "admin");
$can_see_stock = ($is_admin || !empty($_SESSION['can_see_stock']));

// Customer fields typed before the last "Tambah" are kept in the session.
$saved = array();
foreach (array('keterangan', 'ongkos_karyawan', 'pelanggan', 'nomor_polisi', 'km_kendaraan') as $field) {
	$saved[$field] = isset($_SESSION[$kode_user . $field]) ? trim($_SESSION[$kode_user . $field]) : '';
}
$ongkos = ($saved['ongkos_karyawan'] === '') ? 0 : (int) $saved['ongkos_karyawan'];
$total_barang = ($total == "") ? 0 : $total;

if ($is_admin) {
	$kasir_kode = $admin->kode_admin;
	$kasir_nama = $admin->nama_admin;
}
else {
	$kasir_kode = $karyawan->kode_karyawan;
	$kasir_nama = $karyawan->nama_karyawan;
}

$items = array();
foreach ($listbarang as $komp) {
	$item = array(
		'k' => $komp->kode_barang,
		'n' => trim($komp->nama_barang),
		'm' => trim($komp->merk),
		'h' => (float) $komp->harga_jual
	);
	if ($can_see_stock) {
		$item['s'] = (float) $komp->stok;
	}
	$items[] = $item;
}
$items_json = json_encode($items);
if ($items_json === FALSE) {
	$items_json = '[]';
}
?>
<div class="pos">
	<div class="pos-head">
		<div>
			<h1 class="pos-title">Kasir</h1>
			<div class="pos-meta">
				<?php if (trim($kode_jual) !== '' && trim($kode_jual) !== '-') { ?>
				<span><i class="fa fa-hashtag"></i> <?= htmlspecialchars($kode_jual) ?></span>
				<?php } ?>
				<span><i class="fa fa-user"></i> <?= htmlspecialchars($kasir_nama) ?></span>
			</div>
		</div>
		<div class="pos-head-actions">
			<?php if ($is_admin) { ?>
			<button type="button" class="btn btn-default" data-toggle="collapse" data-target="#posSummary" aria-expanded="false" aria-controls="posSummary">
				<i class="fa fa-bar-chart"></i> Ringkasan hari ini
			</button>
			<?php } ?>
		</div>
	</div>

	<?php if ($is_admin) { ?>
	<div class="collapse" id="posSummary">
		<div class="pos-kpis">
			<div class="pos-kpi">
				<div class="pos-kpi-label">Total transaksi hari ini</div>
				<div class="pos-kpi-value tabular">Rp <?= $this->CodeGenerator->rp($semua + $byTgl) ?></div>
			</div>
			<div class="pos-kpi">
				<div class="pos-kpi-label">Penjualan produk</div>
				<div class="pos-kpi-value tabular">Rp <?= $this->CodeGenerator->rp($semua) ?></div>
			</div>
			<div class="pos-kpi">
				<div class="pos-kpi-label">Bea servis &amp; lain-lain</div>
				<div class="pos-kpi-value tabular">Rp <?= $this->CodeGenerator->rp($byTgl) ?></div>
			</div>
		</div>
		<?php if (!empty($kasir)) { ?>
		<div class="pos-cashiers">
			<?php foreach ($kasir as $k) { ?>
			<div class="pos-cashier">
				<div class="pos-cashier-name"><?= htmlspecialchars($k['nama']) ?></div>
				<div class="pos-cashier-total tabular">Rp <?= $this->CodeGenerator->rp($k['total']) ?></div>
				<div class="pos-cashier-split tabular">
					Penjualan Rp <?= $this->CodeGenerator->rp($k['transaksi']) ?> &middot; Bea Rp <?= $this->CodeGenerator->rp($k['bea']) ?>
				</div>
			</div>
			<?php } ?>
		</div>
		<?php } ?>
	</div>
	<?php } ?>

	<form id="formPenjualan" autocomplete="off" novalidate>
		<input type="hidden" name="kode_jual" value="<?= htmlspecialchars($kode_jual) ?>"/>
		<input type="hidden" name="kode_admin" value="<?= htmlspecialchars($kode_admin) ?>"/>
		<input type="hidden" name="kode_karyawan" value="<?= htmlspecialchars($kasir_kode) ?>"/>
		<input type="hidden" name="kode_barang" id="kode_barang" value=""/>
		<input type="hidden" name="rupiah" id="rupiah" value="-1"/>
		<input type="hidden" name="totalharga" id="totalharga" value="<?= (float) $total_barang ?>"/>
		<input type="hidden" name="total" id="total" value="<?= (float) $total_barang + $ongkos ?>"/>
		<input type="hidden" name="ongkos_karyawan" id="ongkos_karyawan" value="<?= $ongkos ?>"/>
		<input type="hidden" name="bayar" id="bayar" value="0"/>
		<input type="hidden" name="kembali" id="kembali" value="0"/>

		<div class="pos-grid">
			<div class="pos-left">
				<!-- Item entry -->
				<section class="app-card pos-entry">
					<div class="pos-search" id="posSearch">
						<i class="fa fa-search pos-search-icon" aria-hidden="true"></i>
						<input type="text" class="form-control pos-search-input" id="itemSearch"
							placeholder="Ketik nama barang…"
							role="combobox" aria-expanded="false" aria-controls="itemResults" aria-autocomplete="list"
							spellcheck="false"/>
						<button type="button" class="pos-search-clear" id="searchClear" aria-label="Hapus pencarian" hidden>
							<i class="fa fa-times-circle"></i>
						</button>
						<ul class="pos-results" id="itemResults" role="listbox" hidden></ul>
					</div>

					<div class="pos-picked" id="pickedItem" hidden>
						<div class="pos-picked-info">
							<div class="pos-picked-name" id="pickedName"></div>
							<div class="pos-picked-sub" id="pickedSub"></div>
						</div>
						<div class="pos-picked-inputs">
							<div class="pos-field pos-qty">
								<span id="jumlahLabel">Jumlah</span>
								<div class="pos-stepper">
									<button type="button" class="pos-step" data-step="-1" aria-label="Kurangi jumlah"><i class="fa fa-minus"></i></button>
									<input type="text" inputmode="decimal" class="form-control tabular" name="jumlah" id="jumlah" value="1" aria-labelledby="jumlahLabel"/>
									<button type="button" class="pos-step" data-step="1" aria-label="Tambah jumlah"><i class="fa fa-plus"></i></button>
								</div>
							</div>
							<label class="pos-field" id="rupiahField" hidden>
								<span>Rupiah</span>
								<input type="text" inputmode="numeric" class="form-control tabular" id="rupiahView" value=""/>
							</label>
							<div class="pos-add" id="btnValidasiPercobaan">
								<button type="submit" class="btn btn-primary" name="submitlist" value="Tambah" id="btnTambah">
									<i class="fa fa-cart-plus"></i> Tambah ke keranjang
								</button>
							</div>
						</div>
						<div class="pos-blocked" id="blockedMsg" hidden>
							<i class="fa fa-lock"></i>
							Input data barang ini sudah lebih dari 2x. Hubungi admin agar bisa membuat transaksi.
						</div>
					</div>
				</section>

				<!-- Cart -->
				<section class="app-card pos-cart">
					<div class="pos-card-head">
						<h2>Keranjang</h2>
						<span class="pos-count"><?= count($listdetail) ?> barang</span>
					</div>
					<?php if (empty($listdetail)) { ?>
					<div class="pos-empty">
						<i class="fa fa-shopping-basket"></i>
						<p>Belum ada barang.<br>Cari barang di atas untuk mulai transaksi.</p>
					</div>
					<?php } else { ?>
					<ul class="pos-lines">
						<?php foreach ($listdetail as $line) { ?>
						<li class="pos-line">
							<div class="pos-line-main">
								<div class="pos-line-name"><?= htmlspecialchars($line['nama_barang']) ?></div>
								<div class="pos-line-sub tabular">
									<?= $line['jumlah'] + 0 ?> &times; Rp <?= $this->CodeGenerator->rp($line['harga_jual']) ?>
								</div>
							</div>
							<div class="pos-line-total tabular">Rp <?= $this->CodeGenerator->rp($line['subtotal']) ?></div>
							<a class="pos-line-remove" title="Hapus dari keranjang"
								href="<?= site_url('penjualan_detail/delete/' . $line['kode_barang'] . '/1') ?>"
								onclick="return confirm('Hapus <?= htmlspecialchars(addslashes($line['nama_barang'])) ?> dari keranjang?')">
								<i class="fa fa-trash-o"></i><span class="sr-only">Hapus</span>
							</a>
						</li>
						<?php } ?>
					</ul>
					<?php } ?>
				</section>

				<!-- Customer & vehicle -->
				<section class="app-card pos-customer">
					<div class="pos-card-head">
						<h2>Pelanggan &amp; kendaraan</h2>
						<span class="pos-card-note">Untuk pengingat penggantian oli berikutnya</span>
					</div>
					<div class="pos-form-grid">
						<label class="pos-field">
							<span>Klien / kendaraan</span>
							<input type="text" class="form-control" name="pelanggan" id="pelanggan" placeholder="Nama pelanggan"
								value="<?= htmlspecialchars($saved['pelanggan']) ?>"/>
						</label>
						<label class="pos-field">
							<span>Nomor polisi</span>
							<input type="text" class="form-control pos-upper" name="nomor_polisi" id="nomor_polisi" placeholder="AB 1234 CD"
								value="<?= htmlspecialchars($saved['nomor_polisi']) ?>"/>
						</label>
						<label class="pos-field">
							<span>Kilometer kendaraan</span>
							<input type="text" inputmode="numeric" class="form-control" name="km_kendaraan" id="km_kendaraan" placeholder="mis. 25000"
								value="<?= htmlspecialchars($saved['km_kendaraan']) ?>"/>
						</label>
						<label class="pos-field">
							<span>Keterangan</span>
							<input type="text" class="form-control" name="keterangan" id="keterangan" placeholder="Catatan (opsional)"
								value="<?= htmlspecialchars($saved['keterangan']) ?>"/>
						</label>
					</div>
				</section>
			</div>

			<!-- Checkout -->
			<aside class="pos-right">
				<section class="app-card pos-checkout" id="checkout">
					<div class="pos-card-head">
						<h2>Pembayaran</h2>
					</div>

					<label class="pos-field">
						<span>Tanggal jual</span>
						<input type="text" class="form-control" name="tanggal_jual" id="tanggal_jual" value="<?= htmlspecialchars($tanggal_jual) ?>"/>
					</label>

					<dl class="pos-sum">
						<div><dt>Subtotal barang</dt><dd class="tabular" id="subtotalView">Rp <?= $this->CodeGenerator->rp($total_barang) ?></dd></div>
					</dl>

					<label class="pos-field">
						<span>Bea servis / lain-lain</span>
						<div class="pos-money">
							<span>Rp</span>
							<input type="text" inputmode="numeric" class="form-control tabular" id="ongkosView" value="<?= $ongkos ? $this->CodeGenerator->rp($ongkos) : '' ?>" placeholder="0"/>
						</div>
					</label>

					<div class="pos-total">
						<span>Total</span>
						<strong class="tabular" id="totalView">Rp 0</strong>
					</div>

					<label class="pos-field">
						<span>Bayar</span>
						<div class="pos-money pos-money-lg">
							<span>Rp</span>
							<input type="text" inputmode="numeric" class="form-control tabular" id="bayarView" placeholder="0"/>
						</div>
					</label>
					<div class="pos-cash" id="quickCash"></div>

					<div class="pos-change" id="changeBox">
						<span id="changeLabel">Kembali</span>
						<strong class="tabular" id="changeView">Rp 0</strong>
					</div>

					<label class="pos-check">
						<input type="checkbox" name="cetak" id="cetak"/>
						<span>Cetak struk setelah simpan</span>
					</label>

					<button type="submit" class="btn btn-primary btn-lg btn-block" id="btnSimpan">
						<i class="fa fa-check"></i> Simpan transaksi
					</button>
					<a href="<?= site_url('penjualan') ?>" class="btn btn-default btn-block pos-cancel">Batal</a>
				</section>
			</aside>
		</div>

		<!-- Phone: total stays in reach while scrolling the cart -->
		<div class="pos-dock">
			<div>
				<div class="pos-dock-label"><?= count($listdetail) ?> barang</div>
				<div class="pos-dock-total tabular" id="dockTotal">Rp 0</div>
			</div>
			<a href="#checkout" class="btn btn-primary" id="dockPay">Bayar <i class="fa fa-arrow-down"></i></a>
		</div>
	</form>
</div>

<script>
(function () {
	var ITEMS = <?= $items_json ?>;
	var SHOW_STOCK = <?= $can_see_stock ? 'true' : 'false' ?>;
	var INSERT_URL = "<?= base_url('Penjualan/insert/1') ?>";
	var STRUK_URL = "<?= base_url('penjualan/struk') ?>";
	var PERCOBAAN_URL = "<?= base_url('home/get_percobaan_stok') ?>";
	var SYNC_URL = <?= $this->config->item('sync_enabled') ? json_encode(sync_target('penjualan/save_penjualan')) : 'null' ?>;
	var MAX_RESULTS = 60;

	var form = document.getElementById('formPenjualan');
	var search = document.getElementById('itemSearch');
	var results = document.getElementById('itemResults');
	var picked = document.getElementById('pickedItem');
	var kodeBarang = document.getElementById('kode_barang');
	var jumlah = document.getElementById('jumlah');
	var rupiah = document.getElementById('rupiah');
	var rupiahField = document.getElementById('rupiahField');
	var rupiahView = document.getElementById('rupiahView');
	var btnTambah = document.getElementById('btnTambah');
	var blockedMsg = document.getElementById('blockedMsg');
	var ongkosView = document.getElementById('ongkosView');
	var bayarView = document.getElementById('bayarView');
	var btnSimpan = document.getElementById('btnSimpan');

	var blockedIds = [];
	var current = null;
	var matches = [];
	var activeIndex = -1;

	/* ---------- formatting helpers ---------- */
	function rp(n) {
		n = Math.round(Number(n) || 0);
		var s = Math.abs(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
		return (n < 0 ? '-' : '') + 'Rp ' + s;
	}
	function digits(str) {
		return parseInt(String(str).replace(/[^\d]/g, ''), 10) || 0;
	}
	function groupDigits(n) {
		return n ? String(n).replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
	}
	function roundToTwo(num) {
		return +(Math.round(num + "e+2") + "e-2");
	}
	function escapeHtml(s) {
		return String(s).replace(/[&<>"']/g, function (c) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
		});
	}
	// Money inputs show "100.000" while a hidden field keeps the raw number.
	function bindMoney(view, hidden, onChange) {
		view.addEventListener('input', function () {
			var n = digits(view.value);
			hidden.value = n;
			view.value = groupDigits(n);
			onChange();
		});
	}

	/* ---------- totals ---------- */
	var totalHarga = parseFloat(document.getElementById('totalharga').value) || 0;

	function recalc() {
		var ongkos = parseInt(document.getElementById('ongkos_karyawan').value, 10) || 0;
		var total = totalHarga + ongkos;
		var bayar = parseInt(document.getElementById('bayar').value, 10) || 0;
		var kembali = bayar - total;

		document.getElementById('total').value = total;
		document.getElementById('kembali').value = kembali;
		document.getElementById('totalView').textContent = rp(total);
		document.getElementById('dockTotal').textContent = rp(total);

		var box = document.getElementById('changeBox');
		var label = document.getElementById('changeLabel');
		box.classList.remove('is-short', 'is-ok');
		if (bayar === 0) {
			label.textContent = 'Kembali';
			document.getElementById('changeView').textContent = rp(0);
		}
		else if (kembali < 0) {
			box.classList.add('is-short');
			label.textContent = 'Kurang';
			document.getElementById('changeView').textContent = rp(-kembali);
		}
		else {
			box.classList.add('is-ok');
			label.textContent = 'Kembali';
			document.getElementById('changeView').textContent = rp(kembali);
		}
		renderQuickCash(total);
	}

	// Suggested cash amounts: exact, then the next round notes above the total.
	function renderQuickCash(total) {
		var wrap = document.getElementById('quickCash');
		if (total <= 0) {
			wrap.innerHTML = '';
			return;
		}
		var options = [total];
		var steps = [10000, 20000, 50000, 100000];
		for (var i = 0; i < steps.length; i++) {
			var v = Math.ceil(total / steps[i]) * steps[i];
			if (options.indexOf(v) === -1) {
				options.push(v);
			}
		}
		options = options.slice(0, 4);
		var html = '';
		for (var j = 0; j < options.length; j++) {
			html += '<button type="button" class="pos-cash-btn tabular" data-cash="' + options[j] + '">' +
				(j === 0 ? 'Uang pas' : rp(options[j]).replace('Rp ', '')) + '</button>';
		}
		wrap.innerHTML = html;
	}

	document.getElementById('quickCash').addEventListener('click', function (e) {
		var btn = e.target.closest('[data-cash]');
		if (!btn) {
			return;
		}
		var n = parseInt(btn.getAttribute('data-cash'), 10);
		document.getElementById('bayar').value = n;
		bayarView.value = groupDigits(n);
		recalc();
		btnSimpan.focus();
	});

	bindMoney(ongkosView, document.getElementById('ongkos_karyawan'), recalc);
	bindMoney(bayarView, document.getElementById('bayar'), recalc);

	/* ---------- item search ---------- */
	for (var i = 0; i < ITEMS.length; i++) {
		ITEMS[i].hay = (ITEMS[i].n + ' ' + ITEMS[i].m + ' ' + ITEMS[i].k).toLowerCase();
	}

	function findItems(query) {
		var terms = query.toLowerCase().split(/\s+/).filter(Boolean);
		if (!terms.length) {
			return ITEMS.slice(0, MAX_RESULTS);
		}
		var starts = [], contains = [];
		for (var i = 0; i < ITEMS.length; i++) {
			var item = ITEMS[i], ok = true;
			for (var t = 0; t < terms.length; t++) {
				if (item.hay.indexOf(terms[t]) === -1) {
					ok = false;
					break;
				}
			}
			if (!ok) {
				continue;
			}
			if (item.n.toLowerCase().indexOf(terms[0]) === 0 || item.k.toLowerCase() === terms[0]) {
				starts.push(item);
			}
			else {
				contains.push(item);
			}
			if (starts.length >= MAX_RESULTS) {
				break;
			}
		}
		return starts.concat(contains).slice(0, MAX_RESULTS);
	}

	function stockBadge(item) {
		if (!SHOW_STOCK || item.s === undefined) {
			return '';
		}
		var cls = item.s <= 0 ? ' is-out' : (item.s <= 3 ? ' is-low' : '');
		return '<span class="pos-stock' + cls + '">Stok ' + (+item.s) + '</span>';
	}

	function openResults() {
		matches = findItems(search.value);
		activeIndex = matches.length ? 0 : -1;
		var html = '';
		if (!matches.length) {
			html = '<li class="pos-noresult">Tidak ada barang yang cocok dengan "' + escapeHtml(search.value) + '"</li>';
		}
		for (var i = 0; i < matches.length; i++) {
			var item = matches[i];
			html += '<li role="option" id="opt-' + i + '" data-index="' + i + '" class="pos-result' + (i === 0 ? ' is-active' : '') + '">' +
				'<div class="pos-result-main"><div class="pos-result-name">' + escapeHtml(item.n) + '</div>' +
				'<div class="pos-result-sub">' + escapeHtml(item.m) + ' &middot; ' + escapeHtml(item.k) + '</div></div>' +
				'<div class="pos-result-side"><div class="pos-result-price tabular">' + rp(item.h) + '</div>' + stockBadge(item) + '</div></li>';
		}
		results.innerHTML = html;
		results.hidden = false;
		search.setAttribute('aria-expanded', 'true');
		search.setAttribute('aria-activedescendant', activeIndex >= 0 ? 'opt-0' : '');
	}

	function closeResults() {
		results.hidden = true;
		search.setAttribute('aria-expanded', 'false');
		search.removeAttribute('aria-activedescendant');
	}

	function moveActive(delta) {
		if (!matches.length) {
			return;
		}
		var prev = results.querySelector('.is-active');
		if (prev) {
			prev.classList.remove('is-active');
		}
		activeIndex = (activeIndex + delta + matches.length) % matches.length;
		var next = document.getElementById('opt-' + activeIndex);
		next.classList.add('is-active');
		next.scrollIntoView({ block: 'nearest' });
		search.setAttribute('aria-activedescendant', 'opt-' + activeIndex);
	}

	function pick(item) {
		current = item;
		kodeBarang.value = item.k;
		search.value = item.n;
		closeResults();

		document.getElementById('pickedName').textContent = item.n;
		document.getElementById('pickedSub').innerHTML = escapeHtml(item.m) + ' &middot; <span class="tabular">' + rp(item.h) + '</span> ' + stockBadge(item);
		picked.hidden = false;

		// "Oli drum" is sold by value: entering rupiah derives the litres and vice versa.
		jumlah.value = 1;
		if (item.m.toLowerCase().indexOf('oli drum') !== -1) {
			rupiah.value = item.h;
			rupiahView.value = groupDigits(item.h);
			rupiahField.hidden = false;
		}
		else {
			rupiah.value = -1;
			rupiahField.hidden = true;
		}

		var blocked = blockedIds.indexOf(String(item.k)) !== -1;
		btnTambah.hidden = blocked;
		blockedMsg.hidden = !blocked;
		searchClear.hidden = false;

		// With a mouse, jump to the quantity; on touch screens this would
		// pop up the keyboard, so leave it to the − / + buttons instead.
		if (window.matchMedia('(pointer: fine)').matches) {
			jumlah.focus();
			jumlah.select();
		}
	}

	/* ---------- quantity − / + ---------- */
	var stepButtons = document.querySelectorAll('.pos-step');
	for (var sb = 0; sb < stepButtons.length; sb++) {
		stepButtons[sb].addEventListener('click', function () {
			var step = parseInt(this.getAttribute('data-step'), 10);
			var q = parseFloat(String(jumlah.value).replace(',', '.')) || 0;
			var next = roundToTwo(q + step);
			if (next < 1) {
				// Never below 1 with the buttons (drum litres can still be typed)
				next = q > 1 ? 1 : q;
			}
			jumlah.value = next;
			jumlah.dispatchEvent(new Event('input'));
		});
	}

	/* ---------- clear search ---------- */
	var searchClear = document.getElementById('searchClear');
	searchClear.addEventListener('click', function () {
		search.value = '';
		current = null;
		kodeBarang.value = '';
		picked.hidden = true;
		searchClear.hidden = true;
		search.focus();
		openResults();
	});

	jumlah.addEventListener('input', function () {
		if (!current || rupiahField.hidden) {
			return;
		}
		var q = parseFloat(jumlah.value.replace(',', '.'));
		if (!isNaN(q)) {
			rupiah.value = Math.round(q * current.h);
			rupiahView.value = groupDigits(rupiah.value);
		}
	});

	rupiahView.addEventListener('input', function () {
		var n = digits(rupiahView.value);
		rupiahView.value = groupDigits(n);
		rupiah.value = n;
		if (current && current.h) {
			jumlah.value = roundToTwo(n / current.h);
		}
	});

	search.addEventListener('focus', function () {
		search.select();
	});
	search.addEventListener('click', function () {
		if (results.hidden && (!current || search.value !== current.n)) {
			openResults();
		}
	});
	search.addEventListener('input', function () {
		current = null;
		kodeBarang.value = '';
		picked.hidden = true;
		searchClear.hidden = search.value === '';
		openResults();
	});
	search.addEventListener('keydown', function (e) {
		if (e.key === 'ArrowDown') {
			e.preventDefault();
			if (results.hidden) {
				openResults();
			}
			else {
				moveActive(1);
			}
		}
		else if (e.key === 'ArrowUp') {
			e.preventDefault();
			moveActive(-1);
		}
		else if (e.key === 'Enter') {
			e.preventDefault();
			if (!results.hidden && activeIndex >= 0) {
				pick(matches[activeIndex]);
			}
		}
		else if (e.key === 'Escape') {
			closeResults();
		}
	});
	results.addEventListener('mousedown', function (e) {
		// mousedown (not click) so the input doesn't blur first
		var li = e.target.closest('[data-index]');
		if (li) {
			e.preventDefault();
			pick(matches[parseInt(li.getAttribute('data-index'), 10)]);
		}
	});
	document.addEventListener('click', function (e) {
		if (!e.target.closest('#posSearch')) {
			closeResults();
		}
	});

	/* ---------- keyboard flow ---------- */
	form.addEventListener('keydown', function (e) {
		if (e.key !== 'Enter' || e.target === search) {
			return;
		}
		if (e.target === jumlah || e.target === rupiahView) {
			e.preventDefault();
			if (!btnTambah.hidden) {
				btnTambah.click();
			}
		}
		else if (e.target === bayarView) {
			e.preventDefault();
			btnSimpan.focus();
		}
		else if (e.target.tagName === 'INPUT') {
			e.preventDefault(); // never save by accident from a text field
		}
	});

	document.addEventListener('keydown', function (e) {
		var typing = /INPUT|TEXTAREA|SELECT/.test(document.activeElement.tagName);
		if (e.key === 'F2' || (e.key === '/' && !typing)) {
			e.preventDefault();
			search.focus();
		}
		else if (e.key === 'F8') {
			e.preventDefault();
			bayarView.focus();
		}
		else if (e.key === 'F9' || (e.key === 'Enter' && (e.ctrlKey || e.metaKey))) {
			e.preventDefault();
			btnSimpan.click();
		}
	});

	if ('IntersectionObserver' in window) {
		var dock = document.querySelector('.pos-dock');
		new IntersectionObserver(function (entries) {
			dock.classList.toggle('is-hidden', entries[0].isIntersecting);
		}, { threshold: 0.15 }).observe(document.getElementById('btnSimpan'));
	}

	document.getElementById('dockPay').addEventListener('click', function (e) {
		e.preventDefault();
		document.getElementById('checkout').scrollIntoView({ behavior: 'smooth', block: 'start' });
		setTimeout(function () { bayarView.focus({ preventScroll: true }); }, 350);
	});

	/* ---------- submit (same contract as before: Penjualan/insert/1) ---------- */
	form.addEventListener('submit', function (e) {
		e.preventDefault();

		var submitter = e.submitter || document.activeElement;
		var isTambahDetail = submitter && submitter.name === 'submitlist';

		if (isTambahDetail) {
			if (!kodeBarang.value) {
				AppToast.show('warning', 'Pilih barang dulu dari kotak pencarian.');
				search.focus();
				return;
			}
			var q = parseFloat(String(jumlah.value).replace(',', '.'));
			if (!q || isNaN(q) || q <= 0) {
				AppToast.show('warning', 'Jumlah harus lebih dari 0.');
				jumlah.focus();
				jumlah.select();
				return;
			}
		}
		else {
			// A service-only sale (bea servis, no items) is allowed
			if (totalHarga <= 0 && (parseInt(document.getElementById('ongkos_karyawan').value, 10) || 0) <= 0) {
				AppToast.show('warning', 'Keranjang masih kosong. Tambahkan barang atau isi bea servis dulu.');
				search.focus();
				return;
			}
		}

		var originalHtml = submitter.innerHTML;
		submitter.disabled = true;
		submitter.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Proses…';

		function restore() {
			submitter.disabled = false;
			submitter.innerHTML = originalHtml;
		}

		var values = new URLSearchParams(new FormData(form));
		if (isTambahDetail) {
			values.append('submitlist', 'Tambah');
		}
		var wantsStruk = document.getElementById('cetak').checked;

		AppHttp.postForm(INSERT_URL, values)
			.then(function (result) {
				if (isTambahDetail) {
					AppToast.next('success', result.message);
					location.reload();
					return;
				}

				AppToast.next('success', 'Transaksi ' + result.kode_jual + ' tersimpan.', { title: 'Transaksi selesai' });
				if (wantsStruk) {
					window.open(STRUK_URL + '/' + result.kode_jual, '_blank');
				}
				if (!SYNC_URL) {
					location.reload();
					return;
				}
				return fetch(SYNC_URL, {
					method: "POST",
					headers: { "Content-Type": "application/json" },
					body: JSON.stringify(result)
				})
					.then(function (r) {
						if (!r.ok) {
							throw new Error('HTTP ' + r.status);
						}
					})
					.catch(function (err) {
						console.error('Sync error:', err);
						AppToast.next('warning', 'Transaksi tersimpan di toko, tapi gagal dikirim ke server pusat. Kirim ulang dari Sync Dashboard.', { title: 'Sinkronisasi gagal' });
					})
					.then(function () { location.reload(); });
			})
			.catch(function (error) {
				console.error('Error:', error);
				restore();
				var title = isTambahDetail ? 'Barang tidak ditambahkan' : 'Transaksi belum tersimpan';
				AppToast.show('error', error.message, { title: title });

				// Out of stock: keep the item picked so the quantity can be fixed,
				// or lock it right away if this attempt crossed the limit.
				if (error.data && error.data.code === 'stok_kurang') {
					if (error.data.blocked && current) {
						blockedIds.push(String(current.k));
						pick(current);
					}
					else {
						jumlah.focus();
						jumlah.select();
					}
				}
			});
	});

	/* ---------- init ---------- */
	recalc();

	fetch(PERCOBAAN_URL, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
		.then(function (res) { return res.text(); })
		.then(function (text) {
			var list = JSON.parse(text.trim()) || [];
			for (var i = 0; i < list.length; i++) {
				blockedIds.push(String(list[i].id_barang));
			}
			if (current) {
				pick(current);
			}
		})
		.catch(function () { /* no restrictions loaded */ });

	// Desktop: land in the search box, ready for the next item. (Not on phones,
	// where focusing would pop the keyboard over the cart.)
	if (window.matchMedia('(min-width: 992px)').matches) {
		search.focus({ preventScroll: true });
	}
})();
</script>
