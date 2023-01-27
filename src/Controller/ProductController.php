<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'products_list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/products',
        parameters: [
            new OA\Parameter(
                name: 'page',
                description: 'Parameter is used to paginate Products',
                in: 'query',
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'count',
                description: 'Parameter is used to limit number of Products per page',
                in: 'query',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns a list of Products or empty array',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Product::class))
                )
            )
        ]
    )]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $page = $request->get('page', 1);
        $count = $request->get('count', 1);

        /** @var Product[] $products */
        $products = $doctrine->getRepository(Product::class)->getAllProducts($count, $page);

        $result = new ArrayCollection();

        foreach ($products as $product) {
            $result->add(
                [
                    'id' => $product->getId(),
                    'productId' => $product->getProductId(),
                    'title' => $product->getTitle(),
                    'description' => $product->getDescription(),
                    'rating' => $product->getRating(),
                    'inetPrice' => $product->getInetPrice(),
                    'image' => $product->getImage(),
                    'price' => $product->getPrice(),
                ]
            );
        }

        return $this->json($result);
    }
}
