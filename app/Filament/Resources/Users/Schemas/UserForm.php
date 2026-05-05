<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\Gender;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('姓名')
                    ->required(),
                TextInput::make('email')
                    ->label('電子信箱')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->label('電話')
                    ->tel()
                    ->default(null),
                TextInput::make('address')
                    ->label('地址')
                    ->default(null),
                DatePicker::make('birthday')
                    ->label('生日'),
                Select::make('gender')
                    ->label('性別')
                    ->options(Gender::class)
                    ->default(null),
                TextInput::make('age')
                    ->label('年齡')
                    ->numeric()
                    ->default(null),
                DateTimePicker::make('email_verified_at')
                    ->label('信箱驗證時間'),
                TextInput::make('password')
                    ->label('密碼')
                    ->password()
                    ->required()
                    ->visibleOn('create'),
                Select::make('roles')
                    ->label('角色')
                    ->relationship('roles', 'name')
                    ->options(Role::all()->pluck('name', 'id'))
                    ->visible(fn () => Auth::user()->hasRole('superadmin')),
            ]);
    }
}