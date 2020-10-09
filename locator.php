<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

use GrizzIt\Configuration\Component\Configuration\PackageLocator;
use Ulrack\DatabaseExtension\Common\UlrackDatabaseExtensionPackage;

PackageLocator::registerLocation(
    __DIR__,
    UlrackDatabaseExtensionPackage::PACKAGE_NAME
);
