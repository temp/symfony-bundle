<?php

/*
 * This file is part of the puli/symfony-bundle package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Puli\Extension\Symfony\PuliBundle\DependencyInjection\Compiler;

use Puli\RepositoryManager\Config\Config;
use Puli\RepositoryManager\ManagerFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Webmozart\PathUtil\Path;

/**
 * Sets the %puli.repository.path% parameter to the correct path of Puli's
 * resource repository.
 *
 * This is necessary in order to skip the manager factory in production.
 *
 * @since  1.0
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class PuliFactoryLoaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $rootDir = Path::canonicalize($container->getParameter('kernel.root_dir').'/..');

        // Bootstrap necessary Puli classes
        $environment = ManagerFactory::createProjectEnvironment($rootDir);
        $config = $environment->getConfig();

        $factoryPath = Path::makeAbsolute($config->get(Config::FACTORY_FILE), $rootDir);
        $factoryClass = $config->get(Config::FACTORY_CLASS);

        // Set the parameters
        $container->setParameter('puli.factory.path', $factoryPath);
        $container->setParameter('puli.factory.class', $factoryClass);
    }
}