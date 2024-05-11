<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\BaseApiController;
use App\Models\product\ProductModel;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
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

    public function checkout(Request $request)
    {
        try {
            
            DB::beginTransaction();

            $cart = Cart::findOrFail($request->cart_id);

            if (!$cart || $cart->cart_total <= 0) {
                return $this->sendError('Cart is empty or invalid');
            }

            $salesTotal = $cart->cart_total;
            $discount = 0;
            $grandTotal = $salesTotal - $discount;

            $order = Order::create([
                'user_id' => auth('api')->user()->id,
               
                'reference_id' => $request->reference_id,
                'order_type' => $request->order_type,
                'sales_total' => $salesTotal,
                'discount' => $discount,
                'grand_total' => $grandTotal,
            ]);

            $cart_items = CartItem::where('cart_id', $cart->id)->get();
            foreach ($cart_items as $cart_item) {
                $product = ProductModel::find($cart_item->product_id);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart_item->product_id,
                    'quantity' => $cart_item->quantity,
                    'price' =>  $product->product_price,
                    'total' => $cart_item->quantity * $product->product_price,
                ]);
            }

            $cart->delete();

            DB::commit();

            $order = $order->fresh(['items', 'table', 'items.product', 'items.product.image']);

            return $this->sendResponse($order, 'Order placed successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError('Something went wrong');
        }
    }

    public function getOrders()
    {
        try {
            $orders = Order::where('user_id', auth('api')->user()->id)->with('items' , 'items.product', 'items.product.image')->latest()->get();
    
            return $this->sendResponse($orders, "User's all orders");
        } catch (Exception $e) {
            return $this->sendError('Something went wrong');
        }
    }
    
    public function getOrderById(Request $request)
    {
        try {
            $order = Order::where('id', $request->id)->with('items', 'items.product', 'items.product.image')->first();
            if ($order) {
                return $this->sendResponse($order, "Order details");
            } else {
                return $this->sendError('Order not found.');
            }
        } catch (Exception $e) {
            return $this->sendError('Something went wrong');
        }
    }
    



    

}