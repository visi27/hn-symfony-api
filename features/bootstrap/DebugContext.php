<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Created by PhpStorm.
 * User: evis
 * Date: 5/17/17
 * Time: 3:59 PM.
 */
class DebugContext extends RawMinkContext implements Context
{
    /**
     * Saving a screenshot.
     *
     * @When I save a screenshot to :filename
     */
    public function iSaveAScreenshotIn($filename)
    {
        $this->saveScreenshot($filename, __DIR__.'/../..');
    }

    /**
     * Pauses the scenario until the user presses a key. Useful when debugging a scenario.
     *
     * @Then (I )break
     */
    public function iPutABreakpoint()
    {
        fwrite(STDOUT, "\033[s    \033[93m[Breakpoint] Press \033[1;93m[RETURN]\033[0;93m to continue...\033[0m");
        while (fgets(STDIN, 1024) === '') {
        }
        fwrite(STDOUT, "\033[u");
    }
}
