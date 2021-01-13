# File Structure Convertor Cli

Convert file structure to other one. For example, yaml to json.

## Get Started

You can convert `yaml <-> json`.

```
> bin/convert dir1/input.yaml json
{
    "users": {
        "mike": {
            "age": 19
        }
    },
    "join": [
        "mike"
    ]
} 

> cat dir1/input.yaml | bin/convert '' -I yaml json
{
    "users": {
        "mike": {
            "age": 19
        }
    },
    "join": [
        "mike"
    ]
} 
```
```
