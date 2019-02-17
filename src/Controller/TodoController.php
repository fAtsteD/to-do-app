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
     * Main page
     *
     * @return Response
     *
     * @Route("/", name="list_page")
     * @Method({"GET", "POST"})
     */
    public function todoList(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $task = new Task();
        $addTaskForm = $this->createForm(AddTaskType::class, $task);

        $addTaskForm->handleRequest($request);

        if ($addTaskForm->isSubmitted() && $addTaskForm->isValid()) {
            $task = $addTaskForm->getData();

            $this->documentManager->persist($task);
            $this->documentManager->flush();
        }

        $repository = $this->documentManager->getRepository(Task::class);

        $tasks = $repository->findAll();

        $assetPackageJS = new PathPackage('/js', new EmptyVersionStrategy());

        return $this->render("todo/index.html.twig", [
            'addTaskForm' => $addTaskForm->createView(),
            'tasks' => $tasks,
            'mainPageJS' => $assetPackageJS->getUrl('mainPage.js')
        ]);
    }

    /**
     * Edit task by id
     *
     * @param string $id
     * @return Response
     *
     * @Route("edit/{id}", name="edit_task")
     * @Method({"GET", "POST"})
     */
    public function editTodo(Request $request, string $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $repository = $this->documentManager->getRepository(Task::class);

        $task = $repository->findOneById($id);
        if ($task == null) {
            throw $this->createNewFoundException('The task does not exist');
        }

        $editTaskForm = $this->createForm(EditTaskType::class, $task);
        $editTaskForm->handleRequest($request);

        if ($editTaskForm->isSubmitted() && $editTaskForm->isValid()) {
            $task = $editTaskForm->getData();

            $this->documentManager->persist($task);
            $this->documentManager->flush();

            return $this->redirectToRoute('list_page');
        }

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
     * @Route("delete/{id}", name="delete_task")
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
                'message' => 'The task is deleted.'
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
     * @Route("check/{id}", name="check_task")
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
