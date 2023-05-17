<div class="col-lg-12">
	<div class="panel panel-default">
		<div class="panel-heading">
			<i class="fa fa-bell fa-fw"></i> Promo
		</div>
		<div class="panel-body">
			<div class="row" style="margin-bottom: 10px">
				<div class="col-md-4">
					<?php echo anchor(site_url('promo/insert'), 'Tambah', 'class="btn btn-primary"'); ?>
				</div>
				<div class="col-md-4 text-center">

				</div>
				<div class="col-md-1 text-right">
				</div>
				<div class="col-md-3 text-right">
					<form
						action="<?php echo site_url('promo/index'); ?>"
						class="form-inline" method="get">
						<div class="input-group">
							<input type="text" class="form-control" name="cari"
								value="<?php echo $cari; ?>">
							<span class="input-group-btn">
								<?php
                                if ($cari <> '') {
                                    ?>
								<a href="<?php echo site_url('promo'); ?>"
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
					<th>Teks Promo</th>
					<th>Tanggal Mulai</th>
					<th>Tanggal Berakhir</th>
					<th>Status Aktif</th>
					<th>Action</th>
				</tr><?php
            foreach ($promo_data as $promo) {
                ?>
				<tr>
					<td width="80px"><?php echo ++$start ?></td>
					<td><?php echo $promo->text ?></td>
					<td><?php echo date('Y-m-d', strtotime($promo->dtfrom)) ?>
					</td>
					<td><?php echo date('Y-m-d', strtotime($promo->dtthru)) ?>
					</td>
					<td><?php echo ($promo->isactive) ? "Aktif" : "Tidak Aktif" ?>
					</td>
					<td style="text-align:center" width="250px">
						<?php
                echo anchor(site_url('Promo/toggle/'.$promo->id), 'Toggle Status', 'class="btn btn-info"');
                ?>
						<?php
                echo anchor(site_url('Promo/update/'.$promo->id), 'Edit', 'class="btn btn-success"'); ?>
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