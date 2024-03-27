<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\WebsitePageResource\Pages;
use App\Models\WebsitePages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Pages\SubNavigationPosition;

class WebsitePageResource extends Resource
{
    protected static ?string $model = WebsitePages::class;

    protected static ?string $navigationIcon = 'phosphor-browsers';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
    public static function getNavigationGroup(): ?string
    {
        return (__('Website'));
    }
    public static function getPluralLabel(): ?string
    {
        return __('Page Contents');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('page')
                            ->label('Page Title')
                            ->translateLabel()
                            ->validationMessages([
                                'regex' => 'Use lowercase letters',
                            ])
                            ->regex('/^[a-z ]+$/')
                            ->disabledOn('edit')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                            ->required(),

                        Forms\Components\TextInput::make('slug')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->maxLength(255)
                            ->unique(WebsitePages::class, 'slug', ignoreRecord: true),

                        Forms\Components\MarkdownEditor::make('content_id')
                            ->fileAttachmentsDirectory('content/attachments')
                            ->label('Content in Indonesian')
                            ->translateLabel()
                            ->required(),

                        Forms\Components\MarkdownEditor::make('content_en')
                            ->fileAttachmentsDirectory('content/attachments')
                            ->label('Content in English')
                            ->translateLabel()
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('page')
                    ->wrap()
                    ->lineClamp(2)
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->translateLabel()
                    ->timezone('asia/makassar')
                    ->datetime('l, d M Y h:m:s')
                    ->sortable(),
            ])
            ->defaultSort('page', 'asc')
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Group::make([
                    Components\Section::make()
                        ->schema([
                            Components\TextEntry::make('page')
                                ->translateLabel(),
                        ]),
                    Components\Section::make(__('Content in Indonesian'))
                        ->schema([
                            Components\TextEntry::make('content_id')
                                ->prose()
                                ->markdown()
                                ->hiddenLabel(),
                        ])
                        ->collapsible(),

                    Components\Section::make(__('Content in English'))
                        ->schema([
                            Components\TextEntry::make('content_en')
                                ->prose()
                                ->markdown()
                                ->hiddenLabel(),
                        ])
                        ->collapsible(),
                ])
                    ->columnSpan(['lg' => 2]),

                Components\Group::make([
                    Components\Section::make()
                        ->schema([
                            Components\TextEntry::make('updated_at')
                                ->timezone('Asia/Makassar')
                                ->datetime('l, d M Y H:m:s')
                                ->label(__('Last Updated')),

                            Components\TextEntry::make('updated_by.name')
                                ->label(__('Last updated by')),

                            Components\TextEntry::make('created_at')
                                ->timezone('Asia/Makassar')
                                ->datetime('l, d M Y H:m:s')
                                ->label(__('Created at')),

                            Components\TextEntry::make('created_by.name')
                                ->label(__('Created by')),
                        ]),
                ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(['lg' => 3]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewWebsitePage::class,
            Pages\EditWebsitePage::class,
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
            'index' => Pages\ListWebsitePage::route('/'),
            'create' => Pages\CreateWebsitePage::route('/create'),
            'view' => Pages\ViewWebsitePage::route('/{record}'),
            'edit' => Pages\EditWebsitePage::route('/{record}/edit'),
        ];
    }
}
