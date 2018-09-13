<?php
/*
 * Example of use:
 *
 * A flood protection script should be included at the beginning of a PHP file that is executed on user's request.
 * Commonly it's an index.php script that executes a wholewebsite engine functionality.
 * If a website runs on several PHP scripts, each should contain a flood protecton script included,
 * but not those that are also included.
 *
 * E.g. if index.php runs on user request and contains config.php included,
 * config.php should not contain flood protection script included.
 * So only a context entry point should be secured.
 *
 */

require_once 'fp.php';

include 'index.internal.php';
