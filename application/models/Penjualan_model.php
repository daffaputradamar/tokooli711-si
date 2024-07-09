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
            $this->db->where("(kode_karyawan = '$filterkaryawan') AND (
                LOWER(kode_jual) = '$_cari' OR
                LOWER(tanggal_jual) = '$_cari' OR
                LOWER(kode_admin) = '$_cari' OR
                LOWER(kode_karyawan) = '$_cari' OR
                LOWER(keterangan) = '$_cari' OR
                LOWER(nomor_polisi) = '$_cari' OR
                LOWER(ongkos_karyawan) = '$_cari' OR
                LOWER(total) = '$_cari' OR
                LOWER(bayar) = '$_cari' OR
                LOWER(pelanggan) = '$_cari'
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
            $this->db->where("(kode_karyawan = '$filterkaryawan') AND (
                LOWER(kode_jual) = '$_cari' OR
                LOWER(tanggal_jual) = '$_cari' OR
                LOWER(kode_admin) = '$_cari' OR
                LOWER(kode_karyawan) = '$_cari' OR
                LOWER(keterangan) = '$_cari' OR
                LOWER(nomor_polisi) = '$_cari' OR
                LOWER(ongkos_karyawan) = '$_cari' OR
                LOWER(total) = '$_cari' OR
                LOWER(bayar) = '$_cari' OR
                LOWER(pelanggan) = '$_cari'
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

        if (!empty($kode_barang)) {
            $sql .= " AND EXISTS (
                    SELECT 1 FROM penjualan_detail pd WHERE pd.kode_jual = p.kode_jual
                    AND pd.kode_barang = '$kode_barang'
                    )
                    AND p.tanggal_jual_date BETWEEN '$tgl_awal' AND '$tgl_akhir'";
        }
        
        if ($filterkaryawan <> 'ALL') {
            $sql .= " AND p.kode_karyawan = '$filterkaryawan'";
        }

        if (!empty($cari)) {
            $sql .= " AND (LOWER(p.kode_jual) LIKE '%$cari%' OR LOWER(p.tanggal_jual) LIKE '%$cari%' OR LOWER(p.kode_admin) LIKE '%$cari%' OR LOWER(p.kode_karyawan) LIKE '%$cari%' OR LOWER(p.keterangan) LIKE '%$cari%' OR LOWER(p.nomor_polisi) LIKE '%$cari%' OR LOWER(p.km_kendaraan) LIKE '%$cari%' OR LOWER(p.ongkos_karyawan) LIKE '%$cari%' OR LOWER(p.total) LIKE '%$cari%' OR LOWER(p.bayar) LIKE '%$cari%' OR LOWER(p.pelanggan) LIKE '%$cari%')";
        }
            
        $sql .= " ORDER BY p.kode_jual DESC;";

        $result = $this->db->query($sql)->result();

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
        return $this->db->query('select sum(penjualan_detail.jumlah) as jumlah, substr(penjualan.tanggal_jual,4,2) as bulan , substr(penjualan.tanggal_jual,7,4) as tahun  from penjualan left join penjualan_detail on penjualan.kode_jual=penjualan_detail.kode_jual where penjualan_detail.kode_barang="'.$idbarang.'" AND substr(penjualan.tanggal_jual,7,4)="'.$tahun.'" AND substr(penjualan.tanggal_jual,4,2)>="'.$bulan.'" group by substr(penjualan.tanggal_jual,4,2),substr(penjualan.tanggal_jual,7,4)')->result();
    }

    public function jumlahdata_min($idbarang, $tahun, $bulan)
    {
        return $this->db->query('select sum(penjualan_detail.jumlah) as jumlah, substr(penjualan.tanggal_jual,4,2) as bulan , substr(penjualan.tanggal_jual,7,4) as tahun  from penjualan left join penjualan_detail on penjualan.kode_jual=penjualan_detail.kode_jual where penjualan_detail.kode_barang="'.$idbarang.'" AND substr(penjualan.tanggal_jual,7,4)="'.$tahun.'" AND substr(penjualan.tanggal_jual,4,2)<"'.$bulan.'" group by substr(penjualan.tanggal_jual,4,2),substr(penjualan.tanggal_jual,7,4)')->result();
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
        $tanggal1 = date('d-m-Y', strtotime($tanggal1));
        $tgl1 = explode('-', $tanggal1);

        $tanggal2 = date('d-m-Y', strtotime($tanggal2));
        $tgl2 = explode('-', $tanggal2);

        return $this->db->query('select *, substr(tanggal_jual,4,2),substr(tanggal_jual,7,4) from penjualan left join penjualan_detail on penjualan.kode_jual=penjualan_detail.kode_jual left join barang on penjualan_detail.kode_barang =barang.kode_barang  where  (substr(tanggal_jual,1,2)>="'.$tgl1[0].'" and substr(tanggal_jual,4,2)>="'.$tgl1[1].'" and substr(tanggal_jual,7,4)>="'.$tgl1[2].'") and (substr(tanggal_jual,1,2)<="'.$tgl2[0].'" and substr(tanggal_jual,4,2)<="'.$tgl2[1].'" and substr(tanggal_jual,7,4)<="'.$tgl2[2].'") and barang.kode_barang="'.$kode.'"')->result();

        $tanggal1=date('Y-m-d', strtotime($tanggal1));
        $tanggal2=date('Y-m-d', strtotime($tanggal2));

        $sql = "SELECT *
            , SUBSTR(tanggal_jual,4,2)
            , SUBSTR(tanggal_jual,7,4) 
            FROM penjualan p
            LEFT JOIN penjualan_detail pd ON p.kode_jual = pd.kode_jual 
            LEFT JOIN barang b ON pd.kode_barang = b.kode_barang  
            WHERE tanggal_jual_date BETWEEN '$tanggal1' and '$tanggal2' 
            AND b.kode_barang = '$kode'"; 

        return $this->db->query($sql)->result();
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

        $transaksi = $this->db->query('select sum(harga_jual*jumlah) as transaksi from penjualan_detail left join penjualan on penjualan_detail.kode_jual=penjualan.kode_jual where substr(penjualan.tanggal_jual,1,2)="'.$tgl[0].'" and substr(penjualan.tanggal_jual,4,2)="'.$tgl[1].'" and substr(penjualan.tanggal_jual,7,4)="'.$tgl[2].'" and penjualan.kode_karyawan="'.$kasir.'" ')->row();

        $bea = $this->db->query('select sum(ongkos_karyawan) as bea from penjualan where substr(penjualan.tanggal_jual,1,2)="'.$tgl[0].'" and substr(penjualan.tanggal_jual,4,2)="'.$tgl[1].'" and substr(penjualan.tanggal_jual,7,4)="'.$tgl[2].'" and penjualan.kode_karyawan="'.$kasir.'" ')->row();

        $nama = $this->db->query('select nama_karyawan as nama from karyawan where kode_karyawan="'.$kasir.'"')->row();

        $data = array();
        $data['total'] = $transaksi->transaksi + $bea->bea;
        $data['transaksi'] = $transaksi->transaksi;
        $data['bea'] = $bea->bea;
        $data['nama'] = $nama->nama;

        return $data;
    }
}

/* End of file Penjualan_model.php */
/* Location: ./application/models/Penjualan_model.php */
/*  2016-07-29 19:31:02 */
/* Computer : Maruf */
