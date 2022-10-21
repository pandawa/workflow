<?php

declare(strict_types=1);

namespace Pandawa\Workflow;

use Pandawa\Bundle\DependencyInjectionBundle\Plugin\ImportServicesPlugin;
use Pandawa\Bundle\FoundationBundle\Plugin\ImportConfigurationPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Foundation\HasPluginInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class WorkflowBundle extends Bundle implements HasPluginInterface
{
    const WORKFLOW_CONFIG_KEY = 'pandawa.workflows';

    public function plugins(): array
    {
        return [
            new ImportConfigurationPlugin(),
            new ImportServicesPlugin(),
        ];
    }
}
