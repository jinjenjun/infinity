<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.parent.name')
                    ->label('主類別')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('category.name')
                    ->label('子類別')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('warning'),
                TextColumn::make('name')
                    ->label('商品名稱')
                    ->searchable(),
                TextColumn::make('price')
                    ->label('價格')
                    ->money('TWD')
                    ->sortable(),
                TextColumn::make('discount')
                    ->label('折扣比')
                    ->formatStateUsing(fn ($state) => ($state * 100) . '%')
                    ->sortable(),
                TextColumn::make('discounted_price')
                    ->label('促銷價')
                    ->money('TWD')
                    ->sortable(false),
                TextColumn::make('stock')
                    ->label('庫存')
                    ->numeric()
                    ->sortable(),
                ImageColumn::make('image')
                    ->label('圖片'),
                IconColumn::make('is_active')
                    ->label('上架')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('更新時間')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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