<?php
declare(strict_types=1);

namespace FileStructureConvertor\ConsoleCommands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

final class ConvertCommand extends Command
{
    protected static $defaultName = 'convert';

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('convert file structure to other one.')
            ->addArgument('inputPath', InputArgument::REQUIRED, 'input file')
            ->addArgument('outputType', InputArgument::REQUIRED, 'output format')
            ->addOption('input-type', 'I', InputOption::VALUE_OPTIONAL, 'If the file input name has no extension, use this.');
    }

    /**
     * @throws \JsonException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $outputType = $input->getArgument('outputType');

        $path = $input->getArgument('inputPath');
        if ($path === '') {
            $content = file_get_contents('php://stdin');
            $inputType = $input->getOption('input-type');
        } else {
            [$inputPath, $inputType] = $this->checkInputType($input);
            if ($inputType === $outputType) {
                $err = "Don't need to convert. Both type of input and ouput are same.";
                throw new \RuntimeException($err);
            }
            $content = file_get_contents($inputPath);
        }

        $decoded = $this->decode($inputType, $content);
        $encoded = $this->encode($outputType, $decoded);
        $output->write($encoded);
        return Command::SUCCESS;
    }

    /**
     * @return array{string, string}
     */
    private function checkInputType(InputInterface $input): array
    {
        $path = $input->getArgument('inputPath');

        $fileType = pathinfo($path, PATHINFO_EXTENSION);
        if (!empty($fileType)) {
            return [$path, $fileType];
        }

        $fileType = $input->getOption('input-type');
        if (!is_string($fileType)) {
            $err = sprintf("Don't know file type from file name or option: %s", $path);
            throw new \InvalidArgumentException($err);
        }
        return [$path, $fileType];
    }

    /**
     * @return mixed
     */
    private function decode(string $decodeType, string $text)
    {
        switch ($decodeType) {
            case 'json':
                return json_decode($text, true);
            case 'yml':
            case 'yaml':
                return Yaml::parse($text);
            default:
                throw new \RuntimeException("Invalid input file type: $decodeType");
        }
    }

    /**
     * @param mixed $value
     */
    private function encode(string $encodeType, $value): string
    {
        switch ($encodeType) {
            case 'json':
                return json_encode($value, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
            case 'yaml':
            case 'yml':
                return Yaml::dump($value);
            default:
                throw new \RuntimeException("Invalid output file type: $encodeType");
        }
    }
}

