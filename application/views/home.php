<div class="row">

	<div class="col-md-12 col-sm-12">
		<script>
			function sum() {
				const ongkoskaryawan = (document.getElementById('ongkos_karyawan').value) ? document.getElementById(
					'ongkos_karyawan').value : 0;

				// var txtFirstNumberValue = document.getElementById('ongkos_karyawan').value;
				var txtSecondNumberValue = document.getElementById('totalharga').value;
				var result = parseInt(ongkoskaryawan) + parseInt(txtSecondNumberValue);

				console.log(result);
				if (!isNaN(result)) {
					document.getElementById('total').value = result;
				}
			}

			function submitForm() {
				const bayar = document.getElementById("bayar").value
				const kembali = document.getElementById("kembali").value

				if (bayar == kembali) {
					alert("Harus ada transaksi yang dibayarkan")
					return false
				}
			}
		</script>

		<div class="panel panel-default">
			<div class="panel-heading" style="font-size:1.7rem">
				<strong>
					<i class="fa fa-bell fa-fw"></i> Penjualan
				</strong>
			</div>

			<div class="panel-body">
				<?php
                echo form_open('Penjualan/insert/1', array('id' => 'insertpenjualan'
                // , "onsubmit" => "return submitForm()"
            ));
				?>
				<div class="panel panel-info">
					<div class="panel-heading" style="font-size:1.5rem">
						<strong>
							<i class="fa fa-book fa-fw"></i> List Barang
						</strong>
					</div>

					<div class="panel-body">
						<div class="row">
							<div class="form-group col-lg-4">
								<label for="int">Kode Jual
									<?php echo form_error('kode_jual') ?></label>
								<input type="text" class="form-control" name="kode_jual" id="kode_jual"
									placeholder="Kode Jual"
									value="<?php echo $kode_jual; ?>"
									readonly />
							</div>
							<div class="form-group col-lg-4">
								<label for=>Tanggal Jual
									<?php echo form_error('tanggal_jual') ?></label>
								<input type="text" class="form-control" name="tanggal_jual" id="tanggal_jual"
									placeholder="Tanggal Jual"
									value="<?php echo $tanggal_jual; ?>" />
							</div>
							<div class="form-group col-lg-4">
								<label for=>Kode Admin
									<?php echo form_error('kode_admin') ?></label>
								<input type="text" class="form-control" name="kode_admin" id="kode_admin"
									placeholder="Kode Admin"
									value="<?php echo $kode_admin; ?>"
									readonly />
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-5 col-lg-8">
								<label for="">Barang</label>
								<select id="kode_barang" name="kode_barang" class="form-control selectpicker"
									data-live-search="true" placeholder="kode_barang">
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
										<?php if ($_SESSION['level'] == "admin" || $_SESSION['can_see_stock'] == true) {
										    echo " || " . $komp->stok;
										} ?>
									</option>
									<?php
				    }
				}

				?>
								</select>
							</div>
							<div id="merk-kemasan">
								<div class="form-group col-md-2 col-lg-2">
									<label
										for=>Jumlah<?php echo form_error('jumlah') ?></label>
									<input type="text" class="form-control" name="jumlah" id="jumlah"
										placeholder="Jumlah" value="1" />
								</div>
							</div>
							<div id="merk-drum" class="d-none">
								<div class="form-group col-md-2 col-lg-2">
									<label
										for=>Rupiah<?php echo form_error('rupiah') ?></label>
									<input type="text" class="form-control" name="rupiah" id="rupiah"
										placeholder="Rupiah" value="-1" />
								</div>
							</div>
							<div class="form-group col text-right" style="margin-right: 2rem; margin-top: 2.5rem">
								<div id="btnValidasiPercobaan">
									<!-- <span class="text-danger">Input data sudah lebih dari 2x, <br> Hubungi admin agar
										bisa membuat transaksi</span> -->

									<input type="submit" class="btn btn-primary" name="submitlist" value="Tambah" />
								</div>

							</div>
						</div>
						<table class="table table-bordered table-hover table-striped">
							<tr>
								<th>No</th>
								<th>Nama Barang</th>
								<th>Harga</th>
								<th>Jumlah</th>
								<th>Subtotal</th>
								<th></th>
							</tr><?php $start = 0;
				foreach ($listdetail as $penjualandetail) {
				    ?>
							<tr>
								<td width="80px"><?= ++$start ?>
								</td>
								<td><?= $penjualandetail['nama_barang'] ?>
								</td>
								<td><?= $penjualandetail['harga_jual'] ?>
								</td>
								<td><?= $penjualandetail['jumlah'] ?>
								</td>
								<td>Rp.
									<?= $this->CodeGenerator->rp($penjualandetail['subtotal']) ?>
								</td>
								<td style="text-align:center" width="50px">
									<?= anchor(site_url('penjualan_detail/delete/' . $penjualandetail['kode_barang'] . '/1'), 'Delete', 'class="btn btn-danger" onclick="javascript: return confirm(\'anda yakin ingin menghapus ?\')"'); ?>
								</td>
							</tr>
							<?php
				}
				?>
						</table>
					</div>
					<!----pane body-->
				</div>

				<div class="panel panel-info">
					<div class="panel-heading" style="font-size:1.5rem">
						<strong>
							<i class="fa fa-book fa-fw"></i> Penggantion oli berikutnya
						</strong>
					</div>

					<div class="panel-body">
						<div class="row">
							<div class="form-group col-md-4">
								<label for=>Kasir
									<?php echo form_error('kode_karyawan') ?></label>
								<!-- <input type="text" class="form-control" name="kode_karyawan" id="kode_karyawan" placeholder="Kode Karyawan" value="<?php echo $kode_karyawan; ?>"
								/> -->
								<select name="kode_karyawan" class="form-control" placeholder="kode_karyawan" readonly>
									<?php
				        if ($_SESSION['level'] != "admin") {
				            ?>
									<option
										value="<?= $karyawan->kode_karyawan ?>"
										selected>
										<?= $karyawan->nama_karyawan ?>
									</option>
									<?php
				        } else {
				            ?>
									<option
										value="<?= $admin->kode_admin ?>"
										selected>
										<?= $admin->nama_admin ?>
									</option>
									<?php
				        }
				?>
								</select>
							</div>
							<div class="form-group col-md-4">
								<label for="keterangan">Keterangan
									<?php echo form_error('keterangan') ?></label>
								<input class="form-control" name="keterangan" id="keterangan" placeholder="Keterangan"
									value="<?php echo isset($_SESSION[$_SESSION['kode'] . 'keterangan']) ? $_SESSION[$_SESSION['kode'] . 'keterangan'] : ' '; ?>">
							</div>
							<div class="form-group col-md-4">
								<label for=>Bea Servis
									<?php echo form_error('ongkos_karyawan') ?></label>
								<input type="number" class="form-control" name="ongkos_karyawan" id="ongkos_karyawan"
									placeholder="Bea Lain-lain"
									value="<?php echo isset($_SESSION[$_SESSION['kode'] . 'ongkos_karyawan']) ? $_SESSION[$_SESSION['kode'] . 'ongkos_karyawan'] : '0'; ?>"
									onkeyup="sum();" />
							</div>
							<div class="form-group col-md-4">
								<label for=>Klien / Kendaraan
									<?php echo form_error('pelanggan') ?></label>
								<input type="text" class="form-control" name="pelanggan" id="pelanggan"
									placeholder="Nama Pelanggan"
									value="<?php echo isset($_SESSION[$_SESSION['kode'] . 'pelanggan']) ? $_SESSION[$_SESSION['kode'] . 'pelanggan'] : ' '; ?>" />
							</div>
							<div class="form-group col-md-4">
								<label for=>Nomor Polisi
									<?php echo form_error('nomor_polisi') ?></label>
								<input type="text" class="form-control" name="nomor_polisi" id="nomor_polisi"
									placeholder="Nomor Polisi"
									value="<?php echo isset($_SESSION[$_SESSION['kode'] . 'nomor_polisi']) ? $_SESSION[$_SESSION['kode'] . 'nomor_polisi'] : ' '; ?>" />
							</div>
							<div class="form-group col-md-4">
								<label for=>Kilo Meter Kendaraan
									<?php echo form_error('km_kendaraan') ?></label>
								<input type="text" class="form-control" name="km_kendaraan" id="km_kendaraan"
									placeholder="Kilometer kendaraan"
									value="<?php echo isset($_SESSION[$_SESSION['kode'] . 'km_kendaraan']) ? $_SESSION[$_SESSION['kode'] . 'km_kendaraan'] : ' '; ?>" />
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-6 pull-right">
						<label for=>Total
							<?php echo form_error('total') ?></label>
						<input type="hidden" name="totalharga" id="totalharga" value="<?php if ($total == "") {
						    echo 0;
						} else {
						    echo $total;
						} ?>">
						<input type="number" class="form-control col-md-6" name="total"
							style="font-size:25px; height:50px; background:none;" id="total" placeholder="Total"
							value="<?php echo $total; ?>" readonly />
						<!-- <h2 id="total">Total : Rp. <?php echo $this->CodeGenerator->rp($total); ?>
						</h2> -->
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-6 pull-right">
						<label for=>Bayar </label>
						<input type="number" onkeyup="kembalian()" class="form-control col-md-6" name="bayar"
							style="font-size:25px; height:50px; background:none;" id="bayar" placeholder="Bayar"
							value="0" />
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-6 pull-right">
						<label for=>Kembali</label>
						<input type="number" class="form-control col-md-6" name="kembali"
							style="font-size:25px; height:50px; background:none;" id="kembali" placeholder="kembali"
							value="0" readonly />
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-6 pull-right">
						<label for=><input type="checkbox" class="form-control col-md-6" name="cetak" /> Cetak Struk
						</label>
					</div>
				</div>

				<div class="text-center">
					<a href="<?php echo site_url('penjualan') ?>"
						class="btn btn-lg" style="margin-right:30px;">Cancel</a>
					<button type="submit" class="btn btn-primary btn-lg">Simpan</button>
				</div>
				</form>
			</div>

		</div>


	</div>

