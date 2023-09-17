<?php

namespace App\Controller;

use matpoppl\SmallMVC\Controller\AbstractController;
use matpoppl\SmallMVC\Router\MatchException;

class ErrorController extends AbstractController
{

    public function errorAction()
    {
        $this->view->meta->title('Error');

        $viewData = [
            'title' => 'Error',
            'message' => 'Unknown error',
        ];

        $ex = $this->request->get('exception');

        if ($ex instanceof MatchException) {
            $this->response = $this->response->withStatus($ex->getCode());
            $this->view->meta->title($ex->getCode() . ' ' . $ex->getMessage());
            $viewData['message'] = $ex->getMessage();
            $viewData['exception'] = $ex;
        }

        return $this->render('error/error.phtml', $viewData);
    }

    public function indexAction()
    {
        throw new \BadMethodCallException('Unsupported method');
    }
}
