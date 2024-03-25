<?php

namespace App\Filament\Admin\Resources\InternshipResource\Pages;

use App\Filament\Admin\Resources\InternshipResource;
use App\Models\InternshipLogbook;
use App\Models\InternshipStudents;
use App\Models\Student;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class Logbook extends ManageRelatedRecords
{
    protected static string $resource = InternshipResource::class;

    protected static string $relationship = 'logbooks';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        $panelId = Filament::getCurrentPanel()->getId();
        if ($panelId == 'student') {
            $student = Student::where('user_id', auth()->user()->id)->first();
            dd($student);
            return parent::getEloquentQuery()->where('student_id', $student->id);
        }
    }

    public static function getNavigationLabel(): string
    {
        return 'Logbook';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('internship_id')
                    ->default(function () {
                        $student = Student::where('user_id', auth()->user()->id)->first();
                        $kerjaPraktekStudent = InternshipStudents::where('student_id', $student->id)->first();
                        return $kerjaPraktekStudent->internship_id;
                    }),
                Forms\Components\Hidden::make('student_id')
                    ->default(function () {
                        $student = Student::where('user_id', auth()->user()->id)->first();
                        return $student->id;
                    }),
                Forms\Components\DatePicker::make('date')
                    ->label(__('Date'))
                    ->default(now())
                    ->columnSpanFull()
                    ->hidden(fn () => Filament::getCurrentPanel()->getId() === 'lecturer')
                    ->required(),
                Forms\Components\MarkdownEditor::make('activity')
                    ->label(__('Activity'))
                    ->disableAllToolbarButtons()
                    ->columnSpanFull()
                    ->disabled(fn () => Filament::getCurrentPanel()->getId() === 'lecturer')
                    ->required(),
                Forms\Components\MarkdownEditor::make('result')
                    ->label(__('Result'))
                    ->columnSpanFull()
                    ->disabled(fn () => Filament::getCurrentPanel()->getId() === 'lecturer')
                    ->required(),
                Forms\Components\MarkdownEditor::make('feedback')
                    ->label(__('Feedback'))
                    ->hidden(fn () => Filament::getCurrentPanel()->getId() === 'student')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('activity')
            ->columns([
                TextColumn::make('student.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable()
                    ->state(function (InternshipLogbook $record) {
                        $student = $record->student;
                        return "{$student->nim} - {$student->name}";
                    }),
                TextColumn::make('date')
                    ->label('Date')
                    ->sortable()
                    ->date('d M Y'),
                TextColumn::make('activity')
                    ->label('Activity')
                    ->wrap(),
                TextColumn::make('result')
                    ->label('Result')
                    ->hidden(fn () => Filament::getCurrentPanel()->getId() === 'student')
                    ->wrap(),
                TextColumn::make('feedback')
                    ->hidden(fn () => Filament::getCurrentPanel()->getId() === 'lecturer')
                    ->wrap()
                    ->label('Feedback'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('student_id')
                    ->label('Student')
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Logbook')
                    ->hidden(fn () => Filament::getCurrentPanel()->getId() === 'lecturer' || Filament::getCurrentPanel()->getId() === 'admin'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(function () {
                        $panelId = Filament::getCurrentPanel()->getId();
                        return $panelId === 'lecturer' ? 'Add Feedback' : 'Edit';
                    }),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn () => Filament::getCurrentPanel()->getId() === 'lecturer' || Filament::getCurrentPanel()->getId() === 'admin'),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\ForceDeleteBulkAction::make(),
                // ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}
