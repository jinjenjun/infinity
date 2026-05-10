<?php

namespace App\Filament\Resources\ProductSubCategories\Pages;

use App\Filament\Resources\ProductSubCategories\ProductSubCategoryResource;
use Filament\Resources\Pages\EditRecord;

class EditProductSubCategory extends EditRecord
{
    protected static string $resource = ProductSubCategoryResource::class;
}