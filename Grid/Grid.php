<?php

namespace Vivait\APYDataGridBundle\Grid;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class Grid extends \APY\DataGridBundle\Grid\Grid {
	protected $viewUrl;

	public function getGridResponse($param1 = null, $param2 = null, Response $response = null)
	{
		$isReadyForRedirect = $this->isReadyForRedirect();

		if ($this->isReadyForExport()) {
			return $this->getExportResponse();
		}

		if ($this->isMassActionRedirect()) {
			return $this->getMassActionResponse();
		}

		$view_url = $this->getViewUrl();

		if (count($this->rows) === 1 && $view_url) {
			// Rewind the object first
			$this->rows->getIterator()->rewind();
			$row = $this->rows->getIterator()->current();

			return new RedirectResponse(
				$this->container->get('router')->generate($view_url,  array(
					'id' => $row->getPrimaryFieldValue()
				))
			);
		}

		if ($isReadyForRedirect) {
			return new RedirectResponse($this->getRouteUrl());
		} else {
			if (is_array($param1) || $param1 === null) {
				$parameters = (array) $param1;
				$view = $param2;
			} else {
				$parameters = (array) $param2;
				$view = $param1;
			}

			$parameters = array_merge(array('grid' => $this), $parameters);

			if ($view === null) {
				return $parameters;
			} else {
				return $this->container->get('templating')->renderResponse($view, $parameters, $response);
			}
		}
	}

	/**
	 * Sets viewUrl
	 * @param mixed $viewUrl
	 */
	public function setViewUrl($viewUrl) {
		$this->viewUrl = $viewUrl;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getViewUrl() {
		return $this->viewUrl;
	}
}