<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Main controller for handle all app
 */
class MainController extends AbstractController
{
    /**
     * Main page that redirect users to log in or tasks
     *
     * @Route("/", name="main_page")
     * @return Response
     */
    public function main()
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('list_page');
        } else {
            return $this->redirectToRoute('app_login');
        }
    }
}
