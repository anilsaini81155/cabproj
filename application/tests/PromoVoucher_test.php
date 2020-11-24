<?php

class PromoVoucher_test extends TestCase {

    //Below Func is the test case for promocode gen part
    public function test_generateNewPromoCode_post() {
        $params = array(
            'event_name' => 'MUSCIC',
            'event_longitude' => '125',
            'event_latitude' => '110abc',
            'promocode_validity_start' => '25-05-2021',
            'promocode_validity_end' => '25-05-2022',
            'promocode_amount' => 100,
            'promocode_status' => 1,
            'promocode_radius' => 5
        );
        $ouput = $this->request('POST', ['PromoVoucher', 'generateNewPromoCode_post'], $params);
        $expected = 2;
        $this->assertContains($expected, $ouput['status']);
    }

    //Below Func is the test case for deactivate coupon code part
    public function test_deactivateCouponCode_post() {
        $params = array(
            'event_coupon_code' => 'ABC'
        );
        $ouput = $this->request('POST', ['PromoVoucher', 'deactivateCouponCode_post'], $params);
        $expected = 2;
        $this->assertContains($expected, $ouput['status']);
    }

    //Below Func is the test case for to get active promo code part
    public function test_getActivePromoCodes_get() {
        $ouput = $this->request('GET', ['PromoVoucher', 'getActivePromoCodes_get']);
        $this->assertTrue($ouput);
    }

    //Below Func is the test case for to get all promo codes part
    public function test_getAllPromoCodes_get() {
        $ouput = $this->request('GET', ['PromoVoucher', 'getAllPromoCodes_get']);
        $this->assertTrue($ouput);
    }

    //Below Func is the test case for to check the lat/lon is within the raidus of the event
    public function test_radiusCheckPickUpDrop_post() {
        $params = array(
            'event_coupon_code' => 'MUSIC',
            'dest_lat' => 1,
            'dest_long' => '',
            'pickup_lat' => '',
            'pickup_long' => ''
        );
        $expected = 2;
        $ouput = $this->request('POST', ['PromoVoucher', 'radiusCheckPickUpDrop_post'], $params);
        $this->assertContains($expected, $ouput['status']);
    }

    //Below Func is the test case for configuring the radius
    public function test_configureRadius_post() {
        $params = array(
            'event_coupon_code' => 'MUSIC',
            'radius' => 20
        );
        $expected = 2;
        $ouput = $this->request('POST', ['PromoVoucher', 'configureRadius_post'], $params);
        $this->assertContains($expected, $ouput['status']);
    }

    //Below Func is the test case for getting promo code details and polyline for maps
    public function test_getPromoCodeDetails_post() {
        $params = array(
            'event_coupon_code' => 'MUSIC',
            'dest_lat' => 20,
            'dest_long' => 'as050',
            'pickup_lat' => 51,
            'pickup_long' => 'q123'
        );
        $expected = 2;
        $ouput = $this->request('POST', ['PromoVoucher', 'getPromoCodeDetails_post'], $params);
        $this->assertContains($expected, $ouput['status']);
    }

}
