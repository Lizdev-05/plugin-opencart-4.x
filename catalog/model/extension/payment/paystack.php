<?php
class ModelExtensionPaymentPaystack extends Model
{
    public function getMethod($address, $total)
    {
        $this->load->language('extension/payment/paystack');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_paystack_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if ($this->config->get('payment_paystack_total') > 0 && $this->config->get('payment_paystack_total') > $total) {
            $status = false;
        } elseif (!$this->config->get('payment_paystack_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }
        
        // Paystack only switches NGN, GHS, USD, ZAR, KES, EGP, and CIV for now
        if ($status && (!in_array(
            strtoupper($this->config->get('config_currency')),
            [
                'NGN',
                'GHS',
                'USD',
                'ZAR'
                'KES',
                'EGP',
                'CIV'
            ]
        ))
        ) {
            $status = true;
        }


        $method_data = [];
        $option_data = [];


       if ($status) {
        $option_data['paystack'] = [
        'code' => 'paystack.paystack',
        'name' => $this->language->get('heading_title')
        ];

       $method_data = [
        'code'       => 'paystack',
        'name'       => $this->language->get('heading_title'),
        'option'     => $option_data,
        'sort_order' => $this->config->get('payment_paystack_sort_order')
       ];
     }

        return $method_data;
    }
}
