<?php

namespace App\Filament\Resources\InviteCodes\Schemas;

use App\Models\InviteCode;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class InviteCodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('邀請碼')
                    ->default(fn () => InviteCode::generate())
                    ->required()
                    ->readOnly(),
                Toggle::make('has_expiry')
                    ->label('設定有效期限')
                    ->default(false)
                    ->live(),
                DateTimePicker::make('expires_at')
                    ->label('有效期限')
                    ->default(now()->addDays(7))
                    ->visible(fn ($get) => $get('has_expiry')),
            ]);
    }
}