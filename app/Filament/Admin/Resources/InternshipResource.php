<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\InternshipResource\Pages;
use App\Models\Internship;
use App\Models\InternshipStudents;
use App\Filament\Admin\Resources\InternshipResource\RelationManagers;
use App\Models\Lecturer;
use App\Models\Student;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components;

class InternshipResource extends Resource
{
    protected static ?string $model = Internship::class;

    public static function getNavigationGroup(): ?string
    {
        return (__('Content'));
    }

    public static function getPluralLabel(): ?string
    {
        return __('Internship');
    }

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getEloquentQuery(): Builder
    {
        $panelId = Filament::getCurrentPanel()->getId();

        if ($panelId == 'student') {
            $student = Student::where('user_id', auth()->user()->id)->first();
            return parent::getEloquentQuery()->whereHas('students', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            });
        }

        if ($panelId == 'lecturer') {
            $lecturer = Lecturer::where('user_id', auth()->user()->id)->first();
            return parent::getEloquentQuery()->where('lecturer_id', $lecturer->id);
        }

        return parent::getEloquentQuery();
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    private static function getFilters(): array
    {
        if (auth()->user()->role != '3' || auth()->user()->role != '4') {
            return [
                Tables\Filters\SelectFilter::make('lecturer_id')
                    ->label(__('Lecturer'))
                    ->native(false)
                    ->options(function () {
                        return Lecturer::whereRaw('CHAR_LENGTH(nip) = 18')
                            ->whereRaw('nip REGEXP \'^[0-9]+$\'')
                            ->pluck('name', 'id');
                    })
                    ->searchable(),
            ];
        } else return [];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('lecturer_id')
                            ->label(__('Lecturer'))
                            ->searchable()
                            ->required()
                            ->columnSpanFull()
                            ->options(function () {
                                return Lecturer::whereRaw('CHAR_LENGTH(nip) = 18')
                                    ->whereRaw('nip REGEXP \'^[0-9]+$\'')
                                    ->pluck('name', 'id');
                            })
                            ->native(false),
                        Forms\Components\TextInput::make('company_name')
                            ->label(__('Company'))
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('location')
                            ->label(__('Location'))
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\MarkdownEditor::make('job_description')
                            ->label(__('Job Description'))
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('supervisor_name')
                            ->label(__('Supervisor Name'))
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('supervisor_phone')
                            ->label(__('Supervisor Phone'))
                            ->numeric()
                            ->placeholder('contoh: 081234567890')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('supervisor_email')
                            ->label(__('Supervisor Email'))
                            ->regex('/^\S+@\S+\.\S+$/')
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('start_date')
                            ->label(__('Start Date'))
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->label(__('End Date'))
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpan(['lg' => fn (?Internship $record) => $record === null ? 3 : 2]),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (Internship $record): ?string => $record->created_at?->diffForHumans()),
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(fn (Internship $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(1)
                    ->hidden(fn (?Internship $record) => $record === null),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(auth()->user()->role != '4')
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label(__('Student'))
                    ->state(function (Internship $record) {
                        $studentData = $record->students->map(function ($student) {
                            return "{$student->nim} - {$student->name}";
                        })
                            ->implode('<br>');
                        return $studentData;
                    })
                    ->searchable(auth()->user()->role != '4', query: function (Builder $query, string $search): Builder {
                        return $query
                            ->whereHas('students', function ($query) use ($search): Builder {
                                return $query
                                    ->where('name', 'like', "%{$search}%")
                                    ->orWhere('nim', 'like', "%{$search}%");
                            });
                            // ->orWhere('title', 'like', "%{$search}%");
                    })
                    ->hidden(function () {
                        return Filament::getCurrentPanel()->getId() === 'student';
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('company_name')
                    ->label(__('Company'))
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('location')
                    ->label(__('Location'))
                    ->wrap(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->state(fn (Internship $record) => now() >= $record->end_date ? __('Done') : __('Ongoing'))
                    ->color(fn (Internship $record) => now() >= $record->end_date ? 'success' : 'gray')
                    ->badge(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('Start Date'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('End Date'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date('d M Y'),
                Tables\Columns\ImageColumn::make('lecturer.image_url')
                    ->label(__('Lecturer'))
                    ->tooltip(function (Internship $record) {
                        return $record->lecturer->name;
                    })
                    ->hidden(function () {
                        return Filament::getCurrentPanel()->getId() === 'lecturer';
                    })
                    ->alignCenter()
                    ->circular(),
            ])
            // ->filters(self::getFilters())
            ->filters([
                // get status filter but i don't have status column in internship table
                // Tables\Filters\SelectFilter::make('status')
                //     ->label('Status')
                //     ->options([
                //         'ongoing' => 'Ongoing',
                //         'done' => 'Done',
                //     ]),
            ])
            // ->filtersFormColumns([
            //     'md', 'lg' => 3,
            // ])
            // ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('logbook')
                    ->label('Logbook')
                    // ->color('dark')
                    ->url(fn (Internship $record) => ("internships/{$record->id}/logbook"))
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->hidden(fn () => Filament::getCurrentPanel()->getId() === 'student'),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make()
                    ->schema([
                        Components\Section::make(__('Internship Details'))
                            ->schema([
                                Components\Grid::make()
                                    ->schema([
                                        Components\TextEntry::make('supervisor_name')
                                            ->label(__('Field Supervisor')),
                                        Components\TextEntry::make('lecturer.name')
                                            ->label(__('Supervisor')),
                                        Components\TextEntry::make('company_name')
                                            ->label(__('Company')),
                                        Components\TextEntry::make('location')
                                            ->label(__('Location')),
                                        Components\TextEntry::make('start_date')
                                            ->label(__('Start Date')),
                                        Components\TextEntry::make('end_date')
                                            ->label(__('End Date')),
                                    ])
                            ]),

                        Components\Section::make(__('Job Description'))
                            ->schema([
                                Components\TextEntry::make('job_description')
                                    ->markdown()
                                    ->label(''),
                            ]),

                    ])
            ])
            ->columns(3);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewInternship::class,
            Pages\EditInternship::class,
            Pages\LogbookInternship::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StudentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'logbook' => Pages\LogbookInternship::route('/{record}/logbook'),
            'index' => Pages\ListInternships::route('/'),
            'create' => Pages\CreateInternship::route('/create'),
            'view' => Pages\ViewInternship::route('/{record}'),
            'edit' => Pages\EditInternship::route('/{record}/edit'),
        ];
    }
}
