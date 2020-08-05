<?php

declare( strict_types = 1 );

namespace Masonite\WP\Widen_Media;

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 */
class Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @var array
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @var array
	 */
	protected $filters;

	/**
	 * The array of shortcodes registered with WordPress.
	 *
	 * @var array
	 */
	protected $shortcodes;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 */
	public function __construct() {
		$this->actions    = [];
		$this->filters    = [];
		$this->shortcodes = [];
	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @param      string            $hook             The name of the WordPress action that is being registered.
	 * @param      object            $component        A reference to the instance of the object on which the action is defined.
	 * @param      string            $callback         The name of the function definition on the $component.
	 * @param      int      Optional $priority         The priority at which the function should be fired.
	 * @param      int      Optional $accepted_args    The number of arguments that should be passed to the $callback.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ): void {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @param      string            $hook             The name of the WordPress filter that is being registered.
	 * @param      object            $component        A reference to the instance of the object on which the filter is defined.
	 * @param      string            $callback         The name of the function definition on the $component.
	 * @param      int      Optional $priority         The priority at which the function should be fired.
	 * @param      int      Optional $accepted_args    The number of arguments that should be passed to the $callback.
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ): void {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new shortcode to the collection to be registered with WordPress.
	 *
	 * @param     string $tag           The name of the new shortcode.
	 * @param     object $component      A reference to the instance of the object on which the shortcode is defined.
	 * @param     string $callback       The name of the function that defines the shortcode.
	 */
	public function add_shortcode( $tag, $component, $callback ): void {
		$this->shortcodes = $this->add( $this->shortcodes, $tag, $component, $callback );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @param      array             $hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param      string            $hook             The name of the WordPress filter that is being registered.
	 * @param      object            $component        A reference to the instance of the object on which the filter is defined.
	 * @param      string            $callback         The name of the function definition on the $component.
	 * @param      int      Optional $priority         The priority at which the function should be fired.
	 * @param      int      Optional $accepted_args    The number of arguments that should be passed to the $callback.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority = 10, $accepted_args = 2 ): array {
		$hooks[] = [
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		];

		return $hooks;
	}

	/**
	 * Register the filters, actions, & shortcodes with WordPress.
	 */
	public function run(): void {
		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], [ $hook['component'], $hook['callback'] ], $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], [ $hook['component'], $hook['callback'] ], $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->shortcodes as $hook ) {
			add_shortcode( $hook['hook'], [ $hook['component'], $hook['callback'] ] );
		}
	}

}
