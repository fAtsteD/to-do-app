<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use App\Document\TasksList;
use App\Form\AddListType;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;

/**
 * Handle actions about lists
 */
class ListController extends AbstractController
{
    /** @var ObjectManager $documentManager  */
    private $documentManager;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->documentManager = $managerRegistry->getManager();
    }

    /**
     * Create new list
     *
     * @param Request $request
     * @return Response
     * 
     * @Method("POST")
     */
    public function createList(Request $request)
    {
        return $this->redirectToRoute('list_page');
    }
}
