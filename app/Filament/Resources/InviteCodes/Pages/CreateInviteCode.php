<?php

namespace App\Filament\Resources\InviteCodes\Pages;

use App\Filament\Resources\InviteCodes\InviteCodeResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateInviteCode extends CreateRecord
{
    protected static string $resource = InviteCodeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['admin_id'] = Auth::id();
        return $data;
    }
}
