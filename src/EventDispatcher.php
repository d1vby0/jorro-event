<?php

namespace Jorro\Event;

use Psr\Container\ContainerInterface;

class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var array イベントリスナ
     */
    protected array $eventListeners = [];

    public function __construct(protected ContainerInterface $container)
    {
    }

    /**
     * イベントのディスパッチ
     *
     * @param mixed $event イベントオブジェクト
     * @return void
     */
    public function dispatch($event): void
    {
        foreach ($this->eventListeners[$event::class] ?? [] as $id => $listener) {
            if ($listener($event) === false) {
                break;
            }
        }
    }

    /**
     * イベントリスナの登録
     *
     * @param string $name イベント名
     * @param string|array|\Closure $listener イベントリスナ クラス@メソッド名(省略時はhandle) もしくは クロージャ
     * @return int|string 登録ID
     */
    public function listen(string $name, string|array|\Closure $listener): int|string
    {
        if ($listener instanceof \Closure) {
            $this->eventListeners[$event][] = $listener;

            return array_key_last($this->eventListeners[$event]);
        }
        if (is_array($listener)) {
            if (is_object($listener[0])) {
                $id = $listener[0]::class . '@' . spl_object_id($listener[0]);
                if (!empty($listener[1])) {
                    $id .= '@' . $listener[1];
                }
                $this->eventListeners[$name][$id] = function ($event) use ($listener) {
                    return $listener[0]->{$listener[1] ?? 'handle'}($event);
                };

                return $id;
            }
            $id = implode('@', $listener);
        } else {
            $id = $listener;
            $listener = explode('@', $listener);
        }
        $this->eventListeners[$name][$id] = function ($event) use ($listener) {
            return $this->container->get($listener[0])->{$listener[1] ?? 'handle'}($event);
        };

        return $id;
    }
}
