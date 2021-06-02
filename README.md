# blade-boring-avatars

A Blade version of [Boring Avatars](https://github.com/boringdesigners/boring-avatars). Built using Laravel Blade.

Based on Boring Avatar's description,
> Boring Avatars a tiny JavaScript React library that generates custom, SVG-based, round avatars from any username and color palette.

Here I make a blade component to use in your laravel projects

## Features

- Similar API with the React version of [Boring Avatars](https://github.com/boringdesigners/boring-avatars).

## Installation

```bash
composer require khaled-sadek/blade-boring-avatars
```

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
<Avatar />
```

With props:

```html
  <!--
      view.blade.php
      Where $colors is php valid array
  -->
  <Avatar size="80" name="Khaled Sadek" :colors="$colors" />
```

## Credits

Credits to [@hihayk](https://twitter.com/hihayk) ([GitHub](https://github.com/hihayk)) and [@josep_martins](https://twitter.com/josep_martins) ([GitHub](https://github.com/josepmartins)) for creating the original [Boring Avatars](https://github.com/boringdesigners/boring-avatars) library at [boringdesigners](https://github.com/boringdesigners)!
