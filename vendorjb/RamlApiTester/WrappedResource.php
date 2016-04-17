<?php

namespace JoeBengalen\RamlApiTester;

use Raml\Resource;

class WrappedResource
{
    /**
     * @var Resource
     */
    protected $resource;

    /**
     * @var WrappedResource|null
     */
    protected $parent;

    /**
     * Create WrappedResource.
     *
     * @param Resource             $resource
     * @param WrappedResource|null $parent
     */
    public function __construct(Resource $resource, WrappedResource $parent = null)
    {
        $this->resource = $resource;
        $this->parent = $parent;
    }

    /**
     * Get the Resource
     *
     * @return Resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Get the parent WrappedResource
     *
     * @return WrappedResource|null
     */
    public function getParent()
    {
        return $this->parent;
    }
}
