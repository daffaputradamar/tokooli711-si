	</main>
	<footer class="app-footer">
		&copy; <?= date('Y') ?> Toko Oli 711
	</footer>
</div>
<button type="button" class="app-to-top" data-to-top aria-label="Kembali ke atas"><i class="fa fa-arrow-up"></i></button>

<!-- BEGIN CORE PLUGINS -->
<script src="<?= base_url() ?>assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/bootstrap-select.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/global/scripts/metronic.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<?php $server_toasts = take_toasts(); if ($server_toasts) { ?>
<script type="application/json" id="serverToasts"><?= str_replace('<', '<', json_encode($server_toasts)) ?></script>
<?php } ?>
<script src="<?= asset_url('assets/js/app.js') ?>" type="text/javascript"></script>
<?php if (strtolower($this->uri->segment(1)) == 'laporan') { ?>
<script src="<?= asset_url('assets/js/report.js') ?>" type="text/javascript"></script>
<?php } ?>
<?php if (strtolower($this->uri->segment(1)) == 'sync') { ?>
<script src="<?= asset_url('assets/js/sync.js') ?>" type="text/javascript"></script>
<?php } ?>
<script>
jQuery(document).ready(function() {
	Metronic.init(); // init metronic core components
});

function kembalian() {
	document.getElementById("kembali").value = document.getElementById("bayar").value - document.getElementById("total").value;
}
</script>

<script>
	// Header clock. The workstation only contributes elapsed time via the
	// monotonic performance.now(); both the absolute reading and the UTC
	// offset come from the server, so a PC with a wrong clock or timezone
	// still shows server time -- the same clock that stamps waktu_jual.
	(function () {
		var el = document.getElementById('server-time');
		if (!el) {
			return;
		}

		var epoch = parseInt(el.getAttribute('data-epoch'), 10);          // seconds, UTC
		var utcOffset = parseInt(el.getAttribute('data-utc-offset'), 10); // seconds
		var syncUrl = el.getAttribute('data-sync-url');
		var syncedAt = performance.now();

		if (isNaN(epoch) || isNaN(utcOffset)) {
			return;
		}

		function pad(n) {
			return n < 10 ? '0' + n : String(n);
		}

		function render() {
			var elapsed = Math.floor((performance.now() - syncedAt) / 1000);
			var secondsToday = (epoch + elapsed + utcOffset) % 86400;
			if (secondsToday < 0) {
				secondsToday += 86400;
			}
			el.textContent = pad(Math.floor(secondsToday / 3600)) + ':' +
				pad(Math.floor(secondsToday / 60) % 60) + ':' +
				pad(secondsToday % 60);
		}

		// Re-read the server periodically so a page left open all day cannot
		// drift, and so a machine waking from sleep corrects itself.
		function resync() {
			if (!syncUrl) {
				return;
			}
			var sentAt = performance.now();
			fetch(syncUrl, { credentials: 'same-origin' })
				.then(function (response) { return response.json(); })
				.then(function (data) {
					if (!data || typeof data.epoch !== 'number') {
						return;
					}
					epoch = data.epoch;
					utcOffset = data.utc_offset;
					syncedAt = sentAt + (performance.now() - sentAt) / 2; // half the round trip
					render();
				})
				.catch(function () { /* keep ticking from the last good reading */ });
		}

		render();
		setInterval(render, 1000);
		setInterval(resync, 60000);
	})();
</script>
</body>
</html>
