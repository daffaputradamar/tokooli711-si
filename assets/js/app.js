/**
 * Toko Oli 711 - application shell behaviour.
 * Plain ES5 so it runs on any browser the shop's PCs and phones have.
 */

/**
 * Toasts.
 *   AppToast.show('success', 'Tersimpan')            - show now
 *   AppToast.show('error', 'Gagal', { title: '…' })   - errors stay longer
 *   AppToast.next('success', 'Barang ditambahkan')    - show after the next page load
 * Server side: set_toast($type, $message) before a redirect (toast_helper.php).
 */
window.AppToast = (function () {
	var STORE_KEY = 'appToastQueue';
	var ICONS = { success: 'fa-check-circle', error: 'fa-times-circle', warning: 'fa-exclamation-triangle', info: 'fa-info-circle' };
	var TITLES = { success: 'Berhasil', error: 'Gagal', warning: 'Perhatian', info: 'Info' };
	var region = null;

	function getRegion() {
		if (!region) {
			region = document.createElement('div');
			region.className = 'app-toasts';
			document.body.appendChild(region);
		}
		return region;
	}

	function dismiss(el) {
		if (!el || el.classList.contains('is-leaving')) {
			return;
		}
		el.classList.add('is-leaving');
		setTimeout(function () {
			if (el.parentNode) {
				el.parentNode.removeChild(el);
			}
		}, 200);
	}

	function show(type, message, opts) {
		opts = opts || {};
		type = ICONS[type] ? type : 'info';
		var el = document.createElement('div');
		el.className = 'app-toast is-' + type;
		// Errors interrupt (assertive); everything else waits its turn (polite)
		el.setAttribute('role', type === 'error' ? 'alert' : 'status');

		var icon = document.createElement('i');
		icon.className = 'fa ' + ICONS[type] + ' app-toast-icon';
		icon.setAttribute('aria-hidden', 'true');

		var body = document.createElement('div');
		body.className = 'app-toast-body';
		var title = document.createElement('div');
		title.className = 'app-toast-title';
		title.textContent = opts.title || TITLES[type];
		var text = document.createElement('div');
		text.className = 'app-toast-text';
		text.textContent = message;
		body.appendChild(title);
		body.appendChild(text);

		var close = document.createElement('button');
		close.type = 'button';
		close.className = 'app-toast-close';
		close.setAttribute('aria-label', 'Tutup');
		close.innerHTML = '<i class="fa fa-times" aria-hidden="true"></i>';
		close.addEventListener('click', function () { dismiss(el); });

		el.appendChild(icon);
		el.appendChild(body);
		el.appendChild(close);
		getRegion().appendChild(el);

		var duration = opts.duration || (type === 'error' ? 8000 : (type === 'warning' ? 6000 : 4000));
		var timer = setTimeout(function () { dismiss(el); }, duration);
		el.addEventListener('mouseenter', function () { clearTimeout(timer); });
		el.addEventListener('mouseleave', function () {
			timer = setTimeout(function () { dismiss(el); }, 2000);
		});
		return el;
	}

	function next(type, message, opts) {
		try {
			var queue = JSON.parse(sessionStorage.getItem(STORE_KEY) || '[]');
			queue.push({ type: type, message: message, opts: opts || null });
			sessionStorage.setItem(STORE_KEY, JSON.stringify(queue));
		}
		catch (e) {
			show(type, message, opts);
		}
	}

	function flush() {
		var queue = [];
		try {
			queue = JSON.parse(sessionStorage.getItem(STORE_KEY) || '[]');
			sessionStorage.removeItem(STORE_KEY);
		}
		catch (e) { /* storage unavailable */ }

		var server = document.getElementById('serverToasts');
		if (server) {
			try {
				queue = queue.concat(JSON.parse(server.textContent));
			}
			catch (e) { /* malformed payload */ }
		}
		for (var i = 0; i < queue.length; i++) {
			show(queue[i].type, queue[i].message, queue[i].opts || undefined);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', flush);
	}
	else {
		flush();
	}

	return { show: show, next: next };
})();

/**
 * Confirmation dialog, a friendlier replacement for window.confirm().
 *   AppConfirm({ title, message, details: ['…'], confirmText, tone: 'danger' })
 *     .then(function (ok) { … });
 * Esc, the backdrop and "Batal" resolve false.
 */
window.AppConfirm = function (opts) {
	opts = opts || {};
	return new Promise(function (resolve) {
		var previous = document.activeElement;
		var backdrop = document.createElement('div');
		backdrop.className = 'app-dialog-backdrop';

		var dialog = document.createElement('div');
		dialog.className = 'app-dialog' + (opts.tone === 'danger' ? ' is-danger' : '');
		dialog.setAttribute('role', 'dialog');
		dialog.setAttribute('aria-modal', 'true');
		dialog.setAttribute('aria-labelledby', 'appDialogTitle');

		var title = document.createElement('h2');
		title.id = 'appDialogTitle';
		title.className = 'app-dialog-title';
		title.textContent = opts.title || 'Lanjutkan?';
		dialog.appendChild(title);

		if (opts.message) {
			var msg = document.createElement('p');
			msg.className = 'app-dialog-text';
			msg.textContent = opts.message;
			dialog.appendChild(msg);
		}

		if (opts.details && opts.details.length) {
			var list = document.createElement('ul');
			list.className = 'app-dialog-list';
			for (var i = 0; i < opts.details.length; i++) {
				var li = document.createElement('li');
				li.textContent = opts.details[i];
				list.appendChild(li);
			}
			dialog.appendChild(list);
		}

		var actions = document.createElement('div');
		actions.className = 'app-dialog-actions';
		var cancel = document.createElement('button');
		cancel.type = 'button';
		cancel.className = 'btn btn-default';
		cancel.textContent = opts.cancelText || 'Batal';
		var ok = document.createElement('button');
		ok.type = 'button';
		ok.className = 'btn ' + (opts.tone === 'danger' ? 'btn-danger' : 'btn-primary');
		ok.textContent = opts.confirmText || 'Lanjutkan';
		actions.appendChild(cancel);
		actions.appendChild(ok);
		dialog.appendChild(actions);

		backdrop.appendChild(dialog);
		document.body.appendChild(backdrop);
		document.body.classList.add('has-dialog');

		function close(result) {
			document.removeEventListener('keydown', onKey, true);
			backdrop.parentNode.removeChild(backdrop);
			document.body.classList.remove('has-dialog');
			if (previous && previous.focus) {
				previous.focus();
			}
			resolve(result);
		}
		function onKey(e) {
			if (e.key === 'Escape') {
				e.preventDefault();
				close(false);
			}
			else if (e.key === 'Tab') {
				// Keep focus inside the dialog
				var first = cancel, last = ok;
				if (e.shiftKey && document.activeElement === first) {
					e.preventDefault();
					last.focus();
				}
				else if (!e.shiftKey && document.activeElement === last) {
					e.preventDefault();
					first.focus();
				}
			}
		}

		cancel.addEventListener('click', function () { close(false); });
		ok.addEventListener('click', function () { close(true); });
		backdrop.addEventListener('mousedown', function (e) {
			if (e.target === backdrop) {
				close(false);
			}
		});
		document.addEventListener('keydown', onKey, true);
		// Risky actions start on "Batal" so Enter doesn't confirm by accident
		(opts.tone === 'danger' ? cancel : ok).focus();
	});
};

/**
 * Form POSTs to endpoints that answer with JSON (see json_reply in toast_helper.php).
 * Resolves with the parsed body; rejects with an Error whose .message is ready
 * to show the user, and .kind = network | session | server | rejected.
 */
window.AppHttp = (function () {
	function fail(message, kind, status, data) {
		var err = new Error(message);
		err.kind = kind;
		err.status = status;
		err.data = data || null;
		return err;
	}

	function postForm(url, params) {
		return fetch(url, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
				'X-Requested-With': 'XMLHttpRequest',
				'Accept': 'application/json'
			},
			body: params.toString()
		}).then(function (res) {
			return res.text().then(function (text) {
				var data = null;
				try {
					data = JSON.parse(text.replace(/^﻿+/, '').trim());
				}
				catch (e) { /* not JSON: handled below */ }

				if (/\/login(\/|\?|$)/.test(res.url || '')) {
					throw fail('Sesi Anda berakhir. Silakan masuk lagi, lalu ulangi.', 'session', res.status);
				}
				if (data === null || typeof data !== 'object') {
					throw fail('Server mengalami gangguan' + (res.status >= 400 ? ' (HTTP ' + res.status + ')' : '') +
						'. Coba lagi; jika berulang, hubungi admin.', 'server', res.status);
				}
				if (!res.ok || data.ok === false) {
					throw fail(data.message || 'Permintaan ditolak server.', 'rejected', res.status, data);
				}
				return data;
			});
		}, function () {
			throw fail('Tidak bisa terhubung ke server. Periksa koneksi lalu coba lagi.', 'network', 0);
		});
	}

	return { postForm: postForm };
})();

