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
use Brotkrueml\JobRouterProcess\Language\TranslationService;

final class LocalisedLabelVariableResolver implements VariableResolverInterface
{
    /** @var TranslationService */
    private $translationService;

    /**
     * @param TranslationService|null $translationService For testing purposes
     */
    public function __construct(TranslationService $translationService = null)
    {
        $this->translationService = $translationService ?? new TranslationService();
    }

    public function resolve(ResolveFinisherVariableEvent $event): void
    {
        $value = $event->getValue();

        if (!\str_contains($value, '{__LLL:')) {
            return;
        }

        $this->checkValidFieldTypes($event);

        if (!\preg_match_all('/{__(LLL:.+?)}/', $value, $matches)) {
            return;
        }

        foreach ($matches[1] as $index => $match) {
            $translation = $this->translationService->translate($match);

            if ($translation) {
                $value = \str_replace($matches[0][$index], $translation, $value);
            }
        }

        $event->setValue($value);
    }

    private function checkValidFieldTypes(ResolveFinisherVariableEvent $event): void
    {
        if (FieldTypeEnumeration::TEXT === $event->getFieldType()) {
            return;
        }

        throw new VariableResolverException(
            \sprintf(
                'The value "%s" contains a localised label which can only be used in Text fields ("%d"), type "%d" used',
                $event->getValue(),
                FieldTypeEnumeration::TEXT,
                $event->getFieldType()
            ),
            1582907006
        );
    }
}
