<script>window.print()</script>
<style>
    * {
        font-family:"arial";
        margin:0px;
        font-size:12px;
    }
    @media print {
      @page { margin: 0; }
    }
</style>
<table style="width:275px;" >
    <tr>
            <td  colspan="3" align="center">
                <h3>TOKO OLI 711</h3>
                <h4>Jl. by pass klaten selatan</h4>
                <h4>Telp. 081332629711</h4>
            </td>
    </tr>
    <tr>
            <td colspan="3">-----------------------------------------------------</td>
    </tr>
    <tr>
        
        <td><?php echo $kode_jual; ?></td>
        <td><?php echo $tanggal_jual; ?></td>
        <td><?php echo date("h:i:s a"); ?></td>
    </tr>
    <tr>
        <td colspan="3">-----------------------------------------------------</td>
    </tr>
    <tr>
        <td colspan="3">
            <table >
                <?php $start = 0;
                foreach ($listdetail as $penjualandetail) {
                    ?>
                    <tr>
                        <td><?php echo $penjualandetail->nama_barang ?></td>
                        <td><?php echo $penjualandetail->jumlah." x ".$penjualandetail->harga_jual ?></td>
                        <td>Rp. <?php echo $this->CodeGenerator->rp($penjualandetail->subtotal) ?></td>

                    </tr>
                    <?php
                }
                ?>

            </table>
        </td>
    </tr>
    <tr>
    <td colspan="3">-----------------------------------------------------</td>
    </tr>
    <tr>
        <td colspan="3">

                <div class="row">
                <div class="col-md-10 pull-right" align="right"><b>Bea lain-lain =
                        Rp. <?php echo $this->CodeGenerator->rp($ongkos_karyawan); ?></b></div>
            </div>
            <div class="row">
                <div class="col-md-10 pull-right" align="right"><b>Total =
                        Rp. <?php echo $this->CodeGenerator->rp($total); ?></b></div>
            </div>
            <div class="row">
                <div class="col-md-10 pull-right" align="right"><b>Bayar =
                        Rp. <?php echo $this->CodeGenerator->rp($bayar); ?></b></div>
            </div>
            <div class="row">
                <div class="col-md-10 pull-right" align="right"><b>Kembali =
                        Rp. <?php echo $this->CodeGenerator->rp($bayar - $total); ?></b></div>
            </div>
            <div class="panel panel-default">

            <div class="panel-body">

            <div class="form-group col-md-12">
            <label colspan="3">-----------------------------------------------------</label>
                <label for=>Kasir : <b><?php echo isset($kasir->nama_karyawan) ? $kasir->nama_karyawan : $kasir->nama_admin; ?></b></label><br>
                <label for=>Pelanggan : <b><?php echo $pelanggan; ?></b></label><br>
                <label for=>Nomor Polisi : <b><?php echo $nomor_polisi; ?></b></label><br>
                <label for=>KM Kendaraan : <b><?php echo $km_kendaraan; ?></b></label><br>
                <label for=>Keterangan : <b><?php echo $keterangan; ?></b></label><br>
                    <label colspan="3">-----------------------------------------------------</label>
                    <p align="center">Terimakasih atas kepercayaan anda, </br> dan semoga lancar rezeki nya.</p><br>
            </div>
            </div>
            </div>
        </td>
    </tr>
</table>