<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\InternshipResource\Pages;
use App\Filament\Admin\Resources\InternshipResource\RelationManagers;
use App\Filament\Admin\Resources\InternshipResource\RelationManagers\StudentRelationManager;
use App\Models\Internship;
use App\Models\Lecturer;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InternshipResource extends Resource
{
    protected static ?string $model = Internship::class;

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
                        ->options(
                            Lecturer::all()->pluck('name', 'id')
                        )
                        ->hidden(fn () => Filament::getCurrentPanel()->getId() === 'mahasiswa')
                        ->native(false),
                    Forms\Components\TextInput::make('company_name')
                        ->label(__('Company Name'))
                        ->required()
                        ->disabled(fn () => Filament::getCurrentPanel()->getId() === 'mahasiswa')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('location')
                        ->label(__('Location'))
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\MarkdownEditor::make('job_description')
                        ->label(__('Job Description'))
                        ->disabled(fn () => Filament::getCurrentPanel()->getId() === 'mahasiswa')
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
        ->columns([
            Tables\Columns\TextColumn::make('student.name')
                ->label(__('Student Name'))
                ->searchable()
                ->state(function (Internship $record) {
                    $studentData = $record->student->map(function ($student) {
                        return "{$student->nim} - {$student->name}";
                    })
                    ->implode('<br>');
                    return $studentData;
                })
                ->hidden(function () {
                    return Filament::getCurrentPanel()->getId() === 'mahasiswa';
                })
                ->html(),
            Tables\Columns\TextColumn::make('company_name')
                ->label(__('Company Name'))
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
                ->tooltip(function (Internship $record) {
                    return $record->lecturer->name;
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
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
}

    public static function getRelations(): array
    {
        return [
            StudentRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInternships::route('/'),
            'create' => Pages\CreateInternship::route('/create'),
            'edit' => Pages\EditInternship::route('/{record}/edit'),
        ];
    }
}
