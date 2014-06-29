<?php

namespace Zeleznypa\Nette\Utils;

/**
 * IVisualPaginatorFactory
 * @author Pavel Železný <info@pavelzelezny.cz>
 */
interface IVisualPaginatorFactory
{

	/**
	 * Visual paginator factory
	 * @author Pavel Železný <info@pavelzelezny.cz>
	 * @return VisualPaginator
	 */
	public function create();
}
