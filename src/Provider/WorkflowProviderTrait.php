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

namespace Pandawa\Workflow\Provider;

use Pandawa\Component\Loader\ChainLoader;
use Pandawa\Workflow\Registry\WorkflowRegistryInterface;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait WorkflowProviderTrait
{
    /**
     * @var string
     */
    protected $workflowPath = 'Resources/workflows';

    protected function bootWorkflowProvider(): void
    {
        if (null === $this->workflowRegistry()) {
            return;
        }

        $basePath = $this->getCurrentPath() . '/' . trim($this->workflowPath, '/');
        $loader = ChainLoader::create();

        if (is_dir($basePath)) {
            /** @var SplFileInfo $file */
            foreach (Finder::create()->in($basePath) as $file) {
                foreach ($loader->load((string) $file) as $name => $workflowConfig) {
                    $this->workflowRegistry()->addFromArray($name, $workflowConfig);
                }
            }
        }
    }

    /**
     * @return null|WorkflowRegistryInterface
     */
    private function workflowRegistry(): ?WorkflowRegistryInterface
    {
        if (app()->has(WorkflowRegistryInterface::class)) {
            return app(WorkflowRegistryInterface::class);
        }
        
        return null;
    }
}
