<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductApiTest extends WebTestCase
{
    public function testResponseTypeAndStructure(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/products?page=1&count=10');

        $response = json_decode($client->getResponse()->getContent(), TRUE);
        $this->assertResponseIsSuccessful();
        
        $this->assertIsArray($response);
        
        if (count($response) > 0)
        {
            foreach ($response as $item) {
                $this->assertArrayHasKey('id', $item);
                $this->assertArrayHasKey('productId', $item);
                $this->assertArrayHasKey('title', $item);
                $this->assertArrayHasKey('description', $item);
                $this->assertArrayHasKey('price', $item);
                $this->assertArrayHasKey('inetPrice', $item);
                $this->assertArrayHasKey('image', $item);
                $this->assertArrayHasKey('rating', $item);
            }
        }
    }
}
