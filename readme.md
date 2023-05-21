# 项目介绍

本项目是一个基于事件和监听器的作业调度系统，用于管理和执行异步任务。它提供了以下核心组件：

- `Job` 接口：定义了任务处理的接口规范。
- `Listener` 抽象类：提供了事件监听器的基础实现，可以通过继承该抽象类来定义具体的事件监听器。
- `Job` 类：任务调度类，负责将事件分发到任务处理或事件监听器进行处理。
- `Execute` 类：任务执行类，负责从队列中获取任务并执行。
- `JobsQueue` 类：任务队列类，用于管理任务队列。
- `Message` 类：消息类，表示从队列中获取的消息。

完整示例代码见 [/example/index.php](/example/index.php)

## 使用示例

以下示例演示了如何使用 EnjoyJob 调度和执行作业：

```php
<?php

use EnjoyJob\Job;
use EnjoyJobExample\Event\TestEvent;

require dirname(__DIR__) . '/vendor/autoload.php';

// 调度 TestEvent 作业
Job::dispatch(new TestEvent([
    'a' => 'aa',
    'b' => 'bb',
    'c' => 'cc',
]));

// 执行作业队列中的作业
Job::execute();
```


## 定义事件和处理器

要使用 EnjoyJob，你需要定义事件和处理器。以下是示例代码：

```php
<?php

namespace EnjoyJobExample\Event;

use EnjoyJob\Contracts\Event;

class TestEvent implements Event
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function listeners(): array
    {
        // 返回该事件对应的监听器类
        return [
            TestListener::class,
        ];
    }

    public function getData(): array
    {
        return $this->data;
    }
}
```

```php
<?php

namespace EnjoyJobExample\Event;

use EnjoyJob\Contracts\Job;
use EnjoyJob\Contracts\Listener;

class TestListener extends Listener implements Job
{
    public function handle($attempts)
    {
        // 在这里处理事件
        $data = $this->event->getData();
        // ...

        // 如果处理失败，你可以抛出异常或者返回 false
        // throw new \Exception('处理失败');
        // return false;
    }
}
```

在上面的示例中，`TestEvent` 类实现了 `Event` 接口，并定义了事件的监听器。`TestListener` 类继承自 `Listener` 抽象类，并实现了 `Job` 接口，用于处理事件。

## 运行作业

你可以使用 `Job::dispatch()` 方法来调度事件。如果事件实现了 `Job` 接口，它将作为作业进行调度。如果事件没有实现 `Job` 接口，它将直接执行事件的处理逻辑。

要执行作业队列中的作业，可以使用 `Job::execute()` 方法。

## 自定义作业队列

EnjoyJob 使用 Redis 作为默认的作业队列，但你可以根据自己的需求自定义作业队列。可以通过继承 `EnjoyJob\Support\Queue\JobsQueue` 类并实现相应的方法来创建自定义的作业队列。