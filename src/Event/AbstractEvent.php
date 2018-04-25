<?php
/**
 * This file is part of the Pandawa Workflow package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Pandawa\Workflow\Event;

use Symfony\Component\Workflow\Event\Event;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class AbstractEvent
{
    /**
     * @var Event
     */
    protected $originalEvent;

    /**
     * Constructor.
     *
     * @param Event $originalEvent
     */
    public function __construct(Event $originalEvent)
    {
        $this->originalEvent = $originalEvent;
    }

    /**
     * @return Event
     */
    public function getOriginalEvent(): Event
    {
        return $this->originalEvent;
    }
}
