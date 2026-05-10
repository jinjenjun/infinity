<?php

namespace App\Filament\Resources\ProductSubCategories\Schemas;

use App\Models\ProductCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductSubCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('parent_id')
                    ->label('主類別歸屬')
                    ->options(function () {
                        return ProductCategory::whereNull('parent_id')
                            ->when(
                                Auth::user()->hasRole('admin'),
                                fn ($q) => $q->where('admin_id', Auth::id())
                            )
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->searchable(),
                TextInput::make('name')
                    ->label('子類別名稱')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, $state, callable $set) {
                        if ($operation === 'create') {
                            $set('slug', Str::slug($state));
                        }
                    }),
                TextInput::make('slug')
                    ->label('網址代碼')
                    ->required()
                    ->unique(ignoreRecord: true),
            ]);
    }
}