<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class fees_details_model extends CI_Model {

	public function addFeeDetails($data) {
		if (isset($data)) {
			return $this -> db -> insert('fees_details', $data);
		}
		return false;
	}

	public function deleteFeeDetails($feeId) {
		if (isset($eventtypeId)) {
			$this -> db -> where('feesId', $feeId);
			$this -> db -> delete('fees_details');
			return true;
		}
		return false;
	}

}
?>