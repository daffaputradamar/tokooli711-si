<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-refresh fa-fw"></i> <strong>Sinkronisasi Data</strong> - Backup Transaksi Antar Server
        </div>
        <div class="panel-body">
            <!-- Status Konfigurasi Sync -->
            <div class="row">
                <div class="col-md-12">
                    <div class="alert <?= $sync_enabled ? 'alert-success' : 'alert-warning' ?>" style="font-size: 13px;">
                        <i class="fa <?= $sync_enabled ? 'fa-check-circle' : 'fa-times-circle' ?> fa-lg"></i>
                        <strong>Status Kirim Data:</strong>
                        <?php if ($sync_enabled): ?>
                            Aplikasi ini <strong>AKTIF</strong> mengirim data ke server: <code><?= $sync_target_url ?></code>
                        <?php else: ?>
                            Aplikasi ini <strong>TIDAK AKTIF</strong> mengirim data ke server lain
                        <?php endif; ?>
                    </div>
                    <div class="alert <?= $sync_receive_enabled ? 'alert-info' : 'alert-warning' ?>" style="font-size: 13px;">
                        <i class="fa <?= $sync_receive_enabled ? 'fa-check-circle' : 'fa-times-circle' ?> fa-lg"></i>
                        <strong>Status Terima Data:</strong>
                        <?php if ($sync_receive_enabled): ?>
                            Aplikasi ini <strong>BISA</strong> menerima data dari server lain
                        <?php else: ?>
                            Aplikasi ini <strong>TIDAK BISA</strong> menerima data dari server lain
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <hr>

            <!-- Petunjuk Penggunaan -->
            <div class="alert alert-info" style="font-size: 13px;">
                <h4><i class="fa fa-info-circle"></i> Cara Penggunaan:</h4>
                <ol style="margin-bottom: 0;">
                    <li>Klik tombol <strong>"Cek Status Sinkronisasi"</strong> untuk melihat apakah ada transaksi yang belum tersinkron</li>
                    <li>Jika ada transaksi yang belum tersinkron (ditandai warna merah), klik tombol <strong>"Sinkronkan Semua"</strong></li>
                    <li>Tunggu proses selesai, lalu cek kembali untuk memastikan semua data sudah tersinkron</li>
                </ol>
            </div>

            <hr>

            <!-- Tombol Cek Otomatis -->
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-12 text-center">
                    <button class="btn btn-primary btn-lg" onclick="checkAllSync()" id="btn_check_all" style="padding: 12px 30px; font-size: 16px;">
                        <i class="fa fa-search"></i> Cek Status Sinkronisasi (Transaksi 3 Hari Terakhir)
                    </button>
                </div>
            </div>

            <hr>

            <!-- Hasil Pengecekan -->
            <div class="row">
                <!-- Penjualan -->
                <div class="col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading" style="font-size: 14px;">
                            <i class="fa fa-shopping-cart"></i> <strong>Transaksi Penjualan</strong>
                        </div>
                        <div class="panel-body">
                            <div id="penjualan_status" class="text-center" style="padding: 15px;">
                                <p class="text-muted" style="font-size: 14px;">
                                    <i class="fa fa-hand-pointer-o"></i> Klik tombol "Cek Status Sinkronisasi" di atas untuk memulai
                                </p>
                            </div>
                            
                            <div id="penjualan_result" style="display: none;">
                                <div id="penjualan_summary" style="font-size: 15px; padding: 15px; background: #f9f9f9; border-radius: 5px;"></div>
                            </div>
                            
                            <div id="penjualan_missing" style="margin-top: 15px; display: none;">
                                <div class="alert alert-danger" style="font-size: 13px;">
                                    <h4><i class="fa fa-exclamation-triangle"></i> Transaksi Belum Tersinkron:</h4>
                                    <div id="penjualan_missing_list" style="max-height: 200px; overflow-y: auto;"></div>
                                </div>
                                <button class="btn btn-danger btn-block btn-lg" onclick="syncMissingPenjualan()" id="btn_sync_penjualan" style="font-size: 14px;">
                                    <i class="fa fa-refresh"></i> Sinkronkan Semua Penjualan
                                </button>
                            </div>
                            
                            <div id="penjualan_ok" style="display: none;">
                                <div class="alert alert-success text-center" style="font-size: 18px; padding: 15px;">
                                    <i class="fa fa-check-circle fa-2x"></i><br><br>
                                    <strong>Semua transaksi penjualan sudah tersinkron!</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pembelian -->
                <div class="col-md-6">
                    <div class="panel panel-success">
                        <div class="panel-heading" style="font-size: 14px;">
                            <i class="fa fa-truck"></i> <strong>Transaksi Pembelian</strong>
                        </div>
                        <div class="panel-body">
                            <div id="pembelian_status" class="text-center" style="padding: 15px;">
                                <p class="text-muted" style="font-size: 14px;">
                                    <i class="fa fa-hand-pointer-o"></i> Klik tombol "Cek Status Sinkronisasi" di atas untuk memulai
                                </p>
                            </div>
                            
                            <div id="pembelian_result" style="display: none;">
                                <div id="pembelian_summary" style="font-size: 15px; padding: 15px; background: #f9f9f9; border-radius: 5px;"></div>
                            </div>
                            
                            <div id="pembelian_missing" style="margin-top: 15px; display: none;">
                                <div class="alert alert-danger" style="font-size: 13px;">
                                    <h4><i class="fa fa-exclamation-triangle"></i> Transaksi Belum Tersinkron:</h4>
                                    <div id="pembelian_missing_list" style="max-height: 200px; overflow-y: auto;"></div>
                                </div>
                                <button class="btn btn-danger btn-block btn-lg" onclick="syncMissingPembelian()" id="btn_sync_pembelian" style="font-size: 14px;">
                                    <i class="fa fa-refresh"></i> Sinkronkan Semua Pembelian
                                </button>
                            </div>
                            
                            <div id="pembelian_ok" style="display: none;">
                                <div class="alert alert-success text-center" style="font-size: 18px; padding: 15px;">
                                    <i class="fa fa-check-circle fa-2x"></i><br><br>
                                    <strong>Semua transaksi pembelian sudah tersinkron!</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Log Aktivitas -->
            <div class="row" style="margin-top: 20px;">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-list"></i> <strong>Log Aktivitas</strong>
                            <button class="btn btn-xs btn-default pull-right" onclick="clearLog()">
                                <i class="fa fa-trash"></i> Bersihkan Log
                            </button>
                        </div>
                        <div class="panel-body">
                            <div id="sync_log" style="max-height: 250px; overflow-y: auto; font-family: monospace; background: #2d2d2d; color: #fff; padding: 15px; border-radius: 5px; font-size: 13px;">
                                <p style="color: #888;">Log aktivitas akan muncul di sini...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pengaturan Lanjutan (Tersembunyi secara default) -->
            <div class="row" style="margin-top: 10px;">
                <div class="col-md-12">
                    <a data-toggle="collapse" href="#advancedSettings" style="font-size: 14px;">
                        <i class="fa fa-cog"></i> Pengaturan Lanjutan (Opsional)
                    </a>
                    <div id="advancedSettings" class="collapse" style="margin-top: 15px;">
                        <div class="well">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="sync_date">Tanggal Transaksi:</label>
                                        <input type="date" id="sync_date" class="form-control" value="">
                                        <small class="text-muted">Kosongkan untuk cek semua tanggal</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button class="btn btn-info btn-block" onclick="checkAllSync()">
                                            <i class="fa fa-search"></i> Cek Dengan Pengaturan Ini
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const BASE_URL = '<?= base_url() ?>';
    const SYNC_TARGET_URL = '<?= $sync_target_url ?>';
    const SYNC_ENABLED = <?= $sync_enabled ? 'true' : 'false' ?>;
    
    let missingPenjualanCodes = [];
    let missingPembelianCodes = [];
    
    function logMessage(message, type = 'info') {
        const logDiv = document.getElementById('sync_log');
        const timestamp = new Date().toLocaleTimeString('id-ID');
        const colors = {
            'info': '#3498db',
            'success': '#2ecc71',
            'error': '#e74c3c',
            'warning': '#f39c12'
        };
        
        const logEntry = document.createElement('div');
        logEntry.style.color = colors[type] || '#fff';
        logEntry.style.marginBottom = '5px';
        logEntry.innerHTML = '<span style="color: #888;">[' + timestamp + ']</span> ' + message;
        
        // Remove placeholder
        const placeholder = logDiv.querySelector('p');
        if (placeholder) {
            placeholder.remove();
        }
        
        logDiv.insertBefore(logEntry, logDiv.firstChild);
    }
    
    function clearLog() {
        document.getElementById('sync_log').innerHTML = '<p style="color: #888;">Log aktivitas akan muncul di sini...</p>';
    }
    
    async function checkAllSync() {
        const btn = document.getElementById('btn_check_all');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sedang Memeriksa...';
        
        // Reset displays
        document.getElementById('penjualan_status').style.display = 'none';
        document.getElementById('pembelian_status').style.display = 'none';
        document.getElementById('penjualan_result').style.display = 'none';
        document.getElementById('pembelian_result').style.display = 'none';
        document.getElementById('penjualan_missing').style.display = 'none';
        document.getElementById('pembelian_missing').style.display = 'none';
        document.getElementById('penjualan_ok').style.display = 'none';
        document.getElementById('pembelian_ok').style.display = 'none';
        
        logMessage('Memulai pengecekan sinkronisasi...', 'info');
        
        await checkPenjualanSync();
        await checkPembelianSync();
        
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-search"></i> Cek Status Sinkronisasi (Transaksi 3 Hari Terakhir)';
        
        logMessage('Pengecekan selesai!', 'success');
    }
    
    async function parseJsonSafe(response) {
        const text = await response.text();
        const cleaned = text.replace(/^\uFEFF/, '').trim();
        return JSON.parse(cleaned);
    }

    async function checkPenjualanSync() {
        // No limit; fetch all for selected days
        const dateInput = document.getElementById('sync_date').value;
        const dates = dateInput ? [dateInput] : [
            new Date().toISOString().slice(0,10),
            new Date(Date.now() - 24*60*60*1000).toISOString().slice(0,10),
            new Date(Date.now() - 2*24*60*60*1000).toISOString().slice(0,10)
        ];
        
        logMessage('Memeriksa penjualan untuk ' + (dateInput ? ('tanggal ' + dateInput) : '3 hari terakhir') + '...', 'info');
        
        try {
            let localCodes = [];
            let remoteCodes = [];
            
            for (const d of dates) {
                const localUrl = BASE_URL + 'penjualan/get_by_date?date=' + d;
                const localResponse = await fetch(localUrl);
                const localData = await parseJsonSafe(localResponse);
                localCodes = localCodes.concat(localData.data.map(item => item.kode_jual));
                
                const remoteUrl = SYNC_TARGET_URL + '/penjualan/get_by_date?date=' + d;
                const remoteResponse = await fetch(remoteUrl);
                const remoteData = await parseJsonSafe(remoteResponse);
                remoteCodes = remoteCodes.concat(remoteData.data.map(item => item.kode_jual));
            }
            
            localCodes = Array.from(new Set(localCodes));
            remoteCodes = Array.from(new Set(remoteCodes));
            
            const missingOnRemote = localCodes.filter(code => !remoteCodes.includes(code));
            
            document.getElementById('penjualan_result').style.display = 'block';
            document.getElementById('penjualan_summary').innerHTML = 
                '<p><i class="fa fa-database"></i> <strong>Server Ini:</strong> ' + localCodes.length + ' transaksi</p>' +
                '<p><i class="fa fa-cloud"></i> <strong>Server Tujuan:</strong> ' + remoteCodes.length + ' transaksi</p>';
            
            if (missingOnRemote.length > 0) {
                missingPenjualanCodes = missingOnRemote;
                document.getElementById('penjualan_missing').style.display = 'block';
                document.getElementById('penjualan_missing_list').innerHTML = missingOnRemote.map(code => 
                    '<span class="label label-danger" style="margin: 2px; display: inline-block;">' + code + '</span>'
                ).join(' ');
                logMessage('⚠️ ' + missingOnRemote.length + ' transaksi penjualan BELUM tersinkron!', 'warning');
            } else {
                document.getElementById('penjualan_ok').style.display = 'block';
                logMessage('✓ Semua transaksi penjualan sudah tersinkron', 'success');
            }
            
        } catch (error) {
            logMessage('Error: ' + error.message, 'error');
            document.getElementById('penjualan_result').style.display = 'block';
            document.getElementById('penjualan_summary').innerHTML = 
                '<div class="alert alert-danger">' +
                '<i class="fa fa-exclamation-triangle"></i> Gagal terhubung ke server. Pastikan koneksi internet stabil.' +
                '</div>';
        }
    }
    
    async function checkPembelianSync() {
        // No limit; fetch all for selected days
        const dateInput = document.getElementById('sync_date').value;
        const dates = dateInput ? [dateInput] : [
            new Date().toISOString().slice(0,10),
            new Date(Date.now() - 24*60*60*1000).toISOString().slice(0,10),
            new Date(Date.now() - 2*24*60*60*1000).toISOString().slice(0,10)
        ];
        
        logMessage('Memeriksa pembelian untuk ' + (dateInput ? ('tanggal ' + dateInput) : '3 hari terakhir') + '...', 'info');
        
        try {
            let localCodes = [];
            let remoteCodes = [];
            
            for (const d of dates) {
                const localUrl = BASE_URL + 'pembelian/get_by_date?date=' + d;
                const localResponse = await fetch(localUrl);
                const localData = await parseJsonSafe(localResponse);
                localCodes = localCodes.concat(localData.data.map(item => item.kode_beli));
                
                const remoteUrl = SYNC_TARGET_URL + '/pembelian/get_by_date?date=' + d;
                const remoteResponse = await fetch(remoteUrl);
                const remoteData = await parseJsonSafe(remoteResponse);
                remoteCodes = remoteCodes.concat(remoteData.data.map(item => item.kode_beli));
            }
            
            localCodes = Array.from(new Set(localCodes));
            remoteCodes = Array.from(new Set(remoteCodes));
            
            const missingOnRemote = localCodes.filter(code => !remoteCodes.includes(code));
            
            document.getElementById('pembelian_result').style.display = 'block';
            document.getElementById('pembelian_summary').innerHTML = 
                '<p><i class="fa fa-database"></i> <strong>Server Ini:</strong> ' + localCodes.length + ' transaksi</p>' +
                '<p><i class="fa fa-cloud"></i> <strong>Server Tujuan:</strong> ' + remoteCodes.length + ' transaksi</p>';
            
            if (missingOnRemote.length > 0) {
                missingPembelianCodes = missingOnRemote;
                document.getElementById('pembelian_missing').style.display = 'block';
                document.getElementById('pembelian_missing_list').innerHTML = missingOnRemote.map(code => 
                    '<span class="label label-danger" style="margin: 2px; display: inline-block;">' + code + '</span>'
                ).join(' ');
                logMessage('⚠️ ' + missingOnRemote.length + ' transaksi pembelian BELUM tersinkron!', 'warning');
            } else {
                document.getElementById('pembelian_ok').style.display = 'block';
                logMessage('✓ Semua transaksi pembelian sudah tersinkron', 'success');
            }
            
        } catch (error) {
            logMessage('Error: ' + error.message, 'error');
            document.getElementById('pembelian_result').style.display = 'block';
            document.getElementById('pembelian_summary').innerHTML = 
                '<div class="alert alert-danger">' +
                '<i class="fa fa-exclamation-triangle"></i> Gagal terhubung ke server. Pastikan koneksi internet stabil.' +
                '</div>';
        }
    }
    
    async function syncMissingPenjualan() {
        if (!SYNC_ENABLED) {
            alert('Maaf, fitur kirim data tidak aktif untuk aplikasi ini.\nHubungi administrator untuk mengaktifkan.');
            logMessage('Sinkronisasi dibatalkan: Fitur tidak aktif', 'error');
            return;
        }
        
        if (missingPenjualanCodes.length === 0) {
            logMessage('Tidak ada transaksi penjualan yang perlu disinkronkan', 'info');
            return;
        }
        
        if (!confirm('Anda akan menyinkronkan ' + missingPenjualanCodes.length + ' transaksi penjualan.\n\nLanjutkan?')) {
            return;
        }
        
        const btn = document.getElementById('btn_sync_penjualan');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sedang Menyinkronkan...';
        
        let successCount = 0;
        let failCount = 0;
        
        for (const code of missingPenjualanCodes) {
            try {
                logMessage('Mengirim transaksi ' + code + '...', 'info');
                
                // Get full data
                const dataResponse = await fetch(BASE_URL + 'sync/get_penjualan_for_sync?kode_jual=' + code);
                const dataResult = await parseJsonSafe(dataResponse);
                
                if (!dataResult.status) {
                    logMessage('Gagal mengambil data ' + code, 'error');
                    failCount++;
                    continue;
                }
                
                // Send to remote
                const syncResponse = await fetch(SYNC_TARGET_URL + '/penjualan/save_penjualan', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(dataResult.data),
                });
                
                const syncResult = await parseJsonSafe(syncResponse);
                
                if (syncResult.status) {
                    logMessage('✓ Berhasil: ' + code, 'success');
                    successCount++;
                } else {
                    logMessage('✗ Gagal: ' + code + ' - ' + syncResult.message, 'error');
                    failCount++;
                }
                
            } catch (error) {
                logMessage('✗ Error: ' + code + ' - ' + error.message, 'error');
                failCount++;
            }
        }
        
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-refresh"></i> Sinkronkan Semua Penjualan';
        
        // Show summary
        alert('Selesai!\n\nBerhasil: ' + successCount + ' transaksi\nGagal: ' + failCount + ' transaksi');
        logMessage('Sinkronisasi penjualan selesai. Berhasil: ' + successCount + ', Gagal: ' + failCount, successCount > 0 ? 'success' : 'error');
        
        // Refresh check
        if (successCount > 0) {
            setTimeout(function() { checkPenjualanSync(); }, 1000);
        }
    }
    
    async function syncMissingPembelian() {
        if (!SYNC_ENABLED) {
            alert('Maaf, fitur kirim data tidak aktif untuk aplikasi ini.\nHubungi administrator untuk mengaktifkan.');
            logMessage('Sinkronisasi dibatalkan: Fitur tidak aktif', 'error');
            return;
        }
        
        if (missingPembelianCodes.length === 0) {
            logMessage('Tidak ada transaksi pembelian yang perlu disinkronkan', 'info');
            return;
        }
        
        if (!confirm('Anda akan menyinkronkan ' + missingPembelianCodes.length + ' transaksi pembelian.\n\nLanjutkan?')) {
            return;
        }
        
        const btn = document.getElementById('btn_sync_pembelian');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sedang Menyinkronkan...';
        
        let successCount = 0;
        let failCount = 0;
        
        for (const code of missingPembelianCodes) {
            try {
                logMessage('Mengirim transaksi ' + code + '...', 'info');
                
                // Get full data
                const dataResponse = await fetch(BASE_URL + 'sync/get_pembelian_for_sync?kode_beli=' + code);
                const dataResult = await parseJsonSafe(dataResponse);
                
                if (!dataResult.status) {
                    logMessage('Gagal mengambil data ' + code, 'error');
                    failCount++;
                    continue;
                }
                
                // Send to remote
                const syncResponse = await fetch(SYNC_TARGET_URL + '/pembelian/save_pembelian', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(dataResult.data),
                });
                
                const syncResult = await parseJsonSafe(syncResponse);
                
                if (syncResult.status) {
                    logMessage('✓ Berhasil: ' + code, 'success');
                    successCount++;
                } else {
                    logMessage('✗ Gagal: ' + code + ' - ' + syncResult.message, 'error');
                    failCount++;
                }
                
            } catch (error) {
                logMessage('✗ Error: ' + code + ' - ' + error.message, 'error');
                failCount++;
            }
        }
        
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-refresh"></i> Sinkronkan Semua Pembelian';
        
        // Show summary
        alert('Selesai!\n\nBerhasil: ' + successCount + ' transaksi\nGagal: ' + failCount + ' transaksi');
        logMessage('Sinkronisasi pembelian selesai. Berhasil: ' + successCount + ', Gagal: ' + failCount, successCount > 0 ? 'success' : 'error');
        
        // Refresh check
        if (successCount > 0) {
            setTimeout(function() { checkPembelianSync(); }, 1000);
        }
    }
</script>
