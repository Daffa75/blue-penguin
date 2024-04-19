<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\InventarisResource\Pages;
use App\Models\Inventaris;
use App\Models\Lecturer;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InventarisResource extends Resource
{
    protected static ?string $model = Inventaris::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return (__('Content'));
    }

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
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('lecturer_id')
                            ->label('Dosen')
                            ->required()
                            ->searchable()
                            ->options(function () {
                                return Lecturer::whereRaw('CHAR_LENGTH(nip) = 18')
                                    ->whereRaw('nip REGEXP \'^[0-9]+$\'')
                                    ->pluck('name', 'id');
                            })
                            ->native(false)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('registration_number')
                            ->label('Nomor Seri Departemen')
                            ->numeric()
                            ->required(),
                        Forms\Components\Select::make('condition')
                            ->label('Kondisi')
                            ->required()
                            ->options([
                                'Baik' => 'Baik',
                                'Rusak Ringan' => 'Rusak Ringan',
                                'Rusak Berat' => 'Rusak Berat',
                            ])
                            ->native(false),
                        Forms\Components\TextInput::make('price')
                            ->label('Harga')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Jumlah Barang')
                            ->numeric()
                            ->required(),
                        Forms\Components\Select::make('is_found')
                            ->label('Barang Ditemukan')
                            ->required()
                            ->options([
                                true => 'Ya',
                                false => 'Tidak',
                            ])
                            ->default(true)
                            ->native(false),
                        Forms\Components\Select::make('is_used')
                            ->label('Barang Digunakan')
                            ->required()
                            ->options([
                                true => 'Ya',
                                false => 'Tidak',
                            ])
                            ->native(false),
                        Forms\Components\DatePicker::make('date')
                            ->label('Date')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpan(['lg' => fn (?Inventaris $record) => $record === null ? 3 : 2]),

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

                Forms\Components\Section::make('Image User')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('image_distribution')
                            ->image()
                            ->imageEditor()
                            ->imageResizeMode('contain')
                            ->collection('inventory/images/distribution')
                            ->downloadable()
                            ->imageCropAspectRatio('1:1')
                            ->hiddenLabel(),
                    ]),

                Forms\Components\Section::make('Image Physique')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('image_physique')
                            ->image()
                            ->imageEditor()
                            ->imageResizeMode('contain')
                            ->collection('inventory/images/physique')
                            ->downloadable()
                            ->imageCropAspectRatio('1:1')
                            ->hiddenLabel(),
                    ]),

                Forms\Components\Section::make('Image Number')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('image_number')
                            ->image()
                            ->imageEditor()
                            ->imageResizeMode('contain')
                            ->collection('inventory/images/number')
                            ->downloadable()
                            ->imageCropAspectRatio('1:1')
                            ->hiddenLabel(),
                    ]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image_distribution')->collection('inventory/images/distribution')
                    ->label(__('Image Distribution')),
                SpatieMediaLibraryImageColumn::make('image_physique')->collection('inventory/images/physique')
                    ->label(__('Image Physique')),
                SpatieMediaLibraryImageColumn::make('image_number')->collection('inventory/images/number')
                    ->label(__('Image Number')),
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
                    ->money('IDR', locale: 'id')
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
                Tables\Filters\SelectFilter::make('date')
                    ->label('Year')
                    ->native(false)
                    ->options(
                        Inventaris::query()
                            ->selectRaw('YEAR(date) as value, YEAR(date) as label')
                            ->distinct()
                            ->orderByDesc('value')
                            ->pluck('label', 'value')
                            ->toArray()
                    )
                    ->query(function (Builder $query, array $data): Builder {

                        if ($data['value'] !== null) {
                            $query->whereYear('date', $data['value']);
                        }

                        return $query;
                    }),
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
