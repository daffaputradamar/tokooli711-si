<div class="col-lg-12">
	<div class="panel panel-default">
		<div class="panel-heading">
			<i class="fa fa-bell fa-fw"></i> Promo
		</div>

		<div class="panel-body">

			<?php if($this->uri->segment(2)=="insert") {
			    echo form_open('Promo/insert');
			} else {
			    echo form_open('Promo/update/'.$this->uri->segment(3));
			} ?>


			<div class="form-group">
				<label for="varchar">Text
					<?php echo form_error('text') ?></label>
				<input type="text" class="form-control" name="text" id="text" placeholder="Teks Promo"
					value="<?php echo $text; ?>" />
			</div>

			<div class="form-group">
				<label for="dtfrom">Tanggal Mulai
					<?php echo form_error('text') ?></label>
				<input type="date" class="form-control" name="dtfrom" id="dtfrom"
					value="<?php echo date('Y-m-d', strtotime($dtfrom)); ?>" />
			</div>

			<div class="form-group">
				<label for="dtthru">Tanggal Berakhir
					<?php echo form_error('text') ?></label>
				<input type="date" class="form-control" name="dtthru" id="dtthru"
					value="<?php echo date('Y-m-d', strtotime($dtthru)); ?>" />
			</div>

			<button type="submit" class="btn btn-primary">Simpan</button>
			<a href="<?php echo site_url('promo') ?>"
				class="btn btn-default">Cancel</a>
			</form>
		</div>

	</div>
</div>