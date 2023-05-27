<?php

namespace App\Controller;

use App\Repository\OrdersDetailsRepository;
use App\Repository\OrdersRepository;
use App\Repository\ProductsRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profil/{id}', name: 'profile_')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'index')]
public function index(int $id, UsersRepository $userRepo, OrdersRepository $orderRepo, OrdersDetailsRepository $orderdetailRepo,ProductsRepository $productRepo): Response
{
    $user = $userRepo->find($id);
    $orders = $orderRepo->findByExampleField($user->getId());
    
    $data = [];
    
    foreach ($orders as $order) {
        $orderDetails = $orderdetailRepo->findOneBySomeField($order->getId());
        $product = $productRepo->find($orderDetails->getProducts());
        // Add the order and its details as an associative array
        $data['orders'][] = [
            'order' => $order,
            'product'=>$product,
            'orderDetails' => $orderDetails,
        ];
    }
    
    $data['user'] = $user;
    $data['controller_name'] = 'Profil de l\'utilisateur';
    
    return $this->render('profile/index.html.twig', $data);
}
}
