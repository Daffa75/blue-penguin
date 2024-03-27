<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\WebsitePagesResource\Pages;
use App\Models\WebsitePages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WebsitePagesResource extends Resource
{
    protected static ?string $model = WebsitePages::class;

    protected static ?string $navigationIcon = 'phosphor-browsers';
    public static function getNavigationGroup(): ?string
    {
        return (__('Website'));
    }
    public static function getPluralLabel(): ?string
    {
        return __('Pages Content');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('page')
                            ->options([
                                'downloads' => 'Download Page',
                                'students' => 'Students Page',
                            ])
                            ->disabledOn('edit')
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('language')
                            ->options([
                                'id' => 'Bahasa Indonesia',
                                'en' => 'English',
                            ])
                            ->disabledOn('edit')
                            ->required()
                            ->live(),

                        Forms\Components\MarkdownEditor::make('content')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        // Dynamically set pages options filter
        $pageNames = \app\Models\WebsitePages::orderBy('page', 'asc')->groupBy('page')->pluck('page')->toArray();
        $pageOptions = [];
        foreach ($pageNames as $page) {
            $pageOptions[$page] = $page;
        }

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('page')
                    ->wrap()
                    ->lineClamp(2)
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('content')
                    ->wrap()
                    ->lineClamp(3),

                Tables\Columns\TextColumn::make('language')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('page')
                    ->options($pageOptions),
                Tables\Filters\SelectFilter::make('language')
                    ->options([
                        'id' => 'Bahasa Indonesia',
                        'en' => 'English',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
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
            'index' => Pages\ListWebsitePages::route('/'),
            'create' => Pages\CreateWebsitePages::route('/create'),
            'edit' => Pages\EditWebsitePages::route('/{record}/edit'),
        ];
    }
}
