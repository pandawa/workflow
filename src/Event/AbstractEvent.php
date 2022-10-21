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
    public function __construct(public readonly Event $originalEvent)
    {
    }
}
