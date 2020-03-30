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
    private $validLanguageVariables = [
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
        if (!\str_contains($event->getValue(), '{__language.')) {
            return;
        }

        $this->checkValidFieldTypes($event);

        $language = $this->getLanguage();

        if ($language === null) {
            return;
        }

        if (!\preg_match_all('/{__language\.(\w+)}/', $event->getValue(), $matches)) {
            return;
        }

        foreach ($matches[1] as $index => $match) {
            if (!\in_array($match, $this->validLanguageVariables)) {
                continue;
            }

            $methodToCall = 'get' . \ucfirst($match);
            $event->setValue(\str_replace($matches[0][$index], $language->$methodToCall(), $event->getValue()));
        }
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
