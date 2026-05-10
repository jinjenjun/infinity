<?php

namespace App\Filament\Resources\ProductCategories\Pages;

use App\Filament\Resources\ProductCategories\ProductCategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateProductCategory extends CreateRecord
{
    protected static string $resource = ProductCategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['admin_id'] = Auth::id();
        return $data;
    }
}