<?php

namespace App\Filament\Resources\ProductCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('主類別名稱')
                    ->searchable(),
                TextColumn::make('slug')
                    ->label('網址代碼')
                    ->searchable(),
                TextColumn::make('products_count')
                    ->label('商品數')
                    ->counts('products')
                    ->sortable(),
                TextColumn::make('visibility')
                    ->label('可見度')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'public' => '公開',
                        'member' => '會員專屬',
                        'private' => '私人',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'public' => 'success',
                        'member' => 'warning',
                        'private' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
