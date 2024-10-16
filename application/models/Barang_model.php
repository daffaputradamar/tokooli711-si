<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Barang_model extends CI_Model
{
    public $table = 'barang';
    public $primary = 'kode_barang';

    public function __construct()
    {
        parent::__construct();
    }


    public function selectByAll()
    {
        $this->db->join('merk', 'barang.kode_merk = merk.kode_merk', 'left');
        return $this->db->get($this->table)->result();
    }

    public function selectById($id)
    {
        $this->db->where($this->primary, $id);
        $this->db->join('merk', 'barang.kode_merk = merk.kode_merk', 'left');
        return $this->db->get($this->table)->row();
    }

    public function total_rows()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function get_limit_data($limit, $start = 0, $cari = null)
    {
        $this->db->like('kode_barang', $cari);
        $this->db->or_like('nama_barang', $cari);
        $this->db->or_like('merk.kode_merk', $cari);
        $this->db->or_like('merk', $cari);
        $this->db->or_like('harga_beli', $cari);
        $this->db->or_like('harga_jual', $cari);
        $this->db->or_like('stok', $cari);
        $this->db->or_like('barang.keterangan', $cari);
        $this->db->limit($limit, $start);
        $this->db->join('merk', 'barang.kode_merk = merk.kode_merk', 'left');
        return $this->db->get($this->table)->result();
    }

    public function getAll_by($cari = null)
    {
        $this->db->like('kode_barang', $cari);
        $this->db->or_like('nama_barang', $cari);
        $this->db->or_like('merk.kode_merk', $cari);
        $this->db->or_like('merk', $cari);
        $this->db->or_like('harga_beli', $cari);
        $this->db->or_like('harga_jual', $cari);
        $this->db->or_like('stok', $cari);
        $this->db->or_like('barang.keterangan', $cari);
        $this->db->join('merk', 'barang.kode_merk = merk.kode_merk', 'left');
        return $this->db->get($this->table)->result();
    }

    public function get_movement_by_barang($kode_barang, $datetime_awal, $datetime_akhir)
    {
        $sql_penjualan = "
            SELECT pd.kode_jual kode_trans, 'Penjualan' jenis_trans, 
                STR_TO_DATE(CONCAT(p.tanggal_jual_date, ' ', p.waktu_jual), '%Y-%m-%d %r') AS tanggal_trans, pd.jumlah
            FROM penjualan p
            JOIN penjualan_detail pd ON p.kode_jual = pd.kode_jual
            WHERE pd.kode_barang = '$kode_barang'
            AND STR_TO_DATE(CONCAT(p.tanggal_jual_date, ' ', p.waktu_jual), '%Y-%m-%d %r') 
                BETWEEN '$datetime_awal' AND '$datetime_akhir'
            ORDER BY tanggal_trans
        ";
        $penjualan = $this->db->query($sql_penjualan)->result();


        $sql_pembelian = "
            SELECT bd.kode_beli kode_trans, 'Pembelian' jenis_trans, 
                STR_TO_DATE(CONCAT(b.tanggal_beli_date, ' ', b.waktu_beli), '%Y-%m-%d %r') AS tanggal_trans, bd.jumlah
            FROM pembelian b
            JOIN pembelian_detail bd ON b.kode_beli = bd.kode_beli
            WHERE bd.kode_barang = '$kode_barang'
            AND STR_TO_DATE(CONCAT(b.tanggal_beli_date, ' ', b.waktu_beli), '%Y-%m-%d %r') 
                BETWEEN '$datetime_awal' AND '$datetime_akhir'
            ORDER BY tanggal_trans
        ";
        $pembelian = $this->db->query($sql_pembelian)->result();



        $merged = array_merge($penjualan, $pembelian);

        // Sort by 'tanggal_trans' in ascending order
        usort($merged, function ($a, $b) {
            $dateA = DateTime::createFromFormat('Y-m-d H:i:s', $a->tanggal_trans);
            $dateB = DateTime::createFromFormat('Y-m-d H:i:s', $b->tanggal_trans);

            // Handle any invalid dates (return 0 to keep order unchanged)
            if (!$dateA || !$dateB) {
                return 0;
            }

            return $dateA <=> $dateB;  // Ascending order
        });

        return $merged;
    }

    public function get_list_history_by_barang($kode_barang, $datetime_awal, $datetime_akhir, $limit, $start = 0)
    {
        // SQL query to count the total number of records (without LIMIT and OFFSET)
        $count_sql = "
            SELECT COUNT(*) as total_count
            FROM barang_stok_history p
            WHERE p.kode_barang = '$kode_barang'
            AND p.createddate BETWEEN '$datetime_awal' AND '$datetime_akhir'
        ";

        // Execute the count query
        $count_result = $this->db->query($count_sql)->row();
        $total_count = $count_result->total_count;

        // SQL query to fetch the limited list with LIMIT and OFFSET
        $sql = "
            SELECT p.*, b.nama_barang
            FROM barang_stok_history p
            JOIN barang b ON p.kode_barang = b.kode_barang
            WHERE p.kode_barang = '$kode_barang'
            AND p.createddate BETWEEN '$datetime_awal' AND '$datetime_akhir'
            ORDER BY p.createddate DESC
            LIMIT $limit OFFSET $start
        ";

        // Execute the list query
        $result = $this->db->query($sql)->result();

        // Return both the result list and the total count
        return [
            'list' => $result,
            'total_count' => $total_count
        ];
    }

    public function get_history_by_id($id)
    {
        // SQL query to fetch the limited list with LIMIT and OFFSET
        $sql = "
            SELECT p.*, b.nama_barang
            FROM barang_stok_history p
            JOIN barang b ON p.kode_barang = b.kode_barang
            WHERE p.id = '$id'
        ";

        $result = $this->db->query($sql)->row();

        // Return both the result list and the total count
        return $result;
    }

    public function update_history_by_id($id, $stok)
    {
        $data = array(
            'stok' => $stok
        );

        $this->db->where('id', $id);
        $this->db->update('barang_stok_history', $data);
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
    }

    public function update($id, $data, $user)
    {
        // Fetch the current stock before updating
        $this->db->where($this->primary, $id);
        $current_data = $this->db->get($this->table)->row();

        // Check if the stock (e.g. `stok`) has changed
        if (isset($data['stok']) && $data['stok'] != $current_data->stok) {
            // Insert into barang_stok_history if the stock has changed
            $this->db->query("
                INSERT INTO barang_stok_history (kode_barang, stok, createddate, createdby, modifieddate, modifiedby)
                VALUES (
                    '{$current_data->kode_barang}', 
                    {$current_data->stok}, 
                    NOW(), 
                    '{$user}',
                    NOW(), 
                    '{$user}'
                )
            ");
        }

        $this->db->where($this->primary, $id);
        $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        $this->db->where($this->primary, $id);
        $this->db->delete($this->table);
    }
}

/* End of file Barang_model.php */
/* Location: ./application/models/Barang_model.php */
/*  2016-07-29 19:31:02 */
/* Computer : Maruf */
