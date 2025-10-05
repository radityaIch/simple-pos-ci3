<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Promo extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Promo_model');
        $this->load->library('form_validation');
        // Security library is now autoloaded
    }

    /**
     * Index - List all promo
     */
    public function index()
    {
        $data['title'] = 'Master Promo';
        $data['promo_list'] = $this->Promo_model->get_all();
        
        $this->load->view('templates/header', $data);
        $this->load->view('promo/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Add new promo
     */
    public function add()
    {
        $data['title'] = 'Tambah Promo';
        
        if ($this->input->post()) {
            $this->_handle_form('add');
        } else {
            $data['promo'] = (object) array(
                'kode_promo' => '',
                'nama_promo' => '',
                'ketereangan' => ''
            );
            
            $this->load->view('templates/header', $data);
            $this->load->view('promo/form', $data);
            $this->load->view('templates/footer');
        }
    }

    /**
     * Edit promo
     */
    public function edit($kode_promo)
    {
        $data['title'] = 'Edit Promo';
        $data['promo'] = $this->Promo_model->get_by_kode($kode_promo);
        
        if (!$data['promo']) {
            show_404();
        }
        
        if ($this->input->post()) {
            $this->_handle_form('edit', $kode_promo);
        } else {
            $this->load->view('templates/header', $data);
            $this->load->view('promo/form', $data);
            $this->load->view('templates/footer');
        }
    }

    /**
     * View promo detail
     */
    public function view($kode_promo)
    {
        $data['title'] = 'Detail Promo';
        $data['promo'] = $this->Promo_model->get_by_kode($kode_promo);
        
        if (!$data['promo']) {
            show_404();
        }
        
        $this->load->view('templates/header', $data);
        $this->load->view('promo/view', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Delete promo
     */
    public function delete($kode_promo)
    {
        // Debug: Log request method and POST data
        log_message('debug', 'Delete promo request method: ' . $this->input->method());
        log_message('debug', 'Is POST: ' . ($this->input->post() ? 'true' : 'false'));
        log_message('debug', 'CSRF Token Name: ' . $this->security->get_csrf_token_name());
        log_message('debug', 'Posted CSRF Token Value: ' . ($this->input->post($this->security->get_csrf_token_name()) ?: 'NOT FOUND'));
        log_message('debug', 'Expected CSRF Token: ' . $this->security->get_csrf_hash());
        
        // Verify it's a POST request with CSRF protection enabled
        if ($this->input->method() !== 'post') {
            $this->session->set_flashdata('error', 'Aksi tidak valid - Bukan permintaan POST');
            redirect('promo');
            return;
        }
        
        // CodeIgniter automatically validates CSRF token when csrf_protection is TRUE
        // Proceed with deletion
        $promo = $this->Promo_model->get_by_kode($kode_promo);
        
        if (!$promo) {
            show_404();
        }
        
        if ($this->Promo_model->delete($kode_promo)) {
            $this->session->set_flashdata('success', 'Promo berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Promo tidak dapat dihapus karena sudah digunakan dalam transaksi');
        }
        
        redirect('promo');
    }

    /**
     * Validate promo code via AJAX
     */
    public function validate_promo()
    {
        // For AJAX requests, CSRF verification is handled automatically
        $kode_promo = $this->input->post('kode_promo');
        
        if (empty($kode_promo)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => true,
                    'discount' => 0,
                    'message' => 'Tidak ada promo'
                )));
            return;
        }
        
        $result = $this->Promo_model->validate_promo($kode_promo);
        
        if (isset($result['error'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => $result['error']
                )));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => true,
                    'discount' => 0, // No promo discount - use item discounts instead
                    'promo_info' => $result['promo_info'],
                    'message' => 'Promo berhasil diterapkan sebagai label'
                )));
        }
    }

    /**
     * Handle form submission
     */
    private function _handle_form($action, $kode_promo = null)
    {
        // CSRF verification is automatically handled by CodeIgniter when csrf_protection is enabled
        $data_input = array(
            'kode_promo' => $this->input->post('kode_promo'),
            'nama_promo' => $this->input->post('nama_promo'),
            'ketereangan' => $this->input->post('ketereangan')
        );
        
        // Validate data
        $is_update = ($action == 'edit');
        $errors = $this->Promo_model->validate($data_input, $is_update);
        
        if (empty($errors)) {
            if ($action == 'add') {
                $success = $this->Promo_model->insert($data_input);
                $message = $success ? 'Promo berhasil ditambahkan' : 'Gagal menambahkan promo';
            } else {
                $success = $this->Promo_model->update($kode_promo, $data_input);
                $message = $success ? 'Promo berhasil diperbarui' : 'Gagal memperbarui promo';
            }
            
            if ($success) {
                $this->session->set_flashdata('success', $message);
                redirect('promo');
            } else {
                $this->session->set_flashdata('error', $message);
            }
        } else {
            $this->session->set_flashdata('error', implode('<br>', $errors));
        }
        
        // Reload form with data
        $data['title'] = ($action == 'add') ? 'Tambah Promo' : 'Edit Promo';
        $data['promo'] = (object) $data_input;
        
        $this->load->view('templates/header', $data);
        $this->load->view('promo/form', $data);
        $this->load->view('templates/footer');
    }
}