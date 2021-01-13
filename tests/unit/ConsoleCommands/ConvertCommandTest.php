<?php
declare(strict_types=1);

namespace UnitTest\ConsoleCommands;

use FileStructureConvertor\ConsoleCommands\ConvertCommand;
use Symfony\Component\Console\Tester\CommandTester;
use TestHelper\TestCase;

class ConvertCommandTest extends TestCase
{
    const FIXTURE = __DIR__ . '/../../fixture';
    private CommandTester $tester;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tester = new CommandTester(new ConvertCommand());
    }

    /**
     * @dataProvider exectueProvider
     */
    public function testExecute(string $inputPath, string $outputType, string $expectedFile)
    {
        $this->tester->execute([
            'inputPath' => $inputPath,
            'outputType' => $outputType,
        ]);
        $output = trim($this->tester->getDisplay());
        $this->assertStringEqualsFile($expectedFile, $output);
    }

    public function exectueProvider()
    {
        $f = self::FIXTURE;

        yield 'json -> yaml' => ["$f/test.json", 'yaml', "$f/test.yaml"];
        yield 'json -> yml' => ["$f/test.json", 'yml', "$f/test.yaml"];
        yield 'yaml -> json' => ["$f/test.yaml", 'json', "$f/test.json"];
        yield 'yml -> json' => ["$f/test.yml", 'json', "$f/test.json"];
    }

    public function testErrorWhenInputAndOutputTypeAreSame()
    {
        $this->expectExceptionMessage("Don't need to convert. Both type of input and ouput are same");

        $fixture = self::FIXTURE . '/test.json';
        $this->tester->execute([
            'inputPath' => $fixture,
            'outputType' => 'json',
        ]);
    }
}
