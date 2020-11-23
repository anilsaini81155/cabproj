This project generates the promo code from the given parameters for an event associated with an rider service.
This project is build on PHP Codeigniter Framework v3.11.It is an REST API driven.

Below file has SQL Commands + REST API Urls to generate and set the data.

--1)Use below SQL Commands to generate a table.Make Db name as 'onroad'.


--Table Commands:

	CREATE TABLE `promo` (
	  `id` int(10) NOT NULL,
	  `event_name` varchar(50) NOT NULL,
	  `event_longitude` varchar(50) NOT NULL,
	  `event_latitude` varchar(50) NOT NULL,
	  `promocode_validity_start` varchar(50) NOT NULL,
	  `promocode_validity_end` varchar(50) NOT NULL,
	  `promocode_amount` varchar(10) NOT NULL,
	  `promocode_status` tinyint(2) NOT NULL,
	  `promocode_radius` varchar(10) NOT NULL,
	  `event_coupon_code` varchar(50) NOT NULL,
	  `created_datetime` datetime NOT NULL,
	  `modified_datetime` datetime NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=latin1;

	ALTER TABLE `promo`
	  ADD PRIMARY KEY (`id`);

	ALTER TABLE `promo`
	  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
	COMMIT;


--REST API URL's:

	-a)To generate new promo code
		URL => http://localhost/cabproj/index.php/PromoVoucher/generateNewPromoCode_post

		Input:
			event_name  => length(3,50)
			event_longitude => longitude value
			event_latitude => latitude value
			promocode_validity_start => (dd-mm-yyyy)
			promocode_validity_end => (dd-mm-yyyy)
			promocode_amount => numeric
			promocode_status => in_list[1 -> Yes,2 -> No]
			promocode_radius => numeric


	-b)To deactivate coupon code
		URL => http://localhost/cabproj/index.php/PromoVoucher/deactivateCouponCode_post

		Input:
			event_coupon_code => length(10,15)


	-c)To Get All Active Promo Codes
		URL => http://localhost/cabproj/index.php/PromoVoucher/getActivePromoCodes_get

	-d)To Get All Promo Codes
		URL => http://localhost/cabproj/index.php/PromoVoucher/getAllPromoCodes_get

	-e)Check PickUpDrop Radius
		URL => http://localhost/cabproj/index.php/PromoVoucher/radiusCheckPickUpDrop_post

		Input:
			event_coupon_code => length(10,15)
			dest_lat => latitude value
			dest_long => longitude value
			pickup_lat => latitude value
			pickup_long => longitude value


	-f)Update Radius
		URL => http://localhost/cabproj/index.php/PromoVoucher/configureRadius_post

		Input:
			event_coupon_code  => length(10,15)
			radius => numeric

	-g)Promo Code Details
		URL => http://localhost/cabproj/index.php/PromoVoucher/getPromoCodeDetails_post

		Input:
			event_coupon_code => => length(10,15)
			pickup_lat => latitude value
			pickup_long => longitude value
			dest_lat => latitude value
			dest_long => longitude value