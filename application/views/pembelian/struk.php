<script>
    window.print()
</script>
<link href="<?= base_url(); ?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<style>
    * {
        font-size: 12px;
    }
</style>
<table class="table" style="width:500px;">
    <tr>
        <td>Kode Beli</td>
        <td><?php echo $kode_beli; ?></td>
    </tr>
    <tr>
        <td>Tanggal Beli</td>
        <td><?php echo $tanggal_beli . ' ' . $waktu_beli; ?></td>
    </tr>
    <tr>
        <td>Kode Admin</td>
        <td><?php echo $kode_admin; ?></td>
    </tr>
    <tr>
        <td>Kode Suplier</td>
        <td><?php echo $kode_suplier; ?></td>
    </tr>
    <tr>
        <td colspan="3">
            <table class="table table-bordered table-hover table-striped">
                <tr>

                    <th>Nama Barang</th>
                    
                    <?php if ($_SESSION['level'] == 'admin') { ?>
                        <th>Harga Beli</th>
                    <?php } ?>
                    
                    <th>Jumlah</th>
                    
                    <?php if ($_SESSION['level'] == 'admin') { ?>
                        <th>Subtotal</th>
                    <?php } ?>
                    
                </tr><?php $start = 0;
                        foreach ($listdetail as $pembelian_detail) {
                        ?>
                    <tr>

                        <td><?php echo $pembelian_detail->nama_barang ?></td>
                        
                        <?php if ($_SESSION['level'] == 'admin') { ?>
                            <td><?php echo $pembelian_detail->harga_beli ?></td>
                        <?php } ?>
                        
                        <td><?php echo $pembelian_detail->jumlah ?></td>
                        
                        <?php if ($_SESSION['level'] == 'admin') { ?>
                            <td>Rp. <?php echo $this->CodeGenerator->rp($pembelian_detail->subtotal) ?></td>
                        <?php } ?>
                    </tr>
                <?php
                        }
                ?>

            </table>
        </td>
    </tr>
    <?php if ($_SESSION['level'] == 'admin') { ?>
    <tr>
        <td colspan="3">
            <div class="form-group">
                <p align="right">
                    <?php if ($this->uri->segment(2) == "insert") { ?>
                        <label for="">Total : Rp. <?= $this->CodeGenerator->rp($this->Pembelian_detail_model->totalall($this->CodeGenerator->buatkode('pembelian', 'kode_beli', 10, 'TRB'))); ?></label>
                    <?php } else { ?>
                        <label for="">Total : Rp. <?= $this->CodeGenerator->rp($this->Pembelian_detail_model->totalall($this->uri->segment(3))); ?></label>
                    <?php } ?>
                </p>
            </div>

        </td>
    </tr>
    <?php } ?>



</table>