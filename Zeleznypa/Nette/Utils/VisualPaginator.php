<?php

namespace Zeleznypa\Nette\Utils;

/**
 * VisualPaginator
 * @author Pavel Železný <info@pavelzelezny.cz>
 */
class VisualPaginator extends \Nette\Application\UI\Control
{

	/** @var array $onSwitch */
	public $onSwitch = array();

	/**
	 * @var integer $page
	 * @persistent 
	 */
	public $page = 1;

	/** @var \Nette\Utils\Paginator $paginator */
	protected $paginator;

	/** @var \Zeleznypa\Nette\Utils\IPaginatorFactory $paginatorFactory */
	protected $paginatorFactory;

	/** @var array $steps */
	protected $steps;

	/** @var integer $stepsCount */
	protected $stepsCount = 4;

	/** @var string $templateFile */
	protected $templateFile;

	/** @var \Nette\Localization\ITranslator $translator $translator */
	protected $translator;

	/**
	 * Constructor
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @param \Zeleznypa\Nette\Utils\IPaginatorFactory $paginatorFactory
	 * @return void
	 */
	public function __construct(\Zeleznypa\Nette\Utils\IPaginatorFactory $paginatorFactory, \Nette\Localization\ITranslator $translator)
	{
		$this->injectPaginatorFactory($paginatorFactory);
		$this->injectTranslator($translator);
	}

	// <editor-fold defaultstate="collapsed" desc="Dependency injection">
	/**
	 * Paginator factory injection
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @param \Zeleznypa\Nette\Utils\IPaginatorFactory $paginatorFactory
	 * @return \Zeleznypa\Nette\Utils\VisualPaginator Provides fluent interface
	 * @throws \Nette\InvalidStateException
	 */
	public function injectPaginatorFactory(\Zeleznypa\Nette\Utils\IPaginatorFactory $paginatorFactory)
	{
		return $this->setPaginatorFactory($paginatorFactory);
	}

	/**
	 * Translator injection
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @param \Nette\Localization\ITranslator $translator
	 * @return \Zeleznypa\Nette\Utils\VisualPaginator Provides fluent interface
	 * @throws \Nette\InvalidStateException
	 */
	public function injectTranslator(\Nette\Localization\ITranslator $translator)
	{
		return $this->setTranslator($translator);
	}

	// </editor-fold>

	/**
	 * Loads state informations
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @param array $params
	 * @return void
	 */
	public function loadState(array $params)
	{
		parent::loadState($params);
		$this->getPaginator()->setPage($this->getPage());
	}

	/**
	 * Renders paginator
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @return void
	 */
	public function render()
	{
		$this->getTemplate()->setFile($this->getTemplateFile());
		$this->getTemplate()->setTranslator($this->getTranslator());
		$this->getTemplate()->steps = $this->getSteps();
		$this->getTemplate()->paginator = $this->getPaginator();
		$this->getTemplate()->render();
	}

	/**
	 * Handle switch page action
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @param integer $page
	 * @return void
	 */
	public function handleSwitch($page)
	{
		$this->setPage($page);
		$this->onSwitch($this);
		$this->redrawControl('paginator');
		if ($this->getPresenter()->isAjax() !== TRUE)
		{
			$this->link('this');
		}
	}

	/**
	 * Count optimal steps layout
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @return array
	 */
	public function countSteps()
	{
		$arr = range(max($this->getPaginator()->getFirstPage(), $this->getPaginator()->getPage() - 3), min($this->getPaginator()->getLastPage(), $this->getPaginator()->getPage() + 3));
		$quotient = ($this->getPaginator()->getPageCount() - 1) / $this->getStepsCount();
		for ($i = 0; $i <= $this->getStepsCount(); $i++)
		{
			$arr[] = round($quotient * $i) + $this->getPaginator()->getFirstPage();
		}
		sort($arr);
		return array_values(array_unique($arr));
	}

	// <editor-fold defaultstate="collapsed" desc="Getters & Setters">
	/**
	 * Page getter
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @return integer
	 */
	public function getPage()
	{
		return $this->page;
	}

	/**
	 * Page setter
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @param integer $page
	 * @return \Zeleznypa\Nette\Utils\VisualPaginator Provides fluent interface
	 */
	public function setPage($page)
	{
		$this->page = $page;
		return $this;
	}

