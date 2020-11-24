<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

class Api_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    //Below Func returns the single row
    function select_single($select, $where, $table) {
        if (!empty($select)) {
            $this->db->select($select);
        }

        if (!empty($where)) {
            $this->db->where($where);
        }
        return $this->db->get($table)->row_array();
    }

    //Below Func returns the multiple row
    function select_multiple($select, $where, $table) {
        if (!empty($select)) {
            $this->db->select($select);
        }

        if (!empty($where)) {
            $this->db->where($where);
        }
        return $this->db->get($table)->result_array();
    }

    //Below Func inserts the data and if the returnID is set then it returns the id of the row
    function insert($data, $table, $returnId = FALSE) {
        if ($returnId === TRUE) {
            $this->db->insert($table, $data);
            return $this->db->insert_id();
        } else {
            return $this->db->insert($table, $data);
        }
    }

    //Below Func inserts the data in batch
    function insert_batch($data, $table) {
        return $this->db->insert_batch($table, $data);
    }

    //Below Func updates the table
    function update($data, $where, $table) {
        if (is_array($where)) {
            $this->db->where($where);
        } else {
            $this->db->where($where, NULL, FALSE);  // check if where clause is sent as a string for e.g where a = 'b' or c = 'd'
        }
        return $this->db->update($table, $data);
    }

    //Below func hard deletes the data
    function delete($where, $table) {
        $this->db->where($where);
        return $this->db->delete($table);
    }

}
