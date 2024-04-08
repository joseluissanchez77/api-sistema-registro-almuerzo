<?php

namespace App\Repositories;

use App\Models\Catalog;
use App\Repositories\Base\BaseRepository;
use App\Exceptions\Custom\NotFoundException;
use Illuminate\Http\Request;

class CatalogRepository extends BaseRepository
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(Catalog $catalog)
    {
        parent::__construct($catalog);
    }
}
