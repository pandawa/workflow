<?php
declare(strict_types=1);

namespace Pandawa\Workflow\Helper;

use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;
use Symfony\Component\Workflow\Metadata\MetadataStoreInterface;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\TransitionBlockerList;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class WorkflowContainer
{
    /**
     * @var Workflow
     */
    private $workflow;

    /**
     * @var object
     */
    private $subject;

    /**
     * Constructor.
     *
     * @param Workflow $workflow
     * @param object   $subject
     */
    public function __construct(Workflow $workflow, object $subject)
    {
        $this->workflow = $workflow;
        $this->subject = $subject;
    }

    /**
     * @return Marking
     */
    public function getMarking(): Marking
    {
        return $this->workflow->getMarking($this->subject);
    }

    /**
     * @param string $transitionName
     *
     * @return bool
     */
    public function can(string $transitionName): bool
    {
        return $this->workflow->can($this->subject, $transitionName);
    }

    /**
     * @param string $transitionName
     *
     * @return Marking
     */
    public function apply(string $transitionName): Marking
    {
        return $this->workflow->apply($this->subject, $transitionName);
    }

    /**
     * @param string $transitionName
     *
     * @return TransitionBlockerList
     */
    public function buildTransitionBlockerList(string $transitionName): TransitionBlockerList
    {
        return $this->workflow->buildTransitionBlockerList($this->subject, $transitionName);
    }

    /**
     * @return array|Transition[]
     */
    public function getEnabledTransitions(): array
    {
        return $this->workflow->getEnabledTransitions($this->subject);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->workflow->getName();
    }

    /**
     * @return Definition
     */
    public function getDefinition(): Definition
    {
        return $this->workflow->getDefinition();
    }

    /**
     * @return MarkingStoreInterface
     */
    public function getMarkingStore(): MarkingStoreInterface
    {
        return $this->workflow->getMarkingStore();
    }

    /**
     * @return MetadataStoreInterface
     */
    public function getMetadataStore(): MetadataStoreInterface
    {
        return $this->workflow->getMetadataStore();
    }
}
