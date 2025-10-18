<?php

namespace App\Core;

/**
 * Hook - Plugin system with actions and filters
 * 钩子系统 - 支持动作和过滤器的插件系统
 */
class Hook {
    /**
     * Registered action hooks
     * @var array
     */
    protected $actions = [];

    /**
     * Registered filter hooks
     * @var array
     */
    protected $filters = [];

    /**
     * Add an action hook
     * 添加动作钩子
     *
     * @param string $hook Hook name
     * @param callable $callback Callback function
     * @param int $priority Priority (lower runs first)
     * @return void
     */
    public function addAction($hook, callable $callback, $priority = 10) {
        $this->actions[$hook][$priority][] = $callback;
    }

    /**
     * Add a filter hook
     * 添加过滤器钩子
     *
     * @param string $hook Hook name
     * @param callable $callback Callback function
     * @param int $priority Priority (lower runs first)
     * @return void
     */
    public function addFilter($hook, callable $callback, $priority = 10) {
        $this->filters[$hook][$priority][] = $callback;
    }

    /**
     * Trigger an action hook
     * 触发动作钩子
     *
     * @param string $hook Hook name
     * @param mixed ...$args Arguments to pass to callbacks
     * @return void
     */
    public function trigger($hook, ...$args) {
        if (!isset($this->actions[$hook])) {
            return;
        }

        // Sort by priority
        ksort($this->actions[$hook]);

        // Execute callbacks
        foreach ($this->actions[$hook] as $priority => $callbacks) {
            foreach ($callbacks as $callback) {
                call_user_func_array($callback, $args);
            }
        }
    }

    /**
     * Apply filter hook
     * 应用过滤器钩子
     *
     * @param string $hook Hook name
     * @param mixed $value Value to filter
     * @param mixed ...$args Additional arguments
     * @return mixed Filtered value
     */
    public function filter($hook, $value, ...$args) {
        if (!isset($this->filters[$hook])) {
            return $value;
        }

        // Sort by priority
        ksort($this->filters[$hook]);

        // Apply filters
        foreach ($this->filters[$hook] as $priority => $callbacks) {
            foreach ($callbacks as $callback) {
                $value = call_user_func($callback, $value, ...$args);
            }
        }

        return $value;
    }

    /**
     * Remove an action hook
     * 移除动作钩子
     *
     * @param string $hook Hook name
     * @param callable|null $callback Specific callback to remove (null for all)
     * @param int|null $priority Specific priority (null for all)
     * @return void
     */
    public function removeAction($hook, $callback = null, $priority = null) {
        if ($callback === null) {
            unset($this->actions[$hook]);
            return;
        }

        if ($priority !== null) {
            if (isset($this->actions[$hook][$priority])) {
                $this->actions[$hook][$priority] = array_filter(
                    $this->actions[$hook][$priority],
                    function($cb) use ($callback) {
                        return $cb !== $callback;
                    }
                );
            }
        } else {
            foreach ($this->actions[$hook] as $pri => $callbacks) {
                $this->actions[$hook][$pri] = array_filter(
                    $callbacks,
                    function($cb) use ($callback) {
                        return $cb !== $callback;
                    }
                );
            }
        }
    }

    /**
     * Remove a filter hook
     * 移除过滤器钩子
     *
     * @param string $hook Hook name
     * @param callable|null $callback Specific callback to remove (null for all)
     * @param int|null $priority Specific priority (null for all)
     * @return void
     */
    public function removeFilter($hook, $callback = null, $priority = null) {
        if ($callback === null) {
            unset($this->filters[$hook]);
            return;
        }

        if ($priority !== null) {
            if (isset($this->filters[$hook][$priority])) {
                $this->filters[$hook][$priority] = array_filter(
                    $this->filters[$hook][$priority],
                    function($cb) use ($callback) {
                        return $cb !== $callback;
                    }
                );
            }
        } else {
            foreach ($this->filters[$hook] as $pri => $callbacks) {
                $this->filters[$hook][$pri] = array_filter(
                    $callbacks,
                    function($cb) use ($callback) {
                        return $cb !== $callback;
                    }
                );
            }
        }
    }

    /**
     * Check if hook has any callbacks
     * 检查钩子是否有回调
     *
     * @param string $hook Hook name
     * @return bool
     */
    public function hasAction($hook) {
        return isset($this->actions[$hook]) && !empty($this->actions[$hook]);
    }

    /**
     * Check if filter has any callbacks
     * 检查过滤器是否有回调
     *
     * @param string $hook Hook name
     * @return bool
     */
    public function hasFilter($hook) {
        return isset($this->filters[$hook]) && !empty($this->filters[$hook]);
    }

    /**
     * Get all registered actions
     * 获取所有注册的动作
     *
     * @return array
     */
    public function getActions() {
        return $this->actions;
    }

    /**
     * Get all registered filters
     * 获取所有注册的过滤器
     *
     * @return array
     */
    public function getFilters() {
        return $this->filters;
    }
}
