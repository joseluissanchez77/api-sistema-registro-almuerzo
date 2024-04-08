<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface IBaseRepository
{
    /**
     * all
     *
     * @param  mixed $request
     * @return void
     */
    public function all($request);

    /**
     * save
     *
     * @param  mixed $model
     * @return void
     */
    public function save(Model $model);

    /**
     * find
     *
     * @param  mixed $id
     * @return void
     */
    public function find($id);

    /**
     * destroy
     *
     * @param  mixed $model
     * @return void
     */
    public function destroy (Model $model);
}
