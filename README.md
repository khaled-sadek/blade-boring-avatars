# blade-boring-avatars

[![tests](https://github.com/khaled-sadek/blade-boring-avatars/actions/workflows/tests.yml/badge.svg)](https://github.com/khaled-sadek/blade-boring-avatars/actions/workflows/tests.yml)

A Blade version of [Boring Avatars](https://github.com/boringdesigners/boring-avatars). Built using Laravel Blade.

Based on Boring Avatar's description,
> Boring Avatars a tiny JavaScript React library that generates custom, SVG-based, round avatars from any username and color palette.

This package provides a Blade component you can use directly in your Laravel projects.

## Features

- Similar API with the React version of [Boring Avatars](https://github.com/boringdesigners/boring-avatars).

## Installation

```bash
composer require khaled-sadek/blade-boring-avatars
```

## Compatibility

- **PHP**: 8.2 or higher
- **Laravel**: 10 – 12

> **Note for Laravel 7-9 users**: If you're using an older version of Laravel, please use version 1.x of this package which supports PHP 8.1 and Laravel 7-9.

This package auto-discovers its service provider, so no manual registration is required.

## Props

Props:

- `size`: number
  - Default: `40`
- `name`: string
  - Default: `"Clara Barton"`
- `colors`: array[]
  - Accepts a php array of colors.
  - Default: `["#92A1C6", "#146A7C", "#F0AB3D", "#C271B4", "#C20D90"]`

## Usage

Basic usage (with default props):

```html
<x-avatar />
```

With props:

```html
  <!--
      view.blade.php
      Where $colors is php valid array
  -->
  <x-avatar size="80" name="Khaled Sadek" :colors="$colors" />
```

Backward compatibility: the PascalCase tag `<x-Avatar />` remains available.

## Credits

Credits to [@hihayk](https://twitter.com/hihayk) ([GitHub](https://github.com/hihayk)) and [@josep_martins](https://twitter.com/josep_martins) ([GitHub](https://github.com/josepmartins)) for creating the original [Boring Avatars](https://github.com/boringdesigners/boring-avatars) library at [boringdesigners](https://github.com/boringdesigners)!