</div>

<?php if ($_SESSION['level'] == "admin") { ?>

<!-- <div class="row"> -->
<button data-toggle="collapse" data-target="#demo" class="btn btn-default btn-sm pull-right">show</button>
<div class="clearfix"></div>
<!-- </div> -->

<div class="margin-top-10 collapse" id="demo">
	<div class="row">
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<div class="dashboard-stat2">
				<div class="display">
					<div class="number">
						<h4 class="font-red-haze">Rp.
							<?= $this->CodeGenerator->rp($semua + $byTgl); ?>
						</h4>
						<small>Total Transaksi Hari ini</small>
					</div>
					<div class="icon">
						<i class="icon-like"></i>
					</div>
				</div>
				<div class="progress-info">
					<div class="progress">
						<span style="width: 100%;" class="progress-bar progress-bar-success red-haze">

					</div>
					<div class="status">
						<div class="status-title">
							Tanggal
						</div>
						<div class="status-number">
							<?= date('d-m-Y'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<div class="dashboard-stat2">
				<div class="display">
					<div class="number">
						<h4 class="font-green-sharp">Rp.
							<?= $this->CodeGenerator->rp($semua); ?>
						</h4>
						<small>Penjualan Produk</small>
					</div>
					<div class="icon">
						<i class="icon-pie-chart"></i>
					</div>
				</div>
				<div class="progress-info">
					<div class="progress">
						<span style="width: 100%;" class="progress-bar progress-bar-success green-sharp">
					</div>
					<div class="status">
						<div class="status-title">
							Tanggal
						</div>
						<div class="status-number">
							<?= date('d-m-Y'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<div class="dashboard-stat2">
				<div class="display">
					<div class="number">
						<h4 class="font-red-haze">Rp.
							<?= $this->CodeGenerator->rp($byTgl); ?>
						</h4>
						<small>Bea Lain - lain</small>
					</div>
					<div class="icon">
						<i class="icon-like"></i>
					</div>
				</div>
				<div class="progress-info">
					<div class="progress">
						<span style="width: 100%;" class="progress-bar progress-bar-success red-haze">

					</div>
					<div class="status">
						<div class="status-title">
							Tanggal
						</div>
						<div class="status-number">
							<?= date('d-m-Y'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<div class="dashboard-stat2">
				<div class="display">
					<div class="number">
						<small><?= $kasir1["nama"] ?></small>
						<h4 class="font-green-sharp">Total Rp.
							<?= $this->CodeGenerator->rp($kasir1['total']); ?>
						</h4>
						<h4 class="font-red-haze">Penjualan Rp.
							<?= $this->CodeGenerator->rp($kasir1['transaksi']); ?>
						</h4>
						<h4>Bea Rp.
							<?= $this->CodeGenerator->rp($kasir1['bea']); ?>
						</h4>
					</div>
					<div class="icon">
						<i class="icon-like"></i>
					</div>
				</div>
				<div class="progress-info">
					<div class="progress">
						<span style="width: 100%;" class="progress-bar progress-bar-success red-haze">

					</div>
					<div class="status">
						<div class="status-title">
							Tanggal
						</div>
						<div class="status-number">
							<?= date('d-m-Y'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<div class="dashboard-stat2">
				<div class="display">
					<div class="number">
						<small><?= $kasir2["nama"] ?></small>
						<h4 class="font-green-sharp">Total Rp.
							<?= $this->CodeGenerator->rp($kasir2['total']); ?>
						</h4>
						<h4 class="font-red-haze">Penjualan Rp.
							<?= $this->CodeGenerator->rp($kasir2['transaksi']); ?>
						</h4>
						<h4>Bea Rp.
							<?= $this->CodeGenerator->rp($kasir2['bea']); ?>
						</h4>
					</div>
					<div class="icon">
						<i class="icon-like"></i>
					</div>
				</div>
				<div class="progress-info">
					<div class="progress">
						<span style="width: 100%;" class="progress-bar progress-bar-success red-haze">

					</div>
					<div class="status">
						<div class="status-title">
							Tanggal
						</div>
						<div class="status-number">
							<?= date('d-m-Y'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<div class="dashboard-stat2">
				<div class="display">
					<div class="number">
						<small><?= $kasir3["nama"] ?></small>
						<h4 class="font-green-sharp">Total Rp.
							<?= $this->CodeGenerator->rp($kasir3['total']); ?>
						</h4>
						<h4 class="font-red-haze">Penjualan Rp.
							<?= $this->CodeGenerator->rp($kasir3['transaksi']); ?>
						</h4>
						<h4>Bea Rp.
							<?= $this->CodeGenerator->rp($kasir3['bea']); ?>
						</h4>
					</div>
					<div class="icon">
						<i class="icon-like"></i>
					</div>
				</div>
				<div class="progress-info">
					<div class="progress">
						<span style="width: 100%;" class="progress-bar progress-bar-success red-haze">

					</div>
					<div class="status">
						<div class="status-title">
							Tanggal
						</div>
						<div class="status-number">
							<?= date('d-m-Y'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	
</div>
<?php } ?>
<script>
	document.addEventListener("DOMContentLoaded", function() {
		fetch(`<?=base_url()?>/home/get_percobaan_stok`, {
				method: "GET",
				headers: {
					'Accept': 'application/json',
					'Content-Type': 'application/json'
				}
			})
			.then(res => res.text())
			.then(data => {
				let dataPercobaan = JSON.parse(data.trim()) ?? []

				if (!dataPercobaan || dataPercobaan.length == 0) {
					return;
				}

				var selectBarang = document.getElementById("kode_barang")
				selectBarang.addEventListener('change', (e) => {
					let btnValidasiPercobaan = document.getElementById("btnValidasiPercobaan")
					if (dataPercobaan.some(v => v.id_barang == e.target.value)) {
						btnValidasiPercobaan.innerHTML =
							`<span class="text-danger">Input data sudah lebih dari 2x, <br> Hubungi admin agar bisa membuat transaksi</span>`
					} else {
						btnValidasiPercobaan.innerHTML =
							`<input type="submit" class="btn btn-primary" name="submitlist" value="Tambah" />`
					}
				})

				selectBarang.dispatchEvent(new Event('change'))
			})
	});

	function roundToTwo(num) {
		return +(Math.round(num + "e+2") + "e-2");
	}

	const rupiah = document.getElementById('rupiah')
	const jumlah = document.getElementById('jumlah')

	const merkDrum = document.getElementById('merk-drum')
	const merkKemasan = document.getElementById('merk-kemasan')

	document.getElementsByName("kode_barang")[0].addEventListener("change", function(e) {
		const selectedOption = this.selectedOptions[0]
		const selectedMerk = selectedOption.getAttribute('data-merk')
		const selectedPrice = selectedOption.getAttribute('data-harga')


		if (selectedMerk.toLowerCase().includes("oli drum")) {
			jumlah.value = 1
			rupiah.value = selectedPrice
			rupiah.addEventListener('keyup', function(e) {
				const rupiahVal = e.target.value
				if (isNaN(rupiahVal)) {
					alert("Rupiah harus berisi angka")
					rupiah.value = selectedPrice
					jumlah.value = 1
				} else {
					jumlah.value = roundToTwo(rupiahVal / selectedPrice)
				}
			})
			jumlah.addEventListener('keyup', keyupJumlah)
			merkDrum.classList.remove("d-none")
		} else {
			jumlah.value = 1
			rupiah.value = -1
			jumlah.removeEventListener('keyup', keyupJumlah)
			merkDrum.classList.add("d-none")
		}

		function keyupJumlah(e) {
			const jumlahVal = e.target.value
			if (isNaN(jumlahVal)) {
				alert("Jumlah harus berisi angka")
				rupiah.value = selectedPrice
				jumlah.value = 1
			} else {
				rupiah.value = jumlahVal * selectedPrice
			}
		}
	})

	document.getElementsByName("kode_barang")[0].dispatchEvent(new Event('change', {
		'bubbles': true
	}));

	sum();
</script>
<!-- END PAGE CONTENT INNER