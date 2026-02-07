<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('firebase.api_key', '');
        $this->migrator->add('firebase.auth_domain', '');
        $this->migrator->add('firebase.database_url', '');
        $this->migrator->add('firebase.project_id', '');
        $this->migrator->add('firebase.storage_bucket', '');
        $this->migrator->add('firebase.messaging_sender_id', '');
        $this->migrator->add('firebase.app_id', '');
    }
};
