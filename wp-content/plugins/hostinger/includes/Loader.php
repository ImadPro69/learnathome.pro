<?php

namespace Hostinger;

defined( 'ABSPATH' ) || exit;

class Loader {
	protected array $actions;
	protected array $filters;

	public function __construct() {
		$this->actions = array();
		$this->filters = array();
	}

    /**
     * @param string $hook
     * @param        $component
     * @param string $callback
     * @param int    $priority
     * @param int    $accepted_args
     *
     * @return void
     */
	public function add_action( string $hook, $component, string $callback, int $priority = 10, int $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

    /**
     * @param string $hook
     * @param        $component
     * @param string $callback
     * @param int    $priority
     * @param int    $accepted_args
     *
     * @return void
     */
	public function add_filter( string $hook, $component, string $callback, int $priority = 10, int $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

    /**
     * @param array  $hooks
     * @param string $hook
     * @param        $component
     * @param string $callback
     * @param int    $priority
     * @param int    $accepted_args
     *
     * @return array
     */
	private function add(
		array $hooks,
		string $hook,
		$component,
		string $callback,
		int $priority,
		int $accepted_args
	): array {
		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $hooks;
	}

	/**
	 * @return void
	 */
	public function run(): void {
		foreach ( $this->filters as $hook ) {
			add_filter(
				$hook['hook'],
				array( $hook['component'], $hook['callback'] ),
				$hook['priority'],
				$hook['accepted_args']
			);
		}

		foreach ( $this->actions as $hook ) {
			add_action(
				$hook['hook'],
				array( $hook['component'], $hook['callback'] ),
				$hook['priority'],
				$hook['accepted_args']
			);
		}
	}
}
