<?php

namespace Netbaseteam\Themeconfig\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class ThemeconfigLoader implements ThemeconfigLoaderInterface
{
    /**
     * @var \Netbaseteam\Themeconfig\Model\ThemeconfigFactory
     */
    protected $themeconfigFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @param \Netbaseteam\Themeconfig\Model\ThemeconfigFactory $themeconfigFactory
     * @param OrderViewAuthorizationInterface $orderAuthorization
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Netbaseteam\Themeconfig\Model\ThemeconfigFactory $themeconfigFactory,
        Registry $registry,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->themeconfigFactory = $themeconfigFactory;
        $this->registry = $registry;
        $this->url = $url;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return bool
     */
    public function load(RequestInterface $request, ResponseInterface $response)
    {
        $id = (int)$request->getParam('id');
        if (!$id) {
            $request->initForward();
            $request->setActionName('noroute');
            $request->setDispatched(false);
            return false;
        }

        $themeconfig = $this->themeconfigFactory->create()->load($id);
        $this->registry->register('current_themeconfig', $themeconfig);
        return true;
    }
}
