<?php

declare(strict_types=1);

namespace Dreibein\YoutubeBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Dreibein\YoutubeBundle\Message\Message;

/**
 * Class ParseBackendTemplateListener.
 *
 * @Hook("parseBackendTemplate")
 */
class ParseBackendTemplateListener
{
    /**
     * @var Message
     */
    private $message;

    /**
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * If there are some error message, show them in the main template.
     *
     * @param string $buffer
     * @param string $template
     *
     * @return string
     */
    public function __invoke(string $buffer, string $template): string
    {
        if ('be_main' !== $template) {
            return $buffer;
        }

        $delimiter = '<h1 id="main_headline">';

        // Add the error message to the main page
        [$head, $body] = explode($delimiter, $buffer);

        $systemMessages = '';
        $messages = $this->message->getErrorMessages();

        // loop through the messages and create the html for it
        foreach ($messages as $message) {
            $systemMessages .= sprintf('<p class="tl_error">%s</p>', $message);
        }

        // rebuild the html and return it
        return $head . $systemMessages . $delimiter . $body;
    }
}
