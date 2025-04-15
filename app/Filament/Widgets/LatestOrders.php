<?php

namespace App\Filament\Widgets;

use App\Models\LaporanOrder;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class LatestOrders extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
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
        ]);
    }
}
