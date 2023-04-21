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
							
							<?= form_open('laporan/beli','class="form-inline" role="form"');?>
                            
								<table class="table">
                                    <tr>
                                        <td>Nama Barang</td>
                                        <td>:</td>
                                        <td>
                                        <select name="kode_barang"  class="form-control selectpicker" data-live-search="true" style="width:300px" placeholder="kode_barang">
                                                <?php foreach ($barang as $komp) {
                                                if($kode_barang==$komp->kode_barang){
                                                ?>
                                                    <option value="<?= $komp->kode_barang ?>"><?= $komp->nama_barang ?></option>
                                                <?php
                                                } }
                                                foreach ($barang as $komp) {
                                                if($kode_barang<>$komp->kode_barang){
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
								<i class="fa fa-book"></i> Laporan Beli per Bulan
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
							
							<?= form_open('laporan/beli','class="form-inline" role="form"');?>
							<div class="form-group">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<label> Harian : 
									    <input type="radio" class="form-control" name="rad" value="0" checked>
                                    </label>
                                    <label> Bulanan : 
									    <input type="radio" class="form-control" name="rad" value="1">
                                    </label>
								</div>
                                <br>
								<div class="form-group">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<label> Tanggal : </label>
									<input type="date" class="form-control" id='mulai' name="mulai">
								</div>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								
								<input type="submit" name="submit" class='btn btn-default' value="submit">
							</form>
							<hr>
							
							
						</div>
					</div>
					<!-- END SAMPLE FORM PORTLET-->
				</div>
			</div>