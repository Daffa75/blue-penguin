<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TeachingStaffResource\Pages;
use App\Filament\Admin\Resources\TeachingStaffResource\RelationManagers;
use App\Models\Lecturer;
use App\Models\StaffRole;
use App\Models\TeachingStaff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeachingStaffResource extends Resource
{
    protected static ?string $model = TeachingStaff::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('lecturer_id')
                            ->label('Lecturer')
                            ->unique()
                            ->translateLabel()
                            ->options(Lecturer::all()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('role_id')
                            ->label('Position')
                            ->helperText('dalam bahasa indonesia')
                            ->translateLabel()
                            ->relationship(name: 'role', titleAttribute: 'role_idn')
                            ->searchable()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('role_en')
                                    ->label('Position in English')
                                    ->translateLabel()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('role_idn')
                                    ->label('Position in Indonesian')
                                    ->translateLabel()
                                    ->required()
                                    ->maxLength(255)
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('expertise_en')
                            ->label('Expertise in English')
                            ->translateLabel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('expertise_idn')
                            ->label('Expertise in Indonesian')
                            ->translateLabel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('link')
                            ->label('Handbook Link')
                            ->translateLabel()
                            ->required()
                            ->maxLength(255),
                    ]),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('lecturer.image_url')
                    ->label('')
                    ->circular(),
                Tables\Columns\TextColumn::make('lecturer.name')
                    ->translateLabel()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role.role_en')
                    ->label('Position')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expertise')
                    ->searchable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeachingStaff::route('/'),
            'view' => Pages\ViewTeachingStaff::route('/{record}'),
            'edit' => Pages\EditTeachingStaff::route('/{record}/edit'),
        ];
    }
}
