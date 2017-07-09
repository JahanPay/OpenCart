<?php
class ControllerPaymentJahanpay extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('payment/jahanpay');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('jahanpay', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');

		$data['entry_jpin'] = $this->language->get('entry_jpin');
		$data['entry_debug'] = $this->language->get('entry_debug');
		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_canceled_reversal_status'] = $this->language->get('entry_canceled_reversal_status');
		$data['entry_completed_status'] = $this->language->get('entry_completed_status');
		$data['entry_failed_status'] = $this->language->get('entry_failed_status');
		$data['entry_pending_status'] = $this->language->get('entry_pending_status');
		$data['entry_processed_status'] = $this->language->get('entry_processed_status');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['help_debug'] = $this->language->get('help_debug');
		$data['help_total'] = $this->language->get('help_total');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_order_status'] = $this->language->get('tab_order_status');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['jpin'])) {
			$data['error_jpin'] = $this->error['jpin'];
		} else {
			$data['error_jpin'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payment/jahanpay', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/jahanpay', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['jahanpay_jpin'])) {
			$data['jahanpay_jpin'] = $this->request->post['jahanpay_jpin'];
		} else {
			$data['jahanpay_jpin'] = $this->config->get('jahanpay_jpin');
		}

		if (isset($this->request->post['jahanpay_debug'])) {
			$data['jahanpay_debug'] = $this->request->post['jahanpay_debug'];
		} else {
			$data['jahanpay_debug'] = $this->config->get('jahanpay_debug');
		}

		if (isset($this->request->post['jahanpay_total'])) {
			$data['jahanpay_total'] = $this->request->post['jahanpay_total'];
		} else {
			$data['jahanpay_total'] = $this->config->get('jahanpay_total');
		}

		if (isset($this->request->post['jahanpay_canceled_reversal_status_id'])) {
			$data['jahanpay_canceled_reversal_status_id'] = $this->request->post['jahanpay_canceled_reversal_status_id'];
		} else {
			$data['jahanpay_canceled_reversal_status_id'] = $this->config->get('jahanpay_canceled_reversal_status_id');
		}

		if (isset($this->request->post['jahanpay_completed_status_id'])) {
			$data['jahanpay_completed_status_id'] = $this->request->post['jahanpay_completed_status_id'];
		} else {
			$data['jahanpay_completed_status_id'] = $this->config->get('jahanpay_completed_status_id');
		}

		if (isset($this->request->post['jahanpay_failed_status_id'])) {
			$data['jahanpay_failed_status_id'] = $this->request->post['jahanpay_failed_status_id'];
		} else {
			$data['jahanpay_failed_status_id'] = $this->config->get('jahanpay_failed_status_id');
		}

		if (isset($this->request->post['jahanpay_pending_status_id'])) {
			$data['jahanpay_pending_status_id'] = $this->request->post['jahanpay_pending_status_id'];
		} else {
			$data['jahanpay_pending_status_id'] = $this->config->get('jahanpay_pending_status_id');
		}

		if (isset($this->request->post['jahanpay_processed_status_id'])) {
			$data['jahanpay_processed_status_id'] = $this->request->post['jahanpay_processed_status_id'];
		} else {
			$data['jahanpay_processed_status_id'] = $this->config->get('jahanpay_processed_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['jahanpay_geo_zone_id'])) {
			$data['jahanpay_geo_zone_id'] = $this->request->post['jahanpay_geo_zone_id'];
		} else {
			$data['jahanpay_geo_zone_id'] = $this->config->get('jahanpay_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['jahanpay_status'])) {
			$data['jahanpay_status'] = $this->request->post['jahanpay_status'];
		} else {
			$data['jahanpay_status'] = $this->config->get('jahanpay_status');
		}

		if (isset($this->request->post['jahanpay_sort_order'])) {
			$data['jahanpay_sort_order'] = $this->request->post['jahanpay_sort_order'];
		} else {
			$data['jahanpay_sort_order'] = $this->config->get('jahanpay_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/jahanpay.tpl', $data));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/jahanpay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['jahanpay_jpin']) {
			$this->error['jpin'] = $this->language->get('error_jpin');
		}

		return !$this->error;
	}
}