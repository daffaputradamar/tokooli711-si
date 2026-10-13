/**
 * Sync Dashboard (application/views/sync/sync_dashboard.php).
 *
 * Talks to this server (cfg.base) and the target server (cfg.target) using the
 * existing endpoints; nothing about the server contract changes. Differences
 * from the old page: dates are local (not UTC), only the rows the user ticks
 * are pushed/pulled, every outcome is a toast, and nothing is written without
 * a confirmation that says exactly what will change.
 */
(function () {
	'use strict';

	var cfgEl = document.getElementById('syncConfig');
	if (!cfgEl) {
		return;
	}
	var cfg = JSON.parse(cfgEl.textContent);
	var CAN_USE = cfg.sendEnabled && !!cfg.target;
	var HOST = cfg.targetHost || 'server tujuan';

	/* ---------- helpers ---------- */

	function $(sel, root) {
		return (root || document).querySelector(sel);
	}
	function role(root, name) {
		return root.querySelector('[data-role="' + name + '"]');
	}
	function esc(s) {
		return String(s === null || s === undefined ? '' : s).replace(/[&<>"']/g, function (c) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
		});
	}
	function num(n) {
		return Number(n).toLocaleString('id-ID');
	}
	function rp(n) {
		return 'Rp ' + Math.round(Number(n)).toLocaleString('id-ID');
	}
	function pad(n) {
		return n < 10 ? '0' + n : String(n);
	}
	// Local calendar date; toISOString() would give yesterday before 07:00 WIB
	function ymd(d) {
		return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
	}
	function humanDate(iso) {
		var parts = iso.split('-');
		var months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
		return parseInt(parts[2], 10) + ' ' + months[parseInt(parts[1], 10) - 1] + ' ' + parts[0];
	}
	// Same rule as Sync::receive_barang_harga on the receiving server
	function hargaJualFrom(hargaBeli) {
		return Math.round((Number(hargaBeli) * 1.1) / 100) * 100;
	}

	function setBusy(btn, busy, label) {
		if (!btn) {
			return;
		}
		if (busy) {
			btn.setAttribute('data-idle', btn.innerHTML);
			btn.disabled = true;
			btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> ' + esc(label || 'Memproses…');
		}
		else {
			if (btn.hasAttribute('data-idle')) {
				btn.innerHTML = btn.getAttribute('data-idle');
				btn.removeAttribute('data-idle');
			}
			btn.disabled = false;
		}
	}

	/**
	 * GET/POST JSON from either server. Errors carry a message that says which
	 * server failed and why, ready for a toast.
	 */
	function request(where, path, body) {
		var remote = where === 'remote';
		var url = (remote ? cfg.target + '/' : cfg.base) + path;
		// The target needs the shared key when one is configured (this server
		// accepts the logged-in session instead)
		if (remote && cfg.syncKey) {
			url += (url.indexOf('?') === -1 ? '?' : '&') + 'sync_key=' + encodeURIComponent(cfg.syncKey);
		}
		var opts = { credentials: remote ? 'omit' : 'same-origin' };
		if (body !== undefined) {
			opts.method = 'POST';
			opts.headers = { 'Content-Type': 'application/json' };
			opts.body = JSON.stringify(body);
		}
		var label = remote ? HOST : 'server ini';
		return fetch(url, opts).then(function (res) {
			return res.text().then(function (text) {
				var data;
				try {
					data = JSON.parse(text.replace(/^﻿+/, '').trim());
				}
				catch (e) {
					throw new Error(label + ' membalas dengan data yang tidak dikenali' + (res.status >= 400 ? ' (HTTP ' + res.status + ')' : '') + '.');
				}
				if (!res.ok) {
					throw new Error(label + ' menolak permintaan (HTTP ' + res.status + ')' + (data && data.message ? ': ' + data.message : '.'));
				}
				return data;
			});
		}, function () {
			throw new Error('Tidak bisa terhubung ke ' + label + '. Periksa koneksi internet.');
		});
	}

	/* ---------- activity log ---------- */

	var logList = $('#syncLog');
	var logCount = 0;
	function log(type, text) {
		var empty = logList.querySelector('.sync-log-empty');
		if (empty) {
			empty.parentNode.removeChild(empty);
		}
		var li = document.createElement('li');
		li.className = 'is-' + type;
		var time = new Date();
		li.innerHTML = '<time>' + pad(time.getHours()) + ':' + pad(time.getMinutes()) + ':' + pad(time.getSeconds()) + '</time> ';
		li.appendChild(document.createTextNode(text));
		logList.insertBefore(li, logList.firstChild);
		while (logList.children.length > 200) {
			logList.removeChild(logList.lastChild);
		}
		logCount++;
		$('#logCount').textContent = logCount;
	}
	$('#btnClearLog').addEventListener('click', function () {
		logList.innerHTML = '<li class="sync-log-empty">Belum ada aktivitas.</li>';
		logCount = 0;
		$('#logCount').textContent = '0';
	});

	/* ---------- tabs ---------- */

	var tabs = document.querySelectorAll('.sync-tab');
	function showTab(name) {
		for (var i = 0; i < tabs.length; i++) {
			var on = tabs[i].getAttribute('data-tab') === name;
			tabs[i].classList.toggle('is-active', on);
			tabs[i].setAttribute('aria-selected', on ? 'true' : 'false');
			tabs[i].tabIndex = on ? 0 : -1;
			$('#panel-' + tabs[i].getAttribute('data-tab')).hidden = !on;
		}
		try {
			sessionStorage.setItem('syncTab', name);
		}
		catch (e) { /* ignore */ }
	}
	for (var t = 0; t < tabs.length; t++) {
		tabs[t].addEventListener('click', function () {
			showTab(this.getAttribute('data-tab'));
		});
		tabs[t].addEventListener('keydown', function (e) {
			// Arrow keys move between tabs (WAI-ARIA tabs pattern)
			if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') {
				return;
			}
			var list = Array.prototype.slice.call(tabs);
			var i = list.indexOf(this) + (e.key === 'ArrowRight' ? 1 : -1);
			var next = list[(i + list.length) % list.length];
			showTab(next.getAttribute('data-tab'));
			next.focus();
		});
	}
	try {
		if (sessionStorage.getItem('syncTab')) {
			showTab(sessionStorage.getItem('syncTab'));
		}
	}
	catch (e) { /* ignore */ }

	function setBadge(name, count, tone) {
		var badge = $('#badge-' + name);
		if (count === null) {
			badge.hidden = true;
			return;
		}
		badge.hidden = false;
		badge.className = 'sync-tab-badge is-' + tone;
		badge.textContent = count === 0 ? '✓' : num(count);
	}

	/* ---------- connection ---------- */

	var connPill = $('#connPill');
	function setPill(pill, tone, text) {
		pill.className = 'sync-pill is-' + tone;
		pill.querySelector('span').textContent = text;
	}
	function ping() {
		if (!CAN_USE) {
			return Promise.resolve(false);
		}
		setPill(connPill, 'idle', 'Memeriksa koneksi…');
		var started = Date.now();
		return request('remote', 'sync/get_config').then(function () {
			var ms = Date.now() - started;
			setPill(connPill, 'ok', 'Terhubung · ' + ms + ' ms');
			return true;
		}, function (err) {
			setPill(connPill, 'bad', 'Tidak terhubung');
			log('error', err.message);
			return false;
		});
	}
	var pingBtn = $('#btnPing');
	if (pingBtn) {
		pingBtn.addEventListener('click', function () {
			setBusy(pingBtn, true, 'Menghubungi…');
			ping().then(function (ok) {
				setBusy(pingBtn, false);
				AppToast.show(ok ? 'success' : 'error', ok ? 'Server tujuan merespons.' : 'Server tujuan tidak merespons.');
			});
		});
	}
	ping();

	/* ---------- transactions ---------- */

	var TRX = {
		penjualan: { label: 'penjualan', key: 'kode_jual', listPath: 'penjualan/get_by_date', dataPath: 'sync/get_penjualan_for_sync?kode_jual=', savePath: 'penjualan/save_penjualan', missing: [] },
		pembelian: { label: 'pembelian', key: 'kode_beli', listPath: 'pembelian/get_by_date', dataPath: 'sync/get_pembelian_for_sync?kode_beli=', savePath: 'pembelian/save_pembelian', missing: [] }
	};

	var periodDays = 3;
	var dateInput = $('#trxDate');
	var periodChips = document.querySelectorAll('[data-days]');
	for (var c = 0; c < periodChips.length; c++) {
		periodChips[c].addEventListener('click', function () {
			periodDays = parseInt(this.getAttribute('data-days'), 10);
			dateInput.value = '';
			for (var k = 0; k < periodChips.length; k++) {
				periodChips[k].classList.toggle('is-active', periodChips[k] === this);
			}
		});
	}
	dateInput.addEventListener('change', function () {
		for (var k = 0; k < periodChips.length; k++) {
			periodChips[k].classList.toggle('is-active', !dateInput.value && parseInt(periodChips[k].getAttribute('data-days'), 10) === periodDays);
		}
	});

	function selectedDates() {
		if (dateInput.value) {
			return [dateInput.value];
		}
		var out = [];
		var today = new Date();
		for (var i = 0; i < periodDays; i++) {
			var d = new Date(today.getFullYear(), today.getMonth(), today.getDate() - i);
			out.push(ymd(d));
		}
		return out;
	}
	function periodLabel(dates) {
		return dates.length === 1 ? humanDate(dates[0]) : humanDate(dates[dates.length - 1]) + ' – ' + humanDate(dates[0]);
	}

	function codesFor(where, kind, dates) {
		var def = TRX[kind];
		return Promise.all(dates.map(function (d) {
			return request(where, def.listPath + '?date=' + d).then(function (res) {
				return (res && res.data ? res.data : []).map(function (row) { return row[def.key]; });
			});
		})).then(function (lists) {
			var seen = {};
			var all = [];
			lists.forEach(function (list) {
				list.forEach(function (code) {
					if (!seen[code]) {
						seen[code] = true;
						all.push(code);
					}
				});
			});
			return all;
		});
	}

	function renderCodes(card, codes) {
		var box = role(card, 'codes');
		box.innerHTML = codes.map(function (code) {
			return '<span class="sync-code" data-code="' + esc(code) + '">' + esc(code) + '</span>';
		}).join('');
	}

	function setTrxState(kind, state, opts) {
		opts = opts || {};
		var card = $('#trx-' + kind);
		card.setAttribute('data-state', state);
		var status = role(card, 'status');
		var hint = role(card, 'hint');
		var missingBox = role(card, 'missing-box');

		if (state === 'checking') {
			setPill(status, 'idle', 'Memeriksa…');
			hint.hidden = false;
			hint.textContent = 'Mengambil daftar transaksi dari kedua server…';
			missingBox.hidden = true;
			return;
		}
		if (state === 'error') {
			setPill(status, 'bad', 'Gagal dicek');
			hint.hidden = false;
			hint.textContent = opts.message;
			role(card, 'counts').hidden = true;
			missingBox.hidden = true;
			return;
		}

		role(card, 'counts').hidden = false;
		role(card, 'local').textContent = num(opts.local);
		role(card, 'remote').textContent = num(opts.remote);
		role(card, 'missing').textContent = num(opts.missing.length);
		card.querySelector('.sync-counts .is-missing').classList.toggle('is-zero', opts.missing.length === 0);

		if (opts.missing.length === 0) {
			setPill(status, 'ok', 'Sinkron');
			hint.hidden = false;
			hint.textContent = 'Semua transaksi ' + TRX[kind].label + ' pada ' + opts.period + ' sudah ada di server tujuan.';
			missingBox.hidden = true;
		}
		else {
			setPill(status, 'warn', num(opts.missing.length) + ' belum terkirim');
			hint.hidden = false;
			hint.textContent = 'Transaksi berikut ada di server ini tapi belum ada di server tujuan (' + opts.period + '):';
			missingBox.hidden = false;
			renderCodes(card, opts.missing);
			role(card, 'progress').hidden = true;
			var send = role(card, 'send');
			send.disabled = false;
			send.querySelector('span').textContent = 'Kirim ' + num(opts.missing.length) + ' ' + TRX[kind].label + ' ke server tujuan';
		}
	}

	function checkKind(kind, dates) {
		setTrxState(kind, 'checking');
		return Promise.all([codesFor('local', kind, dates), codesFor('remote', kind, dates)]).then(function (res) {
			var remoteSet = {};
			res[1].forEach(function (code) { remoteSet[code] = true; });
			var missing = res[0].filter(function (code) { return !remoteSet[code]; });
			TRX[kind].missing = missing;
			TRX[kind].checked = true;
			setTrxState(kind, 'done', { local: res[0].length, remote: res[1].length, missing: missing, period: periodLabel(dates) });
			var total = TRX.penjualan.missing.length + TRX.pembelian.missing.length;
			if (TRX.penjualan.checked && TRX.pembelian.checked) {
				setBadge('trx', total, total ? 'warn' : 'ok');
			}
			log(missing.length ? 'warning' : 'success', 'Cek ' + TRX[kind].label + ' (' + periodLabel(dates) + '): ' +
				(missing.length ? missing.length + ' belum terkirim' : 'semua sinkron'));
			return missing.length;
		}, function (err) {
			TRX[kind].missing = [];
			TRX[kind].checked = false;
			setTrxState(kind, 'error', { message: err.message });
			log('error', 'Cek ' + TRX[kind].label + ' gagal: ' + err.message);
			throw err;
		});
	}

	var lastTrxDates = null;
	var btnCheckTrx = $('#btnCheckTrx');
	function checkTransactions() {
		var dates = selectedDates();
		lastTrxDates = dates;
		setBusy(btnCheckTrx, true, 'Memeriksa…');
		var results = { penjualan: null, pembelian: null };
		var errors = [];
		return Promise.all(['penjualan', 'pembelian'].map(function (kind) {
			return checkKind(kind, dates).then(function (n) { results[kind] = n; }, function (err) { errors.push(err.message); });
		})).then(function () {
			setBusy(btnCheckTrx, false);
			if (errors.length) {
				setBadge('trx', null);
				AppToast.show('error', errors[0], { title: 'Pengecekan gagal' });
				return;
			}
			var total = results.penjualan + results.pembelian;
			AppToast.show(total ? 'warning' : 'success', total
				? total + ' transaksi belum ada di server tujuan.'
				: 'Semua transaksi pada ' + periodLabel(dates) + ' sudah sinkron.');
		});
	}
	btnCheckTrx.addEventListener('click', checkTransactions);

	function sendMissing(kind) {
		var def = TRX[kind];
		var codes = def.missing.slice();
		var card = $('#trx-' + kind);
		if (!codes.length) {
			return;
		}
		AppConfirm({
			title: 'Kirim ' + codes.length + ' ' + def.label + '?',
			message: 'Transaksi berikut akan disalin ke ' + HOST + '. Data di server ini tidak berubah, dan transaksi yang sudah ada di sana tidak dikirim ulang.',
			details: codes.length > 12 ? codes.slice(0, 12).concat(['… dan ' + (codes.length - 12) + ' lainnya']) : codes,
			confirmText: 'Kirim sekarang'
		}).then(function (ok) {
			if (!ok) {
				return;
			}
			var send = role(card, 'send');
			var progress = role(card, 'progress');
			var bar = role(card, 'bar');
			var text = role(card, 'progress-text');
			send.disabled = true;
			progress.hidden = false;
			var done = 0, okCount = 0, failed = [];
			log('info', 'Mengirim ' + codes.length + ' ' + def.label + ' ke ' + HOST + '…');

			// One at a time: the target assigns stock movements per transaction
			var chain = Promise.resolve();
			codes.forEach(function (code) {
				chain = chain.then(function () {
					var chip = card.querySelector('.sync-code[data-code="' + code.replace(/"/g, '') + '"]');
					if (chip) {
						chip.className = 'sync-code is-sending';
					}
					return request('local', def.dataPath + encodeURIComponent(code)).then(function (res) {
						if (!res.status) {
							throw new Error(res.message || 'data tidak ditemukan di server ini');
						}
						return request('remote', def.savePath, res.data);
					}).then(function (res) {
						if (!res.status) {
							throw new Error(res.message || 'ditolak server tujuan');
						}
						okCount++;
						if (chip) {
							chip.className = 'sync-code is-ok';
						}
					}, function (err) {
						failed.push(code);
						if (chip) {
							chip.className = 'sync-code is-fail';
							chip.title = err.message;
						}
						log('error', code + ': ' + err.message);
					}).then(function () {
						done++;
						bar.style.width = Math.round(done / codes.length * 100) + '%';
						text.textContent = done + ' / ' + codes.length + ' terkirim' + (failed.length ? ' · ' + failed.length + ' gagal' : '');
					});
				});
			});

			chain.then(function () {
				log(failed.length ? 'warning' : 'success', 'Selesai mengirim ' + def.label + ': ' + okCount + ' berhasil, ' + failed.length + ' gagal.');
				if (failed.length) {
					AppToast.show('error', okCount + ' berhasil, ' + failed.length + ' gagal. Arahkan kursor ke kode merah untuk melihat alasannya, lalu coba lagi.', { title: 'Sebagian ' + def.label + ' gagal' });
					def.missing = failed;
					send.disabled = false;
					send.querySelector('span').textContent = 'Coba kirim ulang ' + failed.length + ' yang gagal';
				}
				else {
					AppToast.show('success', okCount + ' ' + def.label + ' terkirim ke ' + HOST + '.', { title: 'Sinkronisasi selesai' });
				}
				// Re-check this kind so the counts reflect the target's new state
				if (lastTrxDates && okCount) {
					setTimeout(function () {
						checkKind(kind, lastTrxDates).then(function () {
							if (failed.length) {
								var current = $('#trx-' + kind);
								failed.forEach(function (code) {
									var chip = current.querySelector('.sync-code[data-code="' + code.replace(/"/g, '') + '"]');
									if (chip) {
										chip.className = 'sync-code is-fail';
									}
								});
							}
						}, function () { /* already reported */ });
					}, 600);
				}
			});
		});
	}
	['penjualan', 'pembelian'].forEach(function (kind) {
		role($('#trx-' + kind), 'send').addEventListener('click', function () {
			sendMissing(kind);
		});
	});

	/* ---------- stock & price comparison ---------- */

	var ITEMS = {
		stok: {
			path: 'sync/get_all_barang_stock',
			receivePath: 'sync/receive_barang_stock',
			noun: 'stok',
			differs: function (a, b) { return parseInt(a.stok, 10) !== parseInt(b.stok, 10); },
			head: '<tr><th><span class="sr-only">Pilih</span></th><th>Barang</th><th class="num">Server ini</th><th class="num">Server tujuan</th><th class="num">Selisih</th></tr>',
			row: function (d) {
				var diff = d.remote ? parseInt(d.local.stok, 10) - parseInt(d.remote.stok, 10) : null;
				return '<td class="num" data-label="Server ini">' + num(d.local.stok) + '</td>' +
					'<td class="num" data-label="Server tujuan">' + (d.remote ? num(d.remote.stok) : '<span class="rpt-muted">tidak ada</span>') + '</td>' +
					'<td class="num rpt-cell-hero" data-label="Selisih">' + (diff === null ? '–' : '<span class="rpt-badge ' + (diff > 0 ? 'is-ok' : 'is-out') + '">' + (diff > 0 ? '+' : '') + num(diff) + '</span>') + '</td>';
			},
			payload: function (item) { return { kode_barang: item.kode_barang, stok: parseInt(item.stok, 10) }; },
			preview: function (d, toRemote) {
				var from = toRemote ? d.local : d.remote;
				var to = toRemote ? d.remote : d.local;
				return d.local.nama_barang + ': ' + num(to ? to.stok : 0) + ' → ' + num(from.stok);
			}
		},
		harga: {
			path: 'sync/get_all_barang_harga',
			receivePath: 'sync/receive_barang_harga',
			noun: 'harga',
			differs: function (a, b) {
				return parseInt(a.harga_beli, 10) !== parseInt(b.harga_beli, 10) || parseInt(a.harga_jual, 10) !== parseInt(b.harga_jual, 10);
			},
			head: '<tr><th><span class="sr-only">Pilih</span></th><th>Barang</th><th class="num">Beli · ini</th><th class="num">Beli · tujuan</th><th class="num">Jual · ini</th><th class="num">Jual · tujuan</th></tr>',
			row: function (d) {
				function cell(label, v, other, key) {
					var changed = other && parseInt(v[key], 10) !== parseInt(other[key], 10);
					return '<td class="num' + (changed ? ' sync-changed' : '') + '" data-label="' + label + '">' + rp(v[key]) + '</td>';
				}
				var missing = '<td class="num" data-label="%"><span class="rpt-muted">tidak ada</span></td>';
				return cell('Beli · ini', d.local, d.remote, 'harga_beli') +
					(d.remote ? cell('Beli · tujuan', d.remote, d.local, 'harga_beli') : missing.replace('%', 'Beli · tujuan')) +
					cell('Jual · ini', d.local, d.remote, 'harga_jual') +
					(d.remote ? cell('Jual · tujuan', d.remote, d.local, 'harga_jual') : missing.replace('%', 'Jual · tujuan'));
			},
			payload: function (item) { return { kode_barang: item.kode_barang, harga_beli: parseInt(item.harga_beli, 10) }; },
			preview: function (d, toRemote) {
				var from = toRemote ? d.local : d.remote;
				return d.local.nama_barang + ': beli ' + rp(from.harga_beli) + ', jual menjadi ' + rp(hargaJualFrom(from.harga_beli));
			}
		}
	};

	function itemsPanel(kind) {
		var card = $('#items-' + kind);
		var def = ITEMS[kind];
		var state = { diffs: [], localCount: 0, remoteCount: 0 };

		var compareBtn = role(card, 'compare');
		var rowsEl = role(card, 'rows');
		var selectAll = role(card, 'select-all');
		var filter = role(card, 'filter');
		var pushBtn = role(card, 'push');
		var pullBtn = role(card, 'pull');
		role(card, 'thead').innerHTML = def.head;

		function checkedRows() {
			return Array.prototype.slice.call(rowsEl.querySelectorAll('input[type="checkbox"]:checked')).map(function (cb) {
				return state.diffs[parseInt(cb.value, 10)];
			});
		}
		function updateSelection() {
			var picked = checkedRows();
			var withRemote = picked.filter(function (d) { return !!d.remote; });
			role(card, 'selected').textContent = picked.length + ' dari ' + state.diffs.length + ' barang dipilih';
			pushBtn.disabled = !withRemote.length;
			pullBtn.disabled = !withRemote.length || !cfg.receiveEnabled;
			pushBtn.querySelector('span').textContent = 'Kirim ke server tujuan' + (withRemote.length ? ' (' + withRemote.length + ')' : '');
			pullBtn.querySelector('span').textContent = 'Ambil dari server tujuan' + (withRemote.length ? ' (' + withRemote.length + ')' : '');
			var boxes = rowsEl.querySelectorAll('tr:not(.rpt-hidden) input[type="checkbox"]:not(:disabled)');
			var all = boxes.length > 0;
			for (var i = 0; i < boxes.length; i++) {
				if (!boxes[i].checked) {
					all = false;
				}
			}
			selectAll.checked = all;
		}

		function render() {
			var summary = role(card, 'summary');
			var onlyLocal = state.diffs.filter(function (d) { return !d.remote; }).length;
			summary.innerHTML =
				'<div class="sync-stat"><span>' + num(state.localCount) + '</span>barang di server ini</div>' +
				'<div class="sync-stat"><span>' + num(state.remoteCount) + '</span>barang di server tujuan</div>' +
				'<div class="sync-stat ' + (state.diffs.length ? 'is-warn' : 'is-ok') + '"><span>' + num(state.diffs.length) + '</span>berbeda' +
				(onlyLocal ? ' (' + onlyLocal + ' tidak ada di tujuan)' : '') + '</div>';

			role(card, 'result').hidden = false;
			role(card, 'diff').hidden = !state.diffs.length;
			if (!state.diffs.length) {
				summary.insertAdjacentHTML('beforeend', '<p class="sync-allgood"><i class="fa fa-check-circle"></i> ' +
					(kind === 'stok' ? 'Stok' : 'Harga') + ' semua barang sudah sama di kedua server.</p>');
				return;
			}

			rowsEl.innerHTML = state.diffs.map(function (d, i) {
				var disabled = d.remote ? '' : ' disabled';
				return '<tr data-search="' + esc((d.local.nama_barang + ' ' + d.local.kode_barang).toLowerCase()) + '">' +
					'<td class="sync-check"><input type="checkbox" value="' + i + '" aria-label="Pilih ' + esc(d.local.nama_barang) + '"' + (d.remote ? ' checked' : '') + disabled + '></td>' +
					'<td class="rpt-cell-title">' + esc(d.local.nama_barang) + '<div class="rpt-line-sub">' + esc(d.local.kode_barang) +
					(d.remote ? '' : ' · <span class="rpt-neg">tidak ada di tujuan</span>') + '</div></td>' +
					def.row(d) + '</tr>';
			}).join('');
			filter.value = '';
			updateSelection();
		}

		function compare() {
			setBusy(compareBtn, true, 'Membandingkan…');
			log('info', 'Membandingkan ' + def.noun + ' barang…');
			return Promise.all([request('local', def.path), request('remote', def.path)]).then(function (res) {
				if (!res[0].status || !res[1].status) {
					throw new Error('Data ' + def.noun + ' tidak tersedia di ' + (!res[0].status ? 'server ini' : HOST) + '.');
				}
				var remoteMap = {};
				res[1].data.forEach(function (item) { remoteMap[item.kode_barang] = item; });
				state.localCount = res[0].data.length;
				state.remoteCount = res[1].data.length;
				state.diffs = [];
				res[0].data.forEach(function (local) {
					var remote = remoteMap[local.kode_barang] || null;
					if (!remote || def.differs(local, remote)) {
						state.diffs.push({ local: local, remote: remote });
					}
				});
				render();
				setBadge(kind, state.diffs.length, state.diffs.length ? 'warn' : 'ok');
				log(state.diffs.length ? 'warning' : 'success', 'Perbandingan ' + def.noun + ': ' + state.diffs.length + ' barang berbeda.');
				AppToast.show(state.diffs.length ? 'warning' : 'success', state.diffs.length
					? state.diffs.length + ' barang memiliki ' + def.noun + ' berbeda.'
					: (kind === 'stok' ? 'Stok' : 'Harga') + ' semua barang sudah sama.');
			}).catch(function (err) {
				log('error', 'Perbandingan ' + def.noun + ' gagal: ' + err.message);
				AppToast.show('error', err.message, { title: 'Perbandingan gagal' });
			}).then(function () {
				setBusy(compareBtn, false);
			});
		}

		function apply(toRemote) {
			var picked = checkedRows().filter(function (d) { return !!d.remote; });
			if (!picked.length) {
				return;
			}
			var source = toRemote ? 'local' : 'remote';
			var items = picked.map(function (d) { return def.payload(d[source]); });
			var preview = picked.slice(0, 8).map(function (d) { return def.preview(d, toRemote); });
			if (picked.length > 8) {
				preview.push('… dan ' + (picked.length - 8) + ' barang lainnya');
			}
			var where = toRemote ? HOST : 'server ini';

			AppConfirm({
				title: (toRemote ? 'Kirim ' : 'Ambil ') + def.noun + ' ' + picked.length + ' barang?',
				message: 'Nilai ' + def.noun + ' di ' + where + ' akan ditimpa untuk barang yang dipilih. Barang lain tidak berubah.' +
					(kind === 'harga' ? ' Harga jual dihitung ulang dari harga beli (+10%, dibulatkan ke ratusan).' : ''),
				details: preview,
				confirmText: toRemote ? 'Kirim & timpa' : 'Ambil & timpa',
				tone: 'danger'
			}).then(function (ok) {
				if (!ok) {
					return;
				}
				var btn = toRemote ? pushBtn : pullBtn;
				setBusy(btn, true, toRemote ? 'Mengirim…' : 'Mengambil…');
				log('info', (toRemote ? 'Mengirim ' : 'Mengambil ') + def.noun + ' ' + items.length + ' barang…');
				// Pushing writes on the target; pulling writes here
				request(toRemote ? 'remote' : 'local', def.receivePath, { items: items }).then(function (res) {
					if (!res.status) {
						throw new Error(res.message || 'Permintaan ditolak.');
					}
					var msg = res.updated + ' barang diperbarui di ' + where + (res.skipped ? ', ' + res.skipped + ' dilewati' : '') + '.';
					log(res.skipped ? 'warning' : 'success', msg + (res.errors && res.errors.length ? ' ' + res.errors.join(', ') : ''));
					AppToast.show(res.skipped ? 'warning' : 'success', msg, { title: (kind === 'stok' ? 'Stok' : 'Harga') + ' disamakan' });
					setBusy(btn, false);
					return compare();
				}).catch(function (err) {
					setBusy(btn, false);
					log('error', (toRemote ? 'Kirim ' : 'Ambil ') + def.noun + ' gagal: ' + err.message);
					AppToast.show('error', err.message, { title: 'Tidak ada yang diubah' });
				});
			});
		}

		compareBtn.addEventListener('click', compare);
		pushBtn.addEventListener('click', function () { apply(true); });
		pullBtn.addEventListener('click', function () { apply(false); });
		rowsEl.addEventListener('change', updateSelection);
		selectAll.addEventListener('change', function () {
			var boxes = rowsEl.querySelectorAll('tr:not(.rpt-hidden) input[type="checkbox"]:not(:disabled)');
			for (var i = 0; i < boxes.length; i++) {
				boxes[i].checked = selectAll.checked;
			}
			updateSelection();
		});
		filter.addEventListener('input', function () {
			var terms = filter.value.toLowerCase().split(/\s+/).filter(Boolean);
			var rows = rowsEl.querySelectorAll('tr');
			for (var i = 0; i < rows.length; i++) {
				var hay = rows[i].getAttribute('data-search');
				var ok = terms.every(function (term) { return hay.indexOf(term) !== -1; });
				rows[i].classList.toggle('rpt-hidden', !ok);
			}
			updateSelection();
		});
	}

	itemsPanel('stok');
	itemsPanel('harga');
})();
