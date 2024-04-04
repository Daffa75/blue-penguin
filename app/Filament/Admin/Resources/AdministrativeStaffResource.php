<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Clusters\Staffs;
use App\Filament\Admin\Resources\AdministrativeStaffResource\Pages;
use App\Filament\Admin\Resources\AdministrativeStaffResource\RelationManagers;
use App\Models\AdministrativeStaff;
use App\Models\StaffRole;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;

class AdministrativeStaffResource extends Resource
{
    protected static ?string $model = AdministrativeStaff::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $cluster = Staffs::class;
    protected static ?int $navigationSort = 2;

    public static function getPluralLabel(): ?string
    {
        return __('Administrative Staff');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->translateLabel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->translateLabel()
                            ->required()
                            ->maxLength(255),
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
                    ]),

                SpatieMediaLibraryFileUpload::make('image')
                    ->image()
                    ->translateLabel()
                    ->imageEditor()
                    ->imageResizeMode('contain')
                    ->imageCropAspectRatio('3:4')
                    ->collection('staff/images')
                    ->required()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    SpatieMediaLibraryImageColumn::make('image')->collection('staff/images')
                        ->grow(false)
                        ->circular(),
                    Tables\Columns\TextColumn::make('name')
                        ->numeric()
                        ->weight(FontWeight::Bold)
                        ->sortable(),

                    Stack::make([
                        Tables\Columns\TextColumn::make('role.role_idn')
                        ->sortable()
                            ->icon('heroicon-m-user'),
                        Tables\Columns\TextColumn::make('email')
                            ->icon('heroicon-m-envelope'),
                    ])
                ]),
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
