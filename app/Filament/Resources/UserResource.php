<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string $title = 'Utilisateurs';

    protected static ?string $label = 'Collaborateurs';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Informations Générales'))
                    ->schema([
                        Forms\Components\Select::make('team_id')
                            ->label(__('Équipe'))
                            ->placeholder(__('Sélectionnez une team'))
                            ->required()
                            ->relationship('team', 'name', function ($query) {
                                return $query->where('company_id', Auth::user()->company_id);
                            }),
                        Forms\Components\TextInput::make('name')
                            ->label(__('Nom'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label(__('Email'))
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('role')
                            ->label(__('Rôle'))
                            ->placeholder(__('Sélectionnez un rôle'))
                            ->required()
                            ->options(function () {
                                $baseOptions = [
                                    'Responsable' => 'Responsable',
                                    'Coordinateur' => 'Coordinateur',
                                    'Conseiller' => 'Conseiller',
                                ];

                                if (Auth::user()->isAdmin()) {
                                    $baseOptions['Administateur'] = 'Administateur';
                                }

                                return $baseOptions;
                            }),
                    ]),

                Forms\Components\Section::make(__('Détails Personnels'))
                    ->schema([
                        Forms\Components\DatePicker::make('birthday')
                            ->label(__('Date de Naissance')),

                        Forms\Components\TextInput::make('phone')
                            ->label(__('Téléphone'))
                            ->tel()
                            ->maxLength(255),
                    ]),

                Forms\Components\Section::make(__('Profil et Sécurité'))
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->label(__('Avatar'))
                            ->image()
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper()
                            ->visibility('public')
                            ->preserveFilenames()
                            ->downloadable(),

                        Forms\Components\Toggle::make('account_active')
                            ->label(__('Compte Actif'))
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label(__('Avatar'))
                    ->circular()
                    ->defaultImageUrl(url('images/avatar_default.png')),

                Tables\Columns\TextColumn::make('team.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('birthday')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\IconColumn::make('account_active')
                    ->boolean(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
