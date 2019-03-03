<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use App\Document\TasksList;
use App\Form\AddListType;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use App\Document\Task;

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

    /**
     * Delete list and all its tasks
     *
     * @param string $id
     * @return Response
     * 
     * @Route("list/delete/{listId}", name="delete_list")
     */
    public function delete(string $listId)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Delete list
        $this->documentManager
            ->createQueryBuilder(TasksList::class)
            ->remove()
            ->field('id')->equals($listId)
            ->getQuery()
            ->execute();

        // Delete tasks from list
        $this->documentManager
            ->createQueryBuilder(Task::class)
            ->remove()
            ->field('listId')->equals($listId)
            ->getQuery()
            ->execute();

        return $this->json([
            'code' => 0,
            'message' => 'The list and its tasks were deleted.'
        ]);
    }
}
