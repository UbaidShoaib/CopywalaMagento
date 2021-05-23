<?php

namespace Netbaseteam\Themeconfig\Controller\Adminhtml\Status;

use Magento\Framework\View\Result\PageFactory;

class Setting extends \Magento\Framework\App\Action\Action {
	/**
	 * @var PageFactory
	 */
	protected $resultPageFactory;
	protected $_resultJsonFactory;
	protected $_helper;
	protected $_configWriter;

	/**
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param PageFactory $resultPageFactory
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		PageFactory $resultPageFactory,
		\Netbaseteam\Themeconfig\Helper\Data $helper,
		\Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->_resultJsonFactory = $resultJsonFactory;
		$this->_helper = $helper;
		$this->_configWriter = $configWriter;
		parent::__construct($context);
	}

	public function getCurrentProduct() {
		return $this->_registry->registry('current_product');
	}

	/**
	 * Default Orderupload Index page
	 *
	 * @return void
	 */
	public function execute() {
		$ret = array();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();

		$ret['message'] = 'Unsuccessful!';

		$get_data = $this->getRequest()->getPost();

		$type_status = $get_data['type_status'];
		$key_status = $get_data['key_status'];
		$name_status = $get_data['name_status'];
		$color_status = $get_data['color_status'];
		$act_status = $get_data['act_status'];

		if ($key_status != '' && $name_status != '') {
			if ($type_status == 'order') {
				if ($this->_helper->getIsAllowed('Netbaseteam_Themeconfig::status_order')) {
					$tableName = $resource->getTableName('sales_order_status');
					if ($act_status == 'edit') {
						$sql = "Update " . $tableName . " Set `color` = '$color_status', `label` = '$name_status' where `status` = '$key_status'";
						$connection->query($sql);
						$ret['message'] = 'Successful';
					} else {
						// $status = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '_', $code_status)));
						$sql = "Select * FROM " . $tableName . " where `status` = '$key_status'";
						$result = $connection->fetchAll($sql);
						if (count($result) > 0) {
							$ret['message'] = 'We found another order status with the same order status code!';
						} else {
							$sql = "Insert Into " . $tableName . " (status, label, color) Values ('$key_status','$name_status','$color_status')";
							$connection->query($sql);
							$ret['message'] = 'Successful';
						}
					}
				} else {
					$ret['message'] = 'No permission';
				}
			} elseif ($type_status == 'product') {
				if ($this->_helper->getIsAllowed('Netbaseteam_Themeconfig::status_product')) {
					if ($act_status == 'edit') {
						$result = $this->_helper->getStatusProduct();
						if (count($result) > 0) {
							if (isset($result[0]['value'])) {
								$arr_status = json_decode($result[0]['value'], true);
								if (count($arr_status) > 0) {
									$arr_status[$key_status] = $color_status;
									$this->_configWriter->save('themeconfig/status/product', json_encode($arr_status));
									$ret['message'] = 'Successful';
								}
							}
						} else {
							$arr_status = array('enabled' => '', 'disabled' => '');
							$arr_status[$key_status] = $color_status;
							$this->_configWriter->save('themeconfig/status/product', json_encode($arr_status));
							$ret['message'] = 'Successful';
						}
					}
				} else {
					$ret['message'] = 'No permission';
				}
			}
		}

		$result = $this->_resultJsonFactory->create();
		return $result->setData($ret);
	}
}
