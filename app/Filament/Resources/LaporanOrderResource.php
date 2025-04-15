<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanOrderResource\Pages;
use App\Filament\Resources\LaporanOrderResource\RelationManagers;
use App\Models\LaporanOrder;
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

class LaporanOrderResource extends Resource
{
    protected static ?string $model = LaporanOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Laporan Order';
    protected static ?string $navigationGroup = 'Report';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function() {
                $query = LaporanOrder::query();
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
                Tables\Columns\TextColumn::make('order_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tables')
                    ->searchable(),
                Tables\Columns\TextColumn::make('guest')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_harga_beli')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_harga_jual')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_harga_before_rounding')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_harga_after_all')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rounding')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_paid')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('change')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('keuntungan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_pajak')
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
                Tables\Columns\TextColumn::make('payment')
                    ->searchable(),
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

    public static function canCreate(): bool {
        return false;
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
            'index' => Pages\ListLaporanOrders::route('/'),
            'create' => Pages\CreateLaporanOrder::route('/create'),
            'edit' => Pages\EditLaporanOrder::route('/{record}/edit'),
        ];
    }
}
