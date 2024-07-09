<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pembelian_model extends CI_Model
{
    public $table = 'pembelian';
    public $primary = 'kode_beli';

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
        $this->db->join("suplier", "suplier.kode_suplier=pembelian.kode_suplier", "left");
        return $this->db->get($this->table)->row();
    }

    public function total_rows()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function get_limit_data($limit, $start = 0, $cari = null)
    {
        $this->db->like('kode_beli', $cari);
        $this->db->or_like('tanggal_beli', $cari);
        $this->db->or_like('kode_admin', $cari);
        $this->db->or_like('no_faktur', $cari);
        $this->db->or_like('total', $cari);
        $this->db->or_like('suplier.nama_suplier', $cari);
        $this->db->order_by('kode_beli', 'desc');
        // $this->db->limit($limit, $start);
        $this->db->join("suplier", "suplier.kode_suplier=pembelian.kode_suplier", "left");
        return $this->db->get($this->table)->result();
    }

    public function get_limit_data_filter($limit, $start = 0,$kode_barang, $tgl_awal, $tgl_akhir, $cari = '')
    {
        $sql = "SELECT p.*, s.*
            FROM pembelian p
            LEFT JOIN suplier s ON p.kode_suplier = s.kode_suplier
            WHERE 1 = 1";

        if (!empty($kode_barang)) {
            $sql .= " AND EXISTS (
                    SELECT 1 FROM pembelian_detail pd WHERE pd.kode_beli = p.kode_beli
                    AND pd.kode_barang = '$kode_barang'
                    )
                    AND p.tanggal_beli_date BETWEEN '$tgl_awal' AND '$tgl_akhir'";
        }

        if (!empty($cari)) {
            $sql .= " AND (LOWER(p.kode_beli) LIKE '%$cari%' OR LOWER(p.tanggal_beli) LIKE '%$cari%' OR LOWER(p.kode_admin) LIKE '%$cari%' OR LOWER(p.no_faktur) LIKE '%$cari%' OR LOWER(p.total) LIKE '%$cari%' OR LOWER(s.nama_suplier) LIKE '%$cari%')";
        }
            
        $sql .= " ORDER BY p.kode_beli DESC;";

        $result = $this->db->query($sql)->result();

        return $result;

        return $this->db->query("
            SELECT p.*, s.*
            FROM pembelian p
            JOIN pembelian_detail pd ON p.kode_beli = pd.kode_beli
            LEFT JOIN suplier s ON p.kode_suplier = s.kode_suplier
            WHERE pd.kode_barang = '$kode_barang'
            AND p.tanggal_beli_date BETWEEN '$tgl_awal' AND '$tgl_akhir'
            ORDER BY p.kode_beli DESC
        ")->result();
    }


    public function jumlahdata_plus($idbarang, $tahun, $bulan)
    {
        return $this->db->query('select avg(pembelian_detail.harga_beli) as harga_beli, substr(pembelian.tanggal_beli,4,2) as bulan , substr(pembelian.tanggal_beli,7,4) as tahun  from pembelian left join pembelian_detail on pembelian.kode_beli=pembelian_detail.kode_beli where pembelian_detail.kode_barang="'.$idbarang.'" AND substr(pembelian.tanggal_beli,7,4)="'.$tahun.'" AND substr(pembelian.tanggal_beli,4,2)>="'.$bulan.'" group by substr(pembelian.tanggal_beli,4,2),substr(pembelian.tanggal_beli,7,4)')->result();
    }

    public function jumlahdata_min($idbarang, $tahun, $bulan)
    {
        return $this->db->query('select avg(pembelian_detail.harga_beli) as harga_beli, substr(pembelian.tanggal_beli,4,2) as bulan , substr(pembelian.tanggal_beli,7,4) as tahun  from pembelian left join pembelian_detail on pembelian.kode_beli=pembelian_detail.kode_beli where pembelian_detail.kode_barang="'.$idbarang.'" AND substr(pembelian.tanggal_beli,7,4)="'.$tahun.'" AND substr(pembelian.tanggal_beli,4,2)<"'.$bulan.'" group by substr(pembelian.tanggal_beli,4,2),substr(pembelian.tanggal_beli,7,4)')->result();
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
        $this->db->where($this->primary, $id);
        $this->db->delete('pembelian_detail');
    }
    public function laporan($tanggal, $status)
    {
        $tanggal=date('d-m-Y', strtotime($tanggal));

        $tgl=explode('-', $tanggal);
        if ($status==0) {
            return $this->db->query('select pembelian.* from pembelian where substr(tanggal_beli,1,2)="'.$tgl[0].'" and substr(tanggal_beli,4,2)="'.$tgl[1].'" and substr(tanggal_beli,7,4)="'.$tgl[2].'"')->result();
        } else {
            return $this->db->query('select pembelian.* from pembelian where  substr(tanggal_beli,4,2)="'.$tgl[1].'" and substr(tanggal_beli,7,4)="'.$tgl[2].'"')->result();
        }
    }
    public function laporan2($tanggal1, $tanggal2, $kode)
    {
        $tanggal1=date('Y-m-d', strtotime($tanggal1));
        $tanggal2=date('Y-m-d', strtotime($tanggal2));

        $sql = "SELECT *
            , SUBSTR(tanggal_beli,4,2)
            , SUBSTR(tanggal_beli,7,4) 
            FROM pembelian p
            LEFT JOIN pembelian_detail pd ON p.kode_beli = pd.kode_beli 
            LEFT JOIN barang b ON pd.kode_barang = b.kode_barang  
            WHERE tanggal_beli_date BETWEEN '$tanggal1' and '$tanggal2' 
            AND b.kode_barang = '$kode'"; 

        return $this->db->query($sql)->result();
    }
    public function beliByTgl($tanggal, $status)
    {
        $tanggal=date('d-m-Y', strtotime($tanggal));
        $tgl=explode('-', $tanggal);
        if ($status==0) {
            $data=$this->db->query('select sum(harga_beli*jumlah) as hbeli from pembelian_detail left join pembelian on pembelian_detail.kode_beli=pembelian.kode_beli where substr(pembelian.tanggal_beli,1,2)="'.$tgl[0].'" and substr(pembelian.tanggal_beli,4,2)="'.$tgl[1].'" and substr(pembelian.tanggal_beli,7,4)="'.$tgl[2].'"')->row();
        } else {
            $data=$this->db->query('select sum(harga_beli*jumlah) as hbeli from pembelian_detail left join pembelian on pembelian_detail.kode_beli=pembelian.kode_beli where  substr(pembelian.tanggal_beli,4,2)="'.$tgl[1].'" and substr(pembelian.tanggal_beli,7,4)="'.$tgl[2].'"')->row();
        }
        return $data->hbeli;
    }
}

/* End of file Pembelian_model.php */
/* Location: ./application/models/Pembelian_model.php */
/*  2016-07-29 19:31:02 */
/* Computer : Maruf */
