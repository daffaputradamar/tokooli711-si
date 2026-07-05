<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-bell fa-fw"></i> Barang
        </div>

        <div class="panel-body">

            <?php $is_insert = ($this->uri->segment(2) == "insert"); ?>
            <?php if ($is_insert) {
                echo form_open('Barang/insert');
            } else {
                echo form_open('Barang/update/'.$this->uri->segment(3), array('id' => 'formBarangUpdate'));
            } ?>


            <div class="form-group">
                <label for="text">Kode Barang <?php echo form_error('kode_barang') ?></label>
                <input type="text" class="form-control" name="kode_barang" id="kode_barang" placeholder="Kode Barang"
                    value="<?php echo $kode_barang; ?>" />
            </div>


            <div class="form-group">
                <label for=>Nama Barang <?php echo form_error('nama_barang') ?></label>
                <input type="text" class="form-control" name="nama_barang" id="nama_barang" placeholder="Nama Barang"
                    value="<?php echo $nama_barang; ?>" />
            </div>
            <div class="form-group">
                <label for=>Kode Merk <?php echo form_error('kode_merk') ?></label>
                <select name="kode_merk" class="form-control selectpicker" data-live-search="true"
                    placeholder="kode_merk">
                    <?php foreach ($listmerk as $komp) {
                        if ($kode_merk == $komp->kode_merk) {
                            ?>
                    <option value="<?= $komp->kode_merk ?>"><?= $komp->kode_merk." ".$komp->merk ?>
                    </option>
                    <?php
                        }
                    }
            foreach ($listmerk as $komp) {
                if ($kode_merk <> $komp->kode_merk) {
                    ?>
                    <option value="<?= $komp->kode_merk ?>"><?= $komp->kode_merk." ".$komp->merk ?>
                    </option>
                    <?php
                }
            }

            ?>
                </select>
            </div>
            <div class="form-group">
                <label for=>Harga Beli <?php echo form_error('harga_beli') ?></label>
                <input type="number" class="form-control" name="harga_beli" id="harga_beli" placeholder="Harga Beli"
                    value="<?php echo $harga_beli; ?>" data-original="<?php echo $harga_beli; ?>" />
            </div>
            <div class="form-group">
                <label for=>Harga Jual <?php echo form_error('harga_jual') ?></label>
                <input type="number" class="form-control" name="harga_jual" id="harga_jual" placeholder="Harga Jual"
                    value="<?php echo $harga_jual; ?>" />
            </div>
            <div class="form-group">
                <label for=>Stok <?php echo form_error('stok') ?></label>
                <input type="number" step="any" class="form-control" name="stok" id="stok" placeholder="Stok"
                    value="<?php echo $stok; ?>" />
            </div>
            <div class="form-group">
                <label for="keterangan">Keterangan <?php echo form_error('keterangan') ?></label>
                <textarea class="form-control" rows="3" name="keterangan" id="keterangan"
                    placeholder="Keterangan"><?php echo $keterangan; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?php echo site_url('barang') ?>" class="btn btn-default">Cancel</a>
            </form>
        </div>

    </div>
</div>

<?php if (!$is_insert): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("formBarangUpdate");
    const hargaBeliInput = document.getElementById("harga_beli");
    const originalHargaBeli = parseInt(hargaBeliInput.getAttribute("data-original"));

    form.addEventListener("submit", function(e) {
        e.preventDefault();

        const submitter = e.submitter || form.querySelector('button[type="submit"]');
        submitter.disabled = true;
        const origText = submitter.textContent;
        submitter.textContent = "Proses...";

        const formData = new FormData(form);
        const values = new URLSearchParams(formData);

        fetch("<?= base_url('Barang/update/'.$this->uri->segment(3)) ?>", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
                "X-Requested-With": "XMLHttpRequest",
            },
            body: values.toString(),
        })
        .then(response => response.text())
        .then(data => {
            const result = JSON.parse(data.trim());

            if (!result.status) {
                alert("Gagal menyimpan data");
                submitter.disabled = false;
                submitter.textContent = origText;
                return;
            }

            const hargaBeliChanged = parseInt(hargaBeliInput.value) !== originalHargaBeli;

            if (hargaBeliChanged && result.sync_enabled) {
                return fetch(result.sync_target_url + "/sync/receive_barang_harga", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        items: [{
                            kode_barang: result.kode_barang,
                            harga_beli: result.harga_beli,
                        }]
                    }),
                });
            }
        })
        .then(() => {
            location.href = "<?= site_url('barang') ?>";
        })
        .catch(error => {
            console.error("Error:", error);
            location.href = "<?= site_url('barang') ?>";
        });
    });
});
</script>
<?php endif; ?>