<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-calendar-check-o"></i> Cut-off Stok Bulanan
        </div>
        <div class="tools">
            <a href="" class="collapse" data-original-title="" title=""></a>
        </div>
    </div>
    <div class="portlet-body">
        <br>

        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fa fa-check-circle"></i> <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-circle"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <!-- Cut-off form -->
        <?= form_open('laporan/stok_cutoff', 'role="form"'); ?>
        <div class="row">
            <div class="col-md-3">
                <label>Pilih Periode :</label>
            </div>
            <div class="col-md-4">
                <input type="month" name="periode" class="form-control" required>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-7 col-md-offset-3">
                <button type="submit" name="action" value="cutoff" class="btn btn-primary">
                    <i class="fa fa-scissors"></i> Proses Cut-off
                </button>
            </div>
        </div>
        </form>

        <hr>

        <!-- History table -->
        <h4><i class="fa fa-history"></i> Riwayat Cut-off</h4>
        <br>
        <?php if (empty($cutoff_list)): ?>
            <p class="text-muted">Belum ada data cut-off.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Jumlah Barang</th>
                        <th class="text-right">Total Nilai (Beli)</th>
                        <th class="text-right">Total Nilai (Jual)</th>
                        <th>Dibuat</th>
                        <th>Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cutoff_list as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row->periode) ?></td>
                        <td><?= $row->jumlah_barang ?></td>
                        <td class="text-right">Rp <?= number_format($row->total_nilai_beli, 0, ',', '.') ?></td>
                        <td class="text-right">Rp <?= number_format($row->total_nilai_jual, 0, ',', '.') ?></td>
                        <td><?= date('d-m-Y H:i', strtotime($row->created_at)) ?></td>
                        <td><?= htmlspecialchars($row->created_by) ?></td>
                        <td>
                            <a href="<?= base_url() ?>laporan/export_cutoff_pdf/<?= urlencode($row->periode) ?>"
                               target="_blank" class="btn btn-xs btn-danger" title="Lihat / Cetak PDF">
                                <i class="fa fa-file-pdf-o"></i> PDF
                            </a>
                            <a href="<?= base_url() ?>laporan/export_cutoff_csv/<?= urlencode($row->periode) ?>"
                               class="btn btn-xs btn-success" title="Export CSV">
                                <i class="fa fa-download"></i> CSV
                            </a>
                            <?= form_open('laporan/stok_cutoff', 'style="display:inline;"'); ?>
                                <input type="hidden" name="action"  value="delete">
                                <input type="hidden" name="periode" value="<?= htmlspecialchars($row->periode) ?>">
                                <button type="submit" class="btn btn-xs btn-default"
                                        onclick="return confirm('Hapus cut-off periode <?= htmlspecialchars($row->periode) ?>?')"
                                        title="Hapus">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
