<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess\Domain\VariableResolver;

use Brotkrueml\JobRouterProcess\Enumeration\FieldTypeEnumeration;
use Brotkrueml\JobRouterProcess\Event\ResolveFinisherVariableEvent;
use Brotkrueml\JobRouterProcess\Exception\VariableResolverException;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

final class LanguageVariableResolver implements VariableResolverInterface
{
    private $allowedLanguageVariables = [
        'base',
        'direction',
        'flagIdentifier',
        'hreflang',
        'languageId',
        'locale',
        'navigationTitle',
        'title',
        'twoLetterIsoCode',
        'typo3Language',
    ];

    public function resolve(ResolveFinisherVariableEvent $event): void
    {
        if (\strpos($event->getValue(), '{__language.') === false) {
            return;
        }

        $this->checkValidFieldTypes($event);

        $language = $this->getLanguage();

        if ($language === null) {
            return;
        }

        \preg_match('/{__language\.(\w+)}/', $event->getValue(), $matches);

        if (!($matches[1] ?? false)) {
            return;
        }

        if (!\in_array($matches[1], $this->allowedLanguageVariables)) {
            return;
        }

        $methodToCall = 'get' . \ucfirst($matches[1]);
        $search = \sprintf('{__language.%s}', $matches[1]);
        $event->setValue(\str_replace($search, $language->$methodToCall(), $event->getValue()));
    }

    private function checkValidFieldTypes(ResolveFinisherVariableEvent $event): void
    {
        if (FieldTypeEnumeration::TEXT === $event->getFieldType()) {
            return;
        }

        throw new VariableResolverException(
            \sprintf(
                'The value "%s" contains a variable which can only be used in Text fields ("%d"), type "%d" used',
                $event->getValue(),
                FieldTypeEnumeration::TEXT,
                $event->getFieldType()
            ),
            1582654966
        );
    }

    private function getLanguage(): ?SiteLanguage
    {
        if (($GLOBALS['TYPO3_REQUEST'] ?? false) instanceof ServerRequestInterface) {
            return $GLOBALS['TYPO3_REQUEST']->getAttribute('language', null);
        }

        return null;
    }
}
