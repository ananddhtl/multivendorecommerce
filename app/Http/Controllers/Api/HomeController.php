<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\BaseApiController;
use App\Models\product\ProductModel;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class HomeController extends BaseApiController
{

    public function getCart()
    {
    try {
        $carts = Cart::where('user_id', auth('api')->user()->id)
                     ->with('items', 'items.product', 'items.product.image')
                     ->withCount('items')
                     ->get();

        return response()->json([
            'success' => true,
            'message' => "User's Cart.",
            'data' => $carts,
        ], 200);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
            'error' => $e->getMessage(),
        ], 500);
    }
    }
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

    public function addToCart(Request $request)
    {
        try {
            DB::beginTransaction();
            $product = ProductModel::findOrFail($request->product_id);

            if ($product) {
                $existing_cart = Cart::where('user_id', auth('api')->user()->id)->first();

                if (!$existing_cart) {
                    $cart = Cart::create([
                        'user_id' => auth('api')->user()->id,
                        'cart_total' => 0,
                    ]);
                } else {
                    $cart = $existing_cart;
                }

                $data = [
                    'cart_id' => $cart->id,
                    'product_id' => $product->product_id,
                    'quantity' => $request->quantity
                ];

                CartItem::create($data);

                $new_cart_total = $cart->cart_total + ($request->quantity * $product->product_price);

                $cart->update(['cart_total' => $new_cart_total]);

                DB::commit();

                return $this->sendResponse([], 'Product added to cart');
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }
    public function updateCartItem(Request $request)
    {
        try {
            DB::beginTransaction();

            $item = CartItem::findOrFail($request->id);
            $old_quantity = $item->quantity;

            if ($request->quantity === 0) {
                $item->delete();
            } else {
                $item->quantity = $request->quantity;
                $item->save();
            }

            $cart = Cart::find($item->cart_id);
            $product = ProductModel::find($item->product_id);

            $cart->cart_total = $cart->cart_total - ($old_quantity * $product->price) + ($item->quantity * $product->product_price);
            $cart->save();

            DB::commit();

            return $this->sendResponse($item, 'Cart Item Updated successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    public function deleteCartItem(Request $request)
    {
        try {
            $item = CartItem::findOrFail($request->id);
            $old_quantity = $item->quantity;

            $item->delete();

            $cart = Cart::find($item->cart_id);
            $product = ProductModel::find($item->product_id);

            $cart->cart_total = $cart->cart_total - ($old_quantity * $product->price);
            $cart->save();

            return $this->sendResponse([], 'Cart Item Deleted successfully');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }



    

}