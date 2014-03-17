<?php

namespace Zeleznypa\Nette\Utils;

/**
 * IPaginatorFactory
 * @author Pavel Železný <info@pavelzelezny.cz>
 */
interface IPaginatorFactory
{

	/**
	 * Nette paginator factory
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @return \Nette\Utils\Paginator
	 */
	public function create();
}
