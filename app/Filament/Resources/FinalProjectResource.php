<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FinalProjectResource\Pages;
use App\Filament\Resources\FinalProjectResource\RelationManagers;
use App\Filament\Tables\Columns\AuthorsList;
use App\Filament\Tables\Columns\SupervisorsList;
use App\Models\FinalProject;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class FinalProjectResource extends Resource
{
    protected static ?string $model = FinalProject::class;

    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $navigationGroup = 'Content';

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            '' => $record->student->name,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'student.name'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['student']);
    }

    protected static ?string $navigationIcon = 'phosphor-article-bold';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->native(false)
                            ->relationship('student')
                            ->getOptionLabelFromRecordUsing(fn (Student $record) => "{$record->name} - {$record->nim}")
                            ->searchable(['name', 'nim']),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('submitted_at')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                "Ongoing" => "Ongoing",
                                "Finalizing" => "Finalizing",
                                "Done" => 'Done',
                            ])
                    ])
                    ->columns(2)
                    ->columnSpan(['lg' => fn (?FinalProject $record) => $record === null ? 3 : 2]),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (FinalProject $record): ?string => $record->created_at?->diffForHumans()),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(fn (FinalProject $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?FinalProject $record) => $record === null),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('submitted_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->searchable()
                    ->wrap()
                    ->description(fn (FinalProject $record): string => $record->student->nim),
                Tables\Columns\TextColumn::make('title')
                    ->wrap()
                    ->searchable(),
                SupervisorsList::make('supervisor')
                    ->state(function (FinalProject $record) {
                        $list = [];
                        foreach ($record->lecturers as $lecturer)
                        {
                            if (in_array($lecturer->pivot->role, ['supervisor 1', 'supervisor 2'])) {
                                $list[] = $lecturer;
                            }
                        }
                        return $list;
                    }),
                AuthorsList::make('evaluator')
                    ->state(function (FinalProject $record) {
                        $list = [];
                        foreach ($record->lecturers as $lecturer)
                        {
                            if ($lecturer->pivot->role == 'evaluator') {
                                $list[] = $lecturer;
                            }
                        }
                        return $list;
                    }),
                Tables\Columns\TextColumn::make('submitted_at')
                    ->date('d F Y')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'gray' => 'Ongoing',
                        'info' => 'Finalizing',
                        'success' => 'Done'
                    ]),
                TextColumn::make('time_elapsed')
                    ->label('')
                    ->state(function (FinalProject $record) {
                        if ($record->status == 'Done'){
                            return '';
                        } else {
                            $start_date = Carbon::parse($record->submitted_at);
                            $elapsed_day = $start_date->diffInDays(now());
                            return "$elapsed_day" . " Hari";
                        }
                    })
                    ->color(function (FinalProject $record) {
                        $start_date = Carbon::parse($record->submitted_at);
                        if ($start_date->diffInDays(now()) >= 180) {
                            return 'danger';
                        } elseif ($start_date->diffInDays(now()) >= 90)
                        {
                            return 'warning';
                        } else return 'success';
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->deferLoading()
            ->filters([
                Tables\Filters\Filter::make('time')->form([
                    Forms\Components\Select::make('elapsed_time')
                        ->native(false)
                        ->options([
                            'okay' => '<span class="font-medium text-success-600 dark:text-success-400">Less than 90 days</span>' ,
                            'warning' => '<span class="font-medium text-warning-600 dark:text-warning-400">90 to 180 days</span>',
                            'danger' => '<span class="font-medium text-danger-600 dark:text-danger-400">More than 180 days</span>',
                        ])
                        ->allowHtml(),
                ])
                ->query(function (Builder $query, array $data) {
                    return $query
                        ->when(
                            $data['elapsed_time'] == 'okay',
                            fn(Builder $query, $date): Builder => $query->whereDate('submitted_at', '>=', now()->subDays(90))
                        )
                        ->when(
                            $data['elapsed_time'] == 'warning',
                            fn(Builder $query, $date): Builder => $query->whereBetween('submitted_at', [now()->subDays(180), now()->subDays(90)])
                        )
                        ->when(
                            $data['elapsed_time'] == 'danger',
                            fn(Builder $query, $date): Builder => $query->whereDate('submitted_at', '<', now()->subDays(180))
                        );
                }),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
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
            RelationManagers\LecturersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFinalProjects::route('/'),
            'create' => Pages\CreateFinalProject::route('/create'),
            'edit' => Pages\EditFinalProject::route('/{record}/edit'),
        ];
    }
}
