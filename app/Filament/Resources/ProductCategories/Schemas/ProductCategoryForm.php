<?php

namespace App\Filament\Resources\ProductCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('主類別名稱')
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
                Select::make('visibility')
                    ->label('可見度')
                    ->options([
                        'public' => '公開（所有人）',
                        'member' => '會員專屬（我的邀請會員）',
                        'private' => '私人（僅自己看得到）',
                    ])
                    ->default('public')
                    ->required(),
            ]);
    }
}
