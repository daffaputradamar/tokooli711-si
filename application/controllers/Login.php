<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin_model');
        $this->load->model('Karyawan_model');
        session_start();
        if (isset($_SESSION['level'])) {
            redirect('home');
        }
    }

    public function index()
    {
        $is_valid_login = false;
        $this->load->view('login');
        $kode = 'ADM00001';
        $level = "";
        $can_see_stock = false;
        $can_see_sales = false;
        //var_dump($this->input->post('login'));
        if($this->input->post('login')) {

            $user = $this->input->post("username");
            $psswd = $this->input->post("psswd");

            $admin = $this->Admin_model->selectByUsername($user);

            if(isset($admin)) {
                if($admin->psswd == $psswd) {
                    $is_valid_login = true;
                    $kode = $admin->kode_admin;
                    $level = "admin";
                    $can_see_stock = true;
                    $can_see_sales = true;
                }
            }

            if(!$is_valid_login) {
                $karyawan = $this->Karyawan_model->selectByUsername($user);
                
                if(isset($karyawan)) {
                    if($karyawan->password == $psswd) {
                        $is_valid_login = true;
                        $kode = $karyawan->kode_karyawan;
                        $can_see_stock = $karyawan->can_see_stock;
                        $can_see_sales = $karyawan->can_see_sales;

                        if ($karyawan->level == 0) {
                            $level = "karyawan";
                        } else {
                            $level = "karyawan_admin";
                        }

                    }
                }
            }

        }
        if($is_valid_login) {
            $_SESSION["username"] = $user;
            $_SESSION["kode"] = $kode;
            $_SESSION["level"] = $level;
            $_SESSION["can_see_stock"] = $can_see_stock;
            $_SESSION["can_see_sales"] = $can_see_sales;

            redirect('home', 'refresh');
        }


    }
    public function logout()
    {
        redirect('login', 'refresh');
    }
    public function checksession()
    {
        if($this->session->userdata('username') == "") {
            redirect('login');
        }
    }

    public function session()
    {
        var_dump($_SESSION);
    }
}


/* End of file Login.php */
/* Location: ./application/controllers/Login.php */
