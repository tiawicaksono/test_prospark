<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $resource =  Product::orderBy('id', 'desc')->get();
        // $resource = ProductResource::collection(Product::paginate(5));
        return $resource;
        // return response()->json([
        //     'status' => 'success',
        //     'data' => $resource
        // ], 200);
    }

    public function sorting(Request $request)
    {
        try {
            $sortingParam = $request->sorting;
            $searchParam = $request->search_name;
            $minParam = $request->min;
            $maxParam = $request->max;

            $idOrderBy = "name";
            $orderBy = "asc";
            if ($sortingParam == "za") {
                $orderBy = "desc";
            }
            if ($sortingParam == "qty_asc") {
                $idOrderBy = "qty";
                $orderBy = "asc";
            }
            if ($sortingParam == "qty_desc") {
                $idOrderBy = "qty";
                $orderBy = "desc";
            }

            if (empty($searchParam)) {
                $resource =  Product::orderBy($idOrderBy, $orderBy)->get();
            } else {
                $resource =  Product::orderBy($idOrderBy, $orderBy)
                    ->where('name', 'ilike', '%' . $searchParam . '%')
                    ->orWhere('description', 'ilike', '%' . $searchParam . '%')
                    ->get();
            }
            return $resource;
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProductsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $data = $request->all();
            if ($request->file('image')) {
                $fileName = time() . '_' . str_replace(' ', '_', $request->file('image')->getClientOriginalName());
                $request->file('image')->storeAs('uploads', $fileName);
                $data['image'] = $fileName;
            }
            // $fileName = time() . '_' . str_replace(' ', '_', $request->file('image')->getClientOriginalName());
            // $request->file('image')->storeAs('uploads', $fileName);
            // $data['image'] = $fileName;
            Product::create($data);
            return response()->json([
                'status' => 'success',
                'message' => 'product created',
                'data' => $data
            ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage()
            ], 405);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProductsRequest  $request
     * @param  \App\Models\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            $id = $product->id;
            $data = $request->all();
            if ($request->file('image')) {
                $fileName = time() . '_' . str_replace(' ', '_', $request->file('image')->getClientOriginalName());
                $request->file('image')->storeAs('uploads', $fileName);
                $data['image'] = $fileName;
            }
            $response = Product::find($id);
            if ($response) {
                $response->update($data);
                return response()->json([
                    'status' => 'success',
                    'message' => 'product updated',
                    'data' => $data
                ], 200);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'product not found'
            ], 405);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage()
            ], 405);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $response = Product::find($id);
            if ($response) {
                $response->delete();
                return response()->json([
                    'status' => 'success',
                    'message' => 'product deleted'
                ], 200);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'product not found '
            ], 405);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage()
            ], 405);
        }
    }

    /**
     * Show the specified resource from storage.
     *
     * @param  \App\Models\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            // return new ProductResource(Product::findOrFail($id));
            $response = Product::find($id);
            if ($response) {
                return new ProductResource($response);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'product not found '
            ], 405);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage()
            ], 405);
        }
    }
}
