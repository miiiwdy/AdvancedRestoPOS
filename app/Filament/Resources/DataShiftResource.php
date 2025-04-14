<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataShiftResource\Pages;
use App\Filament\Resources\DataShiftResource\RelationManagers;
use App\Models\DataShift;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class DataShiftResource extends Resource
{
    protected static ?string $model = DataShift::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Data Shift';
    protected static ?string $navigationGroup = 'Master Data';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('no_shift')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('nama_shift')
                    ->label('Nama Shift')
                    ->required(),
                Forms\Components\Hidden::make('restos_id')
                    ->default(Auth::user()->restos_id),
                Forms\Components\Hidden::make('outlets_id')
                    ->default(Auth::user()->outlets_id),
                Forms\Components\TimePicker::make('shift_start')
                    ->seconds(false)
                    ->label('Jam Shift Dimulai')
                    ->required(),
                Forms\Components\TimePicker::make('shift_end')
                    ->seconds(false)
                    ->label('Jam Shift Berakhir')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_shift')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_shift')
                    ->searchable(),
                Tables\Columns\TextColumn::make('resto.nama_resto')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('outlet.nama_outlet')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('shift_start'),
                Tables\Columns\TextColumn::make('shift_end'),
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
            'index' => Pages\ListDataShifts::route('/'),
            'create' => Pages\CreateDataShift::route('/create'),
            'edit' => Pages\EditDataShift::route('/{record}/edit'),
        ];
    }
}
