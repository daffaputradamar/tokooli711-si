<!-- BEGIN SAMPLE FORM PORTLET-->
<div class="portlet box blue">
	<div class="portlet-title">
		<div class="caption">
			<i class="fa fa-book"></i> Laporan Movement Part
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
		<?= form_open('laporan/movement', 'class="form-inline" role="form"'); ?>
		<table class="table">
			<tr>
				<td>Nama Barang</td>
				<td>:</td>
				<td>
					<select name="kode_barang" class="form-control selectpicker" data-live-search="true" style="width:300px" placeholder="kode_barang" required>
						<?php
                        foreach ($barang as $komp) {
                            ?>
							<option value="<?= $komp->kode_barang ?>"><?= $komp->nama_barang ?> [<?= $komp->merk ?>]</option>
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
						<input type="date" class="form-control" id='tgl_awal' name="tgl_awal" required>
						<input type="time" class="form-control" id='wkt_awal' name="wkt_awal" required>
					</div>
				</td>
			</tr>
			<tr>
				<td>Tanggal Akhir</td>
				<td>:</td>
				<td>
					<div style="display: flex;">
						<input type="date" class="form-control" id='tgl_akhir' name="tgl_akhir" required>
						<input type="time" class="form-control" id='wkt_akhir' name="wkt_akhir" required>
					</div>
				</td>
			</tr>
		</table>
		<button type="submit" class="btn btn-primary">Lihat</button>
		</form>

	</div>
</div>
<!-- END SAMPLE FORM PORTLET-->