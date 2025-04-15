<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanOrderProductResource\Pages;
use App\Filament\Resources\LaporanOrderProductResource\RelationManagers;
use App\Models\LaporanOrderProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Enums\FiltersLayout;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class LaporanOrderProductResource extends Resource
{
    protected static ?string $model = LaporanOrderProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Laporan Order Product';
    protected static ?string $navigationGroup = 'Report';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function() {
                $query = LaporanOrderProduct::query();
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
            ->columns([
                Tables\Columns\TextColumn::make('resto.nama_resto')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('outlet.nama_outlet')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_kasir')
                    ->searchable(),
                Tables\Columns\TextColumn::make('id_product')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_product')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode_product')
                    ->searchable(),
                Tables\Columns\TextColumn::make('foto_product')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kategori_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga_beli_per_item')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_harga_beli')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga_jual_per_item')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_harga_jual_before')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_harga_jual_after')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_pajak')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_after_rounding')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_type')
                    ->searchable(),
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
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required(),
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->required(),
                    ])
                    ->columns(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            isset($data['start_date']) && isset($data['end_date']),
                            function (Builder $query) use ($data): Builder {
                                return $query->whereBetween('created_at', [$data['start_date'], $data['end_date']]);
                            }
                        );
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
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
            'index' => Pages\ListLaporanOrderProducts::route('/'),
            'create' => Pages\CreateLaporanOrderProduct::route('/create'),
            'edit' => Pages\EditLaporanOrderProduct::route('/{record}/edit'),
        ];
    }
}
