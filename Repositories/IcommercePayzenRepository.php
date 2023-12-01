<?php

namespace Modules\Icommercepayzen\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface IcommercePayzenRepository extends BaseRepository
{

	public function calculate($parameters,$conf);
	
}
