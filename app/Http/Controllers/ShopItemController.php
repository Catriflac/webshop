<?php

namespace App\Http\Controllers;

use App\Models\ShopItem;
use App\Http\Requests\StoreShopItemRequest;
use App\Http\Requests\UpdateShopItemRequest;
use App\Http\Resources\ShopItemResource;
use Illuminate\Http\Request;

class ShopItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ShopItemResource::collection(ShopItem::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShopItemRequest $request)
    {
        $shopitem = ShopItem::create($request->validated());
        return ShopItemResource::make($shopitem);
    }

    /**
     * Display the specified resource.
     */
    public function show(ShopItem $shopItem)
    {
        return ShopItemResource::make($shopItem);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShopItemRequest $request, ShopItem $shopItem)
    {
        $shopItem->update($request->validated());
        return ShopItemResource::make($shopItem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShopItem $shopItem)
    {
        $shopItem->delete();
        return response('Item ' . $shopItem->name . ' deleted');
    }
}
