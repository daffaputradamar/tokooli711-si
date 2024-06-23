<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-bell fa-fw"></i> Penjualan
        </div>
        <div class="panel-body">
        <form method="post">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Barang</label>
							<select name="kode_barang" class="form-control selectpicker" data-live-search="true"
								placeholder="kode_barang">
								<?php foreach ($listbarang as $komp) {
								    if ($kode_barang == $komp->kode_barang) {
								        ?>
								<option
									value="<?= $komp->kode_barang ?>"
									data-merk="<?= $komp->merk ?>"
									data-harga="<?= $komp->harga_jual ?>">
									<?= $komp->nama_barang ?>
								</option>
								<?php
								    }
								}
                            foreach ($listbarang as $komp) {
                                if ($kode_barang <> $komp->kode_barang) {
                                    ?>
								<option
									value="<?= $komp->kode_barang ?>"
									data-merk="<?= $komp->merk ?>"
									data-harga="<?= $komp->harga_jual ?>">
									<?= $komp->nama_barang ?> ||
									<?= $komp->merk ?> || Rp.
									<?= $this->CodeGenerator->rp($komp->harga_jual) ?>
								</option>
								<?php
                                }
                            }

								?>
							</select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="tgl_awal">Tanggal Awal</label>
							<input type="date" name="tgl_awal" id="tgl_awal" class="form-control"
								value="<?= (isset($filter)) ? date('Y-m-d', strtotime($filter['tgl_awal'])) : date('Y-m-01'); ?>">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="tgl_akhir">Tanggal Akhir</label>
							<input type="date" name="tgl_akhir" id="tgl_akhir" class="form-control"
								value="<?= (isset($filter)) ? date('Y-m-d', strtotime($filter['tgl_akhir'])) : date('Y-m-d'); ?>">
						</div>
					</div>

				</div>
				<div style="text-align: right;">
					<?= (isset($filter)) ? '<button class="btn btn-secondary" type="submit" name="reset">Reset</button>' : ''; ?>
					<button class="btn btn-primary" type="submit" name="filter">Filter</button>
				</div>
			</form>
            <hr>
            <div class="row" style="margin-bottom: 10px; margin: 0px 5px;">
                <!-- <div class="col-md-8"> -->
                <!-- <?php echo anchor(site_url('penjualan/insert'),'Tambah', 'class="btn btn-primary"'); ?> -->
                <!-- </div> -->
                <div class="col-12">
                    <form action="<?php echo site_url('penjualan/index'); ?>" class="form-inline" method="get">
                        <div class="form-group" style="margin: 0px 10px;">
                            <label for="filterkaryawan">Filter Karyawan: </label>
                            <select class="form-control" id="filterkaryawan" name="filterkaryawan">
                                <option value="ALL">All</option>
                                <?php
                                foreach ($karyawan as $karyawan_val):
                            ?>
                                <option value="<?= $karyawan_val->kode_karyawan; ?>"
                                    <?= ($karyawan_val->kode_karyawan == $filterkaryawan && !is_null($filterkaryawan)) ? "selected" : ""; ?>>
                                    <?= $karyawan_val->kode_karyawan; ?>
                                </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin: 0px 10px;">
                            <label for="cari">Filter Teks: </label>
                            <input type="text" class="form-control" id="cari" name="cari" value="<?php echo $cari; ?>">
                        </div>
                        <div class="form-group" style="margin: 0px 10px;">

                            <?php 
                                    if ($cari <> '' || ($filterkaryawan <> '' && $filterkaryawan <> 'ALL'))
                                    {
                                        ?>
                            <a href="<?php echo site_url('penjualan'); ?>" class="btn btn-default">Reset</a>
                            <?php
                                    }
                                ?>
                            <button class="btn btn-primary" type="submit">Cari</button>
                        </div>
                    </form>
                </div>
            </div>
            <table class="table table-striped table-bordered table-hover" style="margin-bottom: 10px">
                <tr>
                    <th>No</th>
                    <th>No Transaksi</th>
                    <th>Tanggal Jual</th>
                    <th>No Polisi</th>
                    <th>Kode Karyawan</th>
                    <th>Total</th>

                    <th>Action</th>
                </tr><?php
            foreach ($penjualan_data as $penjualan)
            {
                ?>
                <tr>
                    <td width="80px"><?php echo ++$start ?></td>
                    <td><?php echo $penjualan->kode_jual ?></td>
                    <td><?php echo $penjualan->tanggal_jual ?></td>
                    <td><?php echo $penjualan->nomor_polisi ?></td>
                    <td><?php echo $penjualan->kode_karyawan ?></td>
                    <td><?php echo $penjualan->total ?></td>

                    <td style="text-align:center">
                        <?php 
				echo anchor(site_url('penjualan/view/'.$penjualan->kode_jual),'Lihat','class="btn btn-info"'); 
				echo anchor(site_url('penjualan/struk/'.$penjualan->kode_jual),'Struk','class="btn btn-success" target="_blank"'); 
				echo anchor(site_url('penjualan/delete/'.$penjualan->kode_jual),'Delete','class="btn btn-danger" onclick="javasciprt: return confirm(\'Are You Sure ?\')"'); 
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
        </div>
    </div>
</div>
<script>
    <?php 
        if(isset($filter)) {
            echo "$('select[name=kode_barang]').val('".$filter['kode_barang']."');$('.selectpicker').selectpicker('refresh')";
        }
    ?>
</script>