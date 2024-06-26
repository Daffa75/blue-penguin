<?php

namespace App\Filament\Lecturer\Resources;

use App\Enums\PublicationScale;
use App\Enums\PublicationType;
use App\Filament\Lecturer\Resources\PublicationResource\Widgets\PublicationStats;
use App\Filament\Lecturer\Resources\PublicationResource\Pages;
use App\Filament\Lecturer\Resources\PublicationResource\RelationManagers;
use App\Filament\Tables\Columns\AuthorsList;
use App\Filament\Tables\Columns\PublicationDetails;
use App\Models\Publication;
use App\Models\Student;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PublicationResource extends Resource
{
    protected static ?string $model = Publication::class;
    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';
    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('Content');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Publications');
    }

    public static function getEloquentQuery(): Builder
    {
        $panelId = Filament::getCurrentPanel()->getId();
        if ($panelId == 'lecturer') {
            return parent::getEloquentQuery()->whereHas('lecturers', function (Builder $query) {
                return $query
                    ->where('nip', auth()->user()->lecturer?->nip);
            });
        }
        return parent::getEloquentQuery();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'lecturers.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $lecturerList = [];
        foreach ($record->lecturers as $lecturer) {
            $lecturerList[] = $lecturer->name;
        }

        if (empty($lecturerList)) {
            return [];
        }

        return array_combine(range(1, count($lecturerList)), array_values($lecturerList));
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with('lecturers');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->heading(__('Main Information'))
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->translateLabel()
                                    ->required()
                                    ->columnSpan('full')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('year')
                                    ->translateLabel()
                                    ->numeric(),
                                Forms\Components\Select::make('type')
                                    ->translateLabel()
                                    ->required()
                                    ->native(false)
                                    ->selectablePlaceholder(false)
                                    ->options(PublicationType::class),
                                Forms\Components\TextInput::make('total_funds')
                                    ->label(__('Total Funds'))
                                    ->prefix('Rp.')
                                    ->numeric()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('fund_source')
                                    ->label(__("Source Fund"))
                                    ->maxLength(255),
                            ])
                            ->collapsible()
                            ->columns(2),
                        Forms\Components\Section::make('Additional Information')
                            ->heading(__('Additional Information'))
                            ->schema([
                                Forms\Components\TextInput::make('link')
                                    ->label(__('Source Link'))
                                    ->columnSpan('full')
                                    ->maxLength(1999),
                                Forms\Components\TextInput::make('citation')
                                    ->translateLabel()
                                    ->numeric()
                                    ->maxLength(255),
                                Forms\Components\Select::make('scale')
                                    ->label(__('Scale'))
                                    ->native(false)
                                    ->options(PublicationScale::class),
                            ])
                            ->columns(2)
                            ->collapsible(),
                    ])->columnSpan(['lg' => fn (?Publication $record) => $record === null ? 3 : 2]),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label(__('Created at'))
                            ->content(fn (Publication $record): ?string => $record->created_at?->diffForHumans()),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label(__('Last modified at'))
                            ->content(fn (Publication $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?Publication $record) => $record === null),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchPlaceholder(__('Search Title, Type and Scale'))
            ->columns([
                PublicationDetails::make('title')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('title', 'like', "%{$search}%")
                            ->orWhere('type', 'like', "%{$search}%")
                            ->orWhere('scale', 'like', "%{$search}%");
                    }),
                AuthorsList::make('lecturers')
                    ->label(__('Research Team')),
                TextColumn::make('students.name')
                    ->label('')
                    ->listWithLineBreaks(),
                Tables\Columns\TextColumn::make('total_funds')
                    ->label(__('Total Funds'))
                    ->prefix('Rp. ')
                    ->toggleable()
                    ->numeric(0, '.', ','),
                Tables\Columns\TextColumn::make('fund_source')
                    ->label(__('Source Fund'))
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('citation')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->translateLabel(),
                Tables\Columns\ViewColumn::make('link')
                    ->label(__(''))
                    ->view('filament.tables.columns.click-here'),
                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->deferLoading()
            ->filters([
                Tables\Filters\SelectFilter::make('lecturers')
                    ->hidden(auth()->user()->role != '0')
                    ->native(false)
                    ->label(__('Researcher'))
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->relationship('lecturers', 'name'),
                Tables\Filters\SelectFilter::make('students')
                    ->native(false)
                    ->label(__('Involved Student'))
                    ->searchable()
                    ->multiple()
                    ->getOptionLabelFromRecordUsing(fn (Student $record) => "{$record->name} - {$record->nim}")
                    ->relationship('students', 'name'),
                Tables\Filters\SelectFilter::make('type')
                    ->translateLabel()
                    ->options(PublicationType::class)
                    ->multiple()
                    ->native(false),
                Tables\Filters\Filter::make('year')
                    ->form([
                        Forms\Components\TextInput::make('year_from')
                            ->label(__('From'))
                            ->numeric()
                            ->placeholder(Publication::min('year'))
                            ->minValue(0),
                        Forms\Components\TextInput::make('year_until')
                            ->label(__('Until'))
                            ->numeric()
                            ->placeholder(now()->year)
                            ->minValue(0),
                    ])
                    ->columns([
                        'sm' => 2,
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['year_from'],
                                fn (Builder $query, $date): Builder => $query->where('year', '>=', $date),
                            )
                            ->when(
                                $data['year_until'],
                                fn (Builder $query, $date): Builder => $query->where('year', '<=', $date),
                            );
                    })
            ], layout: Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormColumns([
                'md' => 2,
                'lg' => 4
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LecturerRelationManager::class,
            RelationManagers\StudentsRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [
            PublicationStats::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPublications::route('/'),
            'create' => Pages\CreatePublication::route('/create'),
            'edit' => Pages\EditPublication::route('/{record}/edit'),
        ];
    }
}
