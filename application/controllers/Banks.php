<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Banks extends CORE_Controller
{

    function __construct() {
        parent::__construct('');
        $this->validate_session();
        // if($this->session->userdata('right_discount_view') == 0 || $this->session->userdata('right_discount_view') == null) {
        //     redirect('../Homepage');
        // }
        $this->load->model('Brands_model');
        $this->load->model('Users_model');
        $this->load->model('Discount_model');
        $this->load->model('Banks_model');
    }

    public function index() {
        // $m_users=$this->Users_model;
        // $data2['users']=$m_users->get_list(
        //   'user_accounts.is_deleted=0 AND user_accounts.user_id!=1',
        //   'user_accounts.*,CONCAT(user_accounts.user_fname," ",user_accounts.user_mname," ",user_accounts.user_lname) as full_name,'
        // );
        //default resources of the active view
        $data['_def_css_files']=$this->load->view('template/assets/css_files','',TRUE);
        $data['_def_js_files']=$this->load->view('template/assets/js_files','',TRUE);
        $data['_top_navigation']=$this->load->view('template/elements/top_navigation','',TRUE);
        $data['_side_navigation']=$this->load->view('template/elements/side_bar_navigation','',TRUE);
        $data['_right_navigation']=$this->load->view('template/elements/right_bar_navigation.php','',TRUE);
        $data['_rights']=$this->load->view('template/elements/rights','',TRUE);
        $data['_title'] = 'Banks';
        $this->load->view('banks_view', $data);
    }

    function transaction($txn = null) {
      function replaceCharsInNumber($num, $chars) {
                       return substr((string) $num, 0, -strlen($chars)) . $chars;
                  }
        switch ($txn) {
            case 'list':
                $m_banks = $this->Banks_model;
                $response['data']=$m_banks->get_list(array('banks.is_deleted'=>FALSE));
                echo json_encode($response);
            break;

            case 'create':
                $m_banks = $this->Banks_model;
                $m_banks->bank_name = $this->input->post('bank_name', TRUE);
                $m_banks->save();

                $bank_id = $m_banks->last_insert_id();

                $format = "000000";
                $temp = replaceCharsInNumber($format, $bank_id); //5069xxx
                $ecode = $temp .'-'. $today = date("Y");

                $m_banks->bank_code=$ecode;

                $m_banks->modify($bank_id);

                $response['title'] = 'Success!';
                $response['stat'] = 'success';
                $response['msg'] = 'Status information successfully created.';
                $response['row_added'] = $m_banks->get_list($bank_id);
                echo json_encode($response);

            break;

            case 'delete':
                $m_banks=$this->Banks_model;
                $bank_id=$this->input->post('bank_id',TRUE);
                $m_banks->is_deleted=1;
                //$m_cards->deleted_by = $this->session->user_id;
                //$m_cards->date_deleted=date('Y-m-d H:i:s');
                if($m_banks->modify($bank_id)){
                    $response['title']='Success!';
                    $response['stat']='success';
                    $response['msg']='Status information successfully deleted.';

                    echo json_encode($response);
                }

            break;

            case 'update':
                $m_banks=$this->Banks_model;

                $bank_id=$this->input->post('bank_id',TRUE);
                $m_banks->bank_name = $this->input->post('bank_name', TRUE);
                $m_banks->modify($bank_id);

                $response['title']='Success!';
                $response['stat']='success';
                $response['msg']='Card information successfully updated.';
                $response['row_updated']=$m_banks->get_list($bank_id);
                echo json_encode($response);

            break;
        }
    }

    // function response_rows($filter){
    //     return $this->Card_model->get_list(
    //         $filter,
    //
    //         'products.*,categories.category_name,units.unit_name,brands.brand_name,vendors.vendor_name',
    //
    //         array(
    //             array('categories','categories.category_id=products.category_id','left'),
    //             array('units','units.unit_id=products.unit_id','left'),
		// 		array('brands','brands.brand_id=products.brand_id','left'),
		// 		array('vendors','vendors.vendor_id=products.vendor_id','left')
    //         )
    //     );
    // }
}