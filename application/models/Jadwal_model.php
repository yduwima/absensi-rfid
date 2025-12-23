<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jadwal_model extends CI_Model {

    private $table = 'jadwal';

    public function get_all($search = '', $limit = 10, $offset = 0, $semester_id = null) {
        $this->db->select('jadwal.*, kelas.nama_kelas, mapel.nama_mapel, mapel.kode_mapel, guru.nama as nama_guru, semester.semester');
        $this->db->from($this->table);
        $this->db->join('kelas', 'kelas.id = jadwal.kelas_id', 'left');
        $this->db->join('mapel', 'mapel.id = jadwal.mapel_id', 'left');
        $this->db->join('guru', 'guru.id = jadwal.guru_id', 'left');
        $this->db->join('semester', 'semester.id = jadwal.semester_id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('kelas.nama_kelas', $search);
            $this->db->or_like('mapel.nama_mapel', $search);
            $this->db->or_like('guru.nama', $search);
            $this->db->group_end();
        }
        
        if ($semester_id) {
            $this->db->where('jadwal.semester_id', $semester_id);
        }
        
        $this->db->order_by('jadwal.hari', 'ASC');
        $this->db->order_by('jadwal.jam_mulai', 'ASC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    public function count_all($search = '', $semester_id = null) {
        $this->db->from($this->table);
        $this->db->join('kelas', 'kelas.id = jadwal.kelas_id', 'left');
        $this->db->join('mapel', 'mapel.id = jadwal.mapel_id', 'left');
        $this->db->join('guru', 'guru.id = jadwal.guru_id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('kelas.nama_kelas', $search);
            $this->db->or_like('mapel.nama_mapel', $search);
            $this->db->or_like('guru.nama', $search);
            $this->db->group_end();
        }
        
        if ($semester_id) {
            $this->db->where('jadwal.semester_id', $semester_id);
        }
        
        return $this->db->count_all_results();
    }

    public function get_by_id($id) {
        $this->db->select('jadwal.*, kelas.nama_kelas, mapel.nama_mapel, guru.nama as nama_guru');
        $this->db->from($this->table);
        $this->db->join('kelas', 'kelas.id = jadwal.kelas_id', 'left');
        $this->db->join('mapel', 'mapel.id = jadwal.mapel_id', 'left');
        $this->db->join('guru', 'guru.id = jadwal.guru_id', 'left');
        $this->db->where('jadwal.id', $id);
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

    public function get_by_guru($guru_id, $hari = null) {
        $this->db->select('jadwal.*, kelas.nama_kelas, mapel.nama_mapel, mapel.kode_mapel, semester.semester');
        $this->db->from($this->table);
        $this->db->join('kelas', 'kelas.id = jadwal.kelas_id', 'left');
        $this->db->join('mapel', 'mapel.id = jadwal.mapel_id', 'left');
        $this->db->join('semester', 'semester.id = jadwal.semester_id', 'left');
        $this->db->where('jadwal.guru_id', $guru_id);
        
        if ($hari) {
            $this->db->where('jadwal.hari', $hari);
        }
        
        $this->db->order_by('jadwal.jam_mulai', 'ASC');
        return $this->db->get()->result();
    }

    public function get_by_kelas($kelas_id, $hari = null) {
        $this->db->select('jadwal.*, mapel.nama_mapel, mapel.kode_mapel, guru.nama as nama_guru');
        $this->db->from($this->table);
        $this->db->join('mapel', 'mapel.id = jadwal.mapel_id', 'left');
        $this->db->join('guru', 'guru.id = jadwal.guru_id', 'left');
        $this->db->where('jadwal.kelas_id', $kelas_id);
        
        if ($hari) {
            $this->db->where('jadwal.hari', $hari);
        }
        
        $this->db->order_by('jadwal.jam_mulai', 'ASC');
        return $this->db->get()->result();
    }

    public function get_today_by_guru($guru_id) {
        $hari = $this->_get_indonesian_day(date('l'));
        return $this->get_by_guru($guru_id, $hari);
    }

    private function _get_indonesian_day($english_day) {
        $days = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];
        return isset($days[$english_day]) ? $days[$english_day] : 'Senin';
    }
}
