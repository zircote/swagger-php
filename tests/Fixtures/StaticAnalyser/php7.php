<?php declare(strict_types=1);

namespace OpenApiFixures;

$o = new class
{
    public function foo()
    {
    }
};

$o = new class extends stdClass
{
};

$o = new class implements foo
{
};

$o = new class()
{
    public function foo()
    {
    }
};

$o = new class() extends stdClass
{
};

$o = new class() implements foo
{
};
