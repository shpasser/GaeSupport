<?php namespace Shpasser\GaeSupport\Foundation;

use Illuminate\Foundation\Application as IlluminateApplication;
use Illuminate\Http\Request;

class Application extends IlluminateApplication {

    /**
     * AppIdentityService class instantiation is done using the class
     * name string so we can first check if the class exists and only then
     * instantiate it.
     */
    const GAE_ID_SERVICE = 'google\appengine\api\app_identity\AppIdentityService';

    /**
     * The GAE app ID.
     *
     * @var string
     */
    protected $appId;

    /**
     * 'true' if running on GAE.
     * @var boolean
     */
    protected $runningOnGae;

    /**
     * Create a new GAE supported application instance.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->detectGae();
    }

    /**
     * Detect if the application is running on GAE.
     *
     * If we run on GAE then 'realpath()' function replacement
     * 'gae_realpath()' is declared, so it won't fail with GAE
     * bucket paths.
     *
     * In order for 'gae_realpath()' function to be called the code has
     * to be patched to use 'gae_realpath()' instead of 'realpath()'
     * using the command 'php artisan gae:deploy you@gmail.com'
     * from the terminal.
     */
    protected function detectGae()
    {
        if (! class_exists(self::GAE_ID_SERVICE)) {
            $this->runningOnGae = false;
            $this->appId = null;
            return;
        }

        $AppIdentityService = self::GAE_ID_SERVICE;
        $this->appId = $AppIdentityService::getApplicationId();
        $this->runningOnGae = isset($this->appId) && !preg_match('/dev~/', $this->appId);

        if ($this->runningOnGae) {
            require_once(__DIR__ . '/gae_realpath.php');
        }
    }

    /**
     * Returns 'true' if running on GAE.
     *
     * @return bool
     */
    public function isRunningOnGae()
    {
        return $this->runningOnGae;
    }

    /**
     * Returns the GAE app ID.
     *
     * @return string
     */
    public function getGaeAppId()
    {
        return $this->appId;
    }

    /**
     * Bind the installation paths to the application.
     *
     * @param  array  $paths
     * @return void
     */
    public function bindInstallPaths(array $paths)
    {
        if ($this->runningOnGae)
        {
            $this->instance('path', gae_realpath($paths['app']));

            // Here we will bind the install paths into the container as strings that can be
            // accessed from any point in the system. Each path key is prefixed with path
            // so that they have the consistent naming convention inside the container.
            foreach (array_except($paths, array('app', 'storage')) as $key => $value)
            {
                $this->instance("path.{$key}", gae_realpath($value));
            }

            $this->bindStoragePath();
        }
        else
        {
            parent::bindInstallPaths($paths);
        }
    }

    /**
     * Binds the GAE storage path if running on GAE.
     * The binding of the new storage path overrides the
     * original one which appears in the 'paths.php' file.
     *
     * The GAE storage directory is created if it does
     * not exist already.
     *
     * @return void
     */
    protected function bindStoragePath()
    {
        if ($this->runningOnGae) {
            $buckets = ini_get('google_app_engine.allow_include_gs_buckets');
            // Get the first bucket in the list.
            $bucket = current(explode(', ', $buckets));

            if ($bucket) {
                $storagePath = "gs://{$bucket}/storage";

                if (!file_exists($storagePath)) {
                    mkdir($storagePath);
                }

                $this->instance("path.storage", $storagePath);
            }
        }
    }

}