<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AdministrativeStaffResource\Pages;
use App\Filament\Admin\Resources\AdministrativeStaffResource\RelationManagers;
use App\Models\AdministrativeStaff;
use App\Models\StaffRole;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdministrativeStaffResource extends Resource
{
    protected static ?string $model = AdministrativeStaff::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                ->label('User')
                ->unique()
                ->translateLabel()
                ->options(User::all()->pluck('name', 'id'))
                ->searchable()
                ->required(),
            Forms\Components\Select::make('role_id')
                ->label('Position')
                ->translateLabel()
                ->options(StaffRole::all()->pluck('role_en', 'id'))
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
                ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role.id')
                    ->numeric()
                    ->sortable(),
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAdministrativeStaff::route('/'),
        ];
    }
}
