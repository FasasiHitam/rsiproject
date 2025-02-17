<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pasien extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $qr_code_dir =  FCPATH . 'assets/qrcodes/';
        if (!file_exists($qr_code_dir)) {
            mkdir($qr_code_dir, 0777, true);
        }

        include APPPATH . 'third_party/phpqrcode/phpqrcode.php';
    }

    public function index()
    {
        $user = $this->session->userdata('server_rs');

        $data['title'] = 'RSI Purwokerto';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('pasien/index', $data);
        $this->load->view('templates/footer', $data);
    }

    public function data_dokter()
    {
        $user = $this->session->userdata('server_rs');

        $data['title'] = 'RSI Purwokerto';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('dokter/index', $data);
        $this->load->view('templates/footer', $data);
    }

    public function register()
    {
        // Sample Data
        $first_names = ["John", "Michael", "Sarah", "Emma", "David", "Daniel", "Sophia", "Olivia"];
        $last_names = ["Smith", "Johnson", "Brown", "Williams", "Jones", "Garcia", "Miller", "Davis"];
        $places = ["New York", "Los Angeles", "Chicago", "Houston", "Phoenix", "San Diego", "Dallas", "Miami"];
        $addresses = ["123 Main St", "456 Oak Rd", "789 Pine Ave", "321 Birch Ln", "654 Cedar Dr"];
        $domains = ["gmail.com", "yahoo.com", "outlook.com", "hotmail.com", "aol.com"];

        $front_name = $first_names[array_rand($first_names)];
        $back_name = $last_names[array_rand($last_names)];
        $identity_card = $this->generate_identity_card();
        $email = $this->generate_email($front_name, $back_name, $domains);

        // Generate Random Data
        $data['random_user'] = [
            'front_name' => $first_names[array_rand($first_names)],
            'back_name' => $last_names[array_rand($last_names)],
            'place_of_birth' => $places[array_rand($places)],
            'address' => $addresses[array_rand($addresses)],
            'identity_card' => $identity_card,
            'email' => $email,
        ];

        $data['title'] = 'RSI Purwokerto';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('pasien/form-pendaftaran-pasien', $data);
        $this->load->view('templates/footer', $data);
    }

    private function generate_identity_card()
    {
        return sprintf("%04d-%04d-%04d", rand(1000, 9999), rand(1000, 9999), rand(1000, 9999));
    }

    private function generate_email($first_name, $last_name, $domains)
    {
        $email_username = strtolower($first_name . "." . $last_name . rand(10, 99));
        return $email_username . "@" . $domains[array_rand($domains)];
    }

    public function create_patient()
    {
        $card_number         = $this->input->post('card_number');
        $nama_pasien         = $this->input->post('nama_pasien');
        $card_type           = $this->input->post('card_type');
        $email               = $this->input->post('email');
        $no_whatsapp         = $this->input->post('no_whatsapp');
        $no_hp1              = $this->input->post('no_hp1');
        $no_hp2              = $this->input->post('no_hp2');
        $jk                  = $this->input->post('jk');
        $agama               = $this->input->post('agama');
        $place_of_birth      = $this->input->post('place_of_birth');
        $date_of_birth       = $this->input->post('date_of_birth');
        $status_perkawinan   = $this->input->post('status_perkawinan');
        $pendidikan          = $this->input->post('pendidikan');
        $pekerjaan           = $this->input->post('pekerjaan');
        $goldar              = $this->input->post('goldar');
        $provinsi            = $this->input->post('provinsi');
        $kabupaten           = $this->input->post('kabupaten');
        $alamat              = $this->input->post('alamat');
        $dokter              = $this->input->post('dokter');
        $keluhan             = $this->input->post('keluhan');

        $place_and_birth        = $place_of_birth . ',' . $date_of_birth;

        // Generate QR code file name first
        $qr_code_file = 'assets/qrcodes/' . $card_number . '_qr.png';
        $qr_code_path = $qr_code_file;

        // Prepare the patient data
        $data = array(
            'card_number'           => $card_number,
            'nama_pasien'           => $nama_pasien,
            'card_type'             => $card_type,
            'email'                 => $email,
            'no_whatsapp'           => $no_whatsapp,
            'no_hp1'                => $no_hp1,
            'no_hp2'                => $no_hp2,
            'jk'                    => $jk,
            'agama'                 => $agama,
            'ttl'                   => $place_and_birth,
            'status_perkawinan'     => $status_perkawinan,
            'pendidikan'            => $pendidikan,
            'pekerjaan'             => $pekerjaan,
            'goldar'                => $goldar,
            'goldar'                => $goldar,
            'provinsi'              => $provinsi,
            'kabupaten'             => $kabupaten,
            'alamat'                => $alamat,
            'dokter'                => $dokter,
            'keluhan'               => $keluhan,
            'barcode'               => $qr_code_path,
        );

        // Register patient and get the patient number
        $result = $this->M_Patient->register_patient($data);

        if ($result) {
            // QR code content including patient number
            $qr_content = "No. Pasien: {$result['patient_number']}\n";
            $qr_content .= "NIK: $card_number\n";
            $qr_content .= "Nama: $nama_pasien\n";
            $qr_content .= "Dokter: $no_telp";
            $qr_content .= "Jenis Urusan: $no_telp";

            // Generate QR code
            \QRcode::png($qr_content, $qr_code_path, QR_ECLEVEL_L, 10);

            // Add additional information to data array
            $data['patient_number'] = $result['patient_number'];
            $data['queue_number'] = $result['queue_number'];

            $this->session->set_flashdata('patient_data', $data);
            redirect('success-pasien');
        } else {
            $this->session->set_flashdata('error', 'Registration failed');
            redirect('kontrol-pasien');
        }
    }

    public function success()
    {
        $data['title'] = 'RSI Purwokerto';
        // Get the stored patient data
        $data['patient_data'] = $this->session->flashdata('patient_data');

        // Check if we have data
        if (!$data['patient_data']) {
            redirect('kontrol-pasien');
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('pasien/success', $data);
        $this->load->view('templates/footer', $data);
    }

    public function success_checkup()
    {
        $data['title'] = 'RSI Purwokerto';
        // Get the stored patient data
        $data['patient_checkup'] = $this->session->flashdata('patient_checkup');

        $this->load->view('templates/header', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('pasien/success_checkup', $data);
        $this->load->view('templates/footer', $data);
    }

    public function email_pdf($patient_data = null)
    {
        // Load email library
        $this->load->library('email');

        if ($patient_id === null) {
            show_error('Patient ID is required');
            return;
        }

        // Generate PDF
        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('RSI Purwokerto');
        $pdf->SetTitle('Patient Registration Information');

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', '', 12);

        // Add content to PDF
        $html = '
        <h1>Registration Information</h1>
        <p>Registration ID: ' . $patient_data['id_pasien'] . '</p>
        <p>barcode ID: ' . $patient_data['barcode'] . '</p>
        <p>Name: ' . $patient_data['name'] . '</p>
        <p>Email: ' . $patient_data['email'] . '</p>
        <p>Phone: ' . $patient_data['phone'] . '</p>
        <p>Address: ' . $patient_data['address'] . '</p>
        ';

        $pdf->writeHTML($html, true, false, true, false, '');

        // Save PDF to temporary file
        $temp_file = FCPATH . 'assets/temp/' . uniqid() . '.pdf';
        $pdf->Output($temp_file, 'F');

        // Configure email
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'your_smtp_host';
        $config['smtp_user'] = 'your_smtp_user';
        $config['smtp_pass'] = 'your_smtp_password';
        $config['smtp_port'] = 587;
        $config['smtp_crypto'] = 'tls';
        $config['mailtype'] = 'html';
        $config['charset'] = 'utf-8';
        $config['newline'] = "\r\n";

        $this->email->initialize($config);

        // Set email content
        $this->email->from('shinomiya.tama@gmail.com', 'RSI Purwokerto');
        $this->email->to($patient_data['email']);
        $this->email->subject('Your Registration Information');
        $this->email->message('Please find your registration information attached.');
        $this->email->attach($temp_file);

        // Send email
        $sent = $this->email->send();

        // Delete temporary file
        unlink($temp_file);

        return $sent;
    }

    public function export_to_pdf()
    {
        // Get patient data from session
        $patient_data = $this->session->flashdata('patient_data');

        // If no data in flashdata, try getting it from session
        if (!$patient_data) {
            $patient_data = $this->session->userdata('patient_data');
        }

        // If still no data, redirect back
        if (!$patient_data) {
            $this->session->set_flashdata('error', 'No patient data available');
            redirect('pasien');
        }

        // Re-save the data to session in case we need it again
        $this->session->set_userdata('patient_data', $patient_data);

        try {
            // Save PDF to server
            $filename = $this->save_pdf_to_server($patient_data);

            // Load TCPDF library
            $this->load->library('pdf');

            // Create new PDF document
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Set document information
            $pdf->SetCreator('RSI Purwokerto');
            $pdf->SetAuthor('RSI Purwokerto');
            $pdf->SetTitle('Data Pasien - ' . $patient_data['nama_pasien']);

            // Remove default header/footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // Set margins
            $pdf->SetMargins(15, 15, 15);

            // Add a page
            $pdf->AddPage();

            // Set font
            $pdf->SetFont('helvetica', 'B', 20);

            // Add content (using the same HTML structure as before)
            $html = '
            <h1 style="text-align: center;">Data Pasien Dibuat!</h1>
            
            <!-- QR Code Section -->
            <div style="text-align: center; margin: 20px 0;">';

            // Add QR Code conditionally
            if (isset($patient_data) && isset($patient_data['barcode']) && file_exists(FCPATH . $patient_data['barcode'])) {
                $qr_path = FCPATH . $patient_data['barcode'];
                $html .= '<img src="' . $qr_path . '" style="width: 200px;">';
            } else {
                $html .= '
                <div style="padding: 10px; background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 5px;">
                    QR Code not available';

                if (!isset($patient_data)) {
                    $html .= '<br>- patient_data not set';
                }
                if (!isset($patient_data['barcode'])) {
                    $html .= '<br>- barcode not set';
                }
                if (isset($patient_data['barcode']) && !file_exists(FCPATH . $patient_data['barcode'])) {
                    $html .= '<br>- file does not exist: ' . FCPATH . $patient_data['barcode'];
                }

                $html .= '</div>';
            }

            $html .= '
            </div>
        
            <div style="text-align: center;">
                <h2>Nomor Urut Pasien:</h2>
                <div style="font-size: 24px; font-weight: bold;">' . $patient_data['patient_number'] . '</div>
            </div>
            
            <div style="margin: 20px 0; padding: 10px; background-color: #f8f9fa;">
                <p style="text-align: center; font-weight: bold;">SIMPAN NOMOR PASIEN UNTUK LOGIN</p>
                <table cellpadding="5">
                    <tr>
                        <td width="30%"><strong>Nomor Pasien:</strong></td>
                        <td>' . $patient_data['patient_number'] . '</td>
                    </tr>
                    <tr>
                        <td><strong>Nomor Identitas:</strong></td>
                        <td>' . $patient_data['card_number'] . '</td>
                    </tr>
                    <tr>
                        <td><strong>Nama:</strong></td>
                        <td>' . $patient_data['nama_pasien'] . '</td>
                    </tr>
                    <tr>
                        <td><strong>TTL:</strong></td>
                        <td>' . $patient_data['ttl'] . '</td>
                    </tr>
                </table>
            </div>';

            // Add this right before using $html in the PDF generation
            $pdf->writeHTML($html, true, false, true, false, '');

            // Close and output PDF document
            $pdf->Output('Data_Pasien_' . $patient_data['patient_number'] . '.pdf', 'D');
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Failed to generate PDF: ' . $e->getMessage());
            redirect('pasien');
        }
    }

    public function save_pdf_to_server($patient_data)
    {
        // Create directory if it doesn't exist
        $save_path = FCPATH . 'assets/patient_pdfs/';
        if (!file_exists($save_path)) {
            mkdir($save_path, 0777, true);
        }

        // Generate filename
        $filename = 'patient_' . $patient_data['patient_number'] . '_' . date('Y-m-d') . '.pdf';

        try {
            // Load PDF library
            $this->load->library('pdf');
            $pdf = new TCPDF();

            // Set up PDF (same as export_to_pdf)
            $pdf->SetCreator('RSI Purwokerto');
            $pdf->SetAuthor('RSI Purwokerto');
            $pdf->SetTitle('Data Pasien - ' . $patient_data['nama_pasien']);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(15, 15, 15);
            $pdf->AddPage();

            // Add content (same as above)
            // ... (same HTML content as in export_to_pdf)

            // Save to server
            $pdf->Output($save_path . $filename, 'F');

            // Save reference to database if needed
            $pdf_data = array(
                'patient_number' => $patient_data['patient_number'],
                'file_path' => 'assets/patient_pdfs/' . $filename,
                'created_at' => date('Y-m-d H:i:s')
            );

            $this->db->insert('patient_documents', $pdf_data);

            return $filename;
        } catch (Exception $e) {
            log_message('error', 'Failed to save PDF: ' . $e->getMessage());
            throw $e;
        }
    }

    public function get_patient_pdfs($patient_number)
    {
        $this->db->where('patient_number', $patient_number);
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('patient_documents')->result();
    }
}
