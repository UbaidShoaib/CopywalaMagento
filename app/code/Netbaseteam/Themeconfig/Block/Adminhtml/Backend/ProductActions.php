<?php
namespace Netbaseteam\Themeconfig\Block\Adminhtml\Backend;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder;
use Magento\Framework\Url;

class ProductActions {

    protected $urlBuilder;
    protected $context;
    protected $actionUrlBuilder;

    public function __construct(
        ContextInterface $context, Url $urlBuilder, UrlBuilder $actionUrlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->context = $context;
    }

    public function afterPrepareDataSource($productActions, $result) {
        if (isset($result['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');
            foreach ($result['data']['items'] as &$item) {
                $item[$productActions->getData('name')]['view'] = [
                    'href' => $this->actionUrlBuilder->getUrl(
                        $this->urlBuilder->getUrl(
                            'catalog/product/view', ['id' => $item['entity_id'], 'store' => $storeId]
                        ), isset($item['_first_store_id']) ? $item['_first_store_id'] : null, isset($item['store_code']) ? $item['store_code'] : null
                    ),
                    'label' => __('View')
                ];
            }
        }
        return $result;
    }
}
?>