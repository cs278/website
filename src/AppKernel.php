<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class AppKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new \Symfony\Bundle\FrameworkBundle\FrameworkBundle();
        yield new \Symfony\Bundle\SecurityBundle\SecurityBundle();
        yield new \Symfony\Bundle\TwigBundle\TwigBundle();
        yield new \Symfony\Bundle\MonologBundle\MonologBundle();

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            yield new \Symfony\Bundle\DebugBundle\DebugBundle();
            yield new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
        }
    }

    public function getProjectDir(): string
    {
        return dirname(__DIR__);
    }

    public function getCacheDir(): string
    {
        return $this->getProjectDir().'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir().'/var/logs';
    }

    public function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->setParameter('container.dumper.inline_class_loader', true);

        $loader->load($this->getProjectDir().'/config/config_'.$this->getEnvironment().'.yml');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import($this->getProjectDir().'/config/routing.yml');

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $routes->import($this->getProjectDir().'/config/routing_dev.yml');
        }
    }
}
