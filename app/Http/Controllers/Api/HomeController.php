<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\BaseApiController;
use App\Models\product\ProductModel;

class HomeController extends BaseApiController
{
    public function forhomepageapi()
    {
        try {
          
            $products = ProductModel::get()->shuffle();
            return $this->sendResponse($products, 'Products fetched successfully!');
        } catch (Exception $e) {
          
            return $this->sendError('Something went wrong!');
        }
    }

    public function getproductdesc($id)
    {
        try {
          
            $products = ProductModel::where('product_id', $id)->get();
            return $this->sendResponse($products, 'Products fetched successfully!');
        } catch (Exception $e) {
          
            return $this->sendError('Something went wrong!');
        }
    }
    

}