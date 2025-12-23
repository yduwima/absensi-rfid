<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jurnal_model extends CI_Model {

    private $table = 'jurnal';

    public function get_all($search = '', $limit = 10, $offset = 0, $guru_id = null) {
        $this->db->select('jurnal.*, jadwal.hari, jadwal.jam_mulai, jadwal.jam_selesai, 
                          kelas.nama_kelas, mapel.nama_mapel, mapel.kode_mapel, guru.nama as nama_guru');
        $this->db->from($this->table);
        $this->db->join('jadwal', 'jadwal.id = jurnal.jadwal_id', 'left');
        $this->db->join('kelas', 'kelas.id = jadwal.kelas_id', 'left');
        $this->db->join('mapel', 'mapel.id = jadwal.mapel_id', 'left');
        $this->db->join('guru', 'guru.id = jurnal.guru_id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('jurnal.materi', $search);
            $this->db->or_like('mapel.nama_mapel', $search);
            $this->db->or_like('kelas.nama_kelas', $search);
            $this->db->group_end();
        }
        
        if ($guru_id) {
            $this->db->where('jurnal.guru_id', $guru_id);
        }
        
        $this->db->order_by('jurnal.tanggal', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    public function count_all($search = '', $guru_id = null) {
        $this->db->from($this->table);
        $this->db->join('jadwal', 'jadwal.id = jurnal.jadwal_id', 'left');
        $this->db->join('kelas', 'kelas.id = jadwal.kelas_id', 'left');
        $this->db->join('mapel', 'mapel.id = jadwal.mapel_id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('jurnal.materi', $search);
            $this->db->or_like('mapel.nama_mapel', $search);
            $this->db->or_like('kelas.nama_kelas', $search);
            $this->db->group_end();
        }
        
        if ($guru_id) {
            $this->db->where('jurnal.guru_id', $guru_id);
        }
        
        return $this->db->count_all_results();
    }

    public function get_by_id($id) {
        $this->db->select('jurnal.*, jadwal.hari, jadwal.jam_mulai, jadwal.jam_selesai,
                          jadwal.kelas_id, jadwal.mapel_id,
                          kelas.nama_kelas, mapel.nama_mapel, mapel.kode_mapel, guru.nama as nama_guru');
        $this->db->from($this->table);
        $this->db->join('jadwal', 'jadwal.id = jurnal.jadwal_id', 'left');
        $this->db->join('kelas', 'kelas.id = jadwal.kelas_id', 'left');
        $this->db->join('mapel', 'mapel.id = jadwal.mapel_id', 'left');
        $this->db->join('guru', 'guru.id = jurnal.guru_id', 'left');
        $this->db->where('jurnal.id', $id);
        return $this->db->get()->row();
    }

    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data) {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    public function delete($id) {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    public function get_by_jadwal_and_date($jadwal_id, $tanggal) {
        return $this->db->get_where($this->table, [
            'jadwal_id' => $jadwal_id,
            'tanggal' => $tanggal
        ])->row();
    }

    public function get_by_guru_and_month($guru_id, $month, $year) {
        $this->db->select('jurnal.*, jadwal.hari, kelas.nama_kelas, mapel.nama_mapel');
        $this->db->from($this->table);
        $this->db->join('jadwal', 'jadwal.id = jurnal.jadwal_id', 'left');
        $this->db->join('kelas', 'kelas.id = jadwal.kelas_id', 'left');
        $this->db->join('mapel', 'mapel.id = jadwal.mapel_id', 'left');
        $this->db->where('jurnal.guru_id', $guru_id);
        $this->db->where('MONTH(jurnal.tanggal)', $month);
        $this->db->where('YEAR(jurnal.tanggal)', $year);
        $this->db->order_by('jurnal.tanggal', 'DESC');
        return $this->db->get()->result();
    }

    public function count_by_guru_and_month($guru_id, $month, $year) {
        $this->db->where('guru_id', $guru_id);
        $this->db->where('MONTH(tanggal)', $month);
        $this->db->where('YEAR(tanggal)', $year);
        return $this->db->count_all_results($this->table);
    }
}
