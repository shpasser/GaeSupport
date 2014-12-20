<?php namespace Shpasser\GaeSupport\Setup;

use ReflectionClass;
use ZipArchive;

/**
 * Class ConfiguratorTest
 *
 * @package Shpasser\GaeSupport\Setup
 */
class ConfiguratorTest extends \PHPUnit_Framework_TestCase {

    protected $testee = null;
    protected $clazz = null;

    /**
     * Helper function.
     *
     * Calls a protected/private method of the Configurator class.
     *
     * @param string $methodName the name of method to call.
     * @param mixed $args the arguments to be passed to the method.
     * @return mixed the value returned by the method.
     */
    protected function call($methodName, $args)
    {
        if ($this->testee === null)
        {
            $fakeCommand = new FakeCommand();
            $this->testee = new Configurator($fakeCommand);
            $this->clazz = new ReflectionClass(get_class($this->testee));
        }

        $method = $this->clazz->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($this->testee, $args);
    }

    /**
     * Helper function.
     *
     * Deletes a directory tree.
     *
     * @param string $dir the root of the directory tree to delete.
     * @return bool 'true' if successful, 'false' otherwise.
     */
    protected static function delTree($dir)
    {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file)
        {
            (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
        }

        return rmdir($dir);
    }

    /**
     * Initializes the 'testee' object and prepares,
     * the 'playground', fake configuration files.
     */
    protected function setUp()
    {
        // Prepare the playground.
        $zip = new ZipArchive();
        $zip->open(__DIR__.'/resources.zip');
        $zip->extractTo(__DIR__.'/playground');
    }

    /**
     * Cleans up the 'playground' with all of its contents.
     */
    protected function tearDown()
    {
        self::delTree(__DIR__.'/playground');
    }


    public function testReplaceAppClass()
    {
        $start_php  = __DIR__.'/playground/bootstrap/start.php_input';
        $expected   = __DIR__.'/playground/bootstrap/start.php_expected_result';
        //$this->configurator->replaceAppClass($start_php);
        $this->call('replaceAppClass', [ $start_php ]);
        $this->assertFileEquals($start_php, $expected);
    }

    public function testReplaceLaravelServiceProviders()
    {
        $app_php    = __DIR__.'/playground/config/app.php_input';
        $expected   = __DIR__.'/playground/config/app.php_expected_result';
        //$this->configurator->replaceLaravelServiceProviders($app_php);
        $this->call('replaceLaravelServiceProviders', [ $app_php ]);
        $this->assertFileEquals($app_php, $expected);
    }

    public function testGenerateProductionConfig()
    {
        $productionDir = __DIR__.'/playground/config/production';
        //$this->configurator->generateProductionConfig($productionDir);
        $this->call('generateProductionConfig', [ $productionDir ]);

        $configFiles = [
            'cache.php',
            'mail.php',
            'queue.php',
            'session.php'
        ];

        foreach ($configFiles as $filename)
        {
            $testPath = "{$productionDir}/{$filename}";
            $this->assertFileExists($testPath);
        }

    }

    public function testCommentOutDefaultLogInit()
    {
        $global_php = __DIR__.'/playground/start/global.php_input';
        $expected   = __DIR__.'/playground/start/global.php_expected_result';
        //$this->configurator->commentOutDefaultLogInit($global_php);
        $this->call('commentOutDefaultLogInit', [ $global_php ]);

        $this->assertFileEquals($global_php, $expected);
    }

    public function testGenerateLogInit()
    {
        $startDir = __DIR__.'/playground/start';
        //$this->configurator->generateLogInit($startDir);
        $this->call('generateLogInit', [ $startDir ]);

        $startFiles = [
            'local.php',
            'production.php'
        ];

        foreach ($startFiles as $filename)
        {
            $testPath = "{$startDir}/{$filename}";
            $this->assertFileExists($testPath);
        }

    }

    public function testGenerateAppYaml()
    {
        $appId      = 'laravel-app-gae-id';
        $app_yaml   = __DIR__.'/playground/app.yaml';
        $publicPath = __DIR__.'/playground/public';
        $expected   = __DIR__.'/playground/app.yaml_expected_result';
        $this->call('generateAppYaml', [ $appId, $app_yaml, $publicPath ]);
        $this->assertFileEquals($app_yaml, $expected);
    }

    public function testGeneratePhpIni()
    {
        $appId    = 'laravel-app-gae-id';
        $bucketId = null;
        $php_ini  = __DIR__.'/playground/php.ini';
        $expected = __DIR__.'/playground/php.ini_expected_result';
        $this->call('generatePhpIni', [ $appId, $bucketId, $php_ini ]);
        $this->assertFileEquals($php_ini, $expected);
    }

}