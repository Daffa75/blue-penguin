<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LaboratoryResource\Pages;
use App\Filament\Admin\Resources\LaboratoryResource\RelationManagers;
use App\Filament\Admin\Resources\LaboratoryResource\RelationManagers\LecturersRelationManager;
use App\Filament\Admin\Resources\LaboratoryResource\RelationManagers\StudentsRelationManager;
use App\Models\Laboratory;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LaboratoryResource extends Resource
{
    protected static ?string $model = Laboratory::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationGroup(): ?string
    {
        return(__('Website'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::Make()
                    ->schema([
                        Forms\Components\TextInput::make('name_en')
                            ->label('Name')
                            ->helperText('In English')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name_id')
                            ->label('Nama')
                            ->helperText('Dalam Bahasa Indonesia')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\MarkdownEditor::make('description_en')
                            ->disableToolbarButtons(['attachFiles'])
                            ->label('English Description')
                            ->translateLabel()
                            ->required()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\MarkdownEditor::make('description_id')
                            ->disableToolbarButtons(['attachFiles'])
                            ->label('Indonesian Description')
                            ->translateLabel()
                            ->required()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('image')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('image')
                            ->image()
                            ->imageEditor()
                            ->imageResizeMode('contain')
                            ->imageCropAspectRatio('16:9')
                            ->collection('laboratory/images')
                            ->multiple()
                            ->hiddenLabel(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_en')
                    ->label('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name_id')
                    ->label('Nama')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\EditLaboratory::class,
            Pages\ManageLaboratoryLecturers::class,
            Pages\ManageLaboratoryStudents::class
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaboratories::route('/'),
            'create' => Pages\CreateLaboratory::route('/create'),
            'view' => Pages\EditLaboratory::route('/{record}'),
            'lecturers' => Pages\ManageLaboratoryLecturers::route('/{record}/lecturers'),
            'students' => Pages\ManageLaboratoryStudents::route('/{record}/students'),
        ];
    }
}
