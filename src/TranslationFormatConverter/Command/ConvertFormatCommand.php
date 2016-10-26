<?php

namespace Bones\TranslationFormatConverter\Command;

use Bones\TranslationFormatConverter\Model\XliffInputFile;
use Bones\TranslationFormatConverter\Model\YamlOutputFile;
use JMS\TranslationBundle\Translation\Dumper\YamlDumper;
use JMS\TranslationBundle\Translation\Loader\XliffLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class ConvertFormatCommand extends Command
{

    const INPUT_SOURCE = 'source';
    const OUTPUT_DIR = 'out';
    const LOCALE = 'locale';

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    public function configure()
    {
        $this->setName('convert:format')
            ->setDescription('convert format from jms-bundle-xliff to yaml')
            ->addArgument(self::LOCALE, InputArgument::REQUIRED, 'the locale you want to convert')
            ->addArgument(self::INPUT_SOURCE, InputArgument::REQUIRED, 'source dir or source file')
            ->addArgument(self::OUTPUT_DIR, InputArgument::REQUIRED, 'output dir');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->fileSystem = new Filesystem();

        $source = $input->getArgument(self::INPUT_SOURCE);
        $locale = $input->getArgument(self::LOCALE);

        $destinationDirectory = $input->getArgument(self::OUTPUT_DIR);

        if (!$this->fileSystem->exists($source)) {
            $output->writeln('ERROR: unable to read '.$source);

            return;
        }
        
        if (!is_dir($destinationDirectory)) {
            $output->writeln('ERROR: unable to read '.$source);

            return;
        }

        if (is_dir($source)) {
            foreach (glob($source.'*'.$locale.'.xliff') as $xliffSource) {
                $this->process($output, $xliffSource, $destinationDirectory);
            }
        } else {
            $this->process($output, $source, $destinationDirectory);
        }

        $output->writeln('executed');
    }

    /**
     * @param XliffInputFile  $XliffFile
     *
     * @return YamlOutputFile
     */
    private function createYamlFromXliff(XliffInputFile $XliffFile)
    {
        $xliffLoader = new XliffLoader();
        $messageCatalogue = $xliffLoader->load($XliffFile->getResource(), $XliffFile->getLocale(), $XliffFile->getDomain());

        $ymlDumper = new YamlDumper();
        $string = $ymlDumper->dump($messageCatalogue, $XliffFile->getDomain());

        $ymlFileName = $this->createFileName($XliffFile->getDomain(), $XliffFile->getLocale(), 'yml');

        return new YamlOutputFile($ymlFileName, $string);
    }

    /**
     * @param OutputInterface $output
     * @param $source
     * @param $destinationDirectory
     */
    protected function process(OutputInterface $output, $source, $destinationDirectory)
    {
        try {
            $xliffFile = new XliffInputFile($source);
            $ymlFile = $this->createYamlFromXliff($xliffFile);
            $ymlFilePath = $destinationDirectory . '/' . $ymlFile->getFileName();

            $output->writeln("Writing File ". $ymlFilePath);

            $this->fileSystem->dumpFile($ymlFilePath, $ymlFile->getContent());

        } catch (\Exception $e) {
            $output->writeln('ERROR: ' . $e->getMessage());
        }
    }

    protected function createFileName($domain, $locale, $extension)
    {
        return sprintf('%s.%s.%s', $domain, $locale, $extension);
    }

}
