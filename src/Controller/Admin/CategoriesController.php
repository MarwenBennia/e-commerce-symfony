<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Repository\CategoriesRepository;
use App\Form\CategoriesFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;


#[Route('/admin/categories', name: 'admin_categories_')]
class CategoriesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoriesRepository $categoriesRepository): Response
    {
        $categories = $categoriesRepository->findBy([], ['categoryOrder' => 'asc']);

        return $this->render('admin/categories/index.html.twig', compact('categories'));
    }

    #[Route('/ajout', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        //On crée un "nouveau produit"
        $category = new Categories();

        // On crée le formulaire
        $categoryForm = $this->createForm(CategoriesFormType::class, $category);

        // On traite la requête du formulaire
        $categoryForm->handleRequest($request);

        //On vérifie si le formulaire est soumis ET valide
        if($categoryForm->isSubmitted() && $categoryForm->isValid()){

            // On génère le slug
            $slug = $slugger->slug($category->getName());
            $category->setSlug($slug->append("slug"));

            // On stocke
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès');

            // On redirige
            return $this->redirectToRoute('admin_categories_index');
        }


         return $this->render('admin/categories/add.html.twig',[
             'categorieForm' => $categoryForm->createView()
         ]);

    }

}