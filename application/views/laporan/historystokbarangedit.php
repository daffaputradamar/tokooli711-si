<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-bell fa-fw"></i> History Stok Barang
        </div>

        <div class="panel-body">

            <?php 
            echo form_open("laporan/history_stok/{$history->id}/update");?>

            <input type="hidden" name="id" value="<?= $history->id; ?>" />

            <div class="form-group">
                <label for="text">Kode Barang</label>
                <input type="text" class="form-control" name="kode_barang" id="kode_barang" placeholder="Kode Barang"
                    value="<?= $history->kode_barang; ?>" readonly/>
            </div>


            <div class="form-group">
                <label for=>Nama Barang</label>
                <input type="text" class="form-control" name="nama_barang" id="nama_barang" placeholder="Nama Barang"
                    value="<?= $history->nama_barang; ?>" readonly />
            </div>
            <div class="form-group">
                <label for=>Stok</label>
                <input type="number" step="any" class="form-control" name="stok" id="stok" placeholder="Stok"
                    value="<?= $history->stok; ?>" />
            </div>
            <div class="form-group">
                <label for=>Tanggal Update Stok</label>
                <input type="datetime" class="form-control" name="createddate" id="createddate" placeholder="tanggal update stok"
                    value="<?= $history->createddate; ?>" readonly/>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?php echo site_url('laporan/history_stok') ?>" class="btn btn-default">Cancel</a>
            </form>
        </div>

    </div>
</div>