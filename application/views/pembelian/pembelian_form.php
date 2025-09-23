<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-bell fa-fw"></i> Pembelian
        </div>

        <div class="panel-body">

            <?php if ($this->uri->segment(2) == "insert") {
                echo form_open('Pembelian/insert');
            } else {
                echo form_open('Pembelian/update/' . $this->uri->segment(3));
            } ?>


            <div class="form-group">
                <label for="int">Kode Beli <?php echo form_error('kode_beli') ?></label>
                <input type="text" class="form-control" name="kode_beli" id="kode_beli" placeholder="Kode Beli" value="<?php echo '-' ?>" readonly />
            </div>


            <div class="form-group">
                <label for=>Tanggal Beli <?php echo form_error('tanggal_beli') ?></label>
                <input type="text" class="form-control" name="tanggal_beli" id="tanggal_beli" placeholder="Tanggal Beli" value="<?php echo isset($_SESSION[$_SESSION['kode'] . 'tanggal_beli']) ? $_SESSION[$_SESSION['kode'] . 'tanggal_beli'] : $tanggal_beli; ?>" />
            </div>
            <div class="form-group">
                <label for=>Kode Admin <?php echo form_error('kode_admin') ?></label>
                <input type="text" class="form-control" name="kode_admin" id="kode_admin" placeholder="Kode Admin" value="<?php echo $kode_admin; ?>" readonly />
            </div>
            <div class="form-group">
                <label for=>No Faktur <?php echo form_error('no_faktur') ?></label>
                <input type="text" class="form-control" name="no_faktur" id="no_faktur" placeholder="No Faktur" value="<?php echo isset($_SESSION[$_SESSION['kode'] . 'no_faktur']) ? $_SESSION[$_SESSION['kode'] . 'no_faktur'] : $no_faktur; ?>" />
            </div>
            <div class="form-group ">
                <label for="">Kode Suplier</label>
                <select name="kode_suplier" class="form-control selectpicker" data-live-search="true" placeholder="kode_suplier">
                    <?php
                    foreach ($listsuplier as $komp) {
                        if ($kode_suplier == $komp->kode_suplier) {
                            ?>
                            <option value="<?= $komp->kode_suplier ?>" <?= isset($_SESSION[$_SESSION['kode'] . 'kode_suplier']) && $komp->kode_suplier == $_SESSION[$_SESSION['kode'] . 'kode_suplier'] ? "selected" : "" ?>><?= $komp->nama_suplier ?></option>
                        <?php
                        }
                    }
            foreach ($listsuplier as $komp) {
                if ($kode_suplier <> $komp->kode_suplier) {
                    ?>
                            <option value="<?= $komp->kode_suplier ?>" <?= isset($_SESSION[$_SESSION['kode'] . 'kode_suplier']) && $komp->kode_suplier == $_SESSION[$_SESSION['kode'] . 'kode_suplier'] ? "selected" : "" ?>><?= $komp->nama_suplier ?></option>
                    <?php
                }
            }

            ?>
                </select>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-book fa-fw"></i> List Barang
                </div>

                <div class="panel-body">
                    <div class="form-group col-md-5 col-lg-5">
                        <label for="">Barang</label>
                        <select name="kode_barang" class="form-control selectpicker" data-live-search="true" placeholder="kode_barang">
                            <?php foreach ($listbarang as $komp):
                                ?>
                                <option value="<?= $komp->kode_barang ?>"><?= $komp->nama_barang ?></option>
                            <?php
                            endforeach;
            ?>
                        </select>
                    </div>
                    <div class="form-group col-md-2 col-lg-2">
                        <label for=>Jumlah<?php echo form_error('jumlah') ?></label>
                        <input type="text" class="form-control" name="jumlah" id="jumlah" placeholder="Kode Admin" value="1" />
                    </div>
                    <div class="form-group col-md-5 col-lg-5">
                        <br>
                        <input type="submit" class="btn btn-info" name="submitlist" value="Tambah" />
                    </div>

                    <table class="table table-bordered table-hover table-striped">
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <?php if ($_SESSION['level'] == 'admin') { ?>
                                <th>Harga Beli</th>
                            <?php } ?>
                            <th>Jumlah</th>
                            <?php if ($_SESSION['level'] == 'admin') { ?>
                                <th>Subtotal</th>
                            <?php } ?>
                            <th>Detail</th>
                            <th></th>
                        </tr><?php $start = 0;
            foreach ($listdetail as $pembelian_detail) {
                ?>
                            <tr>
                                <td width="80px"><?php echo ++$start ?></td>
                                <td><?php echo $pembelian_detail['nama_barang'] ?></td>
                                <?php if ($_SESSION['level'] == 'admin') { ?>
                                    <td><?php echo $pembelian_detail['harga_beli'] ?></td>
                                <?php } ?>
                                <td><?php echo $pembelian_detail['jumlah'] ?></td>
                                <?php if ($_SESSION['level'] == 'admin') { ?>
                                    <td>Rp. <?php echo $this->CodeGenerator->rp($pembelian_detail['subtotal']) ?></td>
                                <?php } ?>
                                <td style="text-align:center" width="50px">
                                    <button type="button" class="btn btn-info btn-sm" onclick="showProductDetail('<?php echo $pembelian_detail['kode_barang'] ?>')" title="Lihat Detail">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </td>
                                <td style="text-align:center" width="50px">
                                    <?php
                    echo anchor(site_url('pembelian_detail/delete/' . $pembelian_detail['kode_barang'] . '/1'), 'Delete', 'class="btn btn-danger" onclick="javasciprt: return confirm(\'anda yakin ingin menghapus ?\')"');
                ?>
                                </td>
                            </tr>
                        <?php
            }
            ?>

                    </table>
                </div><!----pane body-->
            </div>
            <?php if ($_SESSION['level'] == 'admin') { ?>
                <div class="form-group pull-right">
                    <?php if ($this->uri->segment(2) == "insert") { ?>
                        <label for="">
                            <h1>Total : Rp. <?= $totalall ?></h1>
                        </label>
                    <?php } else { ?>
                        <label for="">
                            <h1>Total : Rp. <?= $this->CodeGenerator->rp($this->Pembelian_detail_model->totalall($this->uri->segment(3))); ?></h1>
                        </label>
                    <?php } ?>
                </div>
            <?php } ?>
            <br><br>
            <div class="form-group">
                <label for=><?php echo form_error('total') ?></label>
                <input type="hidden" class="form-control" name="total" id="total" placeholder="Total" value="<?php if ($this->uri->segment(2) == "insert") {
                    echo $totalall;
                } else {
                    echo $this->Pembelian_detail_model->totalall($this->uri->segment(3));
                }
            ?>" />
            </div>
            <input type="submit" class="btn btn-primary" name="simpan" value="Simpan" onclick="javascript: return confirm('Anda yakin mau menyimpan?')" />
            <a href="<?php echo site_url('pembelian') ?>" class="btn btn-default">Cancel</a>
            </form>
        </div>

    </div>
