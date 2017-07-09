<?php
class ControllerPaymentJahanpay extends Controller {
	public function index() {
		$this->language->load('payment/jahanpay');

		$data['button_confirm'] = $this->language->get('button_confirm');

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if ($order_info) {
			$total = $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);
			//$total = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
			//if($this->currency->getCode() != 'RLS') $total *= 10;
			//$order_info['currency_code']
			try
			{
				ini_set("soap.wsdl_cache_enabled", "0");
				$client = new SoapClient("http://www.jpws.me/directservice?wsdl");
			}
			catch (SoapFault $ex)
			{
				die('Error1: constructor error');
			}
			try
			{
				$res = $client->requestpayment($this->config->get('jahanpay_jpin'), $total, $this->url->link('payment/jahanpay/callback', '', 'SSL'), $this->session->data['order_id']);
				if($res['result']==1)
				{
					$this->session->data['jau'] = $res['au'];
					return '<div style="display:none;">'.$res['form'].'</div><script>document.forms["jahanpay"].submit();</script>';
				}
				else
				{
					return 'Error in RequestPayment...';
				}
			}
			catch (SoapFault $ex)
			{
				die('Error2: error in get data from bank');
			}
		}
	}

	public function callback() {
		if (isset($this->session->data['order_id'])) {
			$order_id = $this->session->data['order_id'];
		} else {
			$order_id = 0;
		}
		if (isset($this->session->data['jau'])) {
			$au = $this->session->data['jau'];
		} else {
			$au = 0;
		}
		$this->session->data['jau'] = NULL;

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($order_id);
		if ($order_info) {
			$ok = false;
			$order_status_id = $this->config->get('config_order_status_id');
			$total = $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);
			if ($this->config->get('jahanpay_debug'))
			{
				$this->log->write('JAHANPAY :: OrderID='.$order_id.' ::  au='.$au.' :: POST=' . implode($this->request->post).' :: GET=' . implode($this->request->get));
			}
			try
			{
				ini_set("soap.wsdl_cache_enabled", "0");
				$client = new SoapClient("http://www.jpws.me/directservice?wsdl");
			}
			catch (SoapFault $ex)
			{
				die('Error1: constructor error');
			}
			try
			{
				$res = $client->verification($this->config->get('jahanpay_jpin'), $total, $au, $order_id, $_POST + $_GET );
				if($res['result']==1)
				{
					$ok = true;
					$order_status_id = $this->config->get('jahanpay_completed_status_id');
				}
				else
				{
					$order_status_id = $this->config->get('jahanpay_failed_status_id');
					if ($this->config->get('jahanpay_debug'))
					{
						$this->log->write('JAHANPAY :: OrderID='.$order_id.' :: error in verify ' . $res['result'] );
					}
				}
			}
			catch (SoapFault $ex)
			{
				die ('Error2: error in get data from bank.');
			}
			if (!$order_info['order_status_id']) {
				$this->model_checkout_order->addOrderHistory($order_id, $order_status_id);
			} else {
				$this->model_checkout_order->addOrderHistory($order_id, $order_status_id);
			}
			if ($ok == true)
			{
				header('location: '.$this->url->link('checkout/success'));
			}
			else
			{
				header('location: '.$this->url->link('checkout/checkout', '', 'SSL'));
			}
		}
	}
}