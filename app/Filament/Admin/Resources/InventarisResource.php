<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\InventarisResource\Pages;
use App\Filament\Admin\Resources\InventarisResource\RelationManagers;
use App\Models\Inventaris;
use App\Models\Lecturer;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InventarisResource extends Resource
{
    protected static ?string $model = Inventaris::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canViewAny(): bool
    {
        $panelId = Filament::getCurrentPanel()->getId();
        if ($panelId == 'student') {
            return false;
        }
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Textarea::make('name')
                            ->label('Nama Barang')
                            ->columnSpan(2)
                            ->required(),
                        Forms\Components\Select::make('lecturer_id')
                            ->label('Dosen')
                            ->searchable()
                            ->required()
                            ->options(function () {
                                return Lecturer::whereRaw('CHAR_LENGTH(nip) = 18')
                                    ->whereRaw('nip REGEXP \'^[0-9]+$\'')
                                    ->pluck('name', 'id');
                            })
                            ->columnSpan(1)
                            ->native(false),
                        Forms\Components\TextInput::make('registration_number')
                            ->label('Nomor Seri Departemen')
                            ->columnSpan(1)
                            ->required(),
                        Forms\Components\DatePicker::make('date')
                            ->label('Date')
                            ->columnSpan(1)
                            ->required(),
                        Forms\Components\TextInput::make('price')
                            ->label('Harga')
                            ->columnSpan(1)
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Jumlah Barang')
                            ->columnSpan(1)
                            ->numeric()
                            ->required(),
                        Forms\Components\Select::make('condition')
                            ->label('Condition')
                            ->options([
                                'Baik' => 'Baik',
                                'Rusak Ringan' => 'Rusak Ringan',
                                'Rusak Berat' => 'Rusak Berat',
                            ])
                            ->columnSpan(1)
                            ->required(),
                    ])
                    ->columnSpan(2),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (Inventaris $record): ?string => $record->created_at?->diffForHumans()),
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(fn (Inventaris $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(1)
                    ->hidden(fn (?Inventaris $record) => $record === null),
                Forms\Components\Section::make('Image')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('image')
                            ->image()
                            ->imageEditor()
                            ->imageResizeMode('contain')
                            ->collection('inventory/images')
                            ->multiple()
                            ->downloadable()
                            // ->imageCropAspectRatio('1:1')
                            ->hiddenLabel(),
                    ]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('registration_number')
                    ->label('Nomor Seri Departemen')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lecturer.name')
                    ->label('Dosen')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->searchable()
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->searchable()
                    
                    ->sortable(),
                Tables\Columns\TextColumn::make('condition')
                    ->label('Kondisi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Jumlah Barang')
                    ->searchable()
                    ->sortable(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventaris::route('/'),
            'create' => Pages\CreateInventaris::route('/create'),
            'edit' => Pages\EditInventaris::route('/{record}/edit'),
        ];
    }
}
