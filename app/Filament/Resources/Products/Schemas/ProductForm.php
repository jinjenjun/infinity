<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\ProductCategory;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_category_id')
                    ->label('商品分類')
                    ->options(function () {
                        return ProductCategory::whereNotNull('parent_id')
                            ->when(
                                Auth::user()->hasRole('admin'),
                                fn ($q) => $q->where('admin_id', Auth::id())
                            )
                            ->with('parent')
                            ->get()
                            ->mapWithKeys(fn ($cat) => [
                                $cat->id => $cat->parent->name . ' > ' . $cat->name
                            ]);
                    })
                    ->required()
                    ->searchable(),
                TextInput::make('name')
                    ->label('商品名稱')
                    ->required(),
                Textarea::make('description')
                    ->label('商品描述')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->label('價格')
                    ->required()
                    ->numeric()
                    ->prefix('NT$'),
                TextInput::make('discount')
                    ->label('折扣比（0.1~1.0）')
                    ->required()
                    ->numeric()
                    ->default(1.0)
                    ->minValue(0.1)
                    ->maxValue(1.0),
                TextInput::make('stock')
                    ->label('庫存')
                    ->required()
                    ->numeric()
                    ->default(0),
                FileUpload::make('image')
                    ->label('商品圖片')
                    ->disk('public')
                    ->image()
                    ->imageEditor()
                    ->imageAspectRatio('4:3')
                    ->automaticallyOpenImageEditorForAspectRatio()
                    ->imageEditorAspectRatioOptions([])
                    ->directory('products'),
                Toggle::make('is_active')
                    ->label('上架')
                    ->required(),
            ]);
    }
}