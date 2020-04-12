<?php


namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserInterface;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class UserRepository extends BaseRepository implements UserInterface
{
    public function model()
    {
        return User::class;
    }

    public function findByEmail($email)
    {
        return $this->model
            ->where('email', $email)
            ->first();
    }

    public function search($request)
    {
        $query = (new $this->model)->newQuery();

        if($request->designs)
            $query->has('designs');

        if($request->available_to_hire)
            $query->where('available_to_hire', true);

        //Geo search
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $dist = $request->distance;
        $unit = $request->unit;

        if ($latitude && $longitude) {
            $point = new Point($latitude, $longitude);
            $query->distanceSphereExludingSelf('location', $point, $dist);
        }

        $request->orderByLatest ? $query->latest() : $query->oldest();

        return $query->get();

    }
}
