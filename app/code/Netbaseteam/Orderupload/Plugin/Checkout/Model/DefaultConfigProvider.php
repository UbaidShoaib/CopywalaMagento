<?php
namespace Netbaseteam\Orderupload\Plugin\Checkout\Model;

use Magento\Checkout\Model\Session as CheckoutSession;

class DefaultConfigProvider
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    protected $helperOnlineDesign;

    /**
     * @param CheckoutSession $checkoutSession
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Netbaseteam\Onlinedesign\Helper\Integrate $helperOnlineDesign,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->layoutFactory = $layoutFactory;
        $this->helperOnlineDesign = $helperOnlineDesign;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * add new output orderUpload
     * @param \Magento\Checkout\Model\DefaultConfigProvider $subject
     * @param array $result
     * @return array
     */
    public function afterGetConfig(
        \Magento\Checkout\Model\DefaultConfigProvider $subject,
        array $result
    )
    {
        $items = $result['totalsData']['items'];
        foreach ($items as $index => $item) {
            $quoteItem = $this->checkoutSession->getQuote()->getItemById($item['item_id']);
            $productId = $quoteItem->getProduct()->getId();
            $sku = $quoteItem->getProduct()->getSku();
            $sessionFile = $quoteItem->getSessionFile();

            $result['quoteItemData'][$index]['onlineDesign'] = $this->helperOnlineDesign->showVirtualProductDesign($quoteItem);

            $result['quoteItemData'][$index]['orderUpload'] = $this->showOrderUploadOnCheckoutPage($productId, $sku, $sessionFile);
        }
        return $result;
    }

    /**
     * check ourderupload enable or not
     * @return int
     */
    public function isShowUploadCheckout()
    {
        return $this->scopeConfig->getValue('orderupload/view/show_on_checkout', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * set data to template order upload
     * @param $productId
     * @param $sku
     * @param $sessionFile
     * @return string
     */
    protected function showOrderUploadOnCheckoutPage($productId, $sku, $sessionFile)
    {
        $show_on_checkout = $this->isShowUploadCheckout();
        if ($show_on_checkout) {
            return $this->layoutFactory->create()->createBlock('Magento\Framework\View\Element\Template')
                ->setData('block_data', $productId . "|" . $sku . "|" . $sessionFile)
                ->setTemplate('Netbaseteam_Orderupload::checkout.phtml')->toHtml();
        } else {
            return "";
        }
    }
}