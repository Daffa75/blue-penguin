<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\InternshipResource\Pages;
use App\Models\Internship;
use App\Models\InternshipStudents;
use App\Filament\Admin\Resources\InternshipResource\RelationManagers;
use App\Filament\Publication\Resources\PublicationResource\RelationManagers\StudentsRelationManager;
use App\Models\Lecturer;
use App\Models\Student;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
            $internshipStudent = InternshipStudents::where('student_id', $student->id)->first();
            return parent::getEloquentQuery()->where('id', $internshipStudent?->internship_id);
        }

        if ($panelId == 'lecturer') {
            $lecturer = Lecturer::where('user_id', auth()->user()->id)->first();
            return parent::getEloquentQuery()->where('lecturer_id', $lecturer->id);
        }

        return parent::getEloquentQuery();
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                            ->disabled(fn () => Filament::getCurrentPanel()->getId() === 'student' || Filament::getCurrentPanel()->getId() === 'lecturer')
                            ->native(false),
                        Forms\Components\TextInput::make('company_name')
                            ->label(__('Company'))
                            ->required()
                            ->disabled(fn () => Filament::getCurrentPanel()->getId() === 'student' || Filament::getCurrentPanel()->getId() === 'lecturer')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('location')
                            ->label(__('Location'))
                            ->required()
                            ->disabled(fn () => Filament::getCurrentPanel()->getId() === 'student' || Filament::getCurrentPanel()->getId() === 'lecturer')
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
                            ->extraAttributes(['inputmode' => 'numeric'])
                            ->regex('/^\d+$/')
                            ->placeholder('contoh: 081234567890')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('supervisor_email')
                            ->label(__('Supervisor Email'))
                            ->regex('/^\S+@\S+\.\S+$/')
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('start_date')
                            ->label(__('Start Date'))
                            ->disabled(fn () => Filament::getCurrentPanel()->getId() === 'student' || Filament::getCurrentPanel()->getId() === 'lecturer')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->label(__('End Date'))
                            ->disabled(fn () => Filament::getCurrentPanel()->getId() === 'student' || Filament::getCurrentPanel()->getId() === 'lecturer')
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
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label(__('Student'))
                    ->searchable()
                    ->state(function (Internship $record) {
                        $studentData = $record->students->map(function ($student) {
                            return "{$student->nim} - {$student->name}";
                        })
                            ->implode('<br>');
                        return $studentData;
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
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('Start Date'))
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('End Date'))
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
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->hidden(fn () => Filament::getCurrentPanel()->getId() === 'student'),
                ]),
            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\EditInternship::class,
            Pages\Logbook::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            StudentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'logbook' => Pages\Logbook::route('/{record}/logbook'),
            'index' => Pages\ListInternships::route('/'),
            'create' => Pages\CreateInternship::route('/create'),
            'edit' => Pages\EditInternship::route('/{record}/edit'),
        ];
    }
}
