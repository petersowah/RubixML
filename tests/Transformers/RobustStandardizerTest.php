<?php

namespace Rubix\ML\Tests\Transformers;

use Rubix\ML\Transformers\Stateful;
use Rubix\ML\Transformers\Transformer;
use Rubix\ML\Datasets\Generators\Blob;
use Rubix\ML\Transformers\RobustStandardizer;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class RobustStandardizerTest extends TestCase
{
    protected $transformer;

    protected $generator;

    public function setUp()
    {
        $this->generator = new Blob([0., 3000., -6.], [1., 30., 0.001]);

        $this->transformer = new RobustStandardizer(true);
    }

    public function test_build_transformer()
    {
        $this->assertInstanceOf(RobustStandardizer::class, $this->transformer);
        $this->assertInstanceOf(Transformer::class, $this->transformer);
        $this->assertInstanceOf(Stateful::class, $this->transformer);
    }

    public function test_fit_update_transform()
    {
        $this->transformer->fit($this->generator->generate(30));

        $this->assertTrue($this->transformer->fitted());

        $sample = $this->generator->generate(1)
            ->apply($this->transformer)
            ->row(0);

        $this->assertCount(3, $sample);
        
        $this->assertEquals(0., $sample[0], '', 6);
        $this->assertEquals(0., $sample[1], '', 6);
        $this->assertEquals(0., $sample[2], '', 6);
    }

    public function test_transform_unfitted()
    {
        $this->expectException(RuntimeException::class);

        $this->generator->generate(1)
            ->apply($this->transformer);
    }
}
