<?php

class YourCommandTest extends TestCase
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

        // Check for Warning Text
//        $this->assertTrue(
//            strpos(
//                $resultAsText,
//                'This is a warning'
//            ) !== false
//        );

        // Check for Error Text
//        $this->assertTrue(
//            strpos(
//                $resultAsText,
//                'This is an error'
//            ) !== false
//        );
    }
}