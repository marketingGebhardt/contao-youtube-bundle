<?php

declare(strict_types=1);

namespace Dreibein\YoutubeBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Dreibein\YoutubeBundle\Message\Message;

/**
 * Class GetSystemMessagesListener.
 *
 * @Hook("getSystemMessages")
 */
class GetSystemMessagesListener
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
     * If there are some error messages, add them to the system messages.
     *
     * @return string
     */
    public function __invoke(): string
    {
        $systemMessage = '';
        $messages = $this->message->getErrorMessages();

        // add all messages to a big string to return it back to contao
        foreach ($messages as $message) {
            $systemMessage .= sprintf('<p class="tl_error">%s</p>', $message);
        }

        return $systemMessage;
    }
}
