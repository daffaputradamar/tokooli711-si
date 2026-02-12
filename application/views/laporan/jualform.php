<div class="row">
    <div class="col-md-6">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-book"></i> Laporan Jual per Bulan
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

                <?= form_open('laporan/jual', 'class="form-inline" role="form"'); ?>

                <table class="table">
                    <tr>
                        <td>Nama Barang</td>
                        <td>:</td>
                        <td>
                            <select name="kode_barang" class="form-control selectpicker" data-live-search="true" style="width:300px" placeholder="kode_barang">
                                <?php foreach ($barang as $komp) {
                                    if ($kode_barang == $komp->kode_barang) {
                                ?>
                                        <option value="<?= $komp->kode_barang ?>">
                                            <?= $komp->nama_barang ?>
                                        </option>
                                    <?php
                                    }
                                }
                                foreach ($barang as $komp) {
                                    if ($kode_barang <> $komp->kode_barang) {
                                    ?>
                                        <option value="<?= $komp->kode_barang ?>">
                                            <?= $komp->nama_barang ?>
                                            [<?= $komp->merk ?>]
                                        </option>
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
                    <i class="fa fa-book"></i>Generate Laporan Penjualan
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

                <?= form_open('laporan/jual', 'class="form-inline" role="form"'); ?>
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

    <div class="col-md-6">
        <!-- BEGIN EXPORT TAHUNAN PORTLET-->
        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-file-excel-o"></i> Export Laporan Penjualan Tahunan
                </div>
                <div class="tools">
                    <a href="" class="collapse" data-original-title="" title=""></a>
                </div>
            </div>
            <div class="portlet-body">
                <br>
                <?= form_open('laporan/export_jual_tahunan', 'class="form-inline" role="form"'); ?>
                <div class="row">
                    <div class="col-md-3">
                        <label>Pilih Tahun :</label>
                    </div>
                    <div class="col-md-5">
                        <select name="tahun" class="form-control" required>
                            <option value="">-- Pilih Tahun --</option>
                            <?php foreach ($tahun_jual_list as $t) { ?>
                                <option value="<?= $t->tahun ?>"><?= $t->tahun ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success"><i class="fa fa-download"></i> Export Excel</button>
                    </div>
                </div>
                <br>
                <p class="text-muted">Data akan dikelompokkan per bulan (periode) dengan total dibulatkan ke kelipatan 500.</p>
                </form>
            </div>
        </div>
        <!-- END EXPORT TAHUNAN PORTLET-->
    </div>

    <div class="col-md-6 col-sm-12">
        <!-- BEGIN PORTLET-->
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption caption-md">
                    <i class="icon-bar-chart theme-font-color hide"></i>
                    <span class="caption-subject theme-font-color bold uppercase">Lain - lain per Bulan</span>
                </div>

            </div>
            <div class="portlet-body">

                <div class="table-scrollable">
                    <table class="table table-hover table-bordered table-striped">
                        <thead>
                            <tr class="uppercase">
                                <th>
                                    Nama
                                </th>
                                <th>No Telp</th>
                                <th>
                                    Lain - lain
                                </th>
                                <!-- <th>
                                Act
                            </th> -->
                            </tr>
                        </thead>
                        <?php
                        $total = 0;
                        foreach ($listgaji as $gaji) {
                        ?>
                            <?php if (!is_null($gaji->nama_karyawan)) { ?>
                                <tr>
                                    <td>
                                        <b><?= $gaji->nama_karyawan; ?></b>
                                    </td>
                                    <td><?= $gaji->telp_karyawan; ?>
                                    </td>
                                    <td>
                                        Rp. <?php echo $this->CodeGenerator->rp($gaji->gaji);
                                            $total += $gaji->gaji;  ?>
                                    </td>

                                </tr>
                        <?php
                            }
                        } ?>
                    </table>

                </div>
                <hr>
                <div class="row number-stats margin-bottom-30">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="stat-left pull-left">
                            <div class="stat-number">
                                <div class=" theme-font-color bold">
                                    Total Bea lain- lain bulan ini : Rp. <?= $this->CodeGenerator->rp($total) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PORTLET-->
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