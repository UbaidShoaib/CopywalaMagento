<?php
/**
 * @category   Netbaseteam
 * @package    Netbaseteam_Ajaxcart
 * @copyright  Copyright (c) 2018 Netbaseteam
 */

namespace Netbaseteam\Ajaxcart\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface AjaxcartLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
