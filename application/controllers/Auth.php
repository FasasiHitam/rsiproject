<?php

class Auth extends CI_Controller
{
    public function auth_user()
    {
        $u = $this->input->post('username');
        $p = $this->input->post('password');
        $cek = $this->M_Auth->cek_user($u, $p);

        if ($cek->num_rows() > 0) {
            $user_data = $cek->row_array();
            $session['id_user']         = $user_data['id_user'];
            $session['nama_user']       = $user_data['nama_user'];
            $session['username']        = $user_data['username'];
            $session['password']        = $user_data['password'];
            $session['foto_petugas']    = $user_data['foto_petugas'];
            $session['jk']              = $user_data['jk'];
            $session['role_id']         = $user_data['role_id'];
            $this->session->set_userdata('server_rs', $session);
            redirect('dashboard');
        } else {
            # code...;
        }
    }
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth');
    }
}
