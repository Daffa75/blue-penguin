<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DepartmentEventResource\Pages;
use App\Filament\Admin\Resources\DepartmentEventResource\RelationManagers;
use App\Filament\Admin\Resources\DepartmentEventResource\Widgets\CalendarWidget;
use App\Models\DepartmentEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DepartmentEventResource extends Resource
{
    protected static ?string $model = DepartmentEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    private function echoTest()
    {
        echo "test";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->columnSpanFull()
                                    ->translateLabel()
                                    ->required()
                                    ->unique(DepartmentEvent::class, 'title', ignoreRecord: true)
                                    ->maxLength(255),

                                Forms\Components\DateTimePicker::make('start')
                                    ->maxDate(fn (callable $get) => $get('end'))
                                    ->seconds(false)
                                    ->reactive()
                                    ->required(),

                                Forms\Components\DateTimePicker::make('end')
                                    ->disabled(fn (callable $get) => $get('start') === null)
                                    ->minDate(fn (callable $get) => $get('start'))
                                    ->seconds(false)
                                    ->reactive()
                                    ->required(),

                                Forms\Components\Textarea::make('description')
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('url')
                                    ->columnSpanFull()
                                    ->maxLength(255),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Option')
                            ->schema([
                                //TODO
                            ])
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->recordUrl(
                fn (DepartmentEvent $record): string => Pages\ViewDepartmentEvent::getUrl([$record->id]),
            )
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Group::make([
                    Components\Section::make()
                        ->schema([
                            Components\TextEntry::make('title')
                                ->hiddenLabel()
                                ->size(Components\TextEntry\TextEntrySize::Large)
                                ->translateLabel(),

                            Components\Split::make([
                                Components\Grid::make(4)
                                    ->schema([
                                        Components\TextEntry::make('start')
                                            ->label('Tanggal Mulai')
                                            ->badge()
                                            ->date('l, d M Y'),

                                        Components\TextEntry::make('start')
                                            ->label('Waktu Mulai')
                                            ->badge()
                                            ->date('H:i'),

                                        Components\TextEntry::make('end')
                                            ->label('Tanggal Selesai')
                                            ->badge()
                                            ->date('l, d M Y'),

                                        Components\TextEntry::make('end')
                                            ->label('Waktu Selesai')
                                            ->badge()
                                            ->date('H:i'),
                                    ]),
                            ])->from('lg'),
                        ]),
                    Components\Section::make(__('Description'))
                        ->schema([
                            Components\TextEntry::make('description')
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
                            // Components\Group::make([
                            //     Components\TextEntry::make('updated_by.name')
                            //         ->label(__('Last updated by')),
                            // ]),

                            // Components\Group::make([
                            //     Components\TextEntry::make('created_by.name')
                            //         ->label(__('Created by')),
                            // ]),

                            Components\TextEntry::make('url')
                                ->translatelabel(),
                        ]),
                ])
            ])
            ->columns(['lg' => 3]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LecturersRelationManager::class,
            RelationManagers\StudentsRelationManager::class
        ];
    }

    public static function getWidgets(): array
    {
        return [
            CalendarWidget::class
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewDepartmentEvent::class,
            Pages\EditDepartmentEvent::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartmentEvents::route('/'),
            'create' => Pages\CreateDepartmentEvent::route('/create'),
            'view' => Pages\ViewDepartmentEvent::route('/{record}'),
            'edit' => Pages\EditDepartmentEvent::route('/{record}/edit'),
        ];
    }
}
