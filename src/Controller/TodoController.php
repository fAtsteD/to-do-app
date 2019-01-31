<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

/**
 * Main controller for app. It has root route.
 */
class TodoController extends Controller
{
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
        $documentManager = $this->get('doctrine_mongodb')
            ->getManager();

        $task = new Task();
        $addTaskForm = $this->createForm(AddTaskType::class, $task);

        $addTaskForm->handleRequest($request);

        if ($addTaskForm->isSubmitted() && $addTaskForm->isValid()) {
            $task = $addTaskForm->getData();

            $documentManager->persist($task);
            $documentManager->flush();
        }

        $repository = $documentManager->getRepository(Task::class);

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
        $documentManager = $this->get('doctrine_mongodb')
            ->getManager();
        $repository = $documentManager->getRepository(Task::class);

        if ($task = $repository->findOneById($id) == null) {
            throw $this->createNewFoundException('The task does not exist');
        }

        $editTaskForm = $this->createForm(EditTaskType::class, $task);
        $editTaskForm->handleRequest($request);

        if ($editTaskForm->isSubmitted() && $editTaskForm->isValid()) {
            $task = $editTaskForm->getData();

            $documentManager->persist($task);
            $documentManager->flush();

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
        $documentManager = $this->get('doctrine_mongodb')
            ->getManager();
        $repository = $documentManager->getRepository(Task::class);
        $task = $repository->findOneById($id);

        if ($task == null) {
            $response = new JsonResponse([
                'code' => 1,
                'message' => 'The task is not found.'
            ]);
        } else {
            $documentManager->remove($task);
            $documentManager->flush();

            $response = new JsonResponse([
                'code' => 0,
                'message' => 'The task is deleted.'
            ]);
        }

        return $response;
    }
}
