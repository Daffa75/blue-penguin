<?php

namespace App\Filament\Lecturer\Resources;

use App\Filament\Lecturer\Resources\FinalProjectS2Resource\Pages;
use App\Filament\Lecturer\Resources\FinalProjectResource\RelationManagers;
use App\Filament\Tables\Columns\AuthorsList;
use App\Filament\Tables\Columns\SupervisorsList;
use App\Models\FinalProject;
use App\Models\Student;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Filament\Infolists\Components;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;

class FinalProjectS2Resource extends Resource
{
    protected static ?string $model = FinalProject::class;
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $slug = 'final-projects-s2';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationGroup(): ?string
    {
        return (__('Content'));
    }

    public static function getPluralLabel(): ?string
    {
        return __('Final Project Master');
    }

    public static function getEloquentQuery(): Builder
    {
        $panelId = Filament::getCurrentPanel()->getId();

        $results = parent::getEloquentQuery()->whereDoesntHave('student', function ($query) {
            $query->where('nim', 'like', 'D121%')
                ->orWhere('nim', 'like', 'D421%');
        });

        if ($panelId == 'lecturer') {
            return $results->whereHas('lecturers', function (Builder $query) {
                return $query
                    ->where('nip', auth()->user()->lecturer?->nip)
                    ->whereIn('role', ['supervisor 1', 'supervisor 2']);
            });
        }
        return $results;
    }

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label(__('Student'))
                            ->native(false)
                            ->options(function () {
                                $query = Student::whereRaw("nim NOT LIKE 'D121%' AND nim NOT LIKE 'D421%'")
                                    ->select('name', 'nim', 'id')
                                    ->get();

                                $listStudentS2 = [];

                                foreach ($query as $student) {
                                    $listStudentS2[$student->id] = $student->name . ' - ' . $student->nim;
                                }

                                return $listStudentS2;
                            })
                            ->searchable(),
                        Forms\Components\TextInput::make('title')
                            ->translateLabel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('submitted_at')
                            ->translateLabel()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->native(false)
                            ->options([
                                // "Ongoing" => (__('Seminar Proposal')),
                                // "Finalizing" => (__('Seminar Hasil')),
                                // "Publication" => (__('Publications')),
                                // "Thesis" => (__('Thesis Exam')),
                                // "Done" => (__('Wisuda')),
                                "Ongoing" => "Seminar Proposal",
                                "Finalizing" => "Seminar Hasil",
                                "Publication" => "Publikasi",
                                "Thesis" => "Ujian Tesis",
                                "Done" => "Wisuda",
                            ])
                    ])
                    ->columns(2)
                    ->columnSpan(['lg' => fn (?FinalProject $record) => $record === null ? 3 : 2]),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->translateLabel()
                            ->content(fn (FinalProject $record): ?string => $record->created_at?->diffForHumans()),

                        Forms\Components\Placeholder::make('updated_at')
                            ->translateLabel()
                            ->content(fn (FinalProject $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?FinalProject $record) => $record === null),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordAction(null)
            // ->recordUrl(null)
            ->searchPlaceholder(__('Search Name, NIM, or Title '))
            ->defaultSort('submitted_at')
            ->columns([
                Tables\Columns\TextColumn::make('student.nim')
                    ->label(__('Student'))
                    ->wrap()
                    ->weight(FontWeight::Medium)
                    ->color('gray')
                    ->description(function (FinalProject $record): Htmlable {
                        $name = $record->student->name;
                        return new HtmlString("<span class='text-gray-600 dark:text-gray-500 text-sm font-semibold'>$name</span>");
                    }, position: 'above')
                    ->description(function (FinalProject $record): Htmlable {
                        return new HtmlString("<span class='text-gray-600 dark:text-gray-500 text-xs'>$record->title</span>");
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->whereHas('student', function ($query) use ($search): Builder {
                                return $query
                                    ->where('name', 'like', "%{$search}%")
                                    ->orWhere('nim', 'like', "%{$search}%");
                            })
                            ->orWhere('title', 'like', "%{$search}%");
                    })
                    ->sortable(),
                SupervisorsList::make('supervisor')
                    ->translateLabel()
                    ->state(function (FinalProject $record) {
                        $list = [];
                        foreach ($record->lecturers as $lecturer) {
                            if (in_array($lecturer->pivot->role, ['supervisor 1', 'supervisor 2'])) {
                                $list[] = $lecturer;
                            }
                        }
                        return $list;
                    }),
                AuthorsList::make('evaluator')
                    ->translateLabel()
                    ->state(function (FinalProject $record) {
                        $list = [];
                        foreach ($record->lecturers as $lecturer) {
                            if ($lecturer->pivot->role == 'evaluator') {
                                $list[] = $lecturer;
                            }
                        }
                        return $list;
                    }),
                Tables\Columns\TextColumn::make('submitted_at')
                    ->sortable()
                    ->translateLabel()
                    ->label(__("Proposed at"))
                    ->date('d F Y')
                    ->searchable(),
                Tables\Columns\TextColumn::make('student.phone_number')
                    ->label(__("Phone Number")),
                TextColumn::make('status')
                    ->getStateUsing(function (FinalProject $record): string {
                        switch ($record->status) {
                            case 'Ongoing':
                                return 'Seminar Proposal';
                            case 'Finalizing':
                                return 'Seminar Hasil';
                            case 'Publication':
                                return 'Publikasi';
                            case 'Thesis':
                                return 'Ujian Tesis';
                            case 'Done':
                                return 'Wisuda';
                        }
                    })
                    ->translateLabel()
                    ->badge()
                    ->colors([
                        'gray' => 'Seminar Proposal',
                        'info' => 'Seminar Hasil',
                        'violet' => 'Publikasi',
                        'warning' => 'Ujian Tesis',
                        'success' => 'Wisuda',
                    ])
                    ->formatStateUsing(function (string $state): string {
                        return __($state);
                    }),
                TextColumn::make('time_elapsed')
                    ->label('')
                    ->state(function (FinalProject $record) {
                        if ($record->status == 'Done') {
                            return '';
                        } else {
                            $start_date = Carbon::parse($record->submitted_at);
                            $elapsed_day = $start_date->diffInDays(now());
                            $daysTranslation = (__('Days'));
                            return "$elapsed_day $daysTranslation";
                        }
                    })
                    ->color(function (FinalProject $record) {
                        $start_date = Carbon::parse($record->submitted_at);
                        if ($start_date->diffInDays(now()) >= 540) {
                            return 'danger';
                        } elseif ($start_date->diffInDays(now()) >= 180) {
                            return 'warning';
                        } else return 'success';
                    })
                    ->badge(),
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
                Tables\Filters\SelectFilter::make('supervisorOne')
                    ->label(fn (): string => __('Supervisor') . ' 1')
                    ->translateLabel()
                    ->searchable()
                    ->hidden(auth()->user()->role == '3')
                    ->relationship('lecturers', 'name', function (Builder $query) {
                        return $query->where('role', 'supervisor 1');
                    }),
                Tables\Filters\SelectFilter::make('supervisorTwo')
                    ->label(fn (): string => __('Supervisor') . ' 2')
                    ->searchable()
                    ->hidden(auth()->user()->role == '3')
                    ->relationship('lecturers', 'name', function (Builder $query) {
                        return $query->where('role', 'supervisor 2');
                    }),
                Tables\Filters\SelectFilter::make('evaluator')
                    ->translateLabel()
                    ->searchable()
                    ->relationship('lecturers', 'name', function (Builder $query) {
                        return $query->where('role', 'evaluator');
                    }),
                Tables\Filters\SelectFilter::make('status')
                    ->native(false)
                    ->options([
                        "Ongoing" => "Seminar Proposal",
                        "Finalizing" => "Seminar Hasil",
                        "Publication" => "Publikasi",
                        "Thesis" => "Ujian Tesis",
                        "Done" => "Wisuda",
                    ])
                    ->default('Ongoing'),
                Tables\Filters\Filter::make('time')->form([
                    Forms\Components\Select::make('elapsed_time')
                        ->label(__('Elapsed Time'))
                        ->native(false)
                        ->options([
                            'okay' => '<span class="font-medium text-success-600 dark:text-success-400">' . __("Less than 180 days") . '</span>',
                            'warning' => '<span class="font-medium text-warning-600 dark:text-warning-400">' . __("Between 180 to 540 days") . '</span>',
                            'danger' => '<span class="font-medium text-danger-600 dark:text-danger-400">' . __("More than 540 days") . '</span>',
                        ])
                        ->allowHtml(),
                ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when(
                                $data['elapsed_time'] == 'okay',
                                fn (Builder $query, $date): Builder => $query->whereDate('submitted_at', '>=', now()->subDays(180))
                            )
                            ->when(
                                $data['elapsed_time'] == 'warning',
                                fn (Builder $query, $date): Builder => $query->whereBetween('submitted_at', [now()->subDays(540), now()->subDays(180)])
                            )
                            ->when(
                                $data['elapsed_time'] == 'danger',
                                fn (Builder $query, $date): Builder => $query->whereDate('submitted_at', '<', now()->subDays(540))
                            );
                    }),
            ])
            ->filtersFormColumns([
                'md' => 2,
                'lg' => 5
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('logbook')
                    ->label('Logbook')
                    // ->color('dark')
                    ->url(fn (FinalProject $record) => ("final-projects-s2/{$record->id}/logbook"))
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make()
                    ->schema([
                        Components\Grid::make()
                            ->schema([
                                Components\TextEntry::make('student.name')
                                    ->label(__('Name'))
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->weight(FontWeight::SemiBold),
                                Components\TextEntry::make('student.nim')
                                    ->label('NIM')
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->weight(FontWeight::SemiBold),
                            ]),
                        Components\TextEntry::make('title')
                            ->label(__('Title'))
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::SemiBold),
                    ])
            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewFinalProjectS2::class,
            Pages\EditFinalProjectS2::class,
            Pages\LogbookFinalProjectS2::class
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
            'index' => Pages\ListFinalProjectS2::route('/'),
            'create' => Pages\CreateFinalProjectS2::route('/create'),
            'view' => Pages\ViewFinalProjectS2::route('/{record}'),
            'edit' => Pages\EditFinalProjectS2::route('/{record}/edit'),
            'logbook' => Pages\LogbookFinalProjectS2::route('/{record}/logbook'),
        ];
    }
}
