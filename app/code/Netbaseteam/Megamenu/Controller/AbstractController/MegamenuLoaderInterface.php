<?php

namespace Netbaseteam\Megamenu\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface MegamenuLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Netbaseteam\Megamenu\Model\Megamenu
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
