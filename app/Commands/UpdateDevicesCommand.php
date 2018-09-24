<?php
declare(strict_types=1);

namespace Iotubby\Commands;


use Iotubby\Model\DeviceRepository;
use Iotubby\Services\DeviceManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateDevicesCommand extends Command
{


    /**
     * @var DeviceManager
     */
    private $deviceManager;
    /**
     * @var DeviceRepository
     */
    private $deviceRepository;

    public function __construct(
        DeviceManager $deviceManager,
        DeviceRepository $deviceRepository)
    {
        parent::__construct(null);
        $this->deviceManager = $deviceManager;
        $this->deviceRepository = $deviceRepository;
    }

    protected function configure()
    {
        $this->setName('devices:update')
            ->setDescription('Updates devices measurements');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->deviceRepository->getDevices() as $device) {
            $this->deviceManager->pullMeasurements($device);
        }
        $output->writeln('Devices updated');
    }

}