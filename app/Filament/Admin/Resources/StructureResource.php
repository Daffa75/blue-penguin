<?php

namespace App\Filament\Admin\Resources;

use App\Actions\FilamentImport\Action\ImportAction;
use App\Filament\Admin\Resources\StructureResource\Api\Transformers\StructureTransformer;
use App\Filament\Admin\Resources\StructureResource\Pages;
use App\Filament\Admin\Resources\StructureResource\RelationManager\SemesterRelationManager;
use App\Models\Curriculum\CurriculumStructure;
use App\Models\Curriculum\Semester;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Components;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Konnco\FilamentImport\Actions\ImportField;

class StructureResource extends Resource
{
    protected static ?string $model = CurriculumStructure::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getApiTransformer()
    {
        return StructureTransformer::class;
    }

    public static function getNavigationGroup(): ?string
    {
        return (__('Website'));
    }
    public static function getPluralLabel(): ?string
    {
        return __('Curriculum Structures');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Curriculum')
                            ->schema([
                                Forms\Components\TextInput::make('curriculum_name')
                                    ->label('Curriculum Structure')
                                    ->translateLabel()
                                    ->required()
                                    ->maxLength(64),

                                Forms\Components\Select::make('language')
                                    ->label(__('Language'))
                                    ->native(false)
                                    ->options([
                                        'id' => 'Bahasa Indonesia',
                                        'en' => 'English'
                                    ])
                                    ->required(),

                                Forms\Components\Select::make('website')
                                    ->label(__("Study Program"))
                                    ->options([
                                        'bachelor' => 'Sarjana',
                                        'master' => 'Magister',
                                    ])
                                    ->required(),
                            ])
                            ->columns(3),

                        Forms\Components\Section::make(__('Image'))
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('image')
                                    ->image()
                                    ->collection('curriculum_structure/images')
                                    ->hiddenLabel()
                            ])
                            ->collapsible(),
                    ])
                    ->columnSpan(['lg' => fn (?CurriculumStructure $record) => $record === null ? 3 : 2]),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (CurriculumStructure $record): ?string => $record->created_at?->diffForHumans()),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(fn (CurriculumStructure $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?CurriculumStructure $record) => $record === null),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Curriculum ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('curriculum_name')
                    ->label('Curriculum Name')
                    ->translateLabel()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('language')
                    ->label(__('Language'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('Total Semester')
                    ->label(__('Total Semester'))
                    ->state(function (?CurriculumStructure $record) {
                        $semesterCount = Semester::where('curriculum_id', '=', $record->id)->count();
                        return $semesterCount;
                    }),
                // ->label('Updated at'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated at')
                    ->dateTime('l, j F Y'),
            ])
            ->filters([
                Tables\Filters\Filter::make('curriculum_name')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Curriculum Name'))
                            ->placeholder(__('Curriculum Structure 2023'))
                            ->maxLength(64),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['name'],
                                fn (Builder $query, $name_input): Builder => $query->where('curriculum_name', 'like', "%{$name_input}%"),
                            );
                    })
                    ->columnSpan(3),
                Tables\Filters\SelectFilter::make('language')
                    ->label(__('Language'))
                    ->native(false)
                    ->options([
                        'id' => 'Bahasa Indonesia',
                        'en' => 'English',
                    ])
                    ->columnSpan(2),
            ], layout: Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->deferLoading();
    }

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         ImportAction::make('modules')
    //             ->fields([
    //                 ImportField::make('semester_name'),
    //                 ImportField::make('module_code'),
    //                 ImportField::make('module_name'),
    //                 ImportField::make('credit_points'),
    //             ])
    //             ->handleRecordCreation(function ($data) {

    //             })
    //     ];
    // }

    public static function getRelations(): array
    {
        return [
            SemesterRelationManager::class,
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            StructureResource\Pages\ViewStructure::class,
            StructureResource\Pages\EditStructure::class,
            StructureResource\Pages\SemestersStructure::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStructures::route('/'),
            'create' => Pages\CreateStructure::route('/create'),
            'edit' => Pages\EditStructure::route('/{record}/edit'),
            'view' => Pages\ViewStructure::route('/{record}/view'),
            'semesters' => Pages\SemestersStructure::route('/{record}/semesters'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Group::make()
                    ->schema([
                        Components\Section::make('Curriculum')
                            ->schema([
                                Components\TextEntry::make('id')
                                    ->label(__('Curriculum ID')),
                                Components\TextEntry::make('curriculum_name')
                                    ->label(__('Curriculum Name')),
                                Components\TextEntry::make('language')
                                    ->label(__('Language'))
                                    ->state(fn ($record) => $record === 'id' ? 'Bahasa Indonesia' : 'English'),
                                Components\TextEntry::make('website')
                                    ->label(__("Study Program"))
                                    ->getStateUsing(fn ($record): string => $record->website === 'bachelor' ? 'Sarjana' : 'Magister'),
                            ])
                            ->columns(2)
                            ->columnSpan(2),
                        Components\Section::make()
                            ->schema([
                                SpatieMediaLibraryImageEntry::make('image')
                                ->hiddenLabel()
                                ->collection('curriculum_structure/images'),
                                Components\TextEntry::make('created_at')
                                    ->dateTime('l, j F Y')
                                    ->since(),
                                Components\TextEntry::make('updated_at')
                                    ->dateTime('l, j F Y')
                                    ->since(),
                            ])
                            ->columnSpan(1),
                    ])
                    ->columns(3)
            ])
            ->columns(1);
    }
}
