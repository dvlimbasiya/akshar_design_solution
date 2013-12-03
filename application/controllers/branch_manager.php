<?php
date_default_timezone_set('UTC');
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Branch_manager extends CI_Controller {

	function __construct() {
		parent::__construct();
		$users = array(2);
		parent::authenticate($users);
	}

	//Dashboard
	public function index() {
		$this->data['menu'] = "Dashboard";
		$this -> data['title'] = "ADS | Dashboard";
		$this -> load -> view('backend/master_page/top', $this -> data);
		
		$this -> load -> view('backend/css/dashboard_css');
		
		$this -> load -> view('backend/master_page/header');
		$this -> load -> model("target_model");
		
		$this -> data['TargetPendingCount'] = $this -> target_model -> getPendingCount($this -> branchCode);
		$this -> load -> model("inquiry_model");
		$this -> data['NewInquiryCount'] = $this -> inquiry_model -> getNewInquiryCount($this -> branchCode);
		$this -> load -> model("user_model");
		
		$this -> data['StudentResigsterCount'] = $this -> user_model -> getUserCount(5, $this -> branchCode);
		$this -> data['FacultyCount'] = $this -> user_model -> getUserCount(3, $this -> branchCode);
		$this -> data['chart1'] = $this -> user_model -> getstudentRegisterCountOfMonth($this -> branchCode);
		$this -> data['chart2'] = $this -> inquiry_model -> getstudentinquiryCountOfMonth($this -> branchCode);
		$this -> load -> model("fee_model");
		$this -> data['chart3'] = $this -> fee_model -> getpaymentOfMonth($this -> branchCode);
		$this -> load -> model("event_model");
		$this -> data['events'] = $this -> event_model -> geteventForCalender($this -> branchCode);
		$this -> load -> view('backend/admin/dashboard', $this -> data);
		$this -> load -> view('backend/master_page/footer');
		$this -> load -> view('backend/js/dashboard_js');
		$this -> load -> view('backend/master_page/bottom');
	}

	//Batch
	public function batch($batchId = '') {
		$this->data['menu'] = "Batch";
		$this -> load -> model('batch_model');
		$this -> load -> model("batch_timing_model");
		$weekdays = array();
		if ($batchId != '') {
			$batch_data = $this -> batch_model -> getDetailsByBranchAndBatch($this -> branchCode, $batchId);
			$this -> data['batch_list'] = $batch_data;
			$weekdays = $this -> batch_timing_model -> getBatchTiming($batchId);
			$this -> data['weekdays'] = $weekdays;
			echo json_encode($this -> data);
		} else {
			$batch_data = $this -> batch_model -> getDetailsByBranch($this -> branchCode);
			$this -> load -> model("course_model");
			$courses = $this -> course_model -> getDetailsOfCourse();
			$this -> load -> model('user_model');
			$facultyName = $this -> user_model -> getDetailsByBranchAndRole($this -> branchCode, 3);
			$this -> data['course'] = $courses;
			$this -> data['faculty'] = $facultyName;
			$this -> data['batch_list'] = $batch_data;
			foreach ($batch_data as $key) {
				$weekdays[$key -> batchId] = $this -> batch_timing_model -> getWeekDays($key -> batchId);
			}
			$this -> data['weekdays'] = $weekdays;
			if (isset($_POST['register'])) {
				$this -> load -> library("form_validation");
				$this -> form_validation -> set_rules('course_id', 'Course Name', 'required|trim|alpha_numeric');
				$this -> form_validation -> set_rules('faculty_id', 'Faculty Name', 'required|trim|alpha_numeric|max_length[108]|');
				$this -> form_validation -> set_rules('start_date', 'Start Date', 'required|trim|callback__checkingDate');
				$this -> form_validation -> set_rules('duration', 'Duration', 'required|trim|numeric');
				$this -> form_validation -> set_rules('strength', 'Strength', 'required|trim|numeric');
				if ($this -> form_validation -> run() == FALSE) {
					$this -> data['validate'] = true;
				} else {
					$branchData = array('batchStrength' => $_POST['strength'], 'batchDuration' => $_POST['duration'], 'branchCode' => $this -> branchCode, 'facultyId' => $_POST['faculty_id'], 'courseCode' => $_POST['course_id'], 'batchStartDate' => date("Y-m-d", strtotime($_POST['start_date'])));
					$update = false;
					if ($_POST['batchId'] == '') {
						$year = date('Y');
						$getMaximumBatchId = $this -> batch_model -> getMaxId($year, $this -> branchCode);
						if ($getMaximumBatchId > 0) {
							$batchId = $year . $this -> branchCode . $getMaximumBatchId;
						} else {
							$this -> data['validate'] = true;
						}
					} else {
						$batchId = $_POST['batchId'];
						$this -> batch_timing_model -> deleteDetailsByBatch($batchId);
						$update = true;
					}
					$branchData['batchId'] = $batchId;
					$batch_timings = array();
					$size = sizeof($_POST["batch_timing"]);
					if ($update ? $this -> batch_model -> updateBatch($branchData) : $this -> batch_model -> addBatch($branchData)) {
						for ($i = 0; $i < $size; ) {
							$dummy = array("batchTimingWeekday" => $_POST["batch_timing"][$i], "batchTimingStartTime" => $_POST["batch_timing"][++$i], "batchTimingEndTime" => $_POST["batch_timing"][++$i], "batchId" => $batchId);
							if (!$this -> batch_timing_model -> addBatchTime($dummy)) {
								$this -> data['error'] = "An Error Occured.";
								break;
							}
							$i++;
						}
						if ($this -> data['error'] == null) {
							redirect(base_url() . "branch_manager/batch");
						}
					} else {
						$this -> data['error'] = "An Error Occured.";
					}
				}
			}
			$this -> data['title'] = "ADS | Batch";
			$this -> load -> view('backend/master_page/top', $this -> data);
			$this -> load -> view('backend/css/batch_css');
			$this -> load -> view('backend/master_page/header');
			$this -> load -> view('backend/branch_manager/batch');
			$this -> load -> view('backend/master_page/footer');
			$this -> load -> view('backend/js/batch_js');
			$this -> load -> view('backend/master_page/bottom');
		}
	}

	public function _checkingTime($time) {
		$test_date = explode(':', $time);
		if (count($test_date) == 3 && is_numeric($test_date[0]) && is_numeric($test_date[1]) && is_numeric($test_date[2]) && $test_date[0] <= 23 && $test_date[0] >= 00 && $test_date[1] <= 59 && $test_date[2] >= 00 && $test_date[2] <= 59 && $test_date[2] >= 00) {
			return true;
		} else {
			$this -> form_validation -> set_message('_checkingTime', 'The given date is invalid');
			return false;
		}
	}

	public function _checkingDate($date) {
		$test_date = explode('-', $date);
		if (count($test_date) == 3 && is_numeric($test_date[0]) && is_numeric($test_date[1]) && is_numeric($test_date[2]) && checkdate($test_date[1], $test_date[0], $test_date[2])) {
			return true;
		} else {
			$this -> form_validation -> set_message('_checkingDate', 'The given date is invalid');
			return false;
		}
	}

	public function delete_batch($batchId) {
		$this -> load -> model('batch_model');
		$this -> batch_model -> deleteBatch($batchId);
		redirect(base_url() . "branch_manager/batch");
	}

	//Event
	public function event($eventId = '') {
		$this->data['menu'] = "Event";
		$this -> load -> model('event_model');
		$branchCode = $this -> branchCode;
		if ($eventId != '') {
			$this -> data['event'] = $this -> event_model -> getDetailsByEventBranch($branchCode, $eventId);
			echo json_encode($this -> data);
		} else {
			$this -> data['title'] = "ADS | Event";
			$this -> load -> view('backend/master_page/top', $this -> data);
			$this -> load -> view('backend/css/event_css');
			$this -> load -> view('backend/master_page/header');
			$this -> load -> model("event_type_model");
			$this -> load -> model('user_model');
			$this -> load -> model("batch_model");
			$this -> load -> model("state_model");
			$this -> data['State'] = $this -> state_model -> getDetailsOfState();
			$batch_data = $this -> batch_model -> getDetailsByBranch($this -> branchCode);
			$this -> data['batch_list'] = $batch_data;
			$this -> data['event_type'] = $this -> event_type_model -> getDetailsOfEventType();
			$this -> data['faculty'] = $this -> user_model -> getDetailsByBranchAndRole($branchCode, 3);
			$this -> data['event'] = $this -> event_model -> getDetailsByBranch($branchCode);

			if (isset($_POST['submitEvent'])) {
				$this -> load -> library("form_validation");
				$this -> form_validation -> set_rules('event_type_id', 'Event Type', 'required|trim|numeric|max_length[11]');
				$this -> form_validation -> set_rules('faculty_id', 'Faculty Name', 'required|trim|alpha_numeric|max_length[108]');
				$this -> form_validation -> set_rules('start_date', 'Start Date', 'required|trim|callback__checkingDate');
				$this -> form_validation -> set_rules('end_date', 'End Date', 'required|trim|callback__checkingDate');
				$this -> form_validation -> set_rules('event_name', 'Event Name', 'required|trim|alpha_numeric|max_length[100]');
				$this -> form_validation -> set_rules('description', 'Description', 'trim|max_length[500]');
				$this -> form_validation -> set_rules('street_1', 'Address 1', 'required|trim|alpha_numeric|max_length[100]');
				$this -> form_validation -> set_rules('organize_by', 'Organize By', 'required|trim|alpha_numeric|max_length[100]');
				$this -> form_validation -> set_rules('stateid', 'State', 'required|trim|alpha_numeric|max_length[50]');
				$this -> form_validation -> set_rules('cityid', 'City', 'required|trim|alpha_numeric|max_length[50]');
				$this -> form_validation -> set_rules('pin_code', 'Pin Code', 'required|trim|numeric|exact_length[6]');
				if ($this -> form_validation -> run() == FALSE) {
					$this -> data['validate'] = true;
				} else {
					$eventData = array('eventName' => $_POST['event_name'], 'eventDescription' => $_POST['description'], 'eventStreet1' => $_POST['street_1'], 'eventStreet2' => $_POST['street_2'], 'cityId' => $_POST['cityid'], 'stateId' => $_POST['stateid'], 'eventPinCode' => $_POST['pin_code'], 'eventOrganizerName' => $_POST['organize_by'], 'branchCode' => $branchCode, 'facultyId' => $_POST['faculty_id'], 'eventTypeId' => $_POST['event_type_id'], 'eventStartDate' => date("Y-m-d", strtotime($_POST['start_date'])), 'eventEndDate' => date("Y-m-d", strtotime($_POST['end_date'])));
					if ($_POST['eventId'] != "" ? $this -> event_model -> updateEvent($eventData, $_POST['eventId']) : $this -> event_model -> addEvent($eventData)) {
						redirect(base_url() . "branch_manager/event");
					} else {
						$this -> data['error'] = "An Error Occured.";
					}
				}
			}
			if (isset($_POST['submitEventAttendance'])) {

				$this -> load -> model('student_batch_model');
				$this -> load -> model('event_attendance_model');
				$student_data = $this -> student_batch_model -> getDetailsByBatch($_POST["batch_id"]);
				foreach ($student_data as $key) {
					$this -> event_attendance_model -> deleteAttendance($key -> studentId, $_POST["event_id"]);
				}

				$size = sizeof($_POST["student_ids"]);
				for ($i = 0; $i < $size; $i++) {
					$dummy = array('studentId' => $_POST["student_ids"][$i], 'eventId' => $_POST["event_id"], 'attendanceIsPresent' => 1);
					$this -> event_attendance_model -> addAttendance($dummy);
				}
				redirect(base_url() . "branch_manager/event");

			}
			$this -> load -> view('backend/branch_manager/event', $this -> data);
			$this -> load -> view('backend/master_page/footer');
			$this -> load -> view('backend/js/event_js');
			$this -> load -> view('backend/master_page/bottom');
		}
	}

	public function delete_event($eventId) {
		$this -> load -> model('event_model');
		$this -> event_model -> deleteEvent($eventId);
		redirect(base_url() . "branch_manager/event");
	}

	//target Report
	public function target_report($targetId = '') {
		$this->data['menu'] = "Target Report";
		$this -> load -> model('target_model');
		if ($targetId != '') {
			$this -> data['target'] = $this -> target_model -> getDetailsByTarget($targetId);
			echo json_encode($this -> data);
		} else {
			$this -> data['title'] = "ADS | Target Report";
			$this -> load -> view('backend/master_page/top', $this -> data);
			$this -> load -> view('backend/css/target_report_css');
			$this -> load -> view('backend/master_page/header');
			$this -> load -> model("target_report_model");
			$target_data = $this -> target_report_model -> getDetailsByBranch($this -> branchCode);
			$this -> data['target_report_list'] = $target_data;
			if (isset($_POST['addreport'])) {
				$reportData = array('targetReportDescription' => $_POST['report_description'], 'targetReportDate' => date("Y-m-d", strtotime($_POST['date'])), 'targetId' => $_POST['targetId']);
				$this -> target_report_model -> addReport($reportData);
				redirect(base_url() . "branch_manager/target_report");
			}
			$this -> load -> view('backend/branch_manager/target_report', $this -> data);
			$this -> load -> view('backend/master_page/footer');
			$this -> load -> view('backend/js/target_report_js');
			$this -> load -> view('backend/master_page/bottom');
		}
	}

}
?>