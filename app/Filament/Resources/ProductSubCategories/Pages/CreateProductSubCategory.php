<?php

namespace App\Filament\Resources\ProductSubCategories\Pages;

use App\Filament\Resources\ProductSubCategories\ProductSubCategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateProductSubCategory extends CreateRecord
{
    protected static string $resource = ProductSubCategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['admin_id'] = Auth::id();
        return $data;
    }
}