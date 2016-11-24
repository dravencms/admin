# Dravencms Admin module

This is a Admin module for dravencms

## Instalation

The best way to install dravencms/admin is using  [Composer](http://getcomposer.org/):


```sh
$ composer require dravencms/admin:@dev
```

Then you have to register extension in `config.neon`.

```yaml
extensions:
	admin: Dravencms\Admin\DI\AdminExtension
```