(function () {
	var body = document.body;

	function closest(el, selector) {
		while (el && el !== document) {
			if (el.matches ? el.matches(selector) : el.msMatchesSelector(selector)) {
				return el;
			}
			el = el.parentNode;
		}
		return null;
	}

	function setNav(open) {
		if (open) {
			body.classList.add('nav-open');
		}
		else {
			body.classList.remove('nav-open');
		}
	}

	document.addEventListener('click', function (e) {
		if (closest(e.target, '[data-nav-open]')) {
			setNav(true);
			return;
		}
		if (closest(e.target, '[data-nav-close]')) {
			setNav(false);
			return;
		}

		var toggle = closest(e.target, '[data-nav-toggle]');
		if (toggle) {
			toggle.parentNode.classList.toggle('is-open');
			return;
		}

		if (closest(e.target, '[data-to-top]')) {
			window.scrollTo({ top: 0, behavior: 'smooth' });
		}
	});

	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && body.classList.contains('nav-open')) {
			setNav(false);
		}
	});

	/**
	 * On phones, list tables turn into one card per row. Only plain tables
	 * qualify: a single header row of <th> cells and body rows with the same
	 * number of <td> cells and no colspans. Reports are left to scroll.
	 */
	function prepareStackedTable(table) {
		if (body.classList.contains('page-laporan') || table.hasAttribute('data-no-stack')) {
			return;
		}
		var rows = table.rows;
		if (rows.length < 2) {
			return;
		}
		var headCells = rows[0].cells;
		var labels = [];
		for (var h = 0; h < headCells.length; h++) {
			if (headCells[h].tagName !== 'TH' || headCells[h].colSpan > 1) {
				return;
			}
			labels.push((headCells[h].textContent || '').replace(/\s+/g, ' ').trim());
		}
		if (labels.length < 2 || labels.length > 10) {
			return;
		}
		for (var r = 1; r < rows.length; r++) {
			var cells = rows[r].cells;
			if (cells.length !== labels.length) {
				return;
			}
			for (var c = 0; c < cells.length; c++) {
				if (cells[c].colSpan > 1 || cells[c].tagName !== 'TD') {
					return;
				}
			}
		}

		rows[0].classList.add('app-stack-head');
		for (var s = 1; s < rows.length; s++) {
			for (var d = 0; d < rows[s].cells.length; d++) {
				var cell = rows[s].cells[d];
				var label = labels[d];
				cell.setAttribute('data-label', label);
				if (/^(no|#)$/i.test(label)) {
					cell.classList.add('app-stack-index');
				}
				else if (/^(action|aksi|opsi)$/i.test(label) || (label === '' && cell.querySelector('.btn'))) {
					cell.classList.add('app-stack-actions');
				}
			}
		}
		table.classList.add('app-stack');
	}

	/**
	 * Some views lay out filter forms as "label | : | field" tables. Those are
	 * not data: wrapping them in a scroll box would clip dropdown menus, so
	 * they get their own form styling (and stack on phones) instead.
	 */
	function prepareFormTable(table) {
		if (table.querySelector('th') || !table.querySelector('select, textarea, input:not([type="hidden"])')) {
			return false;
		}
		table.classList.add('app-form-table');
		var cells = table.querySelectorAll('td');
		for (var f = 0; f < cells.length; f++) {
			var text = (cells[f].textContent || '').trim();
			if (text === ':' && !cells[f].children.length) {
				cells[f].classList.add('app-form-sep');
			}
			else if (!cells[f].querySelector('select, textarea, input, button, .btn') && text !== '') {
				cells[f].classList.add('app-form-label');
			}
		}
		return true;
	}

	// Wide tables scroll sideways inside their card instead of stretching the page.
	var tables = document.querySelectorAll('.app-content table.table');
	for (var i = 0; i < tables.length; i++) {
		var table = tables[i];
		if (prepareFormTable(table)) {
			continue;
		}
		prepareStackedTable(table);
		var buttonCells = table.querySelectorAll('td');
		for (var bc = 0; bc < buttonCells.length; bc++) {
			if (buttonCells[bc].querySelector('.btn')) {
				buttonCells[bc].classList.add('app-actions-cell');
			}
			else if (/^\s*Rp\.?\s/.test(buttonCells[bc].textContent)) {
				buttonCells[bc].classList.add('app-money-cell');
			}
		}
		var parent = table.parentNode;
		if (parent.classList.contains('table-responsive') || parent.classList.contains('app-table-wrap')) {
			continue;
		}
		var wrap = document.createElement('div');
		wrap.className = table.classList.contains('app-stack') ? 'app-table-wrap is-stack' : 'app-table-wrap';
		parent.insertBefore(wrap, table);
		wrap.appendChild(table);
	}

	// Give the legacy text-only buttons a matching icon, keyed by their label.
	var buttonIcons = {
		'tambah': 'fa-plus',
		'cari': 'fa-search',
		'filter': 'fa-filter',
		'lihat': 'fa-eye',
		'edit': 'fa-pencil',
		'update': 'fa-pencil',
		'delete': 'fa-trash-o',
		'hapus': 'fa-trash-o',
		'struk': 'fa-print',
		'cetak': 'fa-print',
		'print': 'fa-print',
		'reset': 'fa-undo',
		'simpan': 'fa-check',
		'kembali': 'fa-arrow-left',
		'cancel': 'fa-times',
		'batal': 'fa-times',
		'export': 'fa-download',
		'download': 'fa-download',
		'excel': 'fa-file-excel-o',
		'pdf': 'fa-file-pdf-o'
	};
	var buttons = document.querySelectorAll('.app-content a.btn, .app-content button.btn');
	for (var b = 0; b < buttons.length; b++) {
		var btn = buttons[b];
		if (btn.querySelector('i, img, .fa') || btn.hasAttribute('data-no-icon')) {
			continue;
		}
		var label = (btn.textContent || '').replace(/\s+/g, ' ').trim().toLowerCase();
		var icon = buttonIcons[label] || buttonIcons[label.split(' ')[0]];
		if (!icon) {
			continue;
		}
		var glyph = document.createElement('i');
		glyph.className = 'fa ' + icon;
		glyph.setAttribute('aria-hidden', 'true');
		btn.insertBefore(glyph, btn.firstChild);
	}

	var searchInputs = document.querySelectorAll('.app-content input[name="cari"]:not([placeholder])');
	for (var q = 0; q < searchInputs.length; q++) {
		searchInputs[q].setAttribute('placeholder', 'Cari…');
	}

	var toTop = document.querySelector('[data-to-top]');
	if (toTop) {
		window.addEventListener('scroll', function () {
			if (window.pageYOffset > 600) {
				toTop.classList.add('is-visible');
			}
			else {
				toTop.classList.remove('is-visible');
			}
		}, { passive: true });
	}
})();
