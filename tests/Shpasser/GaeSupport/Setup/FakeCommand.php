<?php namespace Shpasser\GaeSupport\Setup;

use Illuminate\Console\Command;

class FakeCommand extends Command {

    protected $name = 'fake';
    protected $description = 'Fake command for testing purposes.';

    public function info($text)
    {
        return;
    }

    public function confirm($question, $default = true)
    {
        return true;
    }

}