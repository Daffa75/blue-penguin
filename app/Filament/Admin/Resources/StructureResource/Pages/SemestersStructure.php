<?php

namespace App\Filament\Admin\Resources\StructureResource\Pages;

use App\Actions\FilamentImport\Action\ImportAction;
use App\Filament\Admin\Resources\StructureResource;
use App\Filament\Tables\Columns\CurriculumsList;
use App\Models\Curriculum\CurriculumStructure;
use App\Models\Curriculum\Module;
use App\Models\Curriculum\Semester;
use Konnco\FilamentImport\Actions\ImportField;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SemestersStructure extends ManageRelatedRecords
{
    protected static string $resource = StructureResource::class;

    protected static string $relationship = 'semester';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Semester';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Semesters')
                            ->schema([
                                // Forms\Components\Select::make('curriculumstructure')
                                //     ->label('Curriculum Structure')
                                //     ->relationship()
                                //     ->native(false)
                                //     ->options(CurriculumStructure::query()->pluck('curriculum_name', 'id'))
                                //     ->required()
                                //     ->disabled(fn (?Semester $record) => $record !== null),
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

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Semester ID'),
                Tables\Columns\TextColumn::make('semester_name')
                    ->label('Semester Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('Total Mata Kuliah')
                    ->label(__('Total Mata Kuliah'))
                    ->state(function (?Semester $record) {
                        $modulesCount = Module::where('semester_id', '=', $record->id)->count();
                        return $modulesCount;
                    }),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->deferLoading()
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make('modules')
                ->fields([
                    ImportField::make('semester_name'),
                    ImportField::make('module_code'),
                    ImportField::make('module_name'),
                    ImportField::make('credit_points'),
                    ImportField::make('module_handbook'),
                ])
                ->handleRecordCreation(function ($data) {
                    $semesterName = $data['semester_name'];
                    $moduleCode = $data['module_code'];
                    $moduleName = $data['module_name'];
                    $creditPoints = $data['credit_points'];
                    $moduleHandbook = $data['module_handbook'];

                    // Find or create the semester
                    $semester = Semester::firstOrCreate([
                        'semester_name' => $semesterName,
                        'curriculum_id' => $this->getRecord()->id,
                    ]);

                    // Create the module with the proper semester_id
                    $module = new Module([
                        'semester_id' => $semester->id,
                        'module_code' => $moduleCode,
                        'module_name' => $moduleName,
                        'credit_points' => $creditPoints,
                        'module_handbook' => $moduleHandbook,
                        // Add other module attributes here
                    ]);

                    // Save the module
                    $module->save();

                    // Calculate the credit total for the semester
                    $creditTotal = $semester->modules()->sum('credit_points');

                    // Update the semester credit total
                    $semester->update(['credit_total' => $creditTotal]);

                    return $module;
                })
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
            ->relationship();
    }
}
