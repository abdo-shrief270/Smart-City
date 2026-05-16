<?php

namespace App\Filament\Pages;

use App\Settings\FirebaseSettings;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ManageFirebaseSettings extends Page implements HasForms
{
    use HasPageShield;
    use InteractsWithForms;

    protected string $view = 'filament.pages.manage-firebase-settings';

    public ?array $data = [];

    // -----------------------------------------------------------------
    // Navigation
    // -----------------------------------------------------------------
    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public static function getNavigationLabel(): string
    {
        return 'Firebase Settings';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    // -----------------------------------------------------------------
    // Lifecycle
    // -----------------------------------------------------------------
    public function mount(): void
    {
        $settings = app(FirebaseSettings::class);

        $this->form->fill([
            'api_key'             => $settings->api_key             ?? null,
            'auth_domain'         => $settings->auth_domain         ?? null,
            'database_url'        => $settings->database_url        ?? null,
            'project_id'          => $settings->project_id          ?? null,
            'storage_bucket'      => $settings->storage_bucket      ?? null,
            'messaging_sender_id' => $settings->messaging_sender_id ?? null,
            'app_id'              => $settings->app_id              ?? null,
        ]);
    }

    // -----------------------------------------------------------------
    // Schema
    // -----------------------------------------------------------------
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Firebase Configuration')
                    ->description('Manage your Firebase project credentials needed for Smart City modules.')
                    ->schema([
                        TextInput::make('api_key')
                            ->label('API Key')
                            ->required(),
                        TextInput::make('auth_domain')
                            ->label('Auth Domain')
                            ->required(),
                        TextInput::make('database_url')
                            ->label('Database URL')
                            ->required()
                            ->url(),
                        TextInput::make('project_id')
                            ->label('Project ID')
                            ->required(),
                        TextInput::make('storage_bucket')
                            ->label('Storage Bucket')
                            ->required(),
                        TextInput::make('messaging_sender_id')
                            ->label('Messaging Sender ID')
                            ->required(),
                        TextInput::make('app_id')
                            ->label('App ID')
                            ->required(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    // -----------------------------------------------------------------
    // Actions
    // -----------------------------------------------------------------
    public function save(): void
    {
        try {
            $data     = $this->form->getState();
            $settings = app(FirebaseSettings::class);

            $settings->api_key             = $data['api_key'];
            $settings->auth_domain         = $data['auth_domain'];
            $settings->database_url        = $data['database_url'];
            $settings->project_id          = $data['project_id'];
            $settings->storage_bucket      = $data['storage_bucket'];
            $settings->messaging_sender_id = $data['messaging_sender_id'];
            $settings->app_id              = $data['app_id'];

            $settings->save();

            Notification::make()
                ->title('Settings saved successfully')
                ->success()
                ->send();
        } catch (\Exception $exception) {
            Notification::make()
                ->title('Error saving settings')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }
}
