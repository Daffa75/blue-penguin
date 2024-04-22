<?php

namespace App\Filament\Lecturer\Resources;

use App\Enums\HakiStatus;
use App\Enums\HakiType;
use App\Enums\PublicationScale;
use App\Filament\Lecturer\Resources\HakiResource\Pages;
use App\Filament\Tables\Columns\AuthorsList;
use App\Models\Haki;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class HakiResource extends Resource
{
    protected static ?string $model = Haki::class;
    protected static ?string $navigationIcon = 'phosphor-medal';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'name';
    public static function getNavigationGroup(): ?string
    {
        return __('Content');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Intellectual Properties');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $lecturerList = [];
        foreach ($record->lecturers as $lecturer){
            $lecturerList[] = $lecturer->name;
        }

        if (empty($lecturerList)){
            return [];
        }

        return array_combine(range(1, count($lecturerList)), array_values($lecturerList));
    }

    public static function getEloquentQuery(): Builder
    {
        $panelId = Filament::getCurrentPanel()->getId();
        if ($panelId == 'publication') {
            return parent::getEloquentQuery()->whereHas('lecturers', function (Builder $query) {
                return $query
                    ->where('nip', auth()->user()->lecturer?->nip);
            });
        }
        return parent::getEloquentQuery();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->heading(__('General Information'))
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->translateLabel()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('type')
                                            ->label(__('Type of Invention'))
                                            ->maxLength(255),
                                        Forms\Components\Select::make('scale')
                                            ->translateLabel()
                                            ->native(false)
                                            ->options(PublicationScale::class),
                                        Forms\Components\TextInput::make('year')
                                            ->translateLabel()
                                            ->default(now()->year)
                                            ->placeholder(now()->year)
                                            ->numeric()
                                            ->minValue(0),
                                    ])
                                    ->columns(3),
                            ])
                            ->collapsible(),
                        Forms\Components\Section::make()
                            ->heading(__('Inventors Information'))
                            ->schema([
                                Forms\Components\Select::make('inventors')
                                    ->label(__('Inventors'))
                                    ->multiple()
                                    ->relationship('lecturers', titleAttribute: 'name'),
                                Forms\Components\Select::make('faculty_id')
                                    ->label(__('Faculty'))
                                    ->multiple()
                                    ->preload()
                                    ->relationship('faculties', titleAttribute: 'name'),
                            ])
                            ->collapsible(),
                        Forms\Components\Section::make()
                            ->heading(__("Intellectual Property Information"))
                            ->schema([
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('haki_type')
                                            ->translateLabel()
                                            ->maxLength(255),
                                        Forms\Components\Select::make('status')
                                            ->native(false)
                                            ->options(HakiStatus::class),
                                    ])
                                    ->columns(2),
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('registration_no')
                                            ->translateLabel()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('haki_no')
                                            ->translateLabel()
                                            ->maxLength(255),
                                    ])
                                    ->columns(2),
                                Forms\Components\TextInput::make('registered_at')
                                    ->translateLabel()
                                    ->maxLength(255),

                            ])
                            ->collapsible(),
                        Forms\Components\Section::make()
                            ->heading(__('Proof of Intellectual Property'))
                            ->schema([
                                Forms\Components\Tabs::make('')
                                    ->tabs([
                                        Forms\Components\Tabs\Tab::make(__('Upload'))
                                            ->schema([
                                                Forms\Components\FileUpload::make('filename')
                                                    ->label('')
                                                    ->live()
                                                    ->directory('proof-ip')
                                            ]),
                                        Forms\Components\Tabs\Tab::make('Link')
                                            ->schema([
                                                Forms\Components\TextInput::make('link')
                                                    ->maxLength(2000),
                                            ]),
                                    ]),
                            ]),

                        Forms\Components\Section::make()
                            ->heading(__('Invention Output'))
                            ->schema([
                                Forms\Components\Textarea::make('output')
                                    ->label('')
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                            ])
                            ->collapsible(),
                    ])
                    ->columnSpan(['lg' => fn (?Haki $record) => $record === null ? 3 : 2]),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label(__('Created at'))
                            ->content(fn (Haki $record): ?string => $record->created_at?->diffForHumans()),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label(__('Last modified at'))
                            ->content(fn (Haki $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?Haki $record) => $record === null),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchPlaceholder(__('Search IP, Type, or Year'))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->description(function (Haki $record): Htmlable {
                        $haki_type = $record->haki_type ? $record->haki_type->getLabel() : '';
                        $type = $record->type ? $record->type : '';
                        $year = $record->year ? $record->year : '';

                        $separator = ' | ';

                        $content = implode($separator, array_filter([$haki_type, $type, $year]));

                        return new HtmlString("<span class='text-xs'>$content</span>");
                    }, position: 'above')
                    ->size(Tables\Columns\TextColumn\TextColumnSize::Small)
                    ->limit(60)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        // Only render the tooltip if the column content exceeds the length limit.
                        return $state;
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('haki_type', 'like', "%{$search}%")
                            ->orWhere('year', "%{$search}%");
                    }),
                AuthorsList::make('lecturers')
                    ->label(__('Inventors')),
                Tables\Columns\TextColumn::make('scale')
                    ->translateLabel()
                    ->badge()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('faculties.name')
                    ->bulleted()
                    ->searchable(),
                Tables\Columns\TextColumn::make('registration_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('haki_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('registered_at')
                    ->searchable(),
                Tables\Columns\TextColumn::make('link')
                    ->label('')
                    ->view('filament.tables.columns.click-here'),
                Tables\Columns\TextColumn::make('filename')
                    ->label('')
                    ->view('filament.tables.columns.attachment'),
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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('lecturers')
                    ->hidden(auth()->user()->role == '3')
                    ->native(false)
                    ->label(__('Inventors'))
                    ->searchable()
                    ->multiple()
                    ->relationship('lecturers', 'name'),
                Tables\Filters\SelectFilter::make('haki_type')
                    ->options(HakiType::class)
                    ->native(false)
                    ->label(__('Type')),
                Tables\Filters\SelectFilter::make('scale')
                    ->options(PublicationScale::class)
                    ->native(false)
                    ->label(__('Scale')),
                Tables\Filters\SelectFilter::make('status')
                    ->options(HakiStatus::class)
                    ->native(false)
                    ->label(__('Status')),
                Tables\Filters\Filter::make('year')
                    ->form([
                        Forms\Components\TextInput::make('year_from')
                            ->label(__('From'))
                            ->numeric()
                            ->placeholder(Haki::min('year'))
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
            ])->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListHakis::route('/'),
            'create' => Pages\CreateHaki::route('/create'),
            'edit' => Pages\EditHaki::route('/{record}/edit'),
        ];
    }
}
