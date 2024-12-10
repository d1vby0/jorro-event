<?php

namespace Jorro\Event;

interface EventDispatcherInterface
{
    /**
     * イベントのディスパッチ
     *
     * @param mixed $event イベントオブジェクト
     * @return void
     */
    public function dispatch(mixed $event): void;

    /**
     * イベントリスナの登録
     *
     * @param string $name イベント名
     * @param string|array|\Closure $listener イベントリスナ クラス@メソッド名(省略時はhandle) もしくは クロージャ
     * @return int|string 登録ID
     */
    public function listen(string $name, string|array|\Closure $listener): int|string;
}