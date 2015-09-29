<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008, 2012 Filip Procházka (filip@prochazka.su)
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 */

namespace Kdyby\Extension\Diagnostics\HtmlValidator\DI;

use Kdyby;
use Nette;
use Nette\Configurator;
use Nette\DI\Compiler;



/**
 * @author Filip Procházka <filip@prochazka.su>
 */
class ValidatorExtension extends Nette\DI\CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		if (!$builder->parameters['debugMode']) {
			return;
		}

		$builder->addDefinition($this->prefix('panel'))
			->setClass('Kdyby\Extension\Diagnostics\HtmlValidator\ValidatorPanel')
			->addSetup('Tracy\Debugger::getBar()->addPanel(?)', array('@self'));

		$builder->getDefinition('application')
			->addSetup('$service->onStartup[] = ?', array(array($this->prefix('@panel'), 'startBuffering')))
			->addSetup('$service->onShutdown[] = ?', array(array($this->prefix('@panel'), 'validate')))
			->addSetup('$service->onError[] = ?', array(array($this->prefix('@panel'), 'stopBuffering')));
	}

	/**
	 * @param Configurator $config
	 */
	public static function register(Configurator $config)
	{
		$config->onCompile[] = function (Configurator $config, Compiler $compiler) {
			$compiler->addExtension('htmlValidator', new ValidatorExtension());
		};
	}

}
