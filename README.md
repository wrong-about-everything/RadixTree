This library builds a radix tree and outputs it as a php code, as well as a regular expression.
[Here is a post](https://medium.com/p/725e42e7f42f) explaining the source code. It covers fundamentals of Radix trees. Also, it visualizes an algorithm for inserting a new leaf node, as well as an algorithm for looking up a leaf node.

Run tests with

```
php ../vendor/phpunit/phpunit/phpunit --exclude-group slow ./
```

If you want to run slow tests, run them one by one since they are very demanding memory-wise.
