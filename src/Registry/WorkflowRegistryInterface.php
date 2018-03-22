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

namespace Pandawa\Workflow\Registry;

use Symfony\Component\Workflow\WorkflowInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface WorkflowRegistryInterface
{
    public function get(object $subject, string $workflowName = null): WorkflowInterface;

    public function add(WorkflowInterface $workflow, string $supportStrategy): void;

    public function addFromArray(string $name, array $workflowConfig): void;
}
