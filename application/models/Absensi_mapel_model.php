<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi_mapel_model extends CI_Model {

    private $table = 'absensi_mapel';

    public function get_all($search = '', $limit = 10, $offset = 0) {
        $this->db->select('absensi_mapel.*, siswa.nama as nama_siswa, siswa.nis, 
                          jurnal.tanggal, mapel.nama_mapel, kelas.nama_kelas');
        $this->db->from($this->table);
        $this->db->join('jurnal', 'jurnal.id = absensi_mapel.jurnal_id', 'left');
        $this->db->join('siswa', 'siswa.id = absensi_mapel.siswa_id', 'left');
        $this->db->join('jadwal', 'jadwal.id = jurnal.jadwal_id', 'left');
        $this->db->join('mapel', 'mapel.id = jadwal.mapel_id', 'left');
        $this->db->join('kelas', 'kelas.id = jadwal.kelas_id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('siswa.nama', $search);
            $this->db->or_like('siswa.nis', $search);
            $this->db->group_end();
        }
        
        $this->db->order_by('jurnal.tanggal', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    public function count_all($search = '') {
        $this->db->from($this->table);
        $this->db->join('jurnal', 'jurnal.id = absensi_mapel.jurnal_id', 'left');
        $this->db->join('siswa', 'siswa.id = absensi_mapel.siswa_id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('siswa.nama', $search);
            $this->db->or_like('siswa.nis', $search);
            $this->db->group_end();
        }
        
        return $this->db->count_all_results();
    }

    public function get_by_id($id) {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    public function get_by_jurnal($jurnal_id) {
        $this->db->select('absensi_mapel.*, siswa.nama as nama_siswa, siswa.nis');
        $this->db->from($this->table);
        $this->db->join('siswa', 'siswa.id = absensi_mapel.siswa_id', 'left');
        $this->db->where('absensi_mapel.jurnal_id', $jurnal_id);
        $this->db->order_by('siswa.nama', 'ASC');
        return $this->db->get()->result();
    }

    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }

    public function insert_batch($data) {
        return $this->db->insert_batch($this->table, $data);
    }

    public function update($id, $data) {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    public function delete($id) {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    public function delete_by_jurnal($jurnal_id) {
        return $this->db->delete($this->table, ['jurnal_id' => $jurnal_id]);
    }

    public function get_by_siswa_and_month($siswa_id, $month, $year) {
        $this->db->select('absensi_mapel.*, jurnal.tanggal, mapel.nama_mapel, guru.nama as nama_guru');
        $this->db->from($this->table);
        $this->db->join('jurnal', 'jurnal.id = absensi_mapel.jurnal_id', 'left');
        $this->db->join('jadwal', 'jadwal.id = jurnal.jadwal_id', 'left');
        $this->db->join('mapel', 'mapel.id = jadwal.mapel_id', 'left');
        $this->db->join('guru', 'guru.id = jurnal.guru_id', 'left');
        $this->db->where('absensi_mapel.siswa_id', $siswa_id);
        $this->db->where('MONTH(jurnal.tanggal)', $month);
        $this->db->where('YEAR(jurnal.tanggal)', $year);
        $this->db->order_by('jurnal.tanggal', 'ASC');
        return $this->db->get()->result();
    }

    public function count_status_by_siswa($siswa_id, $month, $year, $status) {
        $this->db->from($this->table);
        $this->db->join('jurnal', 'jurnal.id = absensi_mapel.jurnal_id', 'inner');
        $this->db->where('absensi_mapel.siswa_id', $siswa_id);
        $this->db->where('absensi_mapel.status', $status);
        $this->db->where('MONTH(jurnal.tanggal)', $month);
        $this->db->where('YEAR(jurnal.tanggal)', $year);
        return $this->db->count_all_results();
    }

    public function get_rekap_by_kelas_and_mapel($kelas_id, $mapel_id, $month, $year) {
        $this->db->select('siswa.id, siswa.nama, siswa.nis,
                          SUM(CASE WHEN absensi_mapel.status = "H" THEN 1 ELSE 0 END) as hadir,
                          SUM(CASE WHEN absensi_mapel.status = "S" THEN 1 ELSE 0 END) as sakit,
                          SUM(CASE WHEN absensi_mapel.status = "I" THEN 1 ELSE 0 END) as izin,
                          SUM(CASE WHEN absensi_mapel.status = "A" THEN 1 ELSE 0 END) as alpha');
        $this->db->from('siswa');
        $this->db->join($this->table, 'absensi_mapel.siswa_id = siswa.id', 'left');
        $this->db->join('jurnal', 'jurnal.id = absensi_mapel.jurnal_id', 'left');
        $this->db->join('jadwal', 'jadwal.id = jurnal.jadwal_id', 'left');
        $this->db->where('siswa.kelas_id', $kelas_id);
        $this->db->where('jadwal.mapel_id', $mapel_id);
        $this->db->where('MONTH(jurnal.tanggal)', $month);
        $this->db->where('YEAR(jurnal.tanggal)', $year);
        $this->db->group_by('siswa.id');
        $this->db->order_by('siswa.nama', 'ASC');
        return $this->db->get()->result();
    }
}
