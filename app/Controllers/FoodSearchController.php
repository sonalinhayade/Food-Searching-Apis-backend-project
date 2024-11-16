<?php

namespace App\Controllers;

use App\Models\FoodSearchModel;

class FoodSearchController extends BaseController
{
    protected $foodSearchModel;
    public function __construct()
    {
        $this->foodSearchModel = new FoodSearchModel();
    }

    // GETTING ALL PRODUCTS
    public function getAllProducts()
    {
        $page = $this->request->getVar('page') ?? 1;

        $products = $this->foodSearchModel->findAllProducts($page);

        $pager = \Config\Services::pager();

        $totalPages = $pager->getPageCount();
        if ($page > $totalPages) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'message' => 'No more content available. You have reached the last page'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'true',
            'message' => 'All products have been successfully fetched',
            'data' => $products,
            'pagination' => [
                'currentPage' => $pager->getCurrentPage(),
                'totalPages' => $pager->getPageCount(),
                'itemsPerPage' => $pager->getTotal(),
            ]
        ]);
    }

    // GETTING THE FILTERED PRICES
    public function filterPrice()
    {
        $priceRange = $this->request->getVar('price');
        $page = $this->request->getVar('page') ?? 1;
        $pager = \Config\Services::pager();

        if (!$priceRange) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'message' => 'Please provide a valid price range'
            ]);
        }

        $priceArray = explode('-', $priceRange);

        if (count($priceArray) != 2) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'message' => 'Please provide a valid min-max format (e.g., 200-500)'
            ]);
        }

        $minPrice = (float) $priceArray[0];
        $maxPrice = (float) $priceArray[1];

        $data['prices'] = $this->foodSearchModel->filterPrice($minPrice, $maxPrice, $page);

        $totalPages = $pager->getPageCount();

        if ($page > $totalPages) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'message' => 'No more content available. You have reached the last page'
            ]);
        }

        if (empty($data['prices'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'message' => 'No products fetched for the specified price range'
            ]);
        }
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Products successfully fetched within your price range',
            'data' => $data,
            'pagination' => [
                'currentPage' => $pager->getCurrentPage(),
                'totalPages' => $pager->getPageCount(),
                'itemsPerPage' => $pager->getTotal()
            ]
        ]);
    }

    public function filterRatings()
    {
        $ratingsRange = $this->request->getVar('ratings');
        $page = $this->request->getVar('page') ?? 1;
        $pager = \Config\Services::pager();

        if (!$ratingsRange) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'message' => "Please provide a valid ratings range"
            ]);
        }
        $ratingsArray = explode('-', $ratingsRange);

        if (strpos($ratingsRange, '-') === false || count($ratingsArray) != 2) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'message' => 'Ratings should be in the form min-max (e.g., 1-5)'
            ]);
        }

        $minRating = $ratingsArray[0];
        $maxRating = $ratingsArray[1];

        $data['ratings'] = $this->foodSearchModel->filterRatings($minRating, $maxRating, $page);

        $totalPages = $pager->getPageCount();
        if ($page > $totalPages) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'message' => 'No more content available. You have reached the last page'
            ]);
        }

        if (empty($data['ratings'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'error' => "No ratings fetched for the specified range"
            ]);
        }
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Products successfully fetched for your rating range',
            'data' => $data,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $pager->getPageCount(),
                'itemsPerPage' => $pager->getTotal()
            ]
        ]);
    }

    // GETTING THE FILTERED CATEGORY
    public function filterCategory()
    {
        $category = $this->request->getVar('category');
        $page = $this->request->getVar('page') ?? 1;
        $pager = \Config\Services::pager();

        if (!$category) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'error' => 'Please provide a valid category.'
            ]);
        }

        $data['product'] = $this->foodSearchModel->filterProducts($category, $page);
        $totalPages = $pager->getPageCount();

        if ($page > $totalPages) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'error' => 'No more content available. You have reached the last page'
            ]);
        }

        if (empty($data['product'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'error' => "No products fetched for the given category. Please try another."
            ]);
        }
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Products successfully fetched in the selected category',
            'data' => $data,
            'pagination' => [
                'currentPage' => $pager->getCurrentPage(),
                'totalPages' => $pager->getPageCount(),
                'itemsPerPage' => $pager->getTotal()
            ]
        ]);
    }

    // GETTING THE FILTERED TOPPINGS
    public function filterTopping()
    {
        $pager = \Config\Services::pager();
        $toppings = $this->request->getVar('topping');
        $page = $this->request->getVar('page') ?? 1;

        if (!$toppings) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'message' => 'Please provide a valid topping'
            ]);
        }
        $data['topping'] = $this->foodSearchModel->filterToppings($toppings, $page);

        $totalPages = $pager->getPageCount();

        if ($page > $totalPages) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'message' => 'No products fetched for the given category. Please try another'
            ]);
        }

        if (empty($data['topping'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'message' => 'No products fetched for the specified topping'
            ]);
        }
        return $this->response->setJSON(
            [
                'status' => true,
                'message' => 'Products successfully fetched with the selected topping',
                'data' => $data,
                'pagination' => [
                    'currentPage' => $page,
                    'totalPages' => $pager->getPageCount(),
                    'itemsPerPage' => $pager->getTotal()
                ]
            ]
        );
    }

    // GETTING THE FILTERED TYPES

    public function filterType()
    {
        $pager = \Config\Services::pager();
        $types = $this->request->getVar('type');
        $page = $this->request->getVar('page') ?? 1;

        if (!$types) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'false',
                'message' => "Please specify the type (e.g., Veg, Non-Veg)"
            ]);
        }
        $data['type'] = $this->foodSearchModel->filterType($types, $page);
        $totalPages = $pager->getPageCount();

        if ($page > $totalPages) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'false',
                'message' => "No products fetched for the given category. Please try another"
            ]);
        }
        return $this->response->setJSON(
            [
                [
                    'status' => true,
                    'message' => 'Products successfully fetched for the selected type',
                    'data' => $data,
                    'pagination' => [
                        'currentPage' => $page,
                        'totalPages' => $pager->getPageCount(),
                        'itemsPerPage' => $pager->getTotal()
                    ]
                ]
            ]
        );
    }
}