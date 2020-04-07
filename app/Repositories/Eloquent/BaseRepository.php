<?php


namespace App\Repositories\Eloquent;

use App\Exceptions\ModelNotDefined;
use App\Repositories\Contracts\BaseInterface;

abstract class BaseRepository implements BaseInterface
{
    protected $model;

    public function __construct()
    {
        $this->model = $this->getModelClass();
    }

    public function all()
    {
        return $this->model->all();
    }

    protected function getModelClass()
    {
        if (!method_exists($this, 'model')) {
            throw new ModelNotDefined("No model defined");
        }

        return app()->make($this->model());
    }
}
