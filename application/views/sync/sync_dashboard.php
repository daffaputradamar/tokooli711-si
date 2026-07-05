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

            <!-- Sinkronisasi Stok Barang -->
            <hr>
            <div class="row" style="margin-top: 20px;">
                <div class="col-md-12">
                    <div class="panel panel-warning">
                        <div class="panel-heading" style="font-size: 14px;">
                            <i class="fa fa-cubes"></i> <strong>Sinkronisasi Stok Barang</strong>
                            <span class="label label-default pull-right" style="margin-top: 2px; font-size: 12px;">Perbandingan Stok Barang</span>
                        </div>
                        <div class="panel-body">
                            <div class="row" style="margin-bottom: 15px;">
                                <div class="col-md-12 text-center">
                                    <button class="btn btn-warning btn-lg" onclick="checkStokSync()" id="btn_check_stok" style="padding: 10px 25px; font-size: 15px;">
                                        <i class="fa fa-search"></i> Cek Perbedaan Stok
                                    </button>
                                </div>
                            </div>

                            <div id="stok_status" class="text-center" style="padding: 15px;">
                                <p class="text-muted" style="font-size: 14px;">
                                    <i class="fa fa-hand-pointer-o"></i> Klik tombol di atas untuk membandingkan stok antar server
                                </p>
                            </div>

                            <div id="stok_result" style="display: none;">
                                <div id="stok_summary" style="font-size: 14px; padding: 12px; background: #f9f9f9; border-radius: 5px; margin-bottom: 10px;"></div>

                                <div id="stok_ok" style="display: none;">
                                    <div class="alert alert-success text-center" style="font-size: 16px; padding: 15px;">
                                        <i class="fa fa-check-circle fa-2x"></i><br><br>
                                        <strong>Stok semua barang sudah sama di kedua server!</strong>
                                    </div>
                                </div>

                                <div id="stok_diff" style="display: none;">
                                    <div class="alert alert-warning" style="font-size: 13px; margin-bottom: 10px;">
                                        <i class="fa fa-exclamation-triangle"></i>
                                    <strong id="stok_diff_count"></strong> barang memiliki perbedaan stok.
                                    </div>
                                    <div style="overflow-x: auto;">
                                        <table class="table table-bordered table-condensed" style="font-size: 13px;" id="stok_diff_table">
                                            <thead style="background: #f5f5f5;">
                                                <tr>
                                                    <th>Kode Barang</th>
                                                    <th>Nama Barang</th>
                                                    <th class="text-center">Stok (Sini)</th>
                                                    <th class="text-center">Stok (Tujuan)</th>
                                                </tr>
                                            </thead>
                                            <tbody id="stok_diff_tbody"></tbody>
                                        </table>
                                    </div>
                                    <div class="text-center" style="margin-top: 10px;">
                                        <button class="btn btn-danger btn-lg" onclick="pushStokToRemote()" id="btn_push_stok" style="font-size: 14px; margin-right: 10px;">
                                            <i class="fa fa-upload"></i> Kirim Stok Server Ini ke Server Tujuan
                                        </button>
                                        <button class="btn btn-info btn-lg" onclick="pullStokFromRemote()" id="btn_pull_stok" style="font-size: 14px;">
                                            <i class="fa fa-download"></i> Ambil Stok dari Server Tujuan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sinkronisasi Harga Barang -->
            <hr>
            <div class="row" style="margin-top: 20px;">
                <div class="col-md-12">
                    <div class="panel panel-danger">
                        <div class="panel-heading" style="font-size: 14px;">
                            <i class="fa fa-money"></i> <strong>Sinkronisasi Harga Barang</strong>
                            <span class="label label-default pull-right" style="margin-top: 2px; font-size: 12px;">Harga Beli &amp; Harga Jual</span>
                        </div>
                        <div class="panel-body">
                            <div class="alert alert-info" style="font-size: 13px;">
                                <i class="fa fa-info-circle"></i>
                                Saat menerima update <strong>harga_beli</strong>, sistem otomatis menghitung <strong>harga_jual</strong>
                                = (harga_beli + 10%) dan dibulatkan ke ratusan terdekat.
                            </div>
                            <div class="row" style="margin-bottom: 15px;">
                                <div class="col-md-12 text-center">
                                    <button class="btn btn-danger btn-lg" onclick="checkHargaSync()" id="btn_check_harga" style="padding: 10px 25px; font-size: 15px;">
                                        <i class="fa fa-search"></i> Cek Perbedaan Harga
                                    </button>
                                </div>
                            </div>

                            <div id="harga_status" class="text-center" style="padding: 15px;">
                                <p class="text-muted" style="font-size: 14px;">
                                    <i class="fa fa-hand-pointer-o"></i> Klik tombol di atas untuk membandingkan harga antar server
                                </p>
                            </div>

                            <div id="harga_result" style="display: none;">
                                <div id="harga_summary" style="font-size: 14px; padding: 12px; background: #f9f9f9; border-radius: 5px; margin-bottom: 10px;"></div>

                                <div id="harga_ok" style="display: none;">
                                    <div class="alert alert-success text-center" style="font-size: 16px; padding: 15px;">
                                        <i class="fa fa-check-circle fa-2x"></i><br><br>
                                        <strong>Harga semua barang sudah sama di kedua server!</strong>
                                    </div>
                                </div>

                                <div id="harga_diff" style="display: none;">
                                    <div class="alert alert-warning" style="font-size: 13px; margin-bottom: 10px;">
                                        <i class="fa fa-exclamation-triangle"></i>
                                    <strong id="harga_diff_count"></strong> barang memiliki perbedaan harga.
                                    </div>
                                    <div style="overflow-x: auto;">
                                        <table class="table table-bordered table-condensed" style="font-size: 13px;" id="harga_diff_table">
                                            <thead style="background: #f5f5f5;">
                                                <tr>
                                                    <th>Kode Barang</th>
                                                    <th>Nama Barang</th>
                                                    <th class="text-center">Harga Beli (Sini)</th>
                                                    <th class="text-center">Harga Beli (Tujuan)</th>
                                                    <th class="text-center">Harga Jual (Sini)</th>
                                                    <th class="text-center">Harga Jual (Tujuan)</th>
                                                </tr>
                                            </thead>
                                            <tbody id="harga_diff_tbody"></tbody>
                                        </table>
                                    </div>
                                    <div class="text-center" style="margin-top: 10px;">
                                        <button class="btn btn-danger btn-lg" onclick="pushHargaToRemote()" id="btn_push_harga" style="font-size: 14px; margin-right: 10px;">
                                            <i class="fa fa-upload"></i> Kirim Harga Server Ini ke Server Tujuan
                                        </button>
                                        <button class="btn btn-info btn-lg" onclick="pullHargaFromRemote()" id="btn_pull_harga" style="font-size: 14px;">
                                            <i class="fa fa-download"></i> Ambil Harga dari Server Tujuan
                                        </button>
                                    </div>
                                </div>
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

    // ── Stok Sync ──────────────────────────────────────────────────────────────

    let localStokData  = [];
    let remoteStokData = [];
    let stokDiffItems  = [];

    function formatRupiah(val) {
        return 'Rp ' + parseInt(val).toLocaleString('id-ID');
    }

    async function checkStokSync() {
        const btn = document.getElementById('btn_check_stok');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sedang Memeriksa...';

        document.getElementById('stok_status').style.display    = 'none';
        document.getElementById('stok_result').style.display    = 'none';
        document.getElementById('stok_ok').style.display        = 'none';
        document.getElementById('stok_diff').style.display      = 'none';

        logMessage('Memeriksa perbedaan stok barang...', 'info');

        try {
            const localRes  = await fetch(BASE_URL + 'sync/get_all_barang_stock');
            const localData = await parseJsonSafe(localRes);

            const remoteRes  = await fetch(SYNC_TARGET_URL + '/sync/get_all_barang_stock');
            const remoteData = await parseJsonSafe(remoteRes);

            if (!localData.status || !remoteData.status) {
                throw new Error('Gagal mengambil data stok: ' + (!localData.status ? 'server lokal' : 'server tujuan'));
            }

            localStokData  = localData.data;
            remoteStokData = remoteData.data;

            const remoteMap = {};
            remoteData.data.forEach(function(item) { remoteMap[item.kode_barang] = item; });

            stokDiffItems = [];
            localData.data.forEach(function(local) {
                const remote = remoteMap[local.kode_barang];
                const stokDiff  = !remote || parseInt(local.stok) !== parseInt(remote.stok);
                if (stokDiff) {
                    stokDiffItems.push({
                        kode_barang:  local.kode_barang,
                        nama_barang:  local.nama_barang,
                        local_stok:   local.stok,
                        remote_stok:  remote ? remote.stok : 'N/A',
                    });
                }
            });

            document.getElementById('stok_result').style.display = 'block';
            document.getElementById('stok_summary').innerHTML =
                '<p><i class="fa fa-database"></i> <strong>Server Ini:</strong> '   + localData.count  + ' barang</p>' +
                '<p><i class="fa fa-cloud"></i> <strong>Server Tujuan:</strong> ' + remoteData.count + ' barang</p>';

            if (stokDiffItems.length === 0) {
                document.getElementById('stok_ok').style.display   = 'block';
                logMessage('✓ Stok semua barang sudah sama di kedua server', 'success');
            } else {
                document.getElementById('stok_diff').style.display = 'block';
                document.getElementById('stok_diff_count').textContent = stokDiffItems.length;

                const tbody = document.getElementById('stok_diff_tbody');
                tbody.innerHTML = '';
                stokDiffItems.forEach(function(item) {
                    const tr = document.createElement('tr');
                    tr.innerHTML =
                        '<td><code>' + item.kode_barang + '</code></td>' +
                        '<td>' + item.nama_barang + '</td>' +
                        '<td class="text-center bg-danger text-white">' + item.local_stok  + '</td>' +
                        '<td class="text-center bg-danger text-white">' + item.remote_stok + '</td>';
                    tbody.appendChild(tr);
                });
                logMessage('⚠️ Ditemukan ' + stokDiffItems.length + ' barang dengan perbedaan stok', 'warning');
            }

        } catch (error) {
            document.getElementById('stok_result').style.display = 'block';
            document.getElementById('stok_summary').innerHTML =
                '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' + error.message + '</div>';
            logMessage('Error saat cek stok: ' + error.message, 'error');
        }

        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-search"></i> Cek Perbedaan Stok';
    }

    async function pushStokToRemote() {
        if (!SYNC_ENABLED) {
            alert('Maaf, fitur kirim data tidak aktif untuk aplikasi ini.\nHubungi administrator untuk mengaktifkan.');
            return;
        }

        if (localStokData.length === 0) {
            alert('Lakukan pengecekan terlebih dahulu.');
            return;
        }

        if (!confirm('Anda akan mengirim data stok dari server ini ke server tujuan (' + SYNC_TARGET_URL + ').\n\nStok di server tujuan akan ditimpa.\n\nLanjutkan?')) {
            return;
        }

        const btn = document.getElementById('btn_push_stok');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mengirim...';
        logMessage('Mengirim stok ke server tujuan...', 'info');

        try {
            const res    = await fetch(SYNC_TARGET_URL + '/sync/receive_barang_stock', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify({ items: localStokData }),
            });
            const result = await parseJsonSafe(res);

            if (result.status) {
                const msg = 'Berhasil update ' + result.updated + ' barang, lewati ' + result.skipped + ' barang';
                alert('Selesai!\n\n' + msg + (result.errors.length ? '\n\nItem tidak ditemukan di server tujuan:\n' + result.errors.join(', ') : ''));
                logMessage('✓ Push stok selesai: ' + msg, 'success');
                setTimeout(checkStokSync, 800);
            } else {
                alert('Gagal: ' + result.message);
                logMessage('✗ Push stok gagal: ' + result.message, 'error');
            }
        } catch (error) {
            alert('Error: ' + error.message);
            logMessage('✗ Error push stok: ' + error.message, 'error');
        }

        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-upload"></i> Kirim Stok Server Ini ke Server Tujuan';
    }

    async function pullStokFromRemote() {
        if (remoteStokData.length === 0) {
            alert('Lakukan pengecekan terlebih dahulu.');
            return;
        }

        if (!confirm('Anda akan mengambil data stok dari server tujuan (' + SYNC_TARGET_URL + ') ke server ini.\n\nStok di server ini akan ditimpa.\n\nLanjutkan?')) {
            return;
        }

        const btn = document.getElementById('btn_pull_stok');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mengambil...';
        logMessage('Mengambil stok dari server tujuan...', 'info');

        try {
            const res    = await fetch(BASE_URL + 'sync/receive_barang_stock', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify({ items: remoteStokData }),
            });
            const result = await parseJsonSafe(res);

            if (result.status) {
                const msg = 'Berhasil update ' + result.updated + ' barang, lewati ' + result.skipped + ' barang';
                alert('Selesai!\n\n' + msg + (result.errors.length ? '\n\nItem tidak ditemukan:\n' + result.errors.join(', ') : ''));
                logMessage('✓ Pull stok selesai: ' + msg, 'success');
                setTimeout(checkStokSync, 800);
            } else {
                alert('Gagal: ' + result.message);
                logMessage('✗ Pull stok gagal: ' + result.message, 'error');
            }
        } catch (error) {
            alert('Error: ' + error.message);
            logMessage('✗ Error pull stok: ' + error.message, 'error');
        }

        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-download"></i> Ambil Stok dari Server Tujuan';
    }

    // ── Harga Sync ─────────────────────────────────────────────────────────────

    let localHargaData  = [];
    let remoteHargaData = [];
    let hargaDiffItems  = [];

    async function checkHargaSync() {
        const btn = document.getElementById('btn_check_harga');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sedang Memeriksa...';

        document.getElementById('harga_status').style.display   = 'none';
        document.getElementById('harga_result').style.display   = 'none';
        document.getElementById('harga_ok').style.display       = 'none';
        document.getElementById('harga_diff').style.display     = 'none';

        logMessage('Memeriksa perbedaan harga barang...', 'info');

        try {
            const localRes  = await fetch(BASE_URL + 'sync/get_all_barang_harga');
            const localData = await parseJsonSafe(localRes);

            const remoteRes  = await fetch(SYNC_TARGET_URL + '/sync/get_all_barang_harga');
            const remoteData = await parseJsonSafe(remoteRes);

            if (!localData.status || !remoteData.status) {
                throw new Error('Gagal mengambil data harga: ' + (!localData.status ? 'server lokal' : 'server tujuan'));
            }

            localHargaData  = localData.data;
            remoteHargaData = remoteData.data;

            const remoteMap = {};
            remoteData.data.forEach(function(item) { remoteMap[item.kode_barang] = item; });

            hargaDiffItems = [];
            localData.data.forEach(function(local) {
                const remote = remoteMap[local.kode_barang];
                const hargaDiff = !remote ||
                    parseInt(local.harga_beli) !== parseInt(remote.harga_beli) ||
                    parseInt(local.harga_jual) !== parseInt(remote.harga_jual);
                if (hargaDiff) {
                    hargaDiffItems.push({
                        kode_barang:      local.kode_barang,
                        nama_barang:      local.nama_barang,
                        local_harga_beli:  local.harga_beli,
                        remote_harga_beli: remote ? remote.harga_beli : 'N/A',
                        local_harga_jual:  local.harga_jual,
                        remote_harga_jual: remote ? remote.harga_jual : 'N/A',
                    });
                }
            });

            document.getElementById('harga_result').style.display = 'block';
            document.getElementById('harga_summary').innerHTML =
                '<p><i class="fa fa-database"></i> <strong>Server Ini:</strong> '   + localData.count  + ' barang</p>' +
                '<p><i class="fa fa-cloud"></i> <strong>Server Tujuan:</strong> ' + remoteData.count + ' barang</p>';

            if (hargaDiffItems.length === 0) {
                document.getElementById('harga_ok').style.display  = 'block';
                logMessage('✓ Harga semua barang sudah sama di kedua server', 'success');
            } else {
                document.getElementById('harga_diff').style.display = 'block';
                document.getElementById('harga_diff_count').textContent = hargaDiffItems.length;

                const tbody = document.getElementById('harga_diff_tbody');
                tbody.innerHTML = '';
                hargaDiffItems.forEach(function(item) {
                    const tr = document.createElement('tr');
                    tr.innerHTML =
                        '<td><code>' + item.kode_barang + '</code></td>' +
                        '<td>' + item.nama_barang + '</td>' +
                        '<td class="text-right bg-danger text-white">' + formatRupiah(item.local_harga_beli)  + '</td>' +
                        '<td class="text-right bg-danger text-white">' + formatRupiah(item.remote_harga_beli) + '</td>' +
                        '<td class="text-right bg-danger text-white">' + formatRupiah(item.local_harga_jual)  + '</td>' +
                        '<td class="text-right bg-danger text-white">' + formatRupiah(item.remote_harga_jual) + '</td>';
                    tbody.appendChild(tr);
                });
                logMessage('⚠️ Ditemukan ' + hargaDiffItems.length + ' barang dengan perbedaan harga', 'warning');
            }

        } catch (error) {
            document.getElementById('harga_result').style.display = 'block';
            document.getElementById('harga_summary').innerHTML =
                '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' + error.message + '</div>';
            logMessage('Error saat cek harga: ' + error.message, 'error');
        }

        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-search"></i> Cek Perbedaan Harga';
    }

    async function pushHargaToRemote() {
        if (!SYNC_ENABLED) {
            alert('Maaf, fitur kirim data tidak aktif untuk aplikasi ini.\nHubungi administrator untuk mengaktifkan.');
            return;
        }

        if (localHargaData.length === 0) {
            alert('Lakukan pengecekan terlebih dahulu.');
            return;
        }

        if (!confirm('Anda akan mengirim data harga dari server ini ke server tujuan (' + SYNC_TARGET_URL + ').\n\nHarga di server tujuan akan ditimpa dan harga_jual akan dihitung otomatis (+10%, dibulatkan).\n\nLanjutkan?')) {
            return;
        }

        const btn = document.getElementById('btn_push_harga');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mengirim...';
        logMessage('Mengirim harga ke server tujuan...', 'info');

        try {
            const res    = await fetch(SYNC_TARGET_URL + '/sync/receive_barang_harga', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify({ items: localHargaData }),
            });
            const result = await parseJsonSafe(res);

            if (result.status) {
                const msg = 'Berhasil update ' + result.updated + ' barang, lewati ' + result.skipped + ' barang';
                alert('Selesai!\n\n' + msg + (result.errors.length ? '\n\nItem tidak ditemukan di server tujuan:\n' + result.errors.join(', ') : ''));
                logMessage('✓ Push harga selesai: ' + msg, 'success');
                setTimeout(checkHargaSync, 800);
            } else {
                alert('Gagal: ' + result.message);
                logMessage('✗ Push harga gagal: ' + result.message, 'error');
            }
        } catch (error) {
            alert('Error: ' + error.message);
            logMessage('✗ Error push harga: ' + error.message, 'error');
        }

        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-upload"></i> Kirim Harga Server Ini ke Server Tujuan';
    }

    async function pullHargaFromRemote() {
        if (remoteHargaData.length === 0) {
            alert('Lakukan pengecekan terlebih dahulu.');
            return;
        }

        if (!confirm('Anda akan mengambil data harga dari server tujuan (' + SYNC_TARGET_URL + ') ke server ini.\n\nHarga di server ini akan ditimpa dan harga_jual akan dihitung otomatis (+10%, dibulatkan).\n\nLanjutkan?')) {
            return;
        }

        const btn = document.getElementById('btn_pull_harga');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mengambil...';
        logMessage('Mengambil harga dari server tujuan...', 'info');

        try {
            const res    = await fetch(BASE_URL + 'sync/receive_barang_harga', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify({ items: remoteHargaData }),
            });
            const result = await parseJsonSafe(res);

            if (result.status) {
                const msg = 'Berhasil update ' + result.updated + ' barang, lewati ' + result.skipped + ' barang';
                alert('Selesai!\n\n' + msg + (result.errors.length ? '\n\nItem tidak ditemukan:\n' + result.errors.join(', ') : ''));
                logMessage('✓ Pull harga selesai: ' + msg, 'success');
                setTimeout(checkHargaSync, 800);
            } else {
                alert('Gagal: ' + result.message);
                logMessage('✗ Pull harga gagal: ' + result.message, 'error');
            }
        } catch (error) {
            alert('Error: ' + error.message);
            logMessage('✗ Error pull harga: ' + error.message, 'error');
        }

        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-download"></i> Ambil Harga dari Server Tujuan';
    }
</script>
