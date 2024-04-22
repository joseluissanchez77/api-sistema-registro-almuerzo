<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ConflictException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CatalogRequest;
use App\Models\Catalog;
use App\Repositories\CatalogRepository;
use App\Traits\RestResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;


class CatalogController extends Controller
{

    use RestResponse;
    private $catalogRepository;

    /**
     * __construct
     *
     * @param App\Repositories\CatalogRepository $catalogRepository
     * @return void
     */
    public function __construct(CatalogRepository $catalogRepository)
    {
        $this->catalogRepository = $catalogRepository;
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CatalogRequest $request)
    {

        DB::beginTransaction();
        try {
            $catalogs = new Catalog($request->all());
            $rpt = $this->catalogRepository->save($catalogs);

            DB::commit();
            return $this->information(__('messages.success'));
            // return $this->success($rpt, Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            DB::rollBack();
            throw new ConflictException($ex->getMessage());
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Catalog  $catalog
     * @return \Illuminate\Http\Response
     */
    public function update(CatalogRequest $request, Catalog $catalog)
    {
        DB::beginTransaction();
        try {
            $catalog->fill($request->all());

            dd($catalog->isClean());
            if ($catalog->isClean())
                return $this->information(__('messages.nochange'));

            return $this->success($this->catalogRepository->save($catalog));
        } catch (\Exception $ex) {
            DB::rollBack();
            throw new ConflictException($ex->getMessage());
        }
    }
}
