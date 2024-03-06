<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EventResource\Pages;
use App\Filament\Admin\Resources\EventResource\RelationManagers;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;


class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function getNavigationGroup(): ?string
    {
        return (__('Website'));
    }

    public static function getPluralLabel(): ?string
    {
        return __('Event');
    }

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->translateLabel()
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(255)
                                    ->afterStateUpdated(fn(string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                                Forms\Components\TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Event::class, 'slug', ignoreRecord: true),

                                Forms\Components\MarkdownEditor::make('description')
                                    ->translateLabel()
                                    ->fileAttachmentsDirectory('event/attachments')
                                    ->required()
                                    ->columnSpan('full'),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make(__('Image'))
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('image')
                                    ->image()
                                    ->imageEditor()
                                    ->imageResizeMode('contain')
                                    ->imageCropAspectRatio('16:9')
                                    ->collection('event/images')
                                    ->hiddenLabel()
                            ])
                            ->collapsible(),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Option')
                            ->schema([
                                Forms\Components\DatePicker::make('date')
                                    ->timezone('Asia/Makassar')
                                    ->translateLabel()
                                    ->required(),

                                Forms\Components\Select::make('language')
                                    ->label(__("Language"))
                                    ->options([
                                        'id' => 'Bahasa Indonesia',
                                        'en' => 'English',
                                    ])
                                    ->searchable()
                                    ->required(),
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
                SpatieMediaLibraryImageColumn::make('image')->collection('event/images')
                    ->label(__('Image')),

                Tables\Columns\TextColumn::make('title')
                    ->wrap()
                    ->lineclamp(2)
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('date')
                    ->translateLabel()
                    ->sortable()
                    ->date('l, d M Y')
                    ->timezone('Asia/Makassar'),

                Tables\Columns\TextColumn::make('language')
                    ->sortable()
                    ->translatelabel()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'en' => 'success',
                        'id' => 'warning',
                    }),

            ])
            ->filters([
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->placeholder(fn($state): string => now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('until')
                            ->placeholder(fn($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'From ' . Carbon::parse($data['from'])->toFormattedDateString();
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = 'Until ' . Carbon::parse($data['until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
                Tables\Filters\SelectFilter::make('language')
                    ->translatelabel()
                    ->multiple()
                    ->options([
                        'en' => 'English',
                        'id' => 'Indonesia',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
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
                                ->translateLabel(),

                            Components\Split::make([
                                Components\Grid::make(['lg' => 3, 'md' => 2])
                                    ->schema([
                                        Components\TextEntry::make('date')
                                            ->translatelabel()
                                            ->date('l, d M Y'),

                                        Components\Group::make([
                                            Components\TextEntry::make('updated_by.name')
                                                ->label(__('Last updated by')),
                                        ]),

                                        Components\Group::make([
                                            Components\TextEntry::make('created_by.name')
                                                ->label(__('Created by')),
                                        ]),
                                    ]),
                                Components\ImageEntry::make('image')
                                    ->hiddenLabel()
                                    ->grow(false),
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
                            SpatieMediaLibraryImageEntry::make('image')
                                ->hiddenLabel()
                                ->collection('event/images'),
                        ]),
                ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(['lg' => 3]);
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
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'view' => Pages\ViewEvent::route('/{record}'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