</div>

<!-- Modal for Product Details -->
<div class="modal fade" id="productDetailModal" tabindex="-1" role="dialog" aria-labelledby="productDetailModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="productDetailModalLabel">Detail Produk</h4>
            </div>
            <div class="modal-body">
                <div id="productDetailContent">
                    <div class="text-center">
                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                        <p>Memuat data...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function showProductDetail(kodeBarang) {
    // Show the modal
    $('#productDetailModal').modal('show');
    
    // Reset content to loading state
    $('#productDetailContent').html(
        '<div class="text-center">' +
        '<i class="fa fa-spinner fa-spin fa-2x"></i>' +
        '<p>Memuat data...</p>' +
        '</div>'
    );
    
    // Make AJAX request to get product details
    $.ajax({
        url: '<?php echo site_url("pembelian_detail/get_product_detail"); ?>',
        type: 'POST',
        data: {
            kode_barang: kodeBarang
        },
        dataType: 'text', // Change to text first to handle BOM issues
        success: function(responseText) {
            console.log('Raw response:', responseText); // Debug log
            
            try {
                // Clean BOM and other invisible characters
                var cleanResponse = responseText.replace(/^\uFEFF/, '').trim();
                console.log('Cleaned response:', cleanResponse); // Debug log
                
                var response = JSON.parse(cleanResponse);
                console.log('Parsed response:', response); // Debug log
                
                if (response && response.success) {
                var product = response.data;
                var detailHtml = 
                    '<table class="table table-bordered">' +
                    '<tr><td><strong>Kode Barang</strong></td><td>' + (product.kode_barang || '-') + '</td></tr>' +
                    '<tr><td><strong>Nama Barang</strong></td><td>' + (product.nama_barang || '-') + '</td></tr>' +
                    '<tr><td><strong>Merk</strong></td><td>' + (product.nama_merk || '-') + '</td></tr>' +
                    '<tr><td><strong>Stok</strong></td><td>' + (product.stok || '0') + '</td></tr>';
                
                <?php if ($_SESSION['level'] == 'admin') { ?>
                detailHtml += 
                    '<tr><td><strong>Harga Beli</strong></td><td>Rp. ' + formatRupiah(product.harga_beli || 0) + '</td></tr>' +
                    '<tr><td><strong>Harga Jual</strong></td><td>Rp. ' + formatRupiah(product.harga_jual || 0) + '</td></tr>';
                <?php } ?>
                
                detailHtml += '</table>';
                
                $('#productDetailContent').html(detailHtml);
                } else {
                    $('#productDetailContent').html(
                        '<div class="alert alert-danger">' +
                        '<i class="fa fa-exclamation-triangle"></i> ' +
                        'Gagal memuat data produk: ' + (response.message || 'Response tidak valid') +
                        '</div>'
                    );
                }
            } catch (parseError) {
                console.log('JSON Parse Error:', parseError);
                $('#productDetailContent').html(
                    '<div class="alert alert-danger">' +
                    '<i class="fa fa-exclamation-triangle"></i> ' +
                    'Error parsing response: ' + parseError.message +
                    '</div>'
                );
            }
        },
        error: function(xhr, status, error) {
            console.log('AJAX Error:', xhr, status, error); // Debug log
            $('#productDetailContent').html(
                '<div class="alert alert-danger">' +
                '<i class="fa fa-exclamation-triangle"></i> ' +
                'Terjadi kesalahan saat memuat data produk: ' + error +
                '</div>'
            );
        }
    });
}

function formatRupiah(amount) {
    try {
        // Convert to number if it's a string
        var num = parseFloat(amount) || 0;
        return new Intl.NumberFormat('id-ID').format(num);
    } catch (e) {
        console.log('Format Rupiah Error:', e);
        return amount || '0';
    }
}
</script>