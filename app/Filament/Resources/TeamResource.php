<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Filament\Resources\TeamResource\RelationManagers;
use App\Models\Team;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Section::make('Détails de la Team')
                ->description('Informations de base sur l\'équipe.')
                ->schema([
                    Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label(__('Logo'))
                            ->image()
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper()
                            ->visibility('public')
                            ->preserveFilenames()
                            ->downloadable()
                            ->columnSpan(1),
                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('Nom de la Team'))
                                    ->autofocus()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->placeholder(__('Name')),
                                Forms\Components\TextInput::make('code_departement')
                                    ->required()
                                    ->numeric()
                                    ->maxLength(3, 'Code du département trop long')
                                    ->minLength(2, 'Code du département trop court')
                                    ->label(__('Code du département'))
                                    ->placeholder(__('Code du département')),
                            ])->columnSpan(1),
                    ])
                ]),

            Fieldset::make('Paramètres de la Team')
                ->relationship('params')
                       ->schema([
                           Forms\Components\Toggle::make('send_coordinateur')
                               ->label(__('Ajouter le coordinateur dans le planning'))
                               ->inline()
                               ->columnSpan(1),
                           Forms\Components\Toggle::make('module_alert')
                               ->label(__('Activer le module d\'alerte'))
                               ->inline()
                               ->columnSpan(1),
                           Forms\Components\Toggle::make('update_planning')
                               ->label(__('Autoriser les membres à modifier le planning'))
                               ->inline()
                               ->columnSpan(1),
                           Forms\Components\Toggle::make('paid_leave')
                               ->label(__('Activer le module de congés payés'))
                               ->inline()
                               ->columnSpan(1),
                           Forms\Components\Toggle::make('share_link_planning')
                               ->label(__('Autoriser les membres à partager le planning'))
                               ->inline()
                               ->columnSpan(1),
                           Forms\Components\Toggle::make('share_link')
                               ->label(__('Activer le partage de lien'))
                               ->inline()
                               ->columnSpan(1),
                       ])->columns(3),
                Forms\Components\Section::make('Collaborateurs')
                    ->description('Gestion des collaborateurs')
                    ->schema([
                        Repeater::make('users')
                            ->label(__('Collaborateurs'))
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('Nom'))
                                    ->required()
                                    ->placeholder(__('Name')),
                                Forms\Components\TextInput::make('email')
                                     ->label(__('Email'))
                                     ->required()
                                     ->unique(ignoreRecord: true),
                                Forms\Components\DatePicker::make('birthday')
                                 ->label(__('Date d\'anniversaire'))
                                 ->unique(ignoreRecord: true),
                            ])
                            ->deletable(false)
                            ->addable(false)
                            ->columns(3)
                    ]),
                ]);

    }


    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('company_id', Auth::user()->company_id);
            })
            ->columns([
              ImageColumn::make('logo')
                    ->label(__('Logo'))
                    ->circular(),
                TextColumn::make('name')
                    ->label(__('Nom de la Team'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code_departement')
                    ->label(__('Code du département'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('coordinateur.name')
                    ->label(__('Coordinateur'))
                    ->searchable(),
                TextColumn::make('users')
                    ->label(__('Nombre de membres'))
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($record) => $record->users->count()),

            ])
            ->filters([
               //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}
