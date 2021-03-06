<?php
/**
 * @category   Netbaseteam
 * @package    Netbaseteam_Ajaxcart
 * @copyright  Copyright (c) 2018 Netbaseteam
 */

namespace Netbaseteam\Ajaxcart\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class AjaxcartLoader implements AjaxcartLoaderInterface
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        Registry $registry,
        \Magento\Framework\UrlInterface $url
    )
    {
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


        return true;
    }
}
