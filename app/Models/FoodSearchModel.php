<?php

namespace App\Models;

use CodeIgniter\Model;

class FoodSearchModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = "id";
    protected $allowedFields = ['product_name', 'product_description', 'product_category', 'veg_non_veg'];

    public function findAllProducts($page = 1)
    {
        return $this->select('products.product_name as "Product Name",products.product_description as "Product Description",product_prices.price AS "Product Price",round(avg(ratings.rating_value),1) as "Product Average Rating",products.product_category as "Product Category",group_concat(toppings.topping_name , toppings_groups.group_name ) as "Product Customization",products.veg_non_veg as "Product Type"')
            ->join('product_prices', 'product_prices.product_id = products.product_id')
            ->join('ratings', 'ratings.product_id = products.product_id')
            ->join('product_toppings', 'product_toppings.product_id = products.product_id')
            ->join('toppings', 'toppings.topping_id = product_toppings.topping_id')
            ->join('toppings_groups', 'toppings_groups.group_id = toppings.group_id')
            ->groupBy('products.product_id,product_prices.price')
            ->paginate(10, 'default', $page);
    }

    public function filterPrice($minPrice, $maxPrice, $page)
    {
        return $this->select('products.product_name as "Product Name",products.product_description as "Product Description",product_prices.price AS "Product Price",round(avg(ratings.rating_value),1) as "Product Average Rating",products.product_category as "Product Category",group_concat(toppings.topping_name , toppings_groups.group_name ) as "Product Customization",products.veg_non_veg as "Product Type"')
            ->join('product_prices', 'product_prices.product_id = products.product_id')
            ->join('ratings', 'ratings.product_id = products.product_id')
            ->join('product_toppings', 'product_toppings.product_id = products.product_id')
            ->join('toppings', 'toppings.topping_id = product_toppings.topping_id')
            ->join('toppings_groups', 'toppings_groups.group_id = toppings.group_id')
            ->having('product_prices.price >=', $minPrice)
            ->having('product_prices.price <=', $maxPrice)
            ->groupBy('products.product_id,product_prices.price')
            ->paginate(10, 'default', $page);
    }

    public function filterRatings($minRating, $maxRating, $page)
    {
        return $this->select('products.product_name as "Product Name",products.product_description as "Product Description",product_prices.price AS "Product Price",round(avg(ratings.rating_value),1) as "Product Average Rating",products.product_category as "Product Category",group_concat(toppings.topping_name , toppings_groups.group_name ) as "Product Customization",products.veg_non_veg as "Product Type"')
            ->join('product_prices', 'product_prices.product_id = products.product_id')
            ->join('ratings', 'ratings.product_id = products.product_id')
            ->join('product_toppings', 'product_toppings.product_id = products.product_id')
            ->join('toppings', 'toppings.topping_id = product_toppings.topping_id')
            ->join('toppings_groups', 'toppings_groups.group_id = toppings.group_id')
            ->having('round(avg(ratings.rating_value),1) >=', $minRating)
            ->having('round(avg(ratings.rating_value),1) <=', $maxRating)
            ->groupBy('products.product_id,product_prices.price')
            ->paginate(10, 'default', $page);
    }

    public function filterProducts($category, $page)
    {
        $categories = explode(',', $category);

        return $this->select('products.product_name as "Product Name",products.product_description as "Product Description",product_prices.price AS "Product Price",round(avg(ratings.rating_value),1) as "Product Average Rating",products.product_category as "Product Category",group_concat(toppings.topping_name , toppings_groups.group_name ) as "Product Customization",products.veg_non_veg as "Product Type"')
            ->join('product_prices', 'product_prices.product_id = products.product_id')
            ->join('ratings', 'ratings.product_id = products.product_id')
            ->join('product_toppings', 'product_toppings.product_id = products.product_id')
            ->join('toppings', 'toppings.topping_id = product_toppings.topping_id')
            ->join('toppings_groups', 'toppings_groups.group_id = toppings.group_id')
            ->whereIn('products.product_category', $categories)
            ->groupBy('products.product_id,product_prices.price')
            ->paginate(10, 'default', $page);
    }

    public function filterToppings($toppings, $page)
    {
        $toppingvariable = explode(',', $toppings);
        return $this->select('products.product_name as "Product Name",products.product_description as "Product Description",product_prices.price AS "Product Price",round(avg(ratings.rating_value),1) as "Product Average Rating",products.product_category as "Product Category",group_concat(toppings.topping_name , toppings_groups.group_name ) as "Product Customization",products.veg_non_veg as "Product Type"')
            ->join('product_prices', 'product_prices.product_id = products.product_id')
            ->join('ratings', 'ratings.product_id = products.product_id')
            ->join('product_toppings', 'product_toppings.product_id = products.product_id')
            ->join('toppings', 'toppings.topping_id = product_toppings.topping_id')
            ->whereIn('toppings.topping_name', $toppingvariable)
            ->join('toppings_groups', 'toppings_groups.group_id = toppings.group_id')
            ->groupBy('products.product_id,product_prices.price')
            ->paginate(10, 'default', $page);
    }

    public function filterType($type, $page)
    {
        $typevariable = explode(',', $type);
        return $this->select('products.product_name as "Product Name",products.product_description as "Product Description",product_prices.price AS "Product Price",round(avg(ratings.rating_value),1) as "Product Average Rating",products.product_category as "Product Category",group_concat(toppings.topping_name , toppings_groups.group_name ) as "Product Customization",products.veg_non_veg as "Product Type"')
            ->join('product_prices', 'product_prices.product_id = products.product_id')
            ->join('ratings', 'ratings.product_id = products.product_id')
            ->join('product_toppings', 'product_toppings.product_id = products.product_id')
            ->join('toppings', 'toppings.topping_id = product_toppings.topping_id')
            ->join('toppings_groups', 'toppings_groups.group_id = toppings.group_id')
            ->whereIn('products.veg_non_veg', $typevariable)
            ->groupBy('products.product_id,product_prices.price')
            ->paginate(10, 'default', $page);
    }
}
