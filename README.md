# Taxonomies

This add-on package will allow for the creation of taxonomies that will be
available globally. A primary purpose is also the addition of the control panel
edit form - enabling content editors on the front-end the ability to add, delete
or modify as needed without touching yaml.

## Installation

Add the package to add-ons. If you are using the control panel, a taxonomies.yaml
file in config/add-ons will created automatically. If you are not using the CP,
create the following file/path: config/add-ons/taxonomies.yaml.

The yaml for that config file should be created with the following example
stucture:

```
---
taxonomy_groups:
  -
    type: taxonomy_group
    taxonomy_group_slug: xxxxx
    taxonomies:
      -
        taxonomy_name: xxxxx
        taxonomy_slug: xxxxx
  -
    type: taxonomy_group
    taxonomy_group_slug: yyyyy
    taxonomies:
      -
        taxonomy_name: yyyyy
        taxonomy_slug: yyyyy
```

## The Control Panel

If you wish to enable the top navigation shortcut, add taxonomies:true to
config/settings.yaml:

```
_admin_nav:
  taxonomies:true
```

## Plugin Tags

There are two plugin tags available, one for fetching the taxonomies for a given
group, and another for fetching a label from a value.

### Listing tag pair (plugin)

```
{{ taxonomies:listing group_slug="taxonomy_group_slug" }}
  {{ if index == 1 }}
  <ul>
  {{ endif }}
    <li><a href="{{ current_url }}/categories/{{ value }}">{{ label }}</a></li>
  {{ if index == total_options }}
  <ul>
  {{endif}}
{{ /taxonomies:listing }}
```

### Name tag pair (plugin)

```
{{ categories }}
  {{ taxonomies:name slug="{{ value }}" group_slug="taxonomy_group_slug" }}
{{ /categories }}
```

## Modifier

In addition to the plugin method taxonomies:name, there is a modifier for
retrieving the taxonomy label.

```
{{ categories }}
  {{ value|taxonomies:taxonomy_group_slug }}
{{ /categories }}
```
