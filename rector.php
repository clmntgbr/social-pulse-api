<?php

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPreparedSets(
        codingStyle: true,
        naming: true,
        typeDeclarations: true,
    );