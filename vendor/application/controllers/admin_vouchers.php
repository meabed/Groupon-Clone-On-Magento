<?php
class Admin_vouchers extends CI_Controller {
 
    /**
    * Responsable for auto load the model
    * @return void
    */
    public function __construct()
    {
        parent::__construct();

        if(!is_auth()){
            redirect('admin/login');
        }
    }
 
    /**
    * Load the main view with all the current model model's data.
    * @return void
    */
    public function index()
    {
        //all the posts sent by the view
        $vendor_id = $this->input->get('vendor_id');
        $search_string = $this->input->get('search_string');
        $order = $this->input->get('order');
        $order_type = $this->input->get('order_type');

        //pagination settings
        $config['per_page'] = 40000;
        $config['base_url'] = site_url('admin/vouchers');
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = 20;
        $config['full_tag_open'] = '<ul>';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        //limit end
        $page = $this->uri->segment(3);

        //math to get the initial record to be select in the database
        $limit_end = ($page * $config['per_page']) - $config['per_page'];
        if ($limit_end < 0){
            $limit_end = 0;
        } 

        //if order type was changed
        if($order_type){
            $filter_session_data['order_type'] = $order_type;
        }
        else{
            //we have something stored in the session? 
            if($this->session->userdata('order_type')){
                $order_type = $this->session->userdata('order_type');    
            }else{
                //if we have nothing inside session, so it's the default "Asc"
                $order_type = 'Asc';    
            }
        }
        //make the data type var avaible to our view
        $data['order_type_selected'] = $order_type;        


        //we must avoid a page reload with the previous session data
        //if any filter post was sent, then it's the first time we load the content
        //in this case we clean the session filter data
        //if any filter post was sent but we are in some page, we must load the session data

        //filtered && || paginated
        if( $search_string !== false  || $this->uri->segment(3) == true){
           
            /*
            The comments here are the same for line 79 until 99

            if post is not null, we store it in session data array
            if is null, we use the session data already stored
            we save order into the the var to load the view with the param already selected       
            */

            if($vendor_id !== 0){
                $filter_session_data['vendor_selected'] = $vendor_id;
            }else{
                $vendor_id = $this->session->userdata('vendor_selected');
            }
            $data['vendor_selected'] = $vendor_id;

            if($search_string){
                $filter_session_data['search_string_selected'] = $search_string;
            }else{
                //$search_string = $this->session->userdata('search_string_selected');
            }
            $data['search_string_selected'] = $search_string;

            if($order){
                $filter_session_data['order'] = $order;
            }
            else{
                $order = $this->session->userdata('order');
            }
            $data['order'] = $order;

            //save session data into the session
            $this->session->set_userdata($filter_session_data);

            //fetch vendors data into arrays
            $data['vendors'] = $this->vendors_model->get_vendors();

            $data['count_vouchers']= $this->vouchers_model->count_vouchers($vendor_id, $search_string, $order);
            $config['total_rows'] = $data['count_vouchers'];

            //fetch sql data into arrays
            if($search_string){
                if($order){
                    $data['vouchers'] = $this->vouchers_model->get_vouchers($vendor_id, $search_string, $order, $order_type, $config['per_page'],$limit_end);        
                }else{
                    $data['vouchers'] = $this->vouchers_model->get_vouchers($vendor_id, $search_string, '', $order_type, $config['per_page'],$limit_end);           
                }
            }else{
                if($order){
                    $data['vouchers'] = $this->vouchers_model->get_vouchers($vendor_id, '', $order, $order_type, $config['per_page'],$limit_end);        
                }else{
                    $data['vouchers'] = $this->vouchers_model->get_vouchers($vendor_id, '', '', $order_type, $config['per_page'],$limit_end);        
                }
            }

        }else{

            //clean filter data inside section
            $filter_session_data['vendor_selected'] = null;
            $filter_session_data['search_string_selected'] = null;
            $filter_session_data['order'] = null;
            $filter_session_data['order_type'] = null;
            $this->session->set_userdata($filter_session_data);

            //pre selected options
            $data['search_string_selected'] = '';
            $data['vendor_selected'] = 0;
            $data['order'] = 'id';

            //fetch sql data into arrays
            $data['vendors'] = $this->vendors_model->get_vendors();
            $data['count_vouchers']= $this->vouchers_model->count_vouchers();
            $data['vouchers'] = $this->vouchers_model->get_vouchers('', '', '', $order_type, $config['per_page'],$limit_end);        
            $config['total_rows'] = $data['count_vouchers'];

        }//!isset($vendor_id) && !isset($search_string) && !isset($order)

        //initializate the panination helper 
        $this->pagination->initialize($config);   

        //load the view
        $data['main_content'] = 'admin/vouchers/list';
        $this->load->view('includes/template', $data);  

    }//index

    public function add()
    {
        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST')
        {

            //form validation
            $this->form_validation->set_rules('name', 'Name', 'required');
            $this->form_validation->set_rules('description', 'description', 'required');
            $this->form_validation->set_rules('fine_print', 'Fine Print', 'required');
            $this->form_validation->set_rules('highlight', 'Highlight', 'required');
            $this->form_validation->set_rules('cost_price', 'cost_price', 'required|numeric');
            $this->form_validation->set_rules('sell_price', 'sell_price', 'required|numeric');
            $this->form_validation->set_rules('start_date', 'Start Date', 'required');
            $this->form_validation->set_rules('end_date', 'End Date', 'required');
            if(is_admin()){
                $this->form_validation->set_rules('vendor_id', 'vendor_id', 'required');
            }
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');

            //if the form has passed through the validation
            if ($this->form_validation->run())
            {
                $data_to_store = array(
                    'name' => $this->input->post('name'),
                    'short_name' => $this->input->post('short_name'),
                    'description' => $this->input->post('description'),
                    'fine_print' => $this->input->post('fine_print'),
                    'highlight' => $this->input->post('highlight'),
                    'stock' => $this->input->post('stock'),
                    'start_date' => $this->input->post('start_date'),
                    'end_date' => $this->input->post('end_date'),
                    'cost_price' => $this->input->post('cost_price'),
                    'sell_price' => $this->input->post('sell_price'),          
                    'active' => $this->input->post('active'),
                );
                if(is_admin()){
                    $data_to_store['vendor_id']= $this->input->post('vendor_id');
                }else{
                    $data_to_store['vendor_id']= getUID();
                }
                //if the insert has returned true then we show the flash message
                if($this->vouchers_model->store_product($data_to_store)){
                    $data['flash_message'] = TRUE; 
                }else{
                    $data['flash_message'] = FALSE; 
                }

            }

        }
        //fetch vendors data to populate the select field
        $data['vendors'] = $this->vendors_model->get_vendors();
        //load the view
        $data['main_content'] = 'admin/vouchers/add';
        $this->load->view('includes/template', $data);  
    }       

    /**
    * Update item by his id
    * @return void
    */
    public function update()
    {
        //product id 
        $id = $this->uri->segment(4);
  
        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST')
        {
            $this->form_validation->set_rules('name', 'Name', 'required');
            $this->form_validation->set_rules('description', 'description', 'required');
            $this->form_validation->set_rules('fine_print', 'Fine Print', 'required');
            $this->form_validation->set_rules('highlight', 'Highlight', 'required');
            $this->form_validation->set_rules('cost_price', 'cost_price', 'required|numeric');
            $this->form_validation->set_rules('sell_price', 'sell_price', 'required|numeric');
            $this->form_validation->set_rules('start_date', 'Start Date', 'required');
            $this->form_validation->set_rules('end_date', 'End Date', 'required');
            if(is_admin()){
                $this->form_validation->set_rules('vendor_id', 'vendor_id', 'required');
            }
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');

            //if the form has passed through the validation
            if ($this->form_validation->run())
            {
                $data_to_store = array(
                    'name' => $this->input->post('name'),
                    'short_name' => $this->input->post('short_name'),
                    'description' => $this->input->post('description'),
                    'fine_print' => $this->input->post('fine_print'),
                    'highlight' => $this->input->post('highlight'),
                    'stock' => $this->input->post('stock'),
                    'start_date' => $this->input->post('start_date'),
                    'end_date' => $this->input->post('end_date'),
                    'cost_price' => $this->input->post('cost_price'),
                    'sell_price' => $this->input->post('sell_price'),
                    'active' => $this->input->post('active'),
                );
                if(is_admin()){
                    $data_to_store['vendor_id']= $this->input->post('vendor_id');
                }else{
                    $data_to_store['vendor_id']= getUID();
                }
                //if the insert has returned true then we show the flash message
                if($this->vouchers_model->update_product($id, $data_to_store) == TRUE){
                    $this->session->set_flashdata('flash_message', 'updated');
                }else{
                    $this->session->set_flashdata('flash_message', 'not_updated');
                }
                redirect('admin/vouchers/update/'.$id.'');

            }
        }
        //if we are updating, and the data did not pass trough the validation
        //the code below wel reload the current data

        //product data 
        $data['product'] = $this->vouchers_model->get_product_by_id($id);
        if($data['product'][0]['vendor_id'] != getUID())
        {
            if(!is_admin())
            {
                redirect('admin/vouchers');
            }
        }
        //fetch vendors data to populate the select field
        $data['vendors'] = $this->vendors_model->get_vendors();
        //load the view
        $data['main_content'] = 'admin/vouchers/edit';
        $this->load->view('includes/template', $data);            

    }//update

    /**
    * Delete product by his id
    * @return void
    */
    public function delete()
    {
        //product id 
        $id = $this->uri->segment(4);
        $p = $this->vouchers_model->get_product_by_id($id);

        if(isset($p[0]) && $p[0]['vendor_id'] != getUID())
        {
            if(!is_admin())
            {
                redirect('admin/vouchers');
            }
        }
        $this->vouchers_model->delete_product($id);
        redirect('admin/vouchers');
    }//edit

}