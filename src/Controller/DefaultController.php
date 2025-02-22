<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        $user = $request->attributes->get('user');
        if ($user){
            echo "đang đăng nhập";
        }else{
            echo "không đăng nhập";
        }
        return $this->render('home/index.html.twig');
    }
}