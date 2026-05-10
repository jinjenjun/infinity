<?php

namespace App\Filament\Resources\InviteCodes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InviteCodesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('邀請碼')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('已複製')
                    ->fontFamily('mono'),
                TextColumn::make('admin.name')
                    ->label('建立者')
                    ->sortable(),
                TextColumn::make('users_count')
                    ->label('使用人數')
                    ->counts('users')
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label('有效期限')
                    ->formatStateUsing(fn ($state) => $state ? $state->format('Y/m/d H:i') : '無期限')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y/m/d H:i')
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