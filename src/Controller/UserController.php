<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;


class UserController extends AbstractController
{
    /** @var ManagerRegistry $documentManager */
    private $documentManager;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->documentManager = $managerRegistry->getManager();
    }

    // TODO: create view, form etc. for show user data and change them
}
