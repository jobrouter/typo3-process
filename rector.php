<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPromotedPropertyRector;
use Rector\Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddArrayParamDocTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddArrayReturnDocTypeRector;
use Rector\TypeDeclaration\Rector\FunctionLike\ReturnTypeDeclarationRector;

return static function (RectorConfig $config): void {
    $config->phpVersion(PhpVersion::PHP_81);

    $config->autoloadPaths([
        __DIR__ . '/.Build/vendor/autoload.php',
    ]);
    $config->paths([
        __DIR__ . '/Classes',
        __DIR__ . '/Configuration',
        __DIR__ . '/Tests',
    ]);
    $config->sets([
        LevelSetList::UP_TO_PHP_81,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::TYPE_DECLARATION,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
        PHPUnitSetList::PHPUNIT_EXCEPTION,
        PHPUnitSetList::PHPUNIT_SPECIFIC_METHOD,
        PHPUnitSetList::PHPUNIT_YIELD_DATA_PROVIDER
    ]);
    $config->skip([
        __DIR__ . '/Configuration/Services.php',
        AddArrayParamDocTypeRector::class => [
            __DIR__ . '/Classes/Domain/Model/Process.php',
            __DIR__ . '/Classes/Domain/Model/Transfer.php',
            __DIR__ . '/Tests/*',
        ],
        AddArrayReturnDocTypeRector::class => [
            __DIR__ . '/Tests/*',
        ],
        AddLiteralSeparatorToNumberRector::class,
        ReturnTypeDeclarationRector::class => [
            __DIR__ . '/Classes/Domain/Repository/ProcessRepository.php',
            __DIR__ . '/Classes/Domain/Repository/StepRepository.php',
            __DIR__ . '/Classes/Domain/Repository/TransferRepository.php',
        ],
    ]);
};
