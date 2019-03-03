<?php

namespace App\DataFixtures\MongoDB;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Documents\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Document\TasksList;
use App\Document\Task;
use App\Document\Subtask;

class AppFixtures extends Fixture
{
    /** @var ObjectManager $documentManager */
    private $documentManager;

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    public function __contruct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->documentManager = $manager;

        // Load data
        $this->loadUsers();
        $this->loadLists();
        $this->loadTasks();

        $this->documentManager->flush();
    }

    /**
     * Create users and load them to db
     *
     * @return void
     */
    private function loadUsers()
    {
        $users = [
            [
                'name' => 'vasiliy',
                'birthday' => new \DateTime('10.11.952'),
            ],
            [
                'name' => 'anderson',
                'birthday' => new \DateTime('5.2.2016'),
            ],
            [
                'name' => 'vladislav',
                'birthday' => new \DateTime('22.05.1991'),
            ],
        ];

        foreach ($users as $item) {
            $user = new User();
            $user->setUsername($item['name']);
            $user->setEmail($item['name'] + '@mail.com');
            $user->setBirthday($item['birthday']);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $item['name']));

            $this->documentManager->persist($item);
        }
    }

    /**
     * Create default lists and some custom lists.
     *
     * @return void
     */
    private function loadLists()
    {
        $users = $this->documentManager->getRepository(User::class)->findAll();

        $lists = [
            [
                'title' => 'Home',
            ],
            [
                'title' => 'Work',
            ],
            [
                'title' => 'Projects',
            ],
        ];

        foreach ($users as $user) {
            // Save default list
            $list = new TasksList();
            $list->setTitle('Inbox');
            $list->setUserId($user->getId());

            $this->documentManager->persist($list);

            // Add custom lists for each user
            foreach ($lists as $item) {
                $list = new TasksList();
                $list->setTitle($item['title']);
                $list->setUserId($user->getId());

                $this->documentManager->persist($list);
            }
        }
    }

    private function loadTasks()
    {
        $lists = $this->documentManager->getRepository(TasksList::class)->findAll();

        $tasks = [
            [
                'title' => 'Clean a desk',
                'isDone' => false,
                'dueDate' => new \DateTime('20.05.2025'),
                'description' => 'Clean desk all day. So many trash.',
                'subtasks' => [
                    [
                        'title' => 'Clean table',
                        'isDone' => true,
                    ],
                    [
                        'title' => 'Clean boxes',
                        'isDone' => false,
                    ],
                ],
            ],
            [
                'title' => 'Runing',
                'isDone' => false,
                'dueDate' => new \DateTime('15.07.2000'),
                'description' => 'Everyday running.',
                'subtasks' => [],
            ],
            [
                'title' => 'Clean room',
                'isDone' => true,
                'dueDate' => new \DateTime('25.11.2020'),
                'description' => '',
                'subtasks' => [
                    [
                        'title' => 'Clean bed',
                        'isDone' => true,
                    ],
                    [
                        'title' => 'Clean chair',
                        'isDone' => true,
                    ],
                    [
                        'title' => 'Clean bookcase',
                        'isDone' => true,
                    ],
                ],
            ],
        ];

        $lists = $this->documentManager->getRepository(TasksList::class)->findAll();

        foreach ($lists as $list) {
            foreach ($tasks as $item) {
                $task = new Task();
                $task->setIsDone($item['isDone']);
                $task->setTitle($item['title']);
                $task->setDueDate($item['dueData']);
                $task->setDescription($item['description']);
                $task->setListId($list->getId());

                $subtasks = [];
                foreach ($item as $key => $subtask) {
                    $subtasks[$key] = new Subtask();
                    $subtasks[$key]->setTitle($subtask['title']);
                    $subtasks[$key]->setIsDone($subtask['isDone']);
                }

                $task->setSubtasks($subtasks);

                $this->documentManager->persist($task);
            }
        }
    }
}
