<?php declare(strict_types=1);

namespace Dms\Cli\Expressive\Scaffold;

use Dms\Cli\Expressive\Scaffold\Domain\DomainObjectStructure;
use Dms\Cli\Expressive\Scaffold\Domain\DomainStructure;
use Dms\Core\Exception\InvalidArgumentException;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ScaffoldContext
{
    /**
     * @var string
     */
    protected $rootEntityNamespace;

    /**
     * @var DomainStructure
     */
    protected $domainStructure;

    /**
     * ScaffoldContext constructor.
     *
     * @param string          $rootEntityNamespace
     * @param DomainStructure $domainStructure
     */
    public function __construct(string $rootEntityNamespace, DomainStructure $domainStructure)
    {
        $this->rootEntityNamespace = ltrim($rootEntityNamespace, '\\');
        $this->domainStructure = $domainStructure;
    }

    /**
     * @return string
     */
    public function getRootEntityNamespace() : string
    {
        return $this->rootEntityNamespace;
    }

    /**
     * @return DomainStructure
     */
    public function getDomainStructure(): DomainStructure
    {
        return $this->domainStructure;
    }

    /**
     * @param DomainObjectStructure $domainObjectStructure
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public function getRelativeObjectNamespace(DomainObjectStructure $domainObjectStructure) : string
    {
        if (!starts_with($domainObjectStructure->getDefinition()->getClassName(), $this->rootEntityNamespace)) {
            throw InvalidArgumentException::format('Domain object not in root namespace');
        }

        return trim(substr($domainObjectStructure->getReflection()->getNamespaceName(), strlen($this->rootEntityNamespace)), '\\');
    }
}
