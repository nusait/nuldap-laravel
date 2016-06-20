<?php namespace Nusait\Nuldap;

use Faker\Generator as Faker;
use Illuminate\Support\ServiceProvider;

class NuldapServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    public function boot()
    {

        $this->publishes([
            __DIR__ . '/../../config/ldap.php' => config_path('ldap.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('ldap', function ($app) {
            $config = $app['config']->get('ldap');
            if ($config['fake']) {
                $faker = \App::make(Faker::class);

                return new NuldapFake($faker);
            }

            return new NuLdap($config['rdn'], $config['password'], $config['host'], $config['port']);
        });

        $this->app->alias('ldap', 'Nusait\Nuldap\NuLdap');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['ldap', 'Nusait\Nuldap\NuLdap'];
    }
}
