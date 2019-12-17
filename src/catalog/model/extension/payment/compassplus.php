<?php

class ModelExtensionPaymentCompassplus extends Model
{

    /**
     * @param $order_data
     * @return \Compassplus\Sdk\Response\Order
     * @throws Exception
     */
    public function createOrder($order_data)
    {
        $timezone = new DateTimeZone("UTC");
        $date = new DateTime("now", $timezone);

        $order = new \Compassplus\Sdk\Order();
        $order->setAmount($this->currency->format($order_data['total'], $order_data['currency_code'], false, false));
        $order->setCurrency(643); // TODO
//        $order->setDescription("Test description");
        $order->setOrderId($order_data['order_id']);

        $merchant = new \Compassplus\Sdk\Merchant();
        $merchant->setLanguage(substr($order_data['language_code'], 0, 2));
        $merchant->setMerchantId($this->config->get('compassplus_merchant_account_id'));
        $merchant->setApproveUrl($this->url->link('extension/payment/compassplus/callback', '', true));
        $merchant->setCancelUrl($this->url->link('extension/payment/compassplus/callback', '', true));
        $merchant->setDeclineUrl($this->url->link('extension/payment/compassplus/callback', '', true));

        $address = new \Compassplus\Sdk\Customer\Address();
        $address->setCountry(840); // TODO
//        $address->setRegion("Moscow");
//        $address->setCity("Moscow");
//        $address->setAddressline("evergreen street");
//        $address->setZip("123123");

        $customer = new \Compassplus\Sdk\Customer($address);
        $customer->setEmail($order_data['email']);
        $customer->setPhone($order_data['telephone']);
        $customer->setIp($order_data['ip']);

        $connector = new \Compassplus\Sdk\Connector();
        try {
            $connector->setCert(DIR_SYSTEM . '/library/compassplus/compassplus.pem', '');
        } catch (Exception $e) {
            $this->log->write('Key error: ' . $e->getMessage());
        }
        $payment = new \Compassplus\Sdk\Payment($order, $merchant, $customer, $connector);
        try {
            $response = $payment->purchase();
        } catch (Exception $e) {
            $this->log->write('Purchase error: ' . $e->getMessage());
        }
        $this->log->write('Purchase data: ' . $response);


        if ($response->getStatus() == '00') {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "compassplus_order` SET
			`order_id` = '" . (int)$order_data['order_id'] . "',
			  `compassplus_order_id` = '" . (int)$response->getOrderId() . "',
			  `session_id` = '" . (int)$response->getSessionID() . "',
			  `date_added` = NOW(),
			  `currency_code` = '" . (int)$order_data['currency_code'] . "',
			  `total` = '" . (int)$order_data['total'] . "',
			");
        }

        return $response;
    }

    public function getMethod($address, $total)
    {
        $this->load->language('extension/payment/compassplus_hosted');

        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE geo_zone_id = '" . (int)$this->config->get('payment_compassplus_hosted_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if ($this->config->get('payment_compassplus_hosted_total') > 0 && $this->config->get('payment_compassplus_hosted_total') > $total) {
            $status = false;
        } elseif (!$this->config->get('payment_compassplus_hosted_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code' => 'compassplus_hosted',
                'title' => $this->language->get('text_title'),
                'terms' => '',
                'sort_order' => $this->config->get('payment_compassplus_hosted_sort_order')
            );
        }

        return $method_data;
    }
}