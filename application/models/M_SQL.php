<?php

class M_SQL extends CI_Model
{
    /* data result*/

    /* DML */
    public function insert_data($data, $table)
    {
        $this->db->insert($table, $data);
    }
    public function update_data($where, $data, $table)
    {
        $this->db->where($where);
        $this->db->update($table, $data);
    }
    public function delete_data($where, $data, $table)
    {
        $this->db->where($where);
        $this->db->insert($table, $data);
    }
    public function get_data($where, $table)
    {
        $this->db->get_where($table, $where);
    }
}
