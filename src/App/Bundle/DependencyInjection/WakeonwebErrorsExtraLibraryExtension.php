<?php

namespace WakeOnWeb\ErrorsExtraLibrary\App\Bundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class WakeonwebErrorsExtraLibraryExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter('wakeonweb_errors_extra_library.force_format', $config['force_format']);
        $container->setParameter('wakeonweb_errors_extra_library.exception_http_status_codes', $config['exception']['http_status_codes']);
        $container->setParameter('wakeonweb_errors_extra_library.exception_show_messages', $config['exception']['show_messages']);
        $container->setParameter('wakeonweb_errors_extra_library.exception_log_levels', $config['exception']['log_levels']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/services'));
        $loader->load('configuration.xml');
        $loader->load('listener.xml');
    }
}
