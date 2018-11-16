# Architect
Software architecture toolkit for Laravel.

## Introduction

When your code base starts to grow, it becomes harder and harder to release new features without breaking something along the way. Architect's mission is to help you organize your code in a way that is easily maintainable and testable, while letting you release new features harmlessly. 

Architect is built on top of the awesome [Laravel Framework](https://github.com/laravel) by Taylor Otwell, and implements concepts form the mind opening [Lucid Architecture](https://github.com/lucid-architecture) by Abed Halawi.

> ⚠️ Work in progress (first release coming soon).

## Concepts

### Features
Features are classes meant to unclog your controllers. Each feature runs a sequence of tasks, making it easy to read and maintain. They can either be implemented by controllers, the console, and can be queued. Each feature should have an associated test (automatically created when using sprints).

### Tasks
Tasks are meant to be the smallest pieces of logic within your app. They are responsible for one specific thing. Tasks should not use or require other tasks to perform their duty. They should be isolated, and testable. They extend the Laravel Queuable functionality. 

### Sprints (WIP)
Think of sprints as migrations for your code. It will generate features and tasks, and their respective tests. The generated code is boilerplate code: you'll still have to write your business logic within the generated classes, but it is intended to save you time and plan ahead which classes and methods you are going to write to fulfill the business objectives.

First, generate a sprint file by running:
```bash
php artisan make:sprint "my awesome feature"
```

This will create sprint file under /sprints:
```php
...

public function run()
{
    Architect::feature('my awesome feature', function (Blueprint $code) {
        $code->will('do something')->expect('request')->return('foo');
        $code->will('do another thing')->expect('foo')->return('bar');
        $code->will('one last thing')->expect('foo')->return('baz');
    });
}
```
Executing:
```bash
php artisan sprint
```

Will generate the following classes:
```php
App\Features\MyAwesomeFeature;
App\Tasks\DoSomething;
App\Tasks\DoAnotherThing;
App\Tasks\OneLastThing;
App\Tests\Feature\MyAwesomeFeatureTest;
App\Tests\Unit\DoSomethingTest;
App\Tests\Unit\DoAnotherThingTest;
App\Tests\Unit\OneLastThingTest;
```

```php
class MyAwesomeFeature extends Feature 
{
    public function handle($request) 
    {
        $foo = $this->task(\App\Tasks\DoSomething::class, ['request' => $request]);
        $bar = $this->task(\App\Tasks\DoAnotherThing::class, ['foo' => $foo]);
        $baz = $this->task(\App\Tasks\OneLastThing::class, ['foo' => $foo]);

        return $baz;
    }
}
```
## Why build Architect instead of using Lucid?
When I heard Abed say at Laracon EU ["No more legacy code"](https://www.youtube.com/watch?v=wSnM4JkyxPw), I was totally seduced by that idea. But when I dived into the [Lucid Architecture](https://github.com/lucid-architecture), it didn't quite fit my routine: I had to move too many files here and there and tear off too many roots from my code base in order to adopt it. It would have been tedious to adopt this package. Also, I wanted to keep the Laravel folders in place as I love how the vanilla framework is organized. So I built Architect, which simply adds App\Features and App\Tasks folders in your app/ directory.
