<?php
/*
 *
 * (c) Ivo Bathke
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigfiche\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use RedBeanPHP\Facade as R;

class RedBeanServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['db'] = $app->share(function () use ($app) {
            $options = array(
                'dsn'      => null,
                'username' => null,
                'password' => null,
                'frozen'   => false,
            );
            if(isset($app['db.param'])){
                $options = array_replace($options, $app['db.param']);
            }
            R::setup(
                $options['dsn'],
                $options['username'],
                $options['password'],
                $options['frozen']
            );
        });
    }
    public function boot(Application $app)
    {
    }
} 