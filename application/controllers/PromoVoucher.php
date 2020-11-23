<?php

require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class PromoVoucher extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Api_model');
        $this->load->library('form_validation');
    }

    public function generateNewPromoCode_post() {
        if ($this->post('event_name') && $this->post('event_longitude') && $this->post('event_latitude') && $this->post('promocode_validity_start') && $this->post('promocode_validity_end') && $this->post('promocode_amount') && $this->post('promocode_status') && $this->post('promocode_radius')) {

            $this->form_validation->set_rules('event_name', 'event_name', 'trim|required|min_length[3]|max_length[50]');
            $this->form_validation->set_rules('event_longitude', 'event_longitude', 'trim|required|callback_check_longitude');
            $this->form_validation->set_rules('event_latitude', 'event_latitude', 'trim|required|callback_check_latitude');
            $this->form_validation->set_rules('promocode_validity_start', 'promocode_validity_start', 'trim|required|callback_start_date_validity_check');
            $this->form_validation->set_rules('promocode_validity_end', 'promocode_validity_end', 'trim|required|callback_end_date_validity_check');
            $this->form_validation->set_rules('promocode_amount', 'promocode_amount', 'trim|required|numeric');
            $this->form_validation->set_rules('promocode_status', 'promocode_status', 'trim|required|in_list[1,2]');
            $this->form_validation->set_rules('promocode_radius', 'promocode_radius', 'trim|required|numeric');

            if ($this->form_validation->run() === TRUE) {
                $couponCode = $this->generateRandomNumber(strtoupper($this->post('event_name')));
                $insertData = array(
                    'event_name' => $this->post('event_name'),
                    'event_longitude' => $this->post('event_longitude'),
                    'event_latitude' => $this->post('event_latitude'),
                    'promocode_validity_start' => $this->post('promocode_validity_start'),
                    'promocode_validity_end' => $this->post('promocode_validity_end'),
                    'promocode_amount' => $this->post('promocode_amount'),
                    'promocode_status' => $this->post('promocode_status'),
                    'promocode_radius' => $this->post('promocode_radius'),
                    'event_coupon_code' => $couponCode,
                    'created_datetime' => date('Y-m-d H:i:s')
                );

                $this->db->trans_start();
                $this->Api_model->insert($insertData, 'promo');
                $this->db->trans_complete();
                $this->response(array('status' => 1, 'msg' => 'Coupon Code Generated', 'couponCode' => $couponCode), REST_Controller::HTTP_OK);
            } else {
                $response = array();
                $response['error'] = validation_errors();
                $response['status'] = 2;
                $response['msg'] = 'Insufficient Arguments';
                $this->response($response, REST_Controller::HTTP_PARTIAL_CONTENT);
            }
        } else {
            $this->response(array('status' => 2, 'msg' => 'Insufficient Arguments'), REST_Controller::HTTP_PARTIAL_CONTENT);
        }
    }

    public function generateRandomNumber($eventName) {
        $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $res = substr($eventName, 0, 3);
        for ($i = 0; $i < 7; $i++) {
            $res .= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        $code = $this->Api_model->select_single('event_promo_code', '', 'promo')['event_promo_code'];
        if ($code) {
            $this->generateRandomNumber($eventName);
        } else {
            return $res;
        }
    }

    public function start_date_validity_check($dob) {
        $dob = explode('-', $dob);
        if (($dob[0] >= 1 && $dob[0] <= 31) && ($dob[1] >= 1 && $dob[1] <= 12) && ($dob[2] >= date("Y"))) {
            return TRUE;
        } else {
            $this->form_validation->set_message('start_date_validity_check', 'Enter Start Date in DD-MM-YYYY,Date Cannot be Past');
            return FALSE;
        }
    }

    public function end_date_validity_check($dob) {
        $dob = explode('-', $dob);
        if (($dob[0] >= 1 && $dob[0] <= 31) && ($dob[1] >= 1 && $dob[1] <= 12) && ($dob[2] >= date("Y"))) {
            return TRUE;
        } else {
            $this->form_validation->set_message('end_date_validity_check', 'Enter End Date in DD-MM-YYYY,Date Cannot be Past');
            return FALSE;
        }
    }

    public function check_latitude($lat) {
        if (preg_match('/^(\+|-)?(?:90(?:(?:\.0{1,6})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,6})?))$/', $lat)) {
            return TRUE;
        } else {
            $this->form_validation->set_message('check_latitude', 'Enter Valid Latitude');
            return FALSE;
        }
    }

    public function check_longitude($long) {
        if (preg_match('/^(\+|-)?(?:180(?:(?:\.0{1,6})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,6})?))$/', $long)) {
            return TRUE;
        } else {
            $this->form_validation->set_message('check_latitude', 'Enter Valid Latitude');
            return FALSE;
        }
    }

    public function deactivateCouponCode_put() {
        if ($this->post('event_coupon_code')) {

            $this->form_validation->set_rules('event_coupon_code', 'event_coupon_code', 'trim|required|min_length[10]|max_length[15]');
            if ($this->form_validation->run() === TRUE) {
                if ($this->Api_model->select_single('event_promo_code', '', 'promo')['event_promo_code']) {
                    $this->db->trans_start();
                    $this->Api_model->upate(array('status' => 2, 'modified_datetime' => date('Y-m-d H:i:s')), array('event_promo_code' => $this->post('event_coupon_code')), 'promo');
                    $this->db->trans_complete();
                    $this->response(array('status' => 1, 'msg' => 'Coupon Code Deactivated'), REST_Controller::HTTP_OK);
                } else {
                    $this->response(array('status' => 2, 'msg' => 'Coupon Code Does Not Exist'), REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
                }
            } else {
                $response = array();
                $response['error'] = validation_errors();
                $response['status'] = 2;
                $response['msg'] = 'Insufficient Arguments';
                $this->response($response, REST_Controller::HTTP_PARTIAL_CONTENT);
            }
        } else {
            $this->response(array('status' => 2, 'msg' => 'Arguments Not Provided'), REST_Controller::HTTP_PARTIAL_CONTENT);
        }
    }

    public function getActivePromoCodes_get() {
        $promoCodes = $this->Api_model->select_multiple('event_promo_code', array('status' => 1), 'promo');
        $promoCodes['status'] = 1;
        $this->response($promoCodes, REST_Controller::HTTP_OK);
    }

    public function getAllPromoCodes_get() {
        $promoCodes = $this->Api_model->select_multiple('event_promo_code', '', 'promo');
        $promoCodes['status'] = 1;
        $this->response($promoCodes, REST_Controller::HTTP_OK);
    }

    public function radiusCheckPickUpDrop_get() {
        if ($this->post('event_coupon_code') && $this->post('dest_lat') && $this->post('dest_long') && $this->post('pickup_lat') && $this->post('pickup_long')) {


            $this->form_validation->set_rules('event_coupon_code', 'event_coupon_code', 'trim|required|min_length[10]|max_length[15]');
            $this->form_validation->set_rules('dest_long', 'dest_long', 'trim|required|callback_check_longitude');
            $this->form_validation->set_rules('dest_lat', 'dest_lat', 'trim|required|callback_check_latitude');
            $this->form_validation->set_rules('pickup_long', 'pickup_long', 'trim|required|callback_check_longitude');
            $this->form_validation->set_rules('pickup_lat', 'pickup_lat', 'trim|required|callback_check_latitude');
            if ($this->form_validation->run() === TRUE) {
                if ($this->Api_model->select_single('event_promo_code', array('status' => 1, 'event_promo_code' => $this->post('event_coupon_code')), 'promo')) {
                    $data = $this->Api_model->select_single('*', array('status' => 1, 'event_promo_code' => $this->post('event_coupon_code')), 'promo');
                    $data = $data[0];
                    $destCheck = $this->distance($this->post('dest_lat'), $this->post('dest_long'), $data['event_latitude'], $data['event_longitude']);
                    $pickUpCheck = $this->distance($this->post('pickup_lat'), $this->post('pickup_long'), $data['event_latitude'], $data['event_longitude']);

                    if (($destCheck <= $data['promocode_radius']) || ($pickUpCheck <= $data['promocode_radius'])) {
                        $this->response(array('status' => 1, 'msg' => 'Within Radius'), REST_Controller::HTTP_OK);
                    } else {
                        $this->response(array('status' => 2, 'msg' => 'Ouside Radius'), REST_Controller::HTTP_OK);
                    }
                } else {
                    $this->response(array('status' => 2, 'msg' => 'Coupon Code Does Not Exist'), REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
                }
            } else {
                $response = array();
                $response['error'] = validation_errors();
                $response['status'] = 2;
                $response['msg'] = 'Insufficient Arguments';
                $this->response($response, REST_Controller::HTTP_PARTIAL_CONTENT);
            }
        } else {
            $this->response(array('status' => 2, 'msg' => 'Arguments Not Provided'), REST_Controller::HTTP_PARTIAL_CONTENT);
        }
    }

    function distance($lat1, $lon1, $lat2, $lon2) {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            return ($miles * 1.609344);
        }
    }

    public function configureRadius_put() {
        if ($this->post('event_coupon_code') && $this->post('radius')) {

            $this->form_validation->set_rules('event_coupon_code', 'event_coupon_code', 'trim|required|min_length[10]|max_length[15]');
            $this->form_validation->set_rules('radius', 'radius', 'trim|required|numeric');
            if ($this->form_validation->run() === TRUE) {
                if ($this->Api_model->select_single('event_promo_code', array('status' => 1, 'event_promo_code' => $this->post('event_coupon_code')), 'promo')) {
                    $this->Api_model->update(array('radius' => $this->post('radius')), array('status' => 1, 'event_promo_code' => $this->post('event_coupon_code')), 'event_promo_code');
                    $this->response(array('status' => 1, 'msg' => 'Radius Updated'), REST_Controller::HTTP_OK);
                } else {
                    $this->response(array('status' => 2, 'msg' => 'Radius Not Updated || Promo Code Doecnot Existr'), REST_Controller::HTTP_OK);
                }
            } else {
                $response = array();
                $response['error'] = validation_errors();
                $response['status'] = 2;
                $response['msg'] = 'Insufficient Arguments';
                $this->response($response, REST_Controller::HTTP_PARTIAL_CONTENT);
            }
        } else {
            $this->response(array('status' => 2, 'msg' => 'Arguments Not Provided'), REST_Controller::HTTP_PARTIAL_CONTENT);
        }
    }

    public function getPromoCodeDetails_get() {
        if ($this->post('event_coupon_code') && $this->post('dest_lat') && $this->post('dest_long') && $this->post('pickup_lat') && $this->post('pickup_long')) {

            $this->form_validation->set_rules('dest_long', 'event_longitude', 'trim|required|callback_check_longitude');
            $this->form_validation->set_rules('dest_lat', 'event_latitude', 'trim|required|callback_check_latitude');
            $this->form_validation->set_rules('pickup_long', 'event_longitude', 'trim|required|callback_check_longitude');
            $this->form_validation->set_rules('pickup_lat', 'event_latitude', 'trim|required|callback_check_latitude');
            $this->form_validation->set_rules('event_coupon_code', 'event_coupon_code', 'trim|required|min_length[10]|max_length[15]');

            if ($this->form_validation->run() === TRUE) {

                if ($this->Api_model->select_single('event_promo_code', array('status' => 1, 'event_promo_code' => $this->post('event_coupon_code')), 'promo')) {

                    $data = $this->Api_model->select_single('*', array('status' => 1, 'event_promo_code' => $this->post('event_coupon_code')), 'promo');
                    $data = $data[0];

                    $responseData = array(
                        'event_promo_code' => $this->post('event_coupon_code'),
                        'promocode_validity_start' => $this->post('promocode_validity_start'),
                        'promocode_validity_end' => $this->post('promocode_validity_end'),
                        'promocode_amount' => $this->post('promocode_amount'),
                        'promocode_status' => $this->post('promocode_status'),
                        'promocode_radius' => $this->post('promocode_radius'),
                        'zoom' => 10,
                        'mapTypeId' => 'roadmap',
                        'center' => array('lat' => $data['event_latitude'], 'long' => $data['event_longitude']),
                        'flightPlanCoordinates' => array('dest_lat' => $this->post('dest_lat'), 'dest_long' => $this->post('dest_long'),
                            'pickup_lat' => $this->post('pickup_lat'), 'pickup_long' => $this->post('pickup_long'))
                    );
                    $responseData['status'] = 1;
                    $this->response($responseData, REST_Controller::HTTP_OK);
                } else {
                    $this->response(array('status' => 2, 'msg' => 'Promo Code Does Not Exist'), REST_Controller::HTTP_OK);
                }
            } else {
                $response = array();
                $response['error'] = validation_errors();
                $response['status'] = 2;
                $response['msg'] = 'Insufficient Arguments';
                $this->response($response, REST_Controller::HTTP_PARTIAL_CONTENT);
            }
        } else {
            $this->response(array('status' => 2, 'msg' => 'Arguments Not Provided'), REST_Controller::HTTP_PARTIAL_CONTENT);
        }
    }
}
