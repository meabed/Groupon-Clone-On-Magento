<?php
class Vouchers_model extends CI_Model {

    public function __construct()
    {
    }


    public function get_voucher_by_id($id)
    {
		$this->db->select('*');
		$this->db->from('deal_voucher');
		$this->db->where('entity_id', $id);
		$query = $this->db->get();
		return $query->result_array(); 
    }


    public function get_vouchers($member_id=null, $search_string=null, $order=null, $order_type='Asc', $limit_start, $limit_end)
    {
        if(!is_admin())
        {
            $member_id = getUID();
        }
	    
		$this->db->select('deal_voucher.*');
		$this->db->select('membership.name as vendor_name');
		$this->db->select('o.customer_firstname as customer_firstname');
		$this->db->select('o.customer_lastname as customer_lastname');
		$this->db->select('i.name as product_name');
		$this->db->from('deal_voucher');
        $this->db->join('sales_flat_order as o', 'deal_voucher.order_id = o.entity_id', 'left');
        $this->db->join('sales_flat_order_item as i', 'deal_voucher.order_id = i.order_id', 'left');


        if($member_id != null && $member_id != 0){
			$this->db->where('vendor_id', $member_id);
		}

		if($search_string){
			$this->db->like('order_increment_id', $search_string);
		}

		$this->db->join('membership', 'deal_voucher.vendor_id = membership.id', 'left');

		$this->db->group_by('deal_voucher.entity_id');

		if($order){
			$this->db->order_by($order, $order_type);
		}else{
		    $this->db->order_by('entity_id', $order_type);
		}


		$this->db->limit($limit_start, $limit_end);
		//$this->db->limit('4', '4');


		$query = $this->db->get();
		
		return $query->result_array(); 	
    }

    function count_vouchers($member_id=null, $search_string=null, $order=null)
    {
		$this->db->select('*');
		$this->db->from('deal_voucher');
		if($member_id != null && $member_id != 0){
			$this->db->where('vendor_id', $member_id);
		}
		if($order){
			$this->db->order_by($order, 'Asc');
		}else{
		    $this->db->order_by('entity_id', 'Asc');
		}
		$query = $this->db->get();
		return $query->num_rows();        
    }


    function update_voucher($id, $data)
    {
		$this->db->where('entity_id', $id);
		$this->db->update('deal_voucher', $data);
		$report = array();
		$report['error'] = $this->db->_error_number();
		$report['message'] = $this->db->_error_message();
		if($report !== 0){
			return true;
		}else{
			return false;
		}
	}

 
}
?>	
