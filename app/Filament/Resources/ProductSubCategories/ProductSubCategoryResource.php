<?php

namespace App\Filament\Resources\ProductSubCategories;

use App\Filament\Resources\ProductSubCategories\Pages\CreateProductSubCategory;
use App\Filament\Resources\ProductSubCategories\Pages\EditProductSubCategory;
use App\Filament\Resources\ProductSubCategories\Pages\ListProductSubCategories;
use App\Filament\Resources\ProductSubCategories\Schemas\ProductSubCategoryForm;
use App\Filament\Resources\ProductSubCategories\Tables\ProductSubCategoriesTable;
use App\Models\ProductCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ProductSubCategoryResource extends Resource
{
    protected static ?string $model = ProductCategory::class;
    protected static ?string $modelLabel = '子類別';
    protected static ?string $pluralModelLabel = '子類別管理';
    protected static ?string $navigationLabel = '子類別管理';
    protected static UnitEnum|string|null $navigationGroup = '商品管理';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    // 只顯示子類別（有 parent_id 的）
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->whereNotNull('parent_id');

        if (Auth::user()->hasRole('admin')) {
            $query->where('admin_id', Auth::id());
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return ProductSubCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductSubCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListProductSubCategories::route('/'),
            'create' => CreateProductSubCategory::route('/create'),
            'edit'   => EditProductSubCategory::route('/{record}/edit'),
        ];
    }
}