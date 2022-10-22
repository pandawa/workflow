<?php

declare(strict_types=1);

namespace Pandawa\Workflow\Plugin;

use Pandawa\Component\Foundation\Bundle\Plugin;
use Pandawa\Contracts\Config\LoaderInterface;
use Pandawa\Workflow\Registry\WorkflowRegistryInterface;
use Pandawa\Workflow\WorkflowBundle;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportWorkflowPlugin extends Plugin
{
    public function __construct(protected readonly string $workflowPath = 'Resources/workflows')
    {
    }

    public function boot(): void
    {
        $config = $this->bundle->getService('config');

        foreach ($config->get($this->getConfigKey(), []) as $name => $workflow) {
            $this->registry()->addFromArray($name, $workflow);
        }
    }

    public function configure(): void
    {
        if ($this->bundle->getApp()->configurationIsCached()) {
            return;
        }

        $loadedWorkflows = [];

        foreach ($this->getWorkflows() as $workflows) {
            $loadedWorkflows = [...$loadedWorkflows, ...$workflows];
        }

        $config = $this->bundle->getService('config');

        $config->set($this->getConfigKey(), [
            ...$config->get($this->getConfigKey(), []),
            ...$loadedWorkflows,
        ]);
    }

    protected function getWorkflows(): iterable
    {
        foreach (Finder::create()->in($this->bundle->getPath($this->workflowPath))->files() as $file) {
            yield $this->loader()->load($file->getRealPath());
        }
    }

    protected function loader(): LoaderInterface
    {
        return $this->bundle->getService(LoaderInterface::class);
    }

    protected function registry(): WorkflowRegistryInterface
    {
        return $this->bundle->getService(WorkflowRegistryInterface::class);
    }

    protected function getConfigKey(): string
    {
        return WorkflowBundle::WORKFLOW_CONFIG_KEY . '.' . $this->bundle->getName();
    }
}
