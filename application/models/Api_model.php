<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Api_model {

    function select_single($select, $where, $table) {
        if (!empty($select)) {
            $this->db->select($select);
        }

        if (!empty($where)) {
            $this->db->where($where);
        }

        return $this->db->get($table)->row_array();
    }

    function select_multiple($select, $where, $table) {
        if (!empty($select)) {
            $this->db->select($select);
        }

        if (!empty($where)) {
            $this->db->where($where);
        }

        return $this->db->get($table)->result_array();
    }

    function insert($data, $table, $returnId = FALSE) {
        if ($returnId === TRUE) {
            $this->db->insert($table, $data);
            return $this->db->insert_id();
        } else {
            return $this->db->insert($table, $data);
        }
    }

    function insert_batch($data, $table) {
        return $this->db->insert_batch($table, $data);
    }

    function update($data, $where, $table) {
        if (is_array($where)) {
            $this->db->where($where);
        } else {
            $this->db->where($where, NULL, FALSE);  // check if where clause is sent as a string for e.g where a = 'b' or c = 'd'
        }

        return $this->db->update($table, $data);
    }

    function select_single_finite_set($select, $where1, $where2, $table) {
        if (!empty($select)) {
            $this->db->select($select);
        }

        if (!empty($where1) && !empty($where2)) {
            $this->db->where($where1);
            $this->db->where($where2);
        }

        return $this->db->get($table)->row_array();
    }

    function select_multiple_count($select, $where, $table) {
        if (!empty($select)) {
            $this->db->select($select);
        }

        if (!empty($where)) {
            $this->db->where($where);
        }

        $this->db->from($table);

        return $this->db->count_all_results();
    }

    function delete($where, $table) {
        $this->db->where($where);
        return $this->db->delete($table);
    }

}
