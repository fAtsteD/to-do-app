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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Security\Voters\ListVoter;

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
        // Get object of list by id
        $list = $this->documentManager
            ->getRepository(TasksList::class)
            ->findOneById($listId);

        if (is_null($list)) {
            return $this->json([
                'code' => 1,
                'message' => "The list id $listId is wrong."
            ]);
        }

        // If use can not delete
        $this->denyAccessUnlessGranted(ListVoter::DELETE, $list);

        // Delete list
        $this->documentManager->remove($list);
        $this->documentManager->flush();

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
