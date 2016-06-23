# Zver\FileCache

## Table of Contents

* [FileCache](#filecache)
    * [disable](#disable)
    * [enable](#enable)
    * [set](#set)
    * [getDirectory](#getdirectory)
    * [setDirectory](#setdirectory)
    * [setGroup](#setgroup)
    * [get](#get)
    * [getGroup](#getgroup)
    * [clearAll](#clearall)
    * [clearGroup](#cleargroup)
    * [clearKey](#clearkey)
    * [clearGroupKey](#cleargroupkey)
    * [retrieve](#retrieve)
    * [retrieveGroup](#retrievegroup)

## FileCache

Caching system with grouping support using file system



* Full name: \Zver\FileCache


### disable

Disable caching, get() always return null

```php
FileCache::disable(  )
```



* This method is **static**.



---

### enable

Enable caching

```php
FileCache::enable(  )
```



* This method is **static**.



---

### set

Add data to cache associated with group and key

```php
FileCache::set( string $key, mixed $value ): integer
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$key` | **string** |  |
| `$value` | **mixed** |  |




---

### getDirectory

Get current cache directory

```php
FileCache::getDirectory(  ): string
```



* This method is **static**.



---

### setDirectory

Set directory to store cache data

```php
FileCache::setDirectory(  $directory )
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$directory` | **** |  |




---

### setGroup

Add data to cache associated with group and key

```php
FileCache::setGroup( null $group, string $key, mixed $value ): integer
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$group` | **null** |  |
| `$key` | **string** |  |
| `$value` | **mixed** |  |




---

### get

Get value from cache if exists, null otherwise

```php
FileCache::get(  $key ): mixed|null
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$key` | **** |  |




---

### getGroup

Get value from cache if exists, null otherwise

```php
FileCache::getGroup(  $group,  $key ): mixed|null
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$group` | **** |  |
| `$key` | **** |  |




---

### clearAll

Clear all cache data with cache directory

```php
FileCache::clearAll(  )
```



* This method is **static**.



---

### clearGroup

Clear all group data. If no argument passed, default group data will cleared

```php
FileCache::clearGroup( null $group = null )
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$group` | **null** | Group name |




---

### clearKey

Clear data according to key

```php
FileCache::clearKey(  $key )
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$key` | **** |  |




---

### clearGroupKey

Clear data according to key in group

```php
FileCache::clearGroupKey(  $group,  $key )
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$group` | **** |  |
| `$key` | **** |  |




---

### retrieve

Get value from cache if it exist, otherwise return callback result and set callback result to cache for
future usage. It's combination of get() and set()

```php
FileCache::retrieve(  $key,  $callback ): mixed|null
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$key` | **** |  |
| `$callback` | **** |  |




---

### retrieveGroup

Get value from cache if it exist, otherwise return callback result and set callback result to cache for
future usage. It's combination of get() and set()

```php
FileCache::retrieveGroup( null $group,  $key,  $callback ): mixed|null
```



* This method is **static**.
**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$group` | **null** |  |
| `$key` | **** |  |
| `$callback` | **** |  |




---



--------
> This document was automatically generated from source code comments on 2016-06-23 using [phpDocumentor](http://www.phpdoc.org/) and [cvuorinen/phpdoc-markdown-public](https://github.com/cvuorinen/phpdoc-markdown-public)
