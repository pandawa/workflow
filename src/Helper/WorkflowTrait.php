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

namespace Pandawa\Workflow\Helper;

use Pandawa\Workflow\Registry\WorkflowRegistryInterface;
use Pandawa\Workflow\WorkflowContainer;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait WorkflowTrait
{
    /**
     * @param string|null $workflow
     *
     * @return WorkflowContainer
     */
    public function workflow(string $workflow = null): WorkflowContainer
    {
        return new WorkflowContainer($this->workflowRegistry()->get($this, $workflow), $this);
    }

    /**
     * @return WorkflowRegistryInterface
     */
    private function workflowRegistry(): WorkflowRegistryInterface
    {
        return app(WorkflowRegistryInterface::class);
    }
}
