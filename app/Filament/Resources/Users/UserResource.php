<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $modelLabel = '用戶';
    protected static ?string $pluralModelLabel = '用戶管理';
    protected static ?string $navigationLabel = '用戶管理';

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    // 只有 superadmin 和 admin 才能看到用戶管理
    public static function canViewAny(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

    // admin 只看到 user 身分的人，superadmin 看到所有人
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::user()->hasRole('admin')) {
            $query->whereHas('roles', fn ($q) => $q->where('name', 'user'));
        }

        return $query;
    }

    // 控制誰可以編輯
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $authUser = Auth::user();

        if ($authUser->hasRole('superadmin')) {
            return true;
        }

        if ($authUser->hasRole('admin')) {
            return $record->hasRole('user');
        }

        return false;
    }

    // 控制誰可以刪除
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $authUser = Auth::user();

        if ($authUser->hasRole('superadmin')) {
            return true;
        }

        if ($authUser->hasRole('admin')) {
            return $record->hasRole('user');
        }

        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}