<?php

namespace App\Controller;

use matpoppl\SmallMVC\Controller\AbstractController;

class IndexController extends AbstractController
{
    public function indexAction()
    {
        return $this->redirect($this->view->route('tasks'));
    }
}
