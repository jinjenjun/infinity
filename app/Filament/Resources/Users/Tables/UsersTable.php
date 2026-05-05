<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('姓名')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('電子信箱')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('角色')
                    ->badge(),
                TextColumn::make('phone')
                    ->label('電話')
                    ->searchable(),
                TextColumn::make('address')
                    ->label('地址')
                    ->searchable(),
                TextColumn::make('birthday')
                    ->label('生日')
                    ->date()
                    ->sortable(),
                TextColumn::make('gender')
                    ->label('性別')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label())
                    ->sortable(),
                TextColumn::make('age')
                    ->label('年齡')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
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