<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiskonThresholdByProductResource\Pages;
use App\Filament\Resources\DiskonThresholdByProductResource\RelationManagers;
use App\Models\DiskonThresholdByProduct;
use App\Models\KategoriDiskon;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class DiskonThresholdByProductResource extends Resource
{
    protected static ?string $model = DiskonThresholdByProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $activeNavigationIcon = 'heroicon-s-receipt-percent';
    protected static ?string $navigationLabel = 'Diskon Produk By Produk';
    protected static ?string $navigationGroup = 'Discount';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('restos_id')
                    ->default(Auth::user()->restos_id),
                Forms\Components\Hidden::make('outlets_id')
                    ->default(Auth::user()->outlets_id),
                Forms\Components\TextInput::make('nama_diskon')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('kategori_diskons_id')
                    ->label('Kategori')
                    ->required()
                    ->options(function () {
                        return KategoriDiskon::where('outlets_id', Auth::user()->outlets_id)
                            ->pluck('nama_kategori_diskon', 'id');
                    })
                    ->searchable(),
                Forms\Components\TextInput::make('deskripsi_diskon')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('stok_diskon')
                    ->required()
                    ->label('Masukkan stok diskon')
                    ->numeric(),
                Forms\Components\Select::make('product_id')
                    ->label('Product')
                    ->required()
                    ->options(function () {
                        return Product::where('outlets_id', Auth::user()->outlets_id)
                            ->pluck('nama_product', 'id');
                    })
                    ->searchable(),
                Forms\Components\TextInput::make('minimum_items_count')
                    ->required()
                    ->label('Jumlah minimal produk untuk mengaktifkan diskon')
                    ->numeric(),
                Forms\Components\Select::make('target_product_id')
                    ->label('Target Product')
                    ->required()
                    ->options(function () {
                        return Product::where('outlets_id', Auth::user()->outlets_id)
                            ->pluck('nama_product', 'id');
                    })
                    ->searchable(),
                Forms\Components\TextInput::make('target_product_quantity')
                    ->required()
                    ->label('Jumlah produk yang didapat')
                    ->numeric(),
                Forms\Components\Toggle::make('is_active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function() {
                $query = DiskonThresholdByProduct::query();
                if (Auth::user()->hasRole(1)) {
                    $query->where([
                        ['restos_id', '=', Auth::user()->restos_id],
                    ]);
                }
                else if (Auth::user()->hasRole(2)) {
                    $query->where([
                        ['restos_id', '=', Auth::user()->restos_id],
                        ['outlets_id', '=', Auth::user()->outlets_id],
                    ]);
                }
                else if (Auth::user()->hasRole(3)) {
                    $query->get();
                }
                return $query;
            })
           ->poll('5s')
            ->columns([
                Tables\Columns\TextColumn::make('kategoriDiskon.nama_kategori_diskon')
                    ->badge()
                    ->color('danger')
                    ->searchable(),
                Tables\Columns\TextColumn::make('resto.nama_resto')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('outlet.nama_outlet')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_diskon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deskripsi_diskon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product.nama_product')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('minimum_items_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('targetProduct.nama_product')
                        ->badge()
                        ->color('info')
                        ->searchable(),
                Tables\Columns\TextColumn::make('target_product_quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stok_diskon')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state)
                    ->color(fn ($state) => $state > 50 ? 'success' : ($state > 10 ? 'warning' : 'danger'))
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_active'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiskonThresholdByProducts::route('/'),
            'create' => Pages\CreateDiskonThresholdByProduct::route('/create'),
        ];
    }
}
