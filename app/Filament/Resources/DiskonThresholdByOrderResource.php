<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiskonThresholdByOrderResource\Pages;
use App\Filament\Resources\DiskonThresholdByOrderResource\RelationManagers;
use App\Models\DiskonThresholdByOrder;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class DiskonThresholdByOrderResource extends Resource
{
    protected static ?string $model = DiskonThresholdByOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                Forms\Components\TextInput::make('minimum_order_amount')
                    ->suffix('IDR')
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
                Tables\Columns\TextColumn::make('minimum_order_amount')
                    ->numeric()
                    ->suffix('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.nama_product')
                    ->badge()
                    ->label('Target Product')
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
            'index' => Pages\ListDiskonThresholdByOrders::route('/'),
            'create' => Pages\CreateDiskonThresholdByOrder::route('/create'),
            'edit' => Pages\EditDiskonThresholdByOrder::route('/{record}/edit'),
        ];
    }
}
