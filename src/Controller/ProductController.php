<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ProductController extends AbstractController
{
    /*public function createProduct(ValidatorInterface $validator): Response*/
    /**
     * @Route("/product", name="crete_product")
     * TODO validator geht nicht
     */

    public function createProduct(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $product = new Product();
        $product->setName('Mouse');
        $product->setPrice(999);
        $product->setDescription('stylish!');

        $entityManager->persist($product);
        $entityManager->flush();

      /*  $product = new Product();
        $product->setname(null);
        $product->setPrice('1999');
        $product->setDescription('Ergonomic and stylish!');
        $entityManager->persist($product);
        $entityManager->flush();

        $errors = $validator->validate(($product));
        if(count($errors) > 0 ) {
            return new Response((string)$errors, 400);
        }*/

        return new Response('Saved new product with id ' . $product->getId());
    }

    /**
     * @Route ("/product/{id}", name="product_show")
     */
    public function show(int $id, ProductRepository $repository): Response
    {
        // $repository = $this->getDoctrine()->getRepository(Product::class);
        $product = $repository->find($id);

        if(!$product) {
            throw $this->createNotFoundException('No product found for id ' . $id);
        }

//        return new Response('Check out this is great product ' .$product->getName());
        return $this->render('product/show.html.twig', ['product' => $product]);
    }

    /**
     * @param int $id
     * @param ProductRepository $repository
     * @return Response
     * @Route ("/product/edit/{id}")
     * TODO braucht man hier keinen Namen für die Route?
     */
    public function update(int $id, ProductRepository $repository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $product = $repository->find($id);
        if(!$product) {
            throw $this->createNotFoundException('No product found for id ' . $id);
        }

        $product->setName('New product name!');
        $entityManager->flush();

        return $this->redirectToRoute('product_show', ['id' => $product->getId()]);

    }

    /**
     * @param ProductRepository $repo
     * @return Response
     * @Route ("/showall", name="all_products")
     * TODO Wie kann man diese Route sowas wie product/all machen, damit es mit show-function nicht überkreuzt?
     */
    public function showAll(ProductRepository $repo): Response
    {
        $products = $repo->findAll();
        dump($products);
        die;
        return $this->render('product/showAll.html.twig', ['products' => $products]);
    }

    /**
     * @param int $id
     * @return Response
     * @Route ("/product/delete/{id}")
     */
    public function delete(int $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Product::class);
        $product = $repo->find($id);
        if(!$product) {
            throw $this->createNotFoundException('No product found for id ' . $id);
        }
        $em->remove($product);
        //$em->flush();
        return new Response('Product with id ' . $id . ' was deleted');

    }
}
