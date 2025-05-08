# Custom Navigation

## Setup

### Get Menu Navigation

This plugin uses a full site editor (FSE) menu block.
1. Create a menu in the editor
2. Put the menu into a page or header or some place momentarily via the gutenberg menu block
3. In the upper right of the screen hit the three vertical dots and click 'Code editor'
4. Look for 'wp:navigation' and you will see 'ref' it will look something like

```html
wp:navigation {"ref":4,...
```
This shows the id you will use in the shortcode next.

### Menu Shortcode

1. Go to your page or post
2. Put this shorctode on the page
```html
[theme-menu id=x]
```
3. In the shortcode above you would replace 'x' with your navigation id (ex. 4)

### Hamburger Toggle Shortcode

The menu needs a toggle action so you have to put the following shortcode in the header some place

```html
[lj-menu-toggle]
```

## Note

- Any customization to styles should be made in this plugin
- Customizations are likely and based on your header setup