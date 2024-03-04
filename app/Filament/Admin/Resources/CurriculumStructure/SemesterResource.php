<?php

namespace App\Filament\Admin\Resources\CurriculumStructure;

use App\Filament\Admin\Resources\CurriculumStructure\SemesterResource\Pages;
use App\Filament\Admin\Resources\CurriculumStructure\SemesterResource\RelationManagers\CurriculumstructureRelationManager;
use App\Filament\Tables\Columns\CurriculumsList;
use App\Models\Curriculum\CurriculumStructure;
use App\Models\Curriculum\Module;
use App\Models\Curriculum\Semester;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SemesterResource extends Resource
{
    protected static ?string $model = Semester::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return (__('Website'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Semesters')
                            ->schema([
                                Forms\Components\Select::make('curriculumstructure')
                                    ->label('Curriculum Structure')
                                    ->relationship()
                                    ->native(false)
                                    ->options(CurriculumStructure::query()->pluck('curriculum_name', 'id'))
                                    ->required()
                                    ->disabled(fn (?Semester $record) => $record !== null),
                                Forms\Components\TextInput::make('semester_name')
                                    ->label('Semester Name')
                                    ->required()
                                    ->maxLength(64),
                                Forms\Components\TextInput::make('credit_total')
                                    ->label('Credit Total')
                                    ->numeric()
                                    ->required()
                                    ->maxLength(64),
                            ])
                            ->columnSpan(['lg' => fn (?Semester $record) => $record === null ? 3 : 2]),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Placeholder::make('created_at')
                                    ->label('Created at')
                                    ->content(fn (Semester $record): ?string => $record->created_at?->diffForHumans()),

                                Forms\Components\Placeholder::make('updated_at')
                                    ->label('Last modified at')
                                    ->content(fn (Semester $record): ?string => $record->updated_at?->diffForHumans()),
                            ])
                            ->columnSpan(['lg' => 1])
                            ->hidden(fn (?Semester $record) => $record === null)
                    ])
                    ->columnSpanFull()
                    ->columns(3),
                Forms\Components\Section::make(__('Modules'))
                    ->schema([
                        static::getModulesRepeater(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                CurriculumsList::make('curriculumstructure')
                    ->label('Curriculum Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('id')
                    ->label('Semester ID'),
                Tables\Columns\TextColumn::make('semester_name')
                    ->label('Semester Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('Total Mata Kuliah')
                    ->state(function (?Semester $record) {
                        $modulesCount = Module::where('semester_id', '=', $record->id)->count();
                        return $modulesCount;
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('curriculum_id')
                    ->label('Curriculum Structure')
                    ->preload()
                    ->native(false)
                    ->relationship('curriculumstructure', 'curriculum_name')
                    ->columnSpan('full'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSemesters::route('/'),
            'create' => Pages\CreateSemester::route('/create'),
            'edit' => Pages\EditSemester::route('/{record}/edit'),
        ];
    }

    public static function getModulesRepeater(): Repeater
    {
        return Repeater::make('modules')
            ->reorderableWithButtons()
            ->required()
            ->schema([
                Forms\Components\TextInput::make('module_code')
                    ->label(__('Module Code'))
                    ->columnSpan(1)
                    ->required(),

                Forms\Components\TextInput::make('module_name')
                    ->label(__('Module Name'))
                    ->columnSpan(1)
                    ->required(),

                Forms\Components\TextInput::make('credit_points')
                    ->label(__('Credit Points'))
                    ->numeric()
                    ->default(0)
                    ->maxValue(40)
                    ->columnSpan(1)
                    ->required(),

                Forms\Components\TextInput::make('module_handbook')
                    ->label(__('Module Handbook Link'))
                    ->columnSpan(1)
                    ->required(),
            ])
            ->hiddenLabel()
            ->columns([
                'md' => 2,
            ])
            ->grid(2)
            ->relationship();
    }
}
