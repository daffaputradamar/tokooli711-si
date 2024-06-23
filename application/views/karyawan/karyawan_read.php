<div class="col-lg-12">
	<div class="panel panel-default">
		<div class="panel-heading">
			<i class="fa fa-bell fa-fw"></i> Karyawan
		</div>
		<div class="panel-body">

			<table class="table table-bordered">
				<tr>
					<td>Kode Karyawan</td>
					<td><?php echo $kode_karyawan; ?></td>
				</tr>
				<tr>
					<td>Nama Karyawan</td>
					<td><?php echo $nama_karyawan; ?></td>
				</tr>
				<tr>
					<td>Alamat Karyawan</td>
					<td><?php echo $alamat_karyawan; ?></td>
				</tr>
				<tr>
					<td>Telp Karyawan</td>
					<td><?php echo $telp_karyawan; ?></td>
				</tr>
				<tr>
					<td>Username</td>
					<td><?php echo $username; ?></td>
				</tr>
			</table>

			<div class="text-right">
				<a href="<?php echo site_url('karyawan') ?>"
					class="btn btn-default">Kembali</a>
			</div>

			<hr>
			<?php if (count($percobaan_barang) > 0) { ?>
			<div class="row">
				<div class="col-md-6">
					<h4><strong>List Barang Terblokir</strong></h4>
				</div>
				<div class="col-md-6 text-right">
					<?php
                        echo anchor(site_url('karyawan/resetpercobaan/' . $kode_karyawan), 'Reset Percobaan', 'class="btn btn-warning"');
			    ?>
				</div>
			</div>
			<table class="table table-striped table-bordered table-hover" style="margin: 10px 0 10px 0">
				<tr>
					<th>Nama Barang</th>
					<th>Jumlah Percobaan</th>
					<th>Percobaan Terakhir</th>
				</tr>
				<?php foreach ($percobaan_barang as $percobaan) { ?>
				<tr>
					<td><?= $percobaan->nama_barang ?></td>
					<td><?= $percobaan->jml_percobaan ?></td>
					<td><?php echo date('Y-m-d H:i:s', strtotime($percobaan->percobaan_terakhir)) ?>
					</td>
				</tr>
				<?php } ?>
			</table>
			<?php } ?>

		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<i class="fa fa-bell fa-fw"></i> Log Percobaan
		</div>
		<div class="panel-body">
			<div class="row" style="margin-bottom: 10px">
				<div class="col-md-12 text-right">
					<form
						action="<?php echo site_url("karyawan/view/" . $kode_karyawan); ?>"
						class="form-inline" method="get">
						<div class="input-group">
							<input type="text" class="form-control" name="cari"
								value="<?php echo $cari; ?>">
							<span class="input-group-btn">
								<?php
			            if ($cari <> '') {
			                ?>
								<a href="<?php echo site_url("karyawan/view/" . $kode_karyawan); ?>"
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
					<th>Nama Barang</th>
					<th>Jumlah Percobaan</th>
					<th>Percobaan Terakhir</th>
					<th>Detail</th>
				</tr><?php
					    foreach ($percobaan_barang_list as $percobaan_barang) {
					        ?>
				<tr>
					<td width="80px"><?php echo ++$start ?></td>
					<td><?php echo $percobaan_barang->nama_barang ?>
					</td>
					<td><?php echo $percobaan_barang->jml_percobaan ?>
					</td>
					<td><?php echo date('Y-m-d H:i:s', strtotime($percobaan_barang->percobaan_terakhir)) ?>
					</td>
					<td class="text-center">
						<button class="btn btn-info btnModalDetailPercobaan"
							data-barang="<?= $percobaan_barang->id_barang ?>"
							data-karyawan="<?= $kode_karyawan ?>"
							data-toggle="modal" data-target="#modal-detail-percobaan">Detail</button>
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

<!-- Modal -->
<div class="modal fade" id="modal-detail-percobaan">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button class="close" data-dismiss="modal"><span>&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Detail Percobaan</h4>
			</div>
			<div class="modal-body">
				<table class="table table-striped table-bordered table-hover" style="margin-bottom: 10px">
					<thead>
						<th>No</th>
						<th>Waktu Percobaan</th>
					</thead>
					<tbody id="tbl-detail-body">

					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script>
	const modalDetailPercobaan = document.querySelectorAll('.btnModalDetailPercobaan')

	modalDetailPercobaan.forEach(el => {
		console.log(el);
		el.addEventListener('click', function(e) {
			fetch(`<?= base_url() ?>/karyawan/detailpercobaan?${new URLSearchParams({
                karyawan: this.dataset.karyawan,
                barang: this.dataset.barang,
            })}`, {
					method: "GET",
					headers: {
						'Accept': 'application/json',
						'Content-Type': 'application/json'
					}
				})
				.then(res => res.text())
				.then(data => {
					let dataPercobaan = JSON.parse(data.trim()) ?? []

					let myModalLabel = document.getElementById('myModalLabel')
					myModalLabel.innerHTML = dataPercobaan[0].nama_barang

					let tblDetail = document.getElementById('tbl-detail-body')

					tblDetail.innerHTML = ''

					dataPercobaan.forEach((v, i) => {
						const tr = document.createElement('tr');

						const tdNo = document.createElement('td');
						const tdWaktuPercobaan = document.createElement('td');

						tdNo.innerHTML = (i + 1)
						tdWaktuPercobaan.innerHTML = v.createdate

						tr.appendChild(tdNo)
						tr.appendChild(tdWaktuPercobaan)

						tblDetail.appendChild(tr)
					})
				})
		})
	})
</script>