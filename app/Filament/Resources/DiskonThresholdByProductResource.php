<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiskonThresholdByProductResource\Pages;
use App\Filament\Resources\DiskonThresholdByProductResource\RelationManagers;
use App\Models\DiskonThresholdByProduct;
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
                    ->maxLength(255),
                Forms\Components\Select::make('product_id')
                    ->label('Product')
                    ->required()
                    ->options(function () {
                        return Product::where('outlets_id', Auth::user()->outlets_id)
                            ->pluck('nama_product', 'id');
                    })
                    ->searchable(),
                Forms\Components\TextInput::make('minimum_items_count')
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
                    ->label('Jumlah produk yang didapat')
                    ->numeric(),
                Forms\Components\Toggle::make('is_active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiskonThresholdByProducts::route('/'),
            'create' => Pages\CreateDiskonThresholdByProduct::route('/create'),
        ];
    }
}
