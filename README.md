# Architect
Software architecture toolkit for Laravel.

> /!\ THIS PROJECT IS A WORK IN PROGRESS

## Concepts
Architect helps artisans to structure, generate and write maintainable code from human-readable migration-like files called "sprints". It is built on top of the popular [Laravel Framework](https://github.com/laravel), and implement concepts form the [Lucid Architecture](https://github.com/lucid-architecture).

### Features
Features are classes meant to unclog your controllers. Each feature is a sequence of tasks. They can either be served by controllers, via the console, and can be queued. They should be testable.

### Tasks
Tasks are meant to be the smallest pieces of logic within your app. They are responsible for one specific thing. Tasks should not use other tasks. They should be isolated, and testable. They extend the Laravel Queuable functionality. 

### Sprints (WIP)
Think of sprints as migrations for your features. It will generate features and tasks, and their respective tests. The generated code is boilerplate code: you'll still have to write your business logic within the generated classes, but it is intended to save you time and plan ahead which classes and methods you are going to build to fulfill the business objectives.

Example of a sprint:
```php
Architect::feature('my awesome feature', function (Blueprint $code) {
    $code->will('do something')->expect('request')->return('foo');
    $code->will('do another thing')->expect('foo')->return('bar');
    $code->will('one last thing')->expect('foo')->return('baz');
});
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
