</div>
<?php if ($_SESSION['level']=="admin") { ?>
</div>
<?php } ?>
<!-- END CONTENT -->
</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<div class="page-footer">
    <div class="page-footer-inner text-center" style="
        color:#fff;
        width: 100%;
    ">
        <script>
        document.write(new Date().getFullYear())
        </script> &copy; Toko Oli 711
    </div>
    <div class="scroll-to-top">
        <i class="icon-arrow-up"></i>
    </div>
</div>
<!-- END FOOTER -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="<?=base_url();?>/assets/global/plugins/respond.min.js">
</script>
<script src="<?=base_url();?>/assets/global/plugins/excanvas.min.js">
</script>
<![endif]-->


<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js" type="text/javascript"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js" type="text/javascript"></script>

<script src="<?=base_url();?>assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="<?=base_url();?>assets/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?=base_url();?>assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?=base_url();?>assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js"
    type="text/javascript"></script>
<!-- <script src="<?=base_url();?>assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"> -->
</script>
<script src="<?=base_url();?>assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?=base_url();?>assets/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="<?=base_url();?>assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="<?=base_url();?>assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript">
</script>
<script src="<?=base_url();?>assets/bootstrap-select.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->

<!-- IMPORTANT! fullcalendar depends on jquery-ui.min.js for drag & drop support -->
<!-- <script src="<?=base_url();?>assets/global/plugins/morris/morris.min.js" type="text/javascript"></script> -->
<!-- <script src="<?=base_url();?>assets/global/plugins/morris/raphael-min.js" type="text/javascript"></script> -->
<script src="<?=base_url();?>assets/global/plugins/jquery.sparkline.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?=base_url();?>assets/global/scripts/metronic.js" type="text/javascript"></script>
<script src="<?=base_url();?>assets/admin/layout4/scripts/layout.js" type="text/javascript"></script>
<!-- <script src="<?=base_url();?>assets/admin/layout2/scripts/quick-sidebar.js" type="text/javascript"></script> -->
<script src="<?=base_url();?>assets/admin/layout4/scripts/demo.js" type="text/javascript"></script>
<script src="<?=base_url();?>assets/admin/pages/scripts/index3.js" type="text/javascript"></script>
<script src="<?=base_url();?>assets/admin/pages/scripts/tasks.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
jQuery(document).ready(function() {
    Metronic.init(); // init metronic core componets
    Layout.init(); // init layout
    Demo.init(); // init demo features
    // QuickSidebar.init(); // init quick sidebar
    // Index.init(); // init index page
    Tasks.initDashboardWidget(); // init tash dashboard widget
});
</script>

<script>
$("#bayar").on("change", function() {
    console.log($(this).val());
});

function kembalian() {
    console.log(document.getElementById("bayar").value);
    document.getElementById("kembali").value = document.getElementById("bayar").value - document.getElementById("total")
        .value
}
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->

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
</html>