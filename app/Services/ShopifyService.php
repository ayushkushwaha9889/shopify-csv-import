<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Jobs\ImportProductToShopify;

class ShopifyService
{
    protected string $storeUrl;
    protected string $token;

    public function __construct()
    {
        $this->storeUrl = config('shopify.store_url');
        $this->token = config('shopify.access_token');
    }

    public function graphql(string $query, array $variables = [])
    {
        $payload = [
            'query' => $query,
        ];

        if (!empty($variables)) {
            $payload['variables'] = $variables;
        }

        return Http::withHeaders([
            'X-Shopify-Access-Token' => $this->token,
            'Content-Type' => 'application/json',
        ])->post(
                "{$this->storeUrl}/admin/api/2025-01/graphql.json",
                $payload
            );

        return $response->json();
    }

    public function createProduct(array $product)
    {
        $mutation = <<<'GRAPHQL'
            mutation productCreate($product: ProductCreateInput!) {
            productCreate(product: $product) {
                product {
                id
                title
                }
                userErrors {
                field
                message
                }
            }
            }
            GRAPHQL;

        return $this->graphql($mutation, [
            'product' => [
                'title' => $product['title'],
                'descriptionHtml' => $product['description'],
                'vendor' => $product['vendor'],
                'productType' => $product['product_type'],
                'tags' => !empty($product['tags'])
                    ? explode(',', $product['tags'])
                    : [],
            ]
        ]);
    }

    public function addProductToCollection(
        string $productId,
        string $collectionId
    ) {
        $mutation = <<<'GRAPHQL'
            mutation collectionAddProducts(
                $id: ID!,
                $productIds: [ID!]!
            ) {
            collectionAddProducts(
                id: $id,
                productIds: $productIds
            ) {
                job {
                id
                }
                userErrors {
                field
                message
                }
            }
            }
            GRAPHQL;

        return $this->graphql($mutation, [
            'id' => $collectionId,
            'productIds' => [$productId]
        ]);
    }

    public function updateProduct(
        string $productId,
        array $product
    ) {
        $mutation = <<<'GRAPHQL'
            mutation productUpdate(
            $product: ProductUpdateInput!
            ) {
            productUpdate(product: $product) {
                product {
                id
                title
                }
                userErrors {
                field
                message
                }
            }
            }
            GRAPHQL;

        return $this->graphql(
            $mutation,
            [
                'product' => [
                    'id' => $productId,
                    'title' => $product['title'],
                    'descriptionHtml' => $product['description'],
                    'vendor' => $product['vendor'],
                    'productType' => $product['product_type'],
                ]
            ]
        );
    }

}