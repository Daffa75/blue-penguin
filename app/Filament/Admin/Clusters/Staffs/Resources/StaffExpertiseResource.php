<?php

namespace App\Filament\Admin\Clusters\Staffs\Resources;

use App\Filament\Admin\Clusters\Staffs;
use App\Filament\Admin\Clusters\Staffs\Resources\StaffExpertiseResource\Pages;
use App\Filament\Admin\Clusters\Staffs\Resources\StaffExpertiseResource\RelationManagers;
use App\Models\StaffExpertise;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StaffExpertiseResource extends Resource
{
    protected static ?string $model = StaffExpertise::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Staffs::class;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('expertise_idn')
                ->label('Expertise in Indonesian')
                ->translateLabel()
                ->required()
                ->maxLength(255)
                ->unique('staff_expertises', 'expertise_idn', ignoreRecord: true),
            Forms\Components\TextInput::make('expertise_en')
                ->label('Expertise in English')
                ->translateLabel()
                ->required()
                ->maxLength(255)
                ->unique('staff_expertises', 'expertise_en', ignoreRecord: true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('expertise_idn')
                ->searchable(),
                Tables\Columns\TextColumn::make('expertise_en')
                    ->searchable(),
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
            'index' => Pages\ManageStaffExpertises::route('/'),
        ];
    }
}
