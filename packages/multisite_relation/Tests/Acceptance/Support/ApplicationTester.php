<?php

declare(strict_types=1);

/*
 * This file is part of TYPO3 CMS-based extension "hreflang_multisite" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace B13\HreflangMultisite\Tests\Acceptance\Support;

use AbSoftlab\MultisiteRelation\Tests\Acceptance\Support\_generated\ApplicationTesterActions;
use TYPO3\TestingFramework\Core\Acceptance\Step\FrameSteps;

/**
 * Default backend admin or editor actor in the backend
*/
class ApplicationTester extends \Codeception\Actor
{
    use ApplicationTesterActions;
    use FrameSteps;
}
