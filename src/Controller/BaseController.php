<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    public $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
