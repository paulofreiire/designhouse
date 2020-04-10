<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\{CommentInterface, DesignInterface, InvitationInterface, TeamInterface, UserInterface};
use App\Repositories\Eloquent\{CommentRepository,
    DesignRepository,
    InvitationRepository,
    TeamRepository,
    UserRepository
};

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(DesignInterface::class, DesignRepository::class);
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(CommentInterface::class, CommentRepository::class);
        $this->app->bind(TeamInterface::class, TeamRepository::class);
        $this->app->bind(InvitationInterface::class, InvitationRepository::class);
    }
}
