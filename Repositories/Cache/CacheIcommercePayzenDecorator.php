<?php

namespace Modules\Icommercepayzen\Repositories\Cache;

use Modules\Icommercepayzen\Repositories\IcommercePayzenRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheIcommercePayzenDecorator extends BaseCacheDecorator implements IcommercePayzenRepository
{
    public function __construct(IcommercePayzenRepository $icommercepayzen)
    {
        parent::__construct();
        $this->entityName = 'icommercepayzen.icommercepayzens';
        $this->repository = $icommercepayzen;
    }

    public function calculate($parameters,$conf)
    {
        return $this->remember(function () use ($parameters,$conf) {
            return $this->repository->calculate($parameters, $conf);
        });
    }
    
}
