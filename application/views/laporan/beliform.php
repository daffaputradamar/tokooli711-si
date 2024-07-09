<div class="row ">
	<div class="col-md-6">
		<!-- BEGIN SAMPLE FORM PORTLET-->
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-book"></i> Laporan Beli
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

				<?= form_open('laporan/beli', 'class="form-inline" role="form"'); ?>

				<table class="table">
					<tr>
						<td>Nama Barang</td>
						<td>:</td>
						<td>
							<select name="kode_barang" class="form-control selectpicker" data-live-search="true" style="width:300px" placeholder="kode_barang">
								<?php foreach ($barang as $komp) {
									if ($kode_barang == $komp->kode_barang) {
								?>
										<option value="<?= $komp->kode_barang ?>"><?= $komp->nama_barang ?></option>
									<?php
									}
								}
								foreach ($barang as $komp) {
									if ($kode_barang <> $komp->kode_barang) {
									?>
										<option value="<?= $komp->kode_barang ?>"><?= $komp->nama_barang ?> [<?= $komp->merk ?>]</option>
								<?php
									}
								}

								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Tanggal Awal</td>
						<td>:</td>
						<td><input type="date" class="form-control" id='tgl_awal' name="tgl_awal"></td>
					</tr>
					<tr>
						<td>Tanggal Akhir</td>
						<td>:</td>
						<td><input type="date" class="form-control" id='tgl_akhir' name="tgl_akhir"></td>
					</tr>
				</table>
				<button type="submit" class="btn btn-primary">Lihat</button>
				</form>

			</div>
		</div>
		<!-- END SAMPLE FORM PORTLET-->
	</div>

	<div class="col-md-6">
		<!-- BEGIN SAMPLE FORM PORTLET-->
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-book"></i>Generate Laporan Pembelian
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

				<?= form_open('laporan/beli', 'class="form-inline" role="form"'); ?>
				<div class="row">
					<div class="col-md-2">
						<label for="tipe">Jenis Laporan :</label>
					</div>
					<div class="col-md-10">
						<div class="form-group" style="display: block;">
							<input type="radio" class="form-control" name="rad" value="0" checked>
							<label for="tipe"> Harian </label>
						</div>
						<div class="form-group" style="display: block;">
							<input type="radio" class="form-control" name="rad" value="1">
							<label for="tipe"> Bulanan </label>
						</div>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-2">
						<label> Tanggal : </label>
					</div>
					<div class="col-md-10">
						<input type="date" class="form-control" id='mulai' name="mulai">

					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-2">
					</div>
					<div class="col-md-10">
						<button type="submit" class="btn btn-default">Submit</button>
					</div>
				</div>
				</form>
				<hr>


			</div>
		</div>
		<!-- END SAMPLE FORM PORTLET-->
	</div>
</div>

<script>
    $(document).ready(function() {

        $('input[type=radio][name="rad"]').change(function() {
            if (this.value == '0') {
                $('#mulai').attr('type', 'date');
            } else if (this.value == '1') {
                $('#mulai').attr('type', 'month');
            }
        });

        $('input:radio[name="rad"]')
            .filter(`[value="0"]`)
            .prop('checked', true)
            .trigger("change");

    });
</script>