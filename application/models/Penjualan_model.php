<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Penjualan_model extends CI_Model
{
    public $table = 'penjualan';
    public $primary = 'kode_jual';

    public function __construct()
    {
        parent::__construct();
    }

    public function selectByAll()
    {
        return $this->db->get($this->table)->result();
    }

    public function selectById($id)
    {
        $this->db->where($this->primary, $id);
        return $this->db->get($this->table)->row();
    }

    public function total_rows($cari = null, $filterkaryawan = 'ALL')
    {
        $this->db->from($this->table);
        $_cari = strtolower($cari);
        if ($filterkaryawan <> 'ALL') {
            // Both values come from the URL: escape() quotes them safely
            $k = $this->db->escape($filterkaryawan);
            $c = $this->db->escape($_cari);
            $this->db->where("(kode_karyawan = $k) AND (
                LOWER(kode_jual) = $c OR
                LOWER(tanggal_jual) = $c OR
                LOWER(kode_admin) = $c OR
                LOWER(kode_karyawan) = $c OR
                LOWER(keterangan) = $c OR
                LOWER(nomor_polisi) = $c OR
                LOWER(ongkos_karyawan) = $c OR
                LOWER(total) = $c OR
                LOWER(bayar) = $c OR
                LOWER(pelanggan) = $c
            )");
        } else {
            $this->db->like('LOWER(kode_jual)', $_cari);
            $this->db->or_like('LOWER(tanggal_jual)', $_cari);
            $this->db->or_like('LOWER(kode_admin)', $_cari);
            $this->db->or_like('LOWER(kode_karyawan)', $_cari);
            $this->db->or_like('LOWER(keterangan)', $_cari);
            $this->db->or_like('LOWER(nomor_polisi)', $_cari);
            $this->db->or_like('LOWER(ongkos_karyawan)', $_cari);
            $this->db->or_like('LOWER(total)', $_cari);
            $this->db->or_like('LOWER(bayar)', $_cari);
            $this->db->or_like('LOWER(pelanggan)', $_cari);
        }
        return $this->db->count_all_results();
    }

    public function get_limit_data($limit, $start = 0, $cari = null, $filterkaryawan = 'ALL')
    {
        $_cari = strtolower($cari);
        if ($filterkaryawan <> 'ALL') {
            // Both values come from the URL: escape() quotes them safely
            $k = $this->db->escape($filterkaryawan);
            $c = $this->db->escape($_cari);
            $this->db->where("(kode_karyawan = $k) AND (
                LOWER(kode_jual) = $c OR
                LOWER(tanggal_jual) = $c OR
                LOWER(kode_admin) = $c OR
                LOWER(kode_karyawan) = $c OR
                LOWER(keterangan) = $c OR
                LOWER(nomor_polisi) = $c OR
                LOWER(ongkos_karyawan) = $c OR
                LOWER(total) = $c OR
                LOWER(bayar) = $c OR
                LOWER(pelanggan) = $c
            )");
        } else {
            $this->db->like('LOWER(kode_jual)', $_cari);
            $this->db->or_like('LOWER(tanggal_jual)', $_cari);
            $this->db->or_like('LOWER(kode_admin)', $_cari);
            $this->db->or_like('LOWER(kode_karyawan)', $_cari);
            $this->db->or_like('LOWER(keterangan)', $_cari);
            $this->db->or_like('LOWER(nomor_polisi)', $_cari);
            $this->db->or_like('LOWER(ongkos_karyawan)', $_cari);
            $this->db->or_like('LOWER(total)', $_cari);
            $this->db->or_like('LOWER(bayar)', $_cari);
            $this->db->or_like('LOWER(pelanggan)', $_cari);
        }

        $this->db->order_by('kode_jual', 'desc');
        // $this->db->limit($limit, $start);
        return $this->db->get($this->table)->result();
    }

    public function get_limit_data_filter($limit, $start, $kode_barang, $tgl_awal, $tgl_akhir, $filterkaryawan = 'ALL', $cari = '')
    {
        $sql = "SELECT p.*
            FROM penjualan p
            WHERE 1 = 1";
        $params = array();

        if (!empty($kode_barang)) {
            $sql .= " AND EXISTS (
                    SELECT 1 FROM penjualan_detail pd WHERE pd.kode_jual = p.kode_jual
                    AND pd.kode_barang = ?
                    )
                    AND p.tanggal_jual_date BETWEEN ? AND ?";
            array_push($params, $kode_barang, $tgl_awal, $tgl_akhir);
        }

        if ($filterkaryawan <> 'ALL') {
            $sql .= " AND p.kode_karyawan = ?";
            $params[] = $filterkaryawan;
        }

        if (!empty($cari)) {
            $columns = array('kode_jual', 'tanggal_jual', 'kode_admin', 'kode_karyawan', 'keterangan', 'nomor_polisi', 'km_kendaraan', 'ongkos_karyawan', 'total', 'bayar', 'pelanggan');
            $likes = array();
            foreach ($columns as $column) {
                $likes[] = 'LOWER(p.' . $column . ') LIKE ?';
                $params[] = '%' . $cari . '%';
            }
            $sql .= " AND (" . implode(' OR ', $likes) . ")";
        }

        $sql .= " ORDER BY p.kode_jual DESC;";

        // Bound parameters: the filter values come straight from the URL
        $result = $this->db->query($sql, $params)->result();

        return $result;
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        $this->db->where($this->primary, $id);
        $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        $this->db->where($this->primary, $id);
        $this->db->delete($this->table);
    }

    public function jumlahdata_plus($idbarang, $tahun, $bulan)
    {
        return $this->db->query('select sum(penjualan_detail.jumlah) as jumlah, substr(penjualan.tanggal_jual,4,2) as bulan , substr(penjualan.tanggal_jual,7,4) as tahun  from penjualan left join penjualan_detail on penjualan.kode_jual=penjualan_detail.kode_jual where penjualan_detail.kode_barang=? AND substr(penjualan.tanggal_jual,7,4)=? AND substr(penjualan.tanggal_jual,4,2)>=? group by substr(penjualan.tanggal_jual,4,2),substr(penjualan.tanggal_jual,7,4)', array((string) $idbarang, (string) $tahun, (string) $bulan))->result();
    }

    public function jumlahdata_min($idbarang, $tahun, $bulan)
    {
        return $this->db->query('select sum(penjualan_detail.jumlah) as jumlah, substr(penjualan.tanggal_jual,4,2) as bulan , substr(penjualan.tanggal_jual,7,4) as tahun  from penjualan left join penjualan_detail on penjualan.kode_jual=penjualan_detail.kode_jual where penjualan_detail.kode_barang=? AND substr(penjualan.tanggal_jual,7,4)=? AND substr(penjualan.tanggal_jual,4,2)<? group by substr(penjualan.tanggal_jual,4,2),substr(penjualan.tanggal_jual,7,4)', array((string) $idbarang, (string) $tahun, (string) $bulan))->result();
    }

    public function gajiAll($tanggal)
    {
        $tanggal = date('d-m-Y', strtotime($tanggal));
        $tgl = explode('-', $tanggal);
        return $this->db->query('select  SUM(ongkos_karyawan) as gaji, karyawan.nama_karyawan, karyawan.telp_karyawan from penjualan left join karyawan on penjualan.kode_karyawan=karyawan.kode_karyawan where  substr(tanggal_jual,4,2)="'.$tgl[1].'" and substr(tanggal_jual,7,4)="'.$tgl[2].'" GROUP BY penjualan.kode_karyawan')->result();
    }
    public function gajiAllby($tanggal, $status)
    {
        $tanggal = date('d-m-Y', strtotime($tanggal));
        $tgl = explode('-', $tanggal);
        if ($status == 0) {
            $sql = $this->db->query('select  SUM(ongkos_karyawan) as gaji from penjualan where  substr(tanggal_jual,1,2)="'.$tgl[0].'" and substr(tanggal_jual,4,2)="'.$tgl[1].'" and substr(tanggal_jual,7,4)="'.$tgl[2].'"')->row();
        } else {
            $sql = $this->db->query('select  SUM(ongkos_karyawan) as gaji from penjualan where  substr(tanggal_jual,4,2)="'.$tgl[1].'" and substr(tanggal_jual,7,4)="'.$tgl[2].'"')->row();
        }
        return $sql->gaji;
    }

    public function get_all_sum_penjualan_by_kasir($tanggal)
    {
        $tanggal = date('d-m-Y', strtotime($tanggal));
        $tgl = explode('-', $tanggal);
        return $this->db->query('SELECT k.nama_karyawan, SUM(p.total) as total FROM karyawan k LEFT JOIN penjualan p ON k.kode_karyawan = p.kode_karyawan where substr(tanggal_jual,1,2)="'.$tgl[0].'" and substr(tanggal_jual,4,2)="'.$tgl[1].'" and substr(tanggal_jual,7,4)="'.$tgl[2].'"  GROUP BY k.nama_karyawan')->result();
    }

    public function laporan($tanggal, $status)
    {
        $tanggal = date('d-m-Y', strtotime($tanggal));

        $tgl = explode('-', $tanggal);
        if ($status == 0) {
            return $this->db->query('select penjualan.*, substr(tanggal_jual,4,2),substr(tanggal_jual,7,4) from penjualan where  substr(tanggal_jual,1,2)="'.$tgl[0].'" and substr(tanggal_jual,4,2)="'.$tgl[1].'" and substr(tanggal_jual,7,4)="'.$tgl[2].'"')->result();
        } else {
            return $this->db->query('select penjualan.*, substr(tanggal_jual,4,2),substr(tanggal_jual,7,4) from penjualan where  substr(tanggal_jual,4,2)="'.$tgl[1].'" and substr(tanggal_jual,7,4)="'.$tgl[2].'"')->result();
        }
    }

    public function laporan2($tanggal1, $tanggal2, $kode)
    {
        // The old version compared day, month and year separately, so any range
        // crossing a month boundary (e.g. 25-09 .. 05-10) returned nothing.
        $tanggal1 = date('Y-m-d', strtotime($tanggal1));
        $tanggal2 = date('Y-m-d', strtotime($tanggal2));

        $sql = "SELECT *
            , SUBSTR(tanggal_jual,4,2)
            , SUBSTR(tanggal_jual,7,4)
            FROM penjualan p
            LEFT JOIN penjualan_detail pd ON p.kode_jual = pd.kode_jual
            LEFT JOIN barang b ON pd.kode_barang = b.kode_barang
            WHERE p.tanggal_jual_date BETWEEN ? AND ?
            AND b.kode_barang = ?
            ORDER BY p.tanggal_jual_date, p.kode_jual";

        return $this->db->query($sql, array($tanggal1, $tanggal2, $kode))->result();
    }

    public function omsetAll()
    {
        $tanggal = date('d-m-Y');
        $tgl = explode('-', $tanggal);
        $data = $this->db->query('select sum(harga_jual*jumlah) as omset from penjualan_detail left join penjualan on penjualan_detail.kode_jual=penjualan.kode_jual where  substr(penjualan.tanggal_jual,4,2)="' . $tgl[1] . '" and substr(penjualan.tanggal_jual,7,4)="' . $tgl[2] . '"and substr(penjualan.tanggal_jual,1,2)="' . $tgl[0] . '"')->row();
        return $data->omset;
    }
    public function omsetByTgl($tanggal, $status)
    {
        $tanggal = date('d-m-Y', strtotime($tanggal));
        $tgl = explode('-', $tanggal);
        if ($status == 0) {
            $data = $this->db->query('select (sum(harga_jual*jumlah)-sum(harga_beli*jumlah)) as omset from penjualan_detail left join penjualan on penjualan_detail.kode_jual=penjualan.kode_jual where  substr(penjualan.tanggal_jual,1,2)="'.$tgl[0].'" and substr(penjualan.tanggal_jual,4,2)="'.$tgl[1].'" and substr(penjualan.tanggal_jual,7,4)="'.$tgl[2].'"')->row();
        } else {
            $data = $this->db->query('select (sum(harga_jual*jumlah)-sum(harga_beli*jumlah)) as omset from penjualan_detail left join penjualan on penjualan_detail.kode_jual=penjualan.kode_jual where substr(penjualan.tanggal_jual,4,2)="'.$tgl[1].'" and substr(penjualan.tanggal_jual,7,4)="'.$tgl[2].'"')->row();
        }
        return $data->omset;
    }

    public function beaLainByTgl($tanggal, $status)
    {
        $tanggal = date('d-m-Y', strtotime($tanggal));
        $tgl = explode('-', $tanggal);
        if ($status == 1) {
            $data = $this->db->query('select sum(ongkos_karyawan) as omset from penjualan where substr(penjualan.tanggal_jual,1,2)="'.$tgl[0].'" and substr(penjualan.tanggal_jual,4,2)="'.$tgl[1].'" and substr(penjualan.tanggal_jual,7,4)="'.$tgl[2].'"')->row();
        }
        return $data->omset;
    }

    public function omsetByTgl2($tanggal)
    {
        $tanggal = date('d-m-Y', strtotime($tanggal));
        $tgl = explode('-', $tanggal);
        $data = $this->db->query('select SUM(total) as omset from penjualan where substr(penjualan.tanggal_jual,1,2)="'.$tgl[0].'" and substr(penjualan.tanggal_jual,4,2)="'.$tgl[1].'" and substr(penjualan.tanggal_jual,7,4)="'.$tgl[2].'"')->row();
        return $data->omset;
    }

    public function beliByTgl($tanggal, $status)
    {
        $tanggal = date('d-m-Y', strtotime($tanggal));
        $tgl = explode('-', $tanggal);
        if ($status == 0) {
            $data = $this->db->query('select sum(harga_beli*jumlah) as hbeli from penjualan_detail left join penjualan on penjualan_detail.kode_jual=penjualan.kode_jual where  substr(penjualan.tanggal_jual,1,2)="'.$tgl[0].'" and substr(penjualan.tanggal_jual,4,2)="'.$tgl[1].'" and substr(penjualan.tanggal_jual,7,4)="'.$tgl[2].'"')->row();
        } else {
            $data = $this->db->query('select sum(harga_beli*jumlah) as hbeli from penjualan_detail left join penjualan on penjualan_detail.kode_jual=penjualan.kode_jual where  substr(penjualan.tanggal_jual,4,2)="'.$tgl[1].'" and substr(penjualan.tanggal_jual,7,4)="'.$tgl[2].'"')->row();
        }
        return $data->hbeli;
    }
    public function jualByTgl($tanggal, $status)
    {
        $tanggal = date('d-m-Y', strtotime($tanggal));
        $tgl = explode('-', $tanggal);
        if ($status == 0) {
            $data = $this->db->query('select sum(harga_jual*jumlah) as hjual from penjualan_detail left join penjualan on penjualan_detail.kode_jual=penjualan.kode_jual where  substr(penjualan.tanggal_jual,1,2)="'.$tgl[0].'" and substr(penjualan.tanggal_jual,4,2)="'.$tgl[1].'" and substr(penjualan.tanggal_jual,7,4)="'.$tgl[2].'"')->row();
        } else {
            $data = $this->db->query('select sum(harga_jual*jumlah) as hjual from penjualan_detail left join penjualan on penjualan_detail.kode_jual=penjualan.kode_jual where  substr(penjualan.tanggal_jual,4,2)="'.$tgl[1].'" and substr(penjualan.tanggal_jual,7,4)="'.$tgl[2].'"')->row();
        }
        return $data->hjual;
    }
    public function totalByTgl($tanggal, $status)
    {
        $tanggal = date('d-m-Y', strtotime($tanggal));
        $tgl = explode('-', $tanggal);
        if ($status == 0) {
            $data = $this->db->query('select sum(total) as total from penjualan where  substr(penjualan.tanggal_jual,1,2)="'.$tgl[0].'" and substr(penjualan.tanggal_jual,4,2)="'.$tgl[1].'" and substr(penjualan.tanggal_jual,7,4)="'.$tgl[2].'"')->row();
        } else {
            $data = $this->db->query('select sum(total) as total from penjualan where  substr(penjualan.tanggal_jual,4,2)="'.$tgl[1].'" and substr(penjualan.tanggal_jual,7,4)="'.$tgl[2].'"')->row();
        }
        return $data->total;
    }

    public function kasir($kasir)
    {
        $tanggal = date('d-m-Y');
        $tgl = explode('-', $tanggal);

        // $total = $this->db->query('select SUM(total) as total from penjualan where substr(penjualan.tanggal_jual,1,2)="'.$tgl[0].'" and substr(penjualan.tanggal_jual,4,2)="'.$tgl[1].'" and substr(penjualan.tanggal_jual,7,4)="'.$tgl[2].'" and penjualan.kode_karyawan="'.$kasir.'" ')->row();

        $transaksi = $this->db->query('select sum(harga_jual*jumlah) as transaksi from penjualan_detail left join penjualan on penjualan_detail.kode_jual=penjualan.kode_jual where substr(penjualan.tanggal_jual,1,2)="'.$tgl[0].'" and substr(penjualan.tanggal_jual,4,2)="'.$tgl[1].'" and substr(penjualan.tanggal_jual,7,4)="'.$tgl[2].'" and penjualan.kode_karyawan=? ', array((string) $kasir))->row();

        $bea = $this->db->query('select sum(ongkos_karyawan) as bea from penjualan where substr(penjualan.tanggal_jual,1,2)="'.$tgl[0].'" and substr(penjualan.tanggal_jual,4,2)="'.$tgl[1].'" and substr(penjualan.tanggal_jual,7,4)="'.$tgl[2].'" and penjualan.kode_karyawan=? ', array((string) $kasir))->row();

        $nama = $this->db->query('select nama_karyawan as nama from karyawan where kode_karyawan=?', array((string) $kasir))->row();

        $data = array();
        $data['total'] = $transaksi->transaksi + $bea->bea;
        $data['transaksi'] = $transaksi->transaksi;
        $data['bea'] = $bea->bea;
        $data['nama'] = $nama->nama;

        return $data;
    }

    public function laporan_tahunan($tahun)
    {
        $tahun = intval($tahun);
        $sql = "SELECT DATE_FORMAT(tanggal_jual_date, '%Y-%m') AS periode,
                       FLOOR(SUM(total) / 500) * 500 AS total_penjualan
                FROM penjualan
                WHERE YEAR(tanggal_jual_date) = " . $tahun . "
                GROUP BY periode
                ORDER BY periode";
        return $this->db->query($sql);
    }

    public function laporan_tahunan_with_option($tahun, $tipe = 'jual')
    {
        $tahun = intval($tahun);
        // Use a date range to allow the DB to use an index on tanggal_jual_date
        $start = $tahun . '-01-01';
        $end = ($tahun + 1) . '-01-01';

        if ($tipe == 'hpp') {
            // Get total HPP (harga beli) — aggregate from detail table
            $sql = "SELECT DATE_FORMAT(p.tanggal_jual_date, '%Y-%m') AS periode,
                           FLOOR(SUM(pd.harga_beli * pd.jumlah) / 500) * 500 AS total_harga
                    FROM penjualan p
                    INNER JOIN penjualan_detail pd ON p.kode_jual = pd.kode_jual
                    WHERE p.tanggal_jual_date >= '" . $start . "' AND p.tanggal_jual_date < '" . $end . "'
                    GROUP BY periode
                    ORDER BY periode";
        } else {
            // Get total harga jual (default)
            $sql = "SELECT DATE_FORMAT(tanggal_jual_date, '%Y-%m') AS periode,
                           FLOOR(SUM(total) / 500) * 500 AS total_harga
                    FROM penjualan
                    WHERE tanggal_jual_date >= '" . $start . "' AND tanggal_jual_date < '" . $end . "'
                    GROUP BY periode
                    ORDER BY periode";
        }

        return $this->db->query($sql);
    }

    public function get_tahun_list()
    {
        return $this->db->query("SELECT DISTINCT YEAR(tanggal_jual_date) AS tahun FROM penjualan WHERE tanggal_jual_date IS NOT NULL ORDER BY tahun DESC")->result();
    }
}

/* End of file Penjualan_model.php */
/* Location: ./application/models/Penjualan_model.php */
/*  2016-07-29 19:31:02 */
/* Computer : Maruf */
