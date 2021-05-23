<?php

namespace Netbaseteam\Themeconfig\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface ThemeconfigLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Netbaseteam\Themeconfig\Model\Themeconfig
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
