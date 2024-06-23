<script>
	window.print()
</script>
<style>
	* {
		font-family: "arial";
		margin: 10px;
		font-size: 14px;
		font-weight:normal
	}

	@media print {
		@page {
			margin: 0;
		}
	}
</style>
<table style="width:300px;">
	<tr>
		<td colspan="3" align="center">
			<h3>TOKO OLI 711</h3>
			<h4>Jl. by pass klaten selatan</h4>
			<h4>Telp. 081332629711</h4>
		</td>
	</tr>
	<tr>
		<td colspan="3">----------------------------------------------------------</td>
	</tr>
	<tr>
		<td><?php echo $kode_jual; ?></td>
		<td><?php echo $tanggal_jual; ?></td>
		<td style="width: 120px;"><?php echo $waktu_jual ?></td>
	</tr>
	<tr>
		<td colspan="3">----------------------------------------------------------</td>
	</tr>
	<tr>
		<td colspan="3">
			<!--<div class="row">-->
			<!--    <?php foreach ($listdetail as $penjualandetail) { ?>-->
			<!--        <div class="col-sm-6">-->
			<!--            <?php echo $penjualandetail->nama_barang ?>-->
			<!--        </div>-->
			<!--        <div class="col-sm-3">-->
			<!--            <?php echo $penjualandetail->jumlah . " x " . $penjualandetail->harga_jual ?>-->
			<!--        </div>-->
			<!--        <div class="col-sm-3">-->
			<!--            Rp. <?php echo $this->CodeGenerator->rp($penjualandetail->subtotal) ?>-->
			<!--        </div>-->
			<!--    <?php } ?>-->
			<!--</div>-->
			<table style="margin-left :0; width:280px; font-size: 14px;margin-right: 10px; margin-bottom: 0;">
				<?php $start = 0;
		foreach ($listdetail as $penjualandetail) {
		    ?>
				<tr>
					<td colspan="2">
						<?php echo $penjualandetail->nama_barang ?>
					</td>
				</tr>
				<tr>
					<?php $harga_jual = $penjualandetail->subtotal / $penjualandetail->jumlah ?>
					<td><?php echo $penjualandetail->jumlah . " x " . $penjualandetail->harga_jual ?>
					</td>
					<td>Rp.
						<?php echo $this->CodeGenerator->rp($penjualandetail->subtotal) ?>
					</td>
				</tr>
				<tr></tr>
				<tr></tr>
				<?php
		}
		?>

			</table>
		</td>
	</tr>
	<tr>
		<td colspan="3">----------------------------------------------------------</td>
	</tr>
	<tr>
		<td colspan="3">
			<label style="margin-left :0; width:280px; font-size: 14px; display:inline-block" for=>Keterangan :
				(<?php echo $keterangan; ?>)</label><br>
			<div class="row" style="margin-right:35px;">
				<div class="col-md-10 pull-right" align="right">Bea Servis =
					Rp.
					<?php echo $this->CodeGenerator->rp($ongkos_karyawan); ?>
				</div>
			</div>
			<div class="row" style="margin-right:35px;">
				<div class="col-md-10 pull-right" align="right">Total =
					Rp.
					<?php echo $this->CodeGenerator->rp($total); ?>
				</div>
			</div>
			<div class="row" style="margin-right:35px;">
				<div class="col-md-10 pull-right" align="right">Bayar =
					Rp.
					<?php echo $this->CodeGenerator->rp($bayar); ?>
				</div>
			</div>
			<div class="row" style="margin-right:35px;">
				<div class="col-md-10 pull-right" align="right">Kembali =
					Rp.
					<?php echo $this->CodeGenerator->rp($bayar - $total); ?>
				</div>
			</div>

			<div class="form-group col-md-12">
				<label colspan="3"
					style="margin-left :0;">--------------------------------------------------------</label>
				<label style="margin-left :0; width:300px; font-size: 14px;" for=>Kasir :
					<?php echo isset($kasir->nama_karyawan) ? $kasir->nama_karyawan : $kasir->nama_admin; ?></label><br>
				<label style="margin-left :0; width:300px; font-size: 14px;" for=>Klien / Kendaraan :
					<?php echo $pelanggan; ?></label><br>
				<label style="margin-left :0; width:300px; font-size: 14px;" for=>Nomor Polisi :
					<?php echo $nomor_polisi; ?></label><br>
				<label style="margin-left :0; width:300px; font-size: 14px;" for=>KM Kendaraan :
					<?php echo $km_kendaraan; ?></label><br>
				<label style="margin-left :0; width:300px; font-size: 14px;"
					colspan="3">--------------------------------------------------------</label><br />
				<div style="margin:0; width:260px; font-size: 14px;">
					<?php echo (isset($promo->text)) ? $promo->text : ''; ?>
				</div>
				<label style="margin-left :0; width:300px; font-size: 14px;"
					colspan="3">--------------------------------------------------------</label>
				<p align="center" style="text-align:center; font-style=italic">Barang/Pelumas yang sudah di beli tidak dapat di tukar atau di kembalikan</p>
				<p align="center" , margin="0">T E R I M A &nbsp; &nbsp; K A S I H</p><br>
			</div>
		</td>
	</tr>
</table>