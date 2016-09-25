<?php

class BasicArgsTest extends TestCase
{
    public function testOutput()
    {
        Artisan::call('basic:cmd', []);
        $resultAsText = Artisan::output();

        $this->assertTrue(
            strpos(
                $resultAsText,
                'Running the Basic Command'
            ) !== false
        );
    }
}