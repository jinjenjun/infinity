<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class BusinessChart extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    protected static ?string $navigationLabel = '商業圖表';

    protected static ?string $title = '商業圖表';

    protected string $view = 'filament.pages.business-chart';

    public function getLivewireId(): string
    {
        return $this->getId();
    }
}
