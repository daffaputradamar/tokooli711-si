<div class="col-lg-12">
	<div class="panel panel-default">
		<div class="panel-heading">
			<i class="fa fa-bell fa-fw"></i> Pembelian
		</div>
		<div class="panel-body">
			<form method="get">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Barang</label>
							<select name="kode_barang" class="form-control selectpicker" data-live-search="true"
								placeholder="kode_barang">
								<?php foreach ($listbarang as $komp): ?>
								<option
									value="<?= $komp->kode_barang ?>"
									data-merk="<?= $komp->merk ?>"
									data-harga="<?= $komp->harga_jual ?>"
									<?= ($kode_barang == $komp->kode_barang) ? 'selected' : ''; ?>>
									<?= $komp->nama_barang ?> ||
									<?= $komp->merk ?> || Rp.
									<?= $this->CodeGenerator->rp($komp->harga_jual) ?>
								</option>
								<?php
                            		endforeach;
								?>
							</select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="tgl_awal">Tanggal Awal</label>
							<input type="date" name="tgl_awal" id="tgl_awal" class="form-control"
								value="<?= (!empty($tgl_awal)) ? date('Y-m-d', strtotime($tgl_awal)) : date('Y-m-01'); ?>">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="tgl_akhir">Tanggal Akhir</label>
							<input type="date" name="tgl_akhir" id="tgl_akhir" class="form-control"
								value="<?= (!empty($tgl_akhir)) ? date('Y-m-d', strtotime($tgl_akhir)) : date('Y-m-d'); ?>">
						</div>
					</div>

				</div>
				<div style="text-align: right;">
					<?= (!empty($cari) || !empty($kode_barang)) ? "<a href=" . site_url('pembelian') . " class='btn btn-default'>Reset</a>" : ''; ?> 
					<button class="btn btn-primary" type="submit" name="filter">Filter</button>
				</div>
			</form>
			<hr>
			<div class="row" style="margin-bottom: 10px">
				<div class="col-md-4">
					<?php echo anchor(site_url('pembelian/insert'), 'Tambah', 'class="btn btn-primary"'); ?>
				</div>
				<div class="col-md-4 text-center">

				</div>
				<div class="col-md-1 text-right">
				</div>
				<div class="col-md-3 text-right">
					<form
						action="<?php echo site_url('pembelian/index'); ?>"
						class="form-inline" method="get">
						<div class="input-group">
							<input type="text" class="form-control" name="cari"
								value="<?php echo $cari; ?>">
							<span class="input-group-btn">
								<?php
								    if ($cari <> '') {
								        ?>
								<a href="<?php echo site_url('pembelian'); ?>"
									class="btn btn-default">Reset</a>
								<?php
								    }
								?>
								<button class="btn btn-primary" type="submit">Cari</button>
							</span>
						</div>
					</form>
				</div>
			</div>
			<table class="table table-striped table-bordered table-hover" style="margin-bottom: 10px">
				<tr>
					<th>No</th>
					<th>Kode Beli</th>
					<th>Tanggal Beli</th>
					<th>Waktu Beli</th>
					<th>No Faktur</th>
					<th>Suplier</th>
					<?php if($_SESSION['level'] == 'admin') { ?>
						<th>Total</th>
					<?php } ?>
					<th>Action</th>
				</tr><?php
            foreach ($pembelian_data as $pembelian) {
                ?>
				<tr>
					<td width="80px"><?php echo ++$start ?></td>
					<td><?php echo $pembelian->kode_beli ?></td>
					<td><?php echo $pembelian->tanggal_beli ?></td>
					<td><?php echo $pembelian->waktu_beli ?></td>
					<td><?php echo $pembelian->no_faktur ?></td>
					<td><?php echo $pembelian->nama_suplier ?></td>
					<?php if($_SESSION['level'] == 'admin') { ?>
					<td>Rp.
						<?php echo $this->CodeGenerator->rp($pembelian->total) ?>
					</td>
					<?php } ?>
					<td style="text-align:center" width="250px">
						<?php
                echo anchor(site_url('pembelian/view/'.$pembelian->kode_beli), 'Lihat', 'class="btn btn-info"');
                echo anchor(site_url('pembelian/struk/'.$pembelian->kode_beli), 'Struk', 'class="btn btn-success" target="_blank"');
                echo anchor(site_url('pembelian/delete/'.$pembelian->kode_beli), 'Delete', 'class="btn btn-danger" onclick="javasciprt: return confirm(\'Are You Sure ?\')"');
                ?>
					</td>
				</tr>
				<?php
            }
								?>
			</table>
			<div class="row">
				<div class="col-md-6">
					<a href="#" class="btn btn-primary">Total Data :
						<?php echo $total_rows ?></a>
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