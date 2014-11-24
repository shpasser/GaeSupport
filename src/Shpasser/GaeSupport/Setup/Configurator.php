<?php namespace Shpasser\GaeSupport\Setup;

use Illuminate\Console\Command;

/**
 * Created by PhpStorm.
 * Author: Ron Shpasser
 * Date: 11/18/14
 * Time: 5:10 PM
 */


class Configurator {

    protected $myCommand;

    public function __construct(Command $myCommand)
    {
        $this->myCommand = $myCommand;
    }

    /**
     * Configures a Laravel app to be deployed on GAE.
     *
     * @param $appId the GAE application ID.
     * @param $generateConfig if 'true' => generate GAE config files(app.yaml and php.ini).
     */
    public function configure($appId, $generateConfig)
    {
        $this->replaceAppClass();
        $this->generateProductionConfig();
        $this->replaceLaravelServiceProviders();

        if ($generateConfig)
        {
            $this->generateAppYaml($appId);
            $this->generatePhpIni($appId);
        }
    }

    /**
     * Replaces the Laravel application class
     * with the one compatible with GAE.
     */
    protected function replaceAppClass()
    {
        $start_php = app_path().'/../bootstrap/start.php';
        $this->createBackupFile($start_php);

        $contents = file_get_contents($start_php);

        preg_replace(
            'Illuminate\Foundation\Application',
            'Shpasser\GaeSupport\Foundation\Application',
            $contents);

        file_put_contents($start_php, $contents);

        $this->comment('Replaced the application class in "start.php".');
    }

    /**
     * Replaces the Laravel service providers
     * with GAE compatible ones.
     */
    protected function replaceLaravelServiceProviders()
    {
        $app_php = app_path().'/config/app.php';
        $this->createBackupFile($app_php);

        $contents = file_get_contents($app_php);

        $patterns = [
            'Illuminate\Mail\MailServiceProvider',
            'Illuminate\Queue\QueueServiceProvider'
        ];

		// Replacement to:
		//  - additionally support Google App Engine Queues,
        //  - additionally support Google App Engine Mail.
        $replacements = [
            'Shpasser\GaeSupport\Mail\GaeMailServiceProvider',
            'Shpasser\GaeSupport\Queue\QueueServiceProvider'
        ];

        preg_replace($patterns, $replacements, $contents);

        file_put_contents($app_php, $contents);

        $this->comment('Replaced the service providers in "app.php".');
    }

    /**
     * Generates the configuration files
     * for a Laravel GAE app.
     */
    protected function generateProductionConfig()
    {
        $productionDir = app_path().'/config/production';
        mkdir($productionDir);

        $configTemplatesPath = __DIR__.'/templates/config';
        $configFiles = [
            'cache.php',
            'mail.php',
            'queue.php',
            'session.php'
        ];

        foreach ($configFiles as $filename)
        {
            $srcPath = "{$configTemplatesPath}/{$filename}";
            $destPath = "{$productionDir}/{$filename}";
            copy($srcPath, $destPath);
        }

        $this->comment('Generated the production config. files.');
    }

    /**
     * Generates a "app.yaml" file for a GAE app with
     * @param $appId the GAE app id
     */
    protected function generateAppYaml($appId)
    {
        $filePath   = app_path().'/../app.yaml';
        $publicPath = app_path().'/../public';

        if (file_exists($filePath))
        {
            $overwrite = $this->myCommand->confirm(
                'Overwrite the existing "app.yaml" file?', false
            );

            if ( ! $overwrite)
            {
                return;
            }
        }

        $contents =
<<<EOT
application:    {$appId}
version:        1
runtime:        php
api_version:    1

handlers:
        - url: /favicon\.ico
          static_files: public/favicon.ico
          upload: public/favicon\.ico

EOT;

        $ending =
<<<EOT
        - url: /.*
          script: public/index.php

skip_files:
        - ^(.*/)?#.*#$
        - ^(.*/)?.*~$
        - ^(.*/)?.*\.py[co]$
        - ^(.*/)?.*/RCS/.*$
        - ^(.*/)?\.(?!env\.php).*$
        - ^(.*/)?node_modules.*$
        - ^(.*/)?_ide_helper\.php$
EOT;

        foreach (new \DirectoryIterator($publicPath) as $fileInfo)
        {
            if($fileInfo->isDot() || ! $fileInfo->isDir())
            {
                continue;
            }

            $dirName = $fileInfo->getFilename();
            $pathMapping =
<<<EOT
        - url: /{$dirName}
          static_dir: public/{$dirName}

EOT;
            $contents .= PHP_EOL.$pathMapping;
        }

        $contents .= PHP_EOL.$ending;

        file_put_contents($filePath, $contents);

        $this->comment('Generated the "app.yaml" file.');
    }

    /**
     * Generates a "php.ini" file for a GAE app with
     * @param $appId the GAE app id
     */
    protected function generatePhpIni($appId)
    {
        $filePath = app_path().'/../php.ini';

        if (file_exists($filePath))
        {
            $overwrite = $this->myCommand->confirm(
                'Overwrite the existing "php.ini" file?', false
            );

            if ( ! $overwrite)
            {
                return;
            }
        }

        $contents =
<<<EOT
; enable function that are disabled by default in the App Engine PHP runtime
google_app_engine.enable_functions = "php_sapi_name, php_uname, getmypid"
google_app_engine.allow_include_gs_buckets = "{$appId}.appspot.com"
allow_url_include = 1
EOT;
        file_put_contents($filePath, $contents);

        $this->comment('Generated the "php.ini" file.');
    }

    /**
     * Creates a backup copy of a desired file.
     *
     * @param $filePath the file path
     */
    protected function createBackupFile($filePath)
    {
        $sourcePath = $filePath;
        $backupPath = $filePath.'.bak';

        if (file_exists($backupPath))
        {
            $date = new \DateTime();
            $backupPath = "{$filePath}{$date->getTimestamp()}.bak";
        }

        copy($sourcePath, $backupPath);
    }

}
