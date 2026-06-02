<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([__DIR__.'/src'])
    ->withSkip([__DIR__.'/config'])
    ->withImportNames(removeUnusedImports: true)
    ->withPhpSets(php84: true)
    ->withPreparedSets(
        codeQuality: true,
        codingStyle: true,
        privatization: true,
        instanceOf: true,
        earlyReturn: true
    )
    ->withTypeCoverageLevel(36)
    ->withDeadCodeLevel(40)
    ->withComposerBased(
        doctrine: true,
        symfony: true
    )
    ->withCache(__DIR__.'/var/cache/rector', FileCacheStorage::class);
