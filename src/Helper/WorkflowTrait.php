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
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait WorkflowTrait
{
    /**
     * @param string|null $workflow
     *
     * @return WorkflowInterface
     */
    public function workflow(string $workflow = null): WorkflowInterface
    {
        return $this->workflowRegistry()->get($this, $workflow);
    }

    /**
     * @return WorkflowRegistryInterface
     */
    private function workflowRegistry(): WorkflowRegistryInterface
    {
        return app(WorkflowRegistryInterface::class);
    }
}
