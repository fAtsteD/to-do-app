<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\HttpFoundation\Response;
use App\Document\Task;
use App\Form\AddTaskType;
use App\Form\EditTask\EditTaskType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Document\TasksList;
use App\Form\AddListType;

/**
 * Main controller for app. It has root route.
 */
class TodoController extends AbstractController
{
    /** @var ObjectManager $documentManager  */
    private $documentManager;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->documentManager = $managerRegistry->getManager();
    }

    /**
     * Show tasks for choosen list and lists. Render form for quick create task and list
     *
     * @param Request $request
     * @param string $listId
     * @return Response
     *
     * @Route("/list/{listId}", name="list_page")
     * @Method({"GET", "POST"})
     */
    public function todoList(Request $request, string $listId = null)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Title for the page that is name of list
        $titleView = '';

        // Create task for form handle
        $task = new Task();

        // List of tasks and list of lists
        $taskRepository = $this->documentManager->getRepository(Task::class);
        $listRepository = $this->documentManager->getRepository(TasksList::class);

        // Create list form handle
        $tasksList = new TasksList();
        $addListForm = $this->createForm(AddListType::class, $tasksList);

        // If form submitted and valid than safe list
        $addListForm->handleRequest($request);
        if ($addListForm->isSubmitted() && $addListForm->isValid()) {
            $tasksList = $addListForm->getData();
            $tasksList->setUserId($this->getUser()->getId());

            $this->documentManager->persist($tasksList);
            $this->documentManager->flush();
        }

        // Find all list for logged in user
        $lists = $listRepository->findBy(['userId' => $this->getUser()->getId()]);

        // Create list for add task form
        $choicesList = [];
        foreach ($lists as $value) {
            if ($value->getId() == $listId && !is_null($listId)) {
                $titleView = $value->getTitle();
            }
            $choicesList[$value->getTitle()] = $value->getId();
        }
        $addTaskForm = $this->createForm(AddTaskType::class, $task, ['choicesList' => $choicesList]);

        // If form submitted and valid than safe task
        $addTaskForm->handleRequest($request);
        if ($addTaskForm->isSubmitted() && $addTaskForm->isValid()) {
            $task = $addTaskForm->getData();

            $this->documentManager->persist($task);
            $this->documentManager->flush();
        }

        // Find all tasks for the list. If the list does not set, open 'Inbox' list
        $tasks = [];
        if (!is_null($listId)) {
            $tasks = $taskRepository->findBy(['listId' => $listId]);
            $task->setListId($listId);
        }

        // If wrong id of list and did not find any task for current list than it returns tasks from 'Inbox'
        $defaultlist = '';
        if (count($tasks) == 0 || is_null($listId)) {
            $defaultlist = $listRepository->findOneBy([
                'title' => 'Inbox',
                'userId' => $this->getUser()->getId(),
            ]);
            $tasks = $taskRepository->findBy(['listId' => $defaultlist->getId()]);
            $titleView = $defaultlist->getTitle();
            $task->setListId($defaultlist->getId());
        }

        $assetPackageJS = new PathPackage('/js', new EmptyVersionStrategy());

        return $this->render("todo/index.html.twig", [
            'addTaskForm' => $addTaskForm->createView(),
            'tasks' => $tasks,
            'mainPageJS' => $assetPackageJS->getUrl('listPage.js'),
            'lists' => $lists,
            'addListForm' => $addListForm->createView(),
            'title' => $titleView,
        ]);
    }

    /**
     * Edit task by id
     *
     * @param Request $request
     * @param string $id
     * @return Response
     *
     * @Route("tast/edit/{id}", name="edit_task")
     * @Method({"GET", "POST"})
     */
    public function editTodo(Request $request, string $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $repository = $this->documentManager->getRepository(Task::class);

        $task = $repository->findOneById($id);
        if ($task == null) {
            throw $this->createNotFoundException("The task $id does not exist");
        }

        // Create list for add task form
        $lists = $this->documentManager
            ->getRepository(TasksList::class)
            ->findBy([
                'userId' => $this->getUser()->getId()
            ]);
        $choicesList = [];
        foreach ($lists as $value) {
            $choicesList[$value->getTitle()] = $value->getId();
        }

        // Create form for edit task
        $editTaskForm = $this->createForm(EditTaskType::class, $task, ['choicesList' => $choicesList]);

        // If form submitted and valid than safe list
        $editTaskForm->handleRequest($request);
        if ($editTaskForm->isSubmitted() && $editTaskForm->isValid()) {
            $task = $editTaskForm->getData();

            $this->documentManager->persist($task);
            $this->documentManager->flush();

            return $this->redirectToRoute('list_page');
        }

        // Additional JS files for handle forms data
        $assetPackageJS = new PathPackage('/js', new EmptyVersionStrategy());

        return $this->render("todo/editTask/index.html.twig", [
            'editTaskForm' => $editTaskForm->createView(),
            'task' => $task,
            'editTaskJS' => $assetPackageJS->getUrl('editTask.js')
        ]);
    }

    /**
     * Delete task through json request
     *
     * @param string $id
     * @return Response
     * 
     * @Route("task/delete/{id}", name="delete_task")
     * @Method({"POST"})
     */
    public function delete(string $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $repository = $this->documentManager->getRepository(Task::class);
        $task = $repository->findOneById($id);

        if ($task == null) {
            $response = new JsonResponse([
                'code' => 1,
                'message' => 'The task is not found.'
            ]);
        } else {
            $this->documentManager->remove($task);
            $this->documentManager->flush();

            $response = new JsonResponse([
                'code' => 0,
                'message' => 'The task was deleted.'
            ]);
        }

        return $response;
    }

    /**
     * Set done/undone through json request
     * 
     * The action is for main page mainly. Request has to be JSON type and have field isDone.
     *
     * @param Request $request
     * @param string $id
     * @return Response
     * 
     * @Route("task/check/{id}", name="check_task")
     * @Method({"POST"})
     */
    public function taskDone(Request $request, string $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($request->getContentType() == "json") {
            $response = new JsonResponse([
                'code' => 2,
                'message' => 'Wrong content type.'
            ]);
        } else {
            $task = $this->documentManager->getRepository(Task::class)->findOneById($id);

            if ($task == null) {
                $response = new JsonResponse([
                    'code' => 1,
                    'message' => 'The task is not found.'
                ]);
            } else {
                $reqIsDone = json_decode($request->getContent(), true);
                if (is_bool($reqIsDone["isDone"])) {
                    $task->setIsDone($reqIsDone["isDone"]);
                    $this->documentManager->persist($task);
                    $this->documentManager->flush();

                    $response = new JsonResponse([
                        'code' => 0,
                        'message' => 'The task is done/undone.'
                    ]);
                } else {
                    $response = new JsonResponse([
                        'code' => 3,
                        'message' => 'Wrong data.'
                    ]);
                }
            }
        }

        return $response;
    }
}
