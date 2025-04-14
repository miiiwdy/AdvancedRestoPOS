<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Kategori;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\ImageColumn;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';
    protected static ?string $activeNavigationIcon = 'heroicon-s-inbox-arrow-down';
    protected static ?string $navigationLabel = 'Data Product';
    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('foto_product')
                    ->required()
                    ->label('Foto Barang')
                    ->image(),
                Forms\Components\Hidden::make('restos_id')
                    ->default(Auth::user()->restos_id),
                Forms\Components\Hidden::make('outlets_id')
                    ->default(Auth::user()->outlets_id),
                Forms\Components\Select::make('kategoris_id')
                    ->label('Kategori')
                    ->required()
                    ->options(function () {
                        return Kategori::where('outlets_id', Auth::user()->outlets_id)
                            ->pluck('nama_kategori', 'id');
                    })
                    ->searchable(),
                Forms\Components\TextInput::make('kode_product')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama_product')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('deskripsi_product')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('harga_beli_product')
                    ->required()
                    ->suffix('IDR')
                    ->numeric(),
                    Forms\Components\TextInput::make('harga_product')
                    ->required()
                    ->suffix('IDR')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function() {
                $query = Product::query();
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
                    $query::all();
                }
            })
           ->poll('5s')
            ->columns([
                Tables\Columns\TextColumn::make('kode_product')
                    ->badge()
                ->color('info')
                ->searchable(),
                Tables\Columns\ImageColumn::make('foto_product'),
                Tables\Columns\TextColumn::make('resto.nama_resto')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('outlet.nama_outlet')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kategori.nama_kategori')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_product')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deskripsi_product')
                    ->searchable(),
                Tables\Columns\TextColumn::make('harga_beli_product')
                    ->numeric()
                    ->badge()
                    ->suffix(' IDR')
                    ->sortable(),
                    Tables\Columns\TextColumn::make('harga_product')
                    ->numeric()
                    ->label('Harga jual product')
                    ->suffix(' IDR')
                    ->badge()
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
        ];
    }
}
