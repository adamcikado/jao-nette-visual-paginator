#jao-nette-visual-paginator (cc)#
Pavel Železný (2bfree), 2013 ([pavelzelezny.cz](http://pavelzelezny.cz))

## Requirements ##

[Nette Framework 2.1.1](http://nette.org) or higher. PHP 5.3 edition

## Documentation ##

Just another one visual paginator component for Nette framework based on original [VisualPaginator](http://addons.nette.org/cs/visualpaginator) from [David Grudl](http://davidgrudl.com).

Some features added:
- Default template is [Twitter bootstrap 3](http://getbootstrap.com) compatible.
- Text strings are translateable.
- Switch page is handled by [Nette ajax](http://doc.nette.org/en/2.1/ajax).
- Support of setting additional [event](http://doc.nette.org/en/2.1/php-language-enhancements#toc-events).
- Possibility of changing template.
- Possibility of setting count of vissible pages.

## Instalation ##

Prefered way to intall is by [Composer](http://getcomposer.org)

	{
		"require":{
			"zeleznypa/jao-nette-visual-paginator": "dev-master"
		}
	}

## Setup ##

Add following code into neon.conf

	common:
		services:
			paginatorFactory:
				implement: \Zeleznypa\Nette\Utils\IPaginatorFactory
			visualPaginatorFactory:
				implement: \Zeleznypa\Nette\Utils\IVisualPaginatorFactory

## Usage ##

After instalation you can simply set dependency on IVisualPaginatorFacotry by constructor property or [inject method](http://pla.nette.org/cs/inject-autowire)(cs manual) in presenter.

	<?php

	/**
	 * Base presenter for all application presenters.
	 */
	class TestPresenter extends \Nette\Application\UI\Presenter
	{

		/** @var \Zeleznypa\Nette\Utils\IVisualPaginatorFactory */
		protected $visualPaginatorFactory;

		/**
		 * Visual paginator injection
		 * @author Pavel Železný <info@pavelzelezny.cz>
		 * @param \Zeleznypa\Nette\Utils\IVisualPaginatorFactory $visualPaginatorFactory
		 * @return TestPresenter Provides fluent interface
		 * @throws \Nette\InvalidStateException
		 */
		public function injectVisualPaginatorFactory(\Zeleznypa\Nette\Utils\IVisualPaginatorFactory $visualPaginatorFactory)
		{
			if ($this->visualPaginatorFactory !== NULL)
			{
				throw new \Nette\InvalidStateException('Visual paginator factory has already been set');
			}
			$this->visualPaginatorFactory = $visualPaginatorFactory;
			return $this;
		}

		/**
		 * Visual paginator component factory
		 * @author Pavel Železný <info@pavelzelezny.cz>
		 * @return \Zeleznypa\Nette\Utils\VisualPaginator
		 */
		protected function createComponentVisualPaginator()
		{
			$visualPaginator = $this->getVisualPaginatorFactory()->create();
			$visualPaginator->getPaginator()->setItemsPerPage(10);
			$visualPaginator->onSwitch[] = callback($this, 'redrawPaginatedList');
			return $visualPaginator;
		}

		/**
		 * Redraw snippet with list of paginated data
		 * @author Pavel Železný <info@pavelzelezny.cz>
		 * @return void
		 */
		protected function redrawPaginatedList()
		{
			$this->redrawComponent('PaginatedList');
		}

		/**
		 * Visual paginator getter
		 * @author Pavel Železný <info@pavelzelezny.cz>
		 * @return \Zeleznypa\Nette\Utils\IVisualPaginatorFactory
		 */
		public function getVisualPaginatorFactory()
		{
			return $this->visualPaginatorFactory;
		}

	}

In Latte templates you can use standard component renderer

	{control visualPaginator}
