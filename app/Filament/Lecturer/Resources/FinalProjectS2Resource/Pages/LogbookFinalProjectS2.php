<?php

namespace App\Filament\Lecturer\Resources\FinalProjectS2Resource\Pages;

use App\Filament\Lecturer\Resources\FinalProjectS2Resource;
use App\Models\FinalProject;
use App\Models\Lecturer;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Components;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LogbookFinalProjectS2 extends ManageRelatedRecords
{
    protected static string $resource = FinalProjectS2Resource::class;

    protected static string $relationship = 'logbooks';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __("Final Project Logbook");
    }

    public function getTitle(): string
    {
        return (__('Final Project Logbook'));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->label(__('Date'))
                    ->default(now())
                    ->columnSpanFull()
                    ->disabled(auth()->user()->role !== '4')
                    ->required(),
                Forms\Components\MarkdownEditor::make('activity')
                    ->required()
                    ->disableAllToolbarButtons()
                    ->columnSpanFull()
                    ->disabled(auth()->user()->role !== '4')
                    ->required(),
                Repeater::make('feedbacks')
                    ->relationship('feedbacks')
                    ->schema([
                        Forms\Components\Hidden::make('lecturer_id')
                            ->default(fn () => Lecturer::where('user_id', auth()->user()->id)->first()->id)
                            ->live(),
                        Forms\Components\TextInput::make('lecturer_name')
                            ->label('Lecturer')
                            ->formatStateUsing(fn (Get $get) => Lecturer::where('id', $get('lecturer_id'))->first()->name)
                            ->disabled(true),
                        Forms\Components\MarkdownEditor::make('content')
                            ->disabled(fn (Get $get) => $get('lecturer_id') !== Lecturer::where('user_id', auth()->user()->id)->first()->id)
                            ->disableAllToolbarButtons()
                            ->required()
                    ])
                    ->deletable(false)
                    ->addActionLabel(__('Add Feedback'))
                    ->maxItems(2)
                    ->columnSpanFull()
                    ->hidden(auth()->user()->role !== '3')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('date')
            ->defaultSort('date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Student')
                    ->searchable()
                    ->state(function ($record) {
                        $student = $record->student;
                        return "{$student->nim} - {$student->name}";
                    })
                    ->width('200px')
                    ->hidden(auth()->user()->role === '4'),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->sortable()
                    ->width('150px')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('activity')
                    ->label('Activity')
                    ->wrap()
                    ->lineClamp(5),
                Tables\Columns\TextColumn::make('feedbacks')
                    ->label('Feedback')
                    ->formatStateUsing(function ($record) {
                        $feedbackList = $record->feedbacks->map(function ($feedback) {
                            return "<p class='line-clamp-5'><b>{$feedback->lecturer->name}</b>:<br> {$feedback->content}</p>";
                        })
                            ->implode('<br>');

                        return $feedbackList;
                    })
                    ->html()
                    ->wrap()
            ])
            ->filters([
                // 
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->hidden(auth()->user()->role !== '4'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(function () {
                        $panelId = Filament::getCurrentPanel()->getId();
                        return $panelId === 'lecturer' ? 'Add Feedback' : 'Edit';
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make()
                    ->schema([
                        Components\Grid::make()
                            ->schema([
                                Components\TextEntry::make('student.name')
                                    ->label(__('Student')),
                                Components\TextEntry::make('date')
                                    ->label(__('Date'))
                            ])
                    ]),
                Components\Section::make(__("Activity"))
                    ->schema([
                        Components\TextEntry::make('activity')
                            ->label('')
                            ->markdown()
                            ->columnSpanFull(),
                    ]),
                Components\Section::make(__("Feedback Supervisor 1"))
                    ->label(function ($record) {
                        $finalProject = FinalProject::where('id', $record->commentable_id)->first();
                        $supervisorTwo = $finalProject->lecturers->where('pivot.role', 'supervisor 2')->first();

                        return $supervisorTwo ? 'Feedback Supervisor 1' : 'Feedback Supervisor';
                    })
                    ->relationship('feedbacks')
                    ->schema([
                        Components\TextEntry::make('content')
                            ->label('')
                            ->markdown()
                            ->columnSpanFull()
                            ->html()
                            ->formatStateUsing(function ($record) {
                                $finalProject = FinalProject::where('id', $record->commentable_id)->first();
                                $supervisorOne = $finalProject->lecturers->where('pivot.role', 'supervisor 1')->first();
                                $feedback = $record->feedbacks->where('lecturer_id', $supervisorOne->id)->first();

                                return "<p><span class='font-bold text-base'>{$supervisorOne->name}</span> :<br> {$feedback->content}</p>";
                            }),
                    ])
                    ->hidden(function ($record) {
                        $finalProject = FinalProject::where('id', $record->commentable_id)->first();
                        $supervisorOne = $finalProject->lecturers->where('pivot.role', 'supervisor 1')->first();
                        $feedback = $record->feedbacks->where('lecturer_id', $supervisorOne->id)->first();

                        return !$feedback;
                    }),
                Components\Section::make(__("Feedback Supervisor 2"))
                    ->relationship('feedbacks')
                    ->schema([
                        Components\TextEntry::make('content')
                            ->label('')
                            ->markdown()
                            ->columnSpanFull()
                            ->formatStateUsing(function ($record) {
                                $finalProject = FinalProject::where('id', $record->commentable_id)->first();
                                $supervisorTwo = $finalProject->lecturers->where('pivot.role', 'supervisor 2')->first();
                                $feedback = $record->feedbacks->where('lecturer_id', $supervisorTwo->id)->first();

                                return "<p><span class='font-bold text-base'>{$supervisorTwo->name}</span> :<br> {$feedback->content}</p>";
                            }),
                    ])
                    ->hidden(function ($record) {
                        $finalProject = FinalProject::where('id', $record->commentable_id)->first();
                        $supervisorTwo = $finalProject->lecturers->where('pivot.role', 'supervisor 2')->first();
                        $feedback = $record->feedbacks->where('lecturer_id', $supervisorTwo->id)->first();

                        return !$feedback;
                    }),
            ])
            ->columns(3);
    }
}
