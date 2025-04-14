<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManagementShiftResource\Pages;
use App\Filament\Resources\ManagementShiftResource\RelationManagers;
use App\Models\DataShift;
use App\Models\ManagementShift;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ManagementShiftResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Management Shift';
    protected static ?string $navigationGroup = 'F&B Core';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('call_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\SelectColumn::make('shift')
                    ->options(DataShift::where([
                        ['restos_id', '=', Auth::user()->restos_id],
                        ['outlets_id', '=', Auth::user()->outlets_id],
                    ])->pluck('nama_shift', 'no_shift'))
                    ->selectablePlaceholder(false),
                Tables\Columns\TextColumn::make('resto.nama_resto')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('outlet.nama_outlet')
                    ->badge()
                    ->color('success')
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
                //
            ])
            ->actions([
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
            'index' => Pages\ListManagementShifts::route('/'),
            'create' => Pages\CreateManagementShift::route('/create'),
        ];
    }
}
