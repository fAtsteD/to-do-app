<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use App\Document\TasksList;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use App\Document\Task;
use App\Security\Voters\ListVoter;
use App\Form\EditList\EditListType;
use App\Model\TasksList\UserInListModel;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

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
     * Edit list
     * 
     * Change name of list. Add users for sharing.
     *
     * @param string $listId
     * @return Response
     * 
     * @Route("list/edit/{listId}", name="edit_list")
     * @Method("POST")
     */
    public function edit(Request $request, string $listId)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Title is name of list
        $titleView = '';

        //Repositories
        $listRepostitory = $this->documentManager->getRepository(TasksList::class);

        // Find list and check if it exists
        $listRepostitoryResult = $listRepostitory->findListModel($listId);
        if (is_null($listRepostitoryResult)) {
            throw $this->createNotFoundException("The list $listId does not exist");
        }

        list($list, $listModel) = $listRepostitoryResult;
        $titleView = $list->getTitle();

        // Check if user has permission for editing
        $this->denyAccessUnlessGranted(ListVoter::EDIT, $list);

        // Create form for editing
        $editListForm = $this->createForm(EditListType::class, $listModel);

        // If form submitted and valid than safe list
        $editListForm->handleRequest($request);
        if ($editListForm->isSubmitted() && $editListForm->isValid()) {
            $listModel = $editListForm->getData();

            // Save of list of model is persist data
            $listModel->save($this->documentManager);
            $this->documentManager->flush();

            return $this->redirectToRoute('list_page');
        }

        $assetPackageJS = new PathPackage('/js', new EmptyVersionStrategy());

        return $this->render('list/editList/index.html.twig', [
            'title' => $titleView,
            'editListForm' => $editListForm->createView(),
            'listModel' => $listModel,
            'editListJS' => $assetPackageJS->getUrl('editList.js'),
        ]);
    }

    /**
     * Delete list and all its tasks
     *
     * @param string $id
     * @return Response
     * 
     * @Route("list/delete/{listId}", name="delete_list")
     * @Method("POST")
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
