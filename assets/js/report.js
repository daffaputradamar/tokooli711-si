/**
 * Toko Oli 711 - report (laporan) page behaviour.
 * Plain ES5; everything is opt-in through data-* attributes in the views.
 */
(function () {
	function pad(n) {
		return n < 10 ? '0' + n : String(n);
	}
	function ymd(d) {
		return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
	}
	function ym(d) {
		return d.getFullYear() + '-' + pad(d.getMonth() + 1);
	}
	function addDays(d, n) {
		var c = new Date(d.getTime());
		c.setDate(c.getDate() + n);
		return c;
	}
	function closest(el, selector) {
		while (el && el !== document) {
			if ((el.matches || el.msMatchesSelector).call(el, selector)) {
				return el;
			}
			el = el.parentNode;
		}
		return null;
	}
	function field(form, name) {
		return form.querySelector('[name="' + name + '"]');
	}
	function setValue(form, name, value) {
		var el = field(form, name);
		if (el) {
			el.value = value;
			el.dispatchEvent(new Event('change', { bubbles: true }));
		}
	}

	/* A titled bootstrap-select ("Pilih barang…") should start empty, not on the
	   first item; runs before the plugin initialises on window load. */
	var titled = document.querySelectorAll('select.selectpicker[title]');
	for (var s = 0; s < titled.length; s++) {
		if (!titled[s].querySelector('option[selected]')) {
			titled[s].selectedIndex = -1;
		}
	}

	/* ---------- Harian / Bulanan toggle: <input name="rad"> drives #mulai's type ---------- */
	function syncMode(form) {
		var checked = form.querySelector('input[name="rad"]:checked');
		var input = field(form, 'mulai');
		if (!checked || !input) {
			return;
		}
		var monthly = checked.value === '1';
		var current = input.value;
		var wanted = monthly ? 'month' : 'date';
		if (input.type !== wanted) {
			input.type = wanted;
			// Keep the chosen day/month when switching mode
			if (monthly && current.length >= 7) {
				input.value = current.substr(0, 7);
			}
			else if (!monthly && current.length === 7) {
				input.value = current + '-01';
			}
		}
	}

	var modeForms = document.querySelectorAll('form[data-period-form]');
	for (var i = 0; i < modeForms.length; i++) {
		syncMode(modeForms[i]);
	}

	document.addEventListener('change', function (e) {
		if (e.target.name === 'rad') {
			var form = closest(e.target, 'form[data-period-form]');
			if (form) {
				syncMode(form);
			}
		}

		/* Export buttons stay disabled until a year is picked */
		if (e.target.hasAttribute('data-enable-on-value')) {
			var scope = closest(e.target, '[data-export-scope]');
			if (scope) {
				var buttons = scope.querySelectorAll('[data-needs-year]');
				for (var b = 0; b < buttons.length; b++) {
					buttons[b].disabled = !e.target.value;
				}
			}
		}

		/* Optional description swap, e.g. "per supplier" vs "gabungan" */
		if (e.target.hasAttribute('data-toggle-desc')) {
			var on = document.getElementById(e.target.getAttribute('data-toggle-desc'));
			var off = document.getElementById(e.target.getAttribute('data-toggle-desc-off'));
			if (on && off) {
				on.classList.toggle('rpt-hidden', !e.target.checked);
				off.classList.toggle('rpt-hidden', e.target.checked);
			}
		}
	});

	/* ---------- Date presets ---------- */
	function presetRange(name) {
		var today = new Date();
		var first = new Date(today.getFullYear(), today.getMonth(), 1);
		switch (name) {
			case 'today':
				return { day: today };
			case 'yesterday':
				return { day: addDays(today, -1) };
			case 'this-month':
				return { month: today, from: first, to: today };
			case 'last-month':
				var lastFirst = new Date(today.getFullYear(), today.getMonth() - 1, 1);
				return { month: lastFirst, from: lastFirst, to: addDays(first, -1) };
			case '7d':
				return { from: addDays(today, -6), to: today };
			case '30d':
				return { from: addDays(today, -29), to: today };
		}
		return null;
	}

	document.addEventListener('click', function (e) {
		var chip = closest(e.target, '[data-preset]');
		if (chip) {
			var form = closest(chip, 'form');
			var range = presetRange(chip.getAttribute('data-preset'));
			if (!form || !range) {
				return;
			}

			if (form.hasAttribute('data-period-form')) {
				var monthly = !!range.month;
				var radio = form.querySelector('input[name="rad"][value="' + (monthly ? '1' : '0') + '"]');
				if (radio) {
					radio.checked = true;
					syncMode(form);
				}
				setValue(form, 'mulai', monthly ? ym(range.month) : ymd(range.day));
			}
			else {
				setValue(form, 'tgl_awal', ymd(range.from || range.day));
				setValue(form, 'tgl_akhir', ymd(range.to || range.day));
				if (field(form, 'wkt_awal')) {
					setValue(form, 'wkt_awal', '00:00');
					setValue(form, 'wkt_akhir', '23:59');
				}
			}

			var chips = form.querySelectorAll('[data-preset]');
			for (var c = 0; c < chips.length; c++) {
				chips[c].classList.toggle('is-active', chips[c] === chip);
			}

			if (chip.hasAttribute('data-submit')) {
				if (form.reportValidity && !form.reportValidity()) {
					return;
				}
				form.submit();
			}
			return;
		}

		/* Expand / collapse every transaction */
		var toggleAll = closest(e.target, '[data-expand-all]');
		if (toggleAll) {
			var list = document.querySelector(toggleAll.getAttribute('data-expand-all'));
			if (!list) {
				return;
			}
			var items = list.querySelectorAll('details:not(.rpt-hidden)');
			var open = toggleAll.getAttribute('aria-expanded') !== 'true';
			for (var d = 0; d < items.length; d++) {
				items[d].open = open;
			}
			toggleAll.setAttribute('aria-expanded', open ? 'true' : 'false');
			toggleAll.querySelector('span').textContent = open ? 'Tutup semua' : 'Buka semua';
		}
	});

	/* Picking a date by hand clears the highlighted preset */
	document.addEventListener('input', function (e) {
		if (/^(mulai|tgl_awal|tgl_akhir)$/.test(e.target.name)) {
			var form = closest(e.target, 'form');
			var chips = form ? form.querySelectorAll('[data-preset].is-active') : [];
			for (var c = 0; c < chips.length; c++) {
				chips[c].classList.remove('is-active');
			}
		}
	});

	/* ---------- Instant filter + batched reveal for long lists ----------
	 * Container:  <tbody id="x" data-page-size="100" data-noun="barang">
	 * Items:      any child with data-search="lowercase text" (optional data-scope)
	 * Search box: <input data-filter-rows="#x" data-filter-count="#countEl">
	 * Scope chip: <button data-row-scope="all|in|out" data-filter-for="#x">
	 * More:       <button data-show-more="#x">
	 * Empty note: <div data-filter-empty="#x">
	 * With no search text only the first page-size matches show; a search
	 * shows every match.
	 */
	function applyFilter(list) {
		var id = '#' + list.id;
		var input = document.querySelector('[data-filter-rows="' + id + '"]');
		var activeScope = document.querySelector('[data-filter-for="' + id + '"].is-active');
		var terms = input ? input.value.toLowerCase().split(/\s+/).filter(Boolean) : [];
		var scope = activeScope ? activeScope.getAttribute('data-row-scope') : 'all';
		var items = list.querySelectorAll('[data-search]');
		var pageSize = parseInt(list.getAttribute('data-page-size'), 10) || 0;
		var pages = parseInt(list.getAttribute('data-pages'), 10) || 1;
		var limit = (pageSize && !terms.length) ? pageSize * pages : Infinity;
		var matched = 0;
		var visible = 0;

		for (var r = 0; r < items.length; r++) {
			var item = items[r];
			var hay = item.getAttribute('data-search');
			var ok = scope === 'all' || item.getAttribute('data-scope') === scope;
			for (var t = 0; ok && t < terms.length; t++) {
				ok = hay.indexOf(terms[t]) !== -1;
			}
			if (ok) {
				matched++;
			}
			var show = ok && matched <= limit;
			item.classList.toggle('rpt-hidden', !show);
			if (show) {
				visible++;
			}
		}

		var noun = list.getAttribute('data-noun') || 'data';
		var fmt = function (n) { return n.toLocaleString('id-ID'); };
		var countEl = input ? document.querySelector(input.getAttribute('data-filter-count')) : null;
		if (countEl) {
			countEl.textContent = matched === items.length
				? fmt(items.length) + ' ' + noun
				: fmt(matched) + ' dari ' + fmt(items.length) + ' ' + noun;
		}
		var empty = document.querySelector('[data-filter-empty="' + id + '"]');
		if (empty) {
			empty.classList.toggle('rpt-hidden', matched !== 0);
		}
		var more = document.querySelector('[data-show-more="' + id + '"]');
		if (more) {
			var left = matched - visible;
			more.classList.toggle('rpt-hidden', left <= 0);
			more.querySelector('span').textContent = 'Tampilkan ' + fmt(Math.min(left, pageSize)) + ' lagi (sisa ' + fmt(left) + ')';
		}
	}

	var lists = document.querySelectorAll('[data-page-size]');
	for (var l = 0; l < lists.length; l++) {
		applyFilter(lists[l]);
	}

	var filterTimer = null;
	document.addEventListener('input', function (e) {
		var target = e.target.getAttribute && e.target.getAttribute('data-filter-rows');
		if (!target) {
			return;
		}
		clearTimeout(filterTimer);
		filterTimer = setTimeout(function () {
			var list = document.querySelector(target);
			if (list) {
				applyFilter(list);
			}
		}, 120);
	});

	document.addEventListener('click', function (e) {
		var more = closest(e.target, '[data-show-more]');
		if (more) {
			var moreList = document.querySelector(more.getAttribute('data-show-more'));
			if (moreList) {
				moreList.setAttribute('data-pages', (parseInt(moreList.getAttribute('data-pages'), 10) || 1) + 1);
				applyFilter(moreList);
			}
			return;
		}

		var btn = closest(e.target, '[data-row-scope]');
		if (!btn) {
			return;
		}
		var target = btn.getAttribute('data-filter-for');
		var siblings = document.querySelectorAll('[data-filter-for="' + target + '"]');
		for (var s = 0; s < siblings.length; s++) {
			siblings[s].classList.toggle('is-active', siblings[s] === btn);
		}
		var list = document.querySelector(target);
		if (list) {
			applyFilter(list);
		}
	});

	/* Busy state for forms that navigate in this tab. Skipped for downloads
	   (data-no-busy) and new-tab results, where the page never changes. */
	document.addEventListener('submit', function (e) {
		var form = e.target;
		if (form.getAttribute('target') === '_blank' || form.hasAttribute('data-no-busy')) {
			return;
		}
		var btn = e.submitter || form.querySelector('button[type="submit"]');
		if (btn) {
			// Disable on the next tick: a disabled submitter is left out of the
			// posted data, which would drop e.g. action=cutoff.
			setTimeout(function () {
				btn.setAttribute('data-label', btn.innerHTML);
				btn.disabled = true;
				btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memuat…';
			}, 0);
		}
	});

	/* Coming back with the browser's Back button restores the page from cache */
	window.addEventListener('pageshow', function () {
		var busy = document.querySelectorAll('button[data-label]');
		for (var b = 0; b < busy.length; b++) {
			busy[b].innerHTML = busy[b].getAttribute('data-label');
			busy[b].removeAttribute('data-label');
			busy[b].disabled = false;
		}
	});
})();
