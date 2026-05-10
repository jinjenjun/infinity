<?php

namespace App\Filament\Resources\InviteCodes;

use App\Filament\Resources\InviteCodes\Pages\CreateInviteCode;
use App\Filament\Resources\InviteCodes\Pages\EditInviteCode;
use App\Filament\Resources\InviteCodes\Pages\ListInviteCodes;
use App\Filament\Resources\InviteCodes\Schemas\InviteCodeForm;
use App\Filament\Resources\InviteCodes\Tables\InviteCodesTable;
use App\Models\InviteCode;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class InviteCodeResource extends Resource
{
    protected static ?string $model = InviteCode::class;
    protected static ?string $modelLabel = '邀請碼';
    protected static ?string $pluralModelLabel = '邀請碼管理';
    protected static ?string $navigationLabel = '邀請碼管理';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    public static function canViewAny(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::user()->hasRole('admin')) {
            $query->where('admin_id', Auth::id());
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return InviteCodeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InviteCodesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListInviteCodes::route('/'),
            'create' => CreateInviteCode::route('/create'),
            'edit'   => EditInviteCode::route('/{record}/edit'),
        ];
    }
}