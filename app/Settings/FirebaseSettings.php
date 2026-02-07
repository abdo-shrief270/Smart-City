<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class FirebaseSettings extends Settings
{
    public string $api_key;
    public string $auth_domain;
    public string $database_url;
    public string $project_id;
    public string $storage_bucket;
    public string $messaging_sender_id;
    public string $app_id;

    public static function group(): string
    {
        return 'firebase';
    }
}
