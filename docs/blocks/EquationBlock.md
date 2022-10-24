# Embed

Display a standalone math equation.

## Create

```php
$block = EquationBlock::create("
    |x| = \\begin{cases}
    x, &\\quad x \geq 0 \\\\
    -x, &\\quad x < 0
    \\end{cases}
");
```

![](../images/equation-block.png)

## Change equation

```php
$block = EquationBlock::create("a^2 + b^2 = c^2");
$newEquation = Equation::create("E = m * c^2");
$block = $block->changeEquation($newEquation);
```
