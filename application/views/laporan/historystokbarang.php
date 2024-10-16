<!-- BEGIN SAMPLE FORM PORTLET-->
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-book"></i> Laporan History Stok Barang
        </div>
        <div class="tools">
            <a href="" class="collapse" data-original-title="" title="">
            </a>
            <a href="" class="reload" data-original-title="" title="">
            </a>
            <a href="" class="remove" data-original-title="" title="">
            </a>
        </div>
    </div>
    <div class="portlet-body">
        <br>
        <?= form_open('laporan/history_stok', 'class="form-inline" role="form"'); ?>
        <table class="table">
            <tr>
                <td>Nama Barang</td>
                <td>:</td>
                <td>
                    <select name="kode_barang" class="form-control selectpicker" data-live-search="true" style="width:300px" placeholder="kode_barang" required>
                        <?php
                        foreach ($barang as $komp) {
                            // Check if $kode_barang is set and use it for selected option
                            $selected = isset($kode_barang) && $kode_barang == $komp->kode_barang ? "selected" : "";
                        ?>
                            <option <?= $selected ?> value="<?= $komp->kode_barang ?>">
                                <?= $komp->nama_barang ?> [<?= $komp->merk ?>]
                            </option>
                        <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Tanggal Awal</td>
                <td>:</td>
                <td>
                    <div style="display: flex;">
                        <input type="date" class="form-control" id="tgl_awal" name="tgl_awal" value="<?= isset($tgl_awal) ? $tgl_awal : '' ?>" required>
                        <input type="time" class="form-control" id="wkt_awal" name="wkt_awal" value="<?= isset($wkt_awal) ? $wkt_awal : '' ?>" required>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Tanggal Akhir</td>
                <td>:</td>
                <td>
                    <div style="display: flex;">
                        <input type="date" class="form-control" id="tgl_akhir" name="tgl_akhir" value="<?= isset($tgl_akhir) ? $tgl_akhir : '' ?>" required>
                        <input type="time" class="form-control" id="wkt_akhir" name="wkt_akhir" value="<?= isset($wkt_akhir) ? $wkt_akhir : '' ?>" required>
                    </div>
                </td>
            </tr>
        </table>

        <div class="text-left">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
        </form>

        <?php
        if (count($history) > 0):
        ?>
            <table class="table table-striped table-bordered table-hover" style="margin: 50px 0">
                <tr>
                    <th>No</th>
                    <th>Stok</th>
                    <th>Tanggal Perubahan Stok</th>
                    <th>Action</th>
                </tr><?php
                        foreach ($history as $hist) {
                        ?>
                    <tr>
                        <td width="80px"><?php echo ++$start ?></td>
                        <td><?php echo $hist->stok ?></td>
                        <td><?php echo $hist->createddate ?></td>
                        <td style="text-align:center" width="250px">
                            <?php
                            echo anchor(site_url('laporan/history_stok/' . $hist->id .'/update'), 'Edit', 'class="btn btn-success"');
                            ?>
                        </td>
                    </tr>
                <?php
                        }
                ?>
            </table>
            <div class="row">
                <div class="col-md-6">
                    <a href="#" class="btn btn-primary">Total Data : <?php echo $total_rows ?></a>
                </div>
                <div class="col-md-6 text-right">
                    <?php echo $pagination ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>
<!-- END SAMPLE FORM PORTLET-->