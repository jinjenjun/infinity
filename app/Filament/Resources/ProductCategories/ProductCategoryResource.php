<?php

namespace App\Filament\Resources\ProductCategories;

use App\Filament\Resources\ProductCategories\Pages\CreateProductCategory;
use App\Filament\Resources\ProductCategories\Pages\EditProductCategory;
use App\Filament\Resources\ProductCategories\Pages\ListProductCategories;
use App\Filament\Resources\ProductCategories\Schemas\ProductCategoryForm;
use App\Filament\Resources\ProductCategories\Tables\ProductCategoriesTable;
use App\Models\ProductCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ProductCategoryResource extends Resource
{
    protected static ?string $model = ProductCategory::class;
    protected static ?string $modelLabel = '主類別';
    protected static ?string $pluralModelLabel = '主類別管理';
    protected static ?string $navigationLabel = '主類別管理';
    protected static UnitEnum|string|null $navigationGroup = '商品管理';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->whereNull('parent_id');

        if (Auth::user()->hasRole('admin')) {
            $query->where('admin_id', Auth::id());
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return ProductCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListProductCategories::route('/'),
            'create' => CreateProductCategory::route('/create'),
            'edit'   => EditProductCategory::route('/{record}/edit'),
        ];
    }
}