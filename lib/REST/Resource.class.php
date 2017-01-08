<?php
/* 
 * Sendanor's PHP REST Framework
 * Copyright 2017 Jaakko-Heikki Heusala <jhh@sendanor.com> 
 */

namespace REST;

/* Security check */
if(!defined('REST_PHP')) {
	die("Direct access not permitted\n");
}

/** Strip phpdoc comment */
function stripDocComment ($str) {
	return trim(rtrim(trim(implode(" ", array_map(function($row) {
		return trim(ltrim(trim($row), '*'));
	}, explode("\n", substr($str, 1, -1))))), '*'));
}

/** */
function is_method ($item) {
	return API::isMethod($item->getName());
}

/** */
function prepare_method ($item) {
	return array(
		'method' => $item->getName(),
		'description' => stripDocComment($item->getDocComment())
	);
}

/** */
function prepare_lesser_method ($item) {
	return array(
		'method' => $item->getName(),
		'description' => ''
	);
}

/** */
abstract class Resource implements iResource, iAutoOptions {

	/** If true, automatic documentation will be provided with OPTIONS method */
	private $auto_options_enabled = null;

	/** Enable automatic documentation for this resource using the OPTIONS method */
	public function enableAutoOptions () {
		$this->auto_options_enabled = true;
		return $this;
	}

	/** Disable automatic documentation for this resource using the OPTIONS method */
	public function disableAutoOptions () {
		$this->auto_options_enabled = false;
		return $this;
	}

	/** Enable fetching automatic documentation for this resource using the OPTIONS method */
	public function getAutoOptions () {
		if(is_null($this->auto_options_enabled)) {
			$this->auto_options_enabled = API::getAutoOptions();
		}
		return $this->auto_options_enabled;
	}

	/** Returns information for this REST resource */
	public function options (iRequest $request) {

		$router = $request->getRouter();

		// Automatic documentation using reflection class and ->getDocComment()
		if($this->getAutoOptions()) {
			$rc = new \ReflectionClass($this);
			$methods = array_filter($rc->getMethods(\ReflectionMethod::IS_PUBLIC), "REST\\is_method");
			if(!is_array($methods)) {
				throw new \Exception('$methods invalid');
			}
			return array(
				'description' => stripDocComment($rc->getDocComment()),
				'methods' => array_values(array_map("REST\\prepare_method", $methods)),
				'routes' => array_map(function($item) {
					$child_rc = new \ReflectionClass($item['class_name']);
					return array(
						'route' => $item['path'],
						'description' => stripDocComment($child_rc->getDocComment())
					);
				}, $router->getChildRoutes($request->getPath()))
			);
		}

		// Automatic documentation without using `->getDocomment()`
		$rc = new \ReflectionClass($this);
		$methods = array_filter($rc->getMethods(\ReflectionMethod::IS_PUBLIC), "REST\\is_method");
		return array(
			'description' => '',
			'methods' => array_values(array_map("prepare_lesser_method", $methods)),
			'routes' => array_map(function($item) {
				$child_rc = new \ReflectionClass($item['class_name']);
				return array(
					'route' => $item['path'],
					'description' => ''
				);
			}, $router->getChildRoutes($request->getPath()))
		);
	}

}
