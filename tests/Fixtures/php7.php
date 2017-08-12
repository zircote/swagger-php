<?php declare(strict_types=1);

namespace SwaggerFixures;

$o = new class {
    public function foo()
    {
    }
};

$o = new class extends stdClass {
};

$o = new class implements foo {
};

$o = new class() {
    public function foo()
    {
    }
};

$o = new class() extends stdClass {
};

$o = new class() implements foo {
};