	/**
	 * Singleton implementation of paginator getter
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @return \Nette\Utils\Paginator
	 */
	public function getPaginator()
	{
		if ($this->paginator === NULL)
		{
			$this->paginator = $this->getPaginatorFactory()->create();
		}

		return $this->paginator;
	}

	/**
	 * Paginator factory getter
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @return \Zeleznypa\Nette\Utils\IPaginatorFactory
	 */
	public function getPaginatorFactory()
	{
		return $this->paginatorFactory;
	}

	/**
	 * Paginator factory setter
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @param \Zeleznypa\Nette\Utils\IPaginatorFactory $paginatorFactory
	 * @return \Zeleznypa\Nette\Utils\VisualPaginator Provides fluent interface
	 * @throws \Nette\InvalidStateException
	 */
	public function setPaginatorFactory(\Zeleznypa\Nette\Utils\IPaginatorFactory $paginatorFactory)
	{
		if ($this->paginatorFactory !== NULL)
		{
			throw new \Nette\InvalidStateException('Paginator factory has already been set');
		}
		$this->paginatorFactory = $paginatorFactory;
		return $this;
	}

	/**
	 * Singleton implementation of steps getter
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @return array
	 */
	public function getSteps()
	{
		if ($this->steps === NULL)
		{
			$this->steps = ($this->getPaginator()->getPageCount() < 2) ? array($this->getPaginator()->getPage()) : $this->countSteps();
		}
		return $this->steps;
	}

	/**
	 * Steps count getter
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @return integer
	 */
	public function getStepsCount()
	{
		return $this->stepsCount;
	}

	/**
	 * Steps count setter
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @param type $stepsCount
	 * @return \Zeleznypa\Nette\Utils\VisualPaginator Provides fluent interface
	 * @throws \Nette\InvalidStateException
	 */
	public function setStepsCount($stepsCount)
	{
		if (is_integer($stepsCount) !== FALSE)
		{
			throw new \Nette\InvalidStateException('Steps count have to be integer');
		}
		$this->stepsCount = $stepsCount;
		return $this;
	}

	/**
	 * Options setter
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @param array $options
	 * @return \Zeleznypa\Nette\Utils\VisualPaginator Provides fluent interface
	 * @throws \Nette\InvalidStateException
	 */
	public function setOptions(array $options)
	{
		if ($this->steps !== NULL)
		{
			throw new \Nette\InvalidStateException('Unable to change options, because steps for visual paginator has already been generated');
		}
		if (count(array_diff(array('count', 'pageSize'), array_keys($options))) > 0)
		{
			throw new \Nette\InvalidStateException('Invalid format of options array');
		}

		$this->getPaginator()->setItemCount($options['count']);
		$this->getPaginator()->setItemsPerPage($options['pageSize']);
		return $this;
	}

	/**
	 * Template file getter
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @return string
	 */
	public function getTemplateFile()
	{
		return $this->templateFile !== NULL ? $this->templateFile : __DIR__ . DIRECTORY_SEPARATOR . 'VisualPaginator.latte';
	}

	/**
	 * Template file setter
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @param string $templateFile
	 * @return \Zeleznypa\Nette\Utils\VisualPaginator Provides flueant interface
	 * @throws \Nette\InvalidStateException
	 */
	public function setTemplateFile($templateFile)
	{
		if ($this->templateFile !== NULL)
		{
			throw new \Nette\InvalidStateException('Template file has already been set');
		}
		if ((is_file($templateFile) !== TRUE) || (is_readable($templateFile) !== TRUE))
		{
			throw new \Nette\InvalidStateException('Template file does not exist or is not readable');
		}
		$this->templateFile = realpath($templateFile);
		return $this;
	}

	/**
	 * Translator getter
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @return \Nette\Localization\ITranslator
	 */
	public function getTranslator()
	{
		return $this->translator;
	}

	/**
	 * Translator setter
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @param \Nette\Localization\ITranslator $translator
	 * @return \Zeleznypa\Nette\Utils\VisualPaginator Provides fluent interface
	 * @throws \Nette\InvalidStateException
	 */
	public function setTranslator(\Nette\Localization\ITranslator $translator)
	{
		if ($this->translator !== NULL)
		{
			throw new \Nette\InvalidStateException('Translator has already been set');
		}
		$this->translator = $translator;
		return $this;
	}

	// </editor-fold>
}
