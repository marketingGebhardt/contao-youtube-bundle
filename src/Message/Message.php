<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein-Youtube-Bundle.
 *
 * (c) Werbeagentur Dreibein GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace Dreibein\YoutubeBundle\Message;

/*
 * This file is part of the Dreibein-Youtube-Bundle.
 *
 * (c) Werbeagentur Dreibein GmbH
 *
 * @license LGPL-3.0-or-later
 */

use Contao\PageModel;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class Message.
 */
class Message
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Get some translated error messages.
     *
     * @return array
     */
    public function getErrorMessages(): array
    {
        $messages = [];

        // get all root pages
        $rootPages = PageModel::findByPid(0);

        if (null === $rootPages) {
            return $messages;
        }

        // check if the data-protection-page is set on the page
        foreach ($rootPages as $rootPage) {
            if (!$rootPage->dataProtectionPage) {
                $transData = [
                    'page_name' => $rootPage->title,
                    'page_lang' => $rootPage->language,
                ];

                // create the error message
                $messages[] = $this->translator->trans('system.data_protection_page', $transData, 'dreibein_youtube');
            }
        }

        return $messages;
    }
}
