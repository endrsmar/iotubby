<?php

namespace Iotubby\Presenters;


use Iotubby\Forms\DeviceFormFactory;
use Iotubby\Model\DeviceRepository;
use Iotubby\Model\Entities\Device;
use Iotubby\Model\MeasurementManager;
use Iotubby\Model\MeasurementRepository;
use Iotubby\Services\DeviceApiService;
use Iotubby\Services\DeviceManager;
use Nette\Application\BadRequestException;
use Nette\Forms\Form;
use Tracy\Debugger;

class DevicePresenter extends BasePresenter
{

    /**
     * @var DeviceRepository
     * @inject
     */
    public $deviceRepository;

    /**
     * @var MeasurementRepository
     * @inject
     */
    public $measurementRepository;

    /**
     * @var DeviceFormFactory
     * @inject
     */
    public $deviceFormFactory;

    /**
     * @var DeviceManager
     * @inject
     */
    public $deviceManager;

    /**
     * @var Device
     */
    private $device;

    public function renderDefault()
    {
        $this->template->devices = $this->deviceRepository->getDevices();
    }

    /**
     * @return Form
     */
    public function createComponentDeviceForm(): Form
    {
        return $this->deviceFormFactory->createDeviceForm(
            function(Device $device) {
                $this->redirect('detail', $device->getId());
            },
            $this->device
        );
    }

    /**
     * GET /device/detail/<id>
     */
    public function actionDetail(int $id)
    {
        if (!($this->device = $this->deviceRepository->getDevice($id))) {
            throw new BadRequestException(404);
        }
        $this->template->device = $this->device;
    }

    public function handleToggle(int $id)
    {
        if (!($device = $this->deviceRepository->getDevice($id))) {
            throw new BadRequestException('Device not found');
        }
        if ($device->isOffline()) {
            throw new BadRequestException('Device is offline');
        }
        if ($device->isOn()) {
            $this->deviceManager->turnOff($device);
        } else {
            $this->deviceManager->turnOn($device);
        }
        if ($this->getAction() == 'default') {
            $this->template->devices = $this->deviceRepository->getDevices();
            $this->redrawControl('devices');
        } else {
            $this->template->device = $device;
            $this->redrawControl('dashboard');
        }
    }

    /**
     * GET /device/recent-measurements
     */
    public function actionRecentMeasurements()
    {
        $since = new \DateTime();
        $until = new \DateTime();
        $since->modify('-5 minutes');
        $data = [];
        foreach ($this->deviceRepository->getDevices() as $device) {
            $data[$device->getId()] = [
                'consumedTotal' => $device->getConsumedTotal(),
                'measurements' => []
            ];
            $measurements = $this->measurementRepository->getMeasurementsForDevice($device, $since, $until);
            if (empty($measurements)) {
                $data[$device->getId()]['measurements'][] = [
                    'time' => $since->format(\DateTime::ISO8601),
                    'value' => 0
                ];
                $data[$device->getId()]['measurements'][] = [
                    'time' => $until->format(\DateTime::ISO8601),
                    'value' => 0
                ];
                continue;
            }
            $data[$device->getId()]['measurements'][] = [
                'time' => $since,
                'value' => reset($measurements)->getValue()
            ];
            foreach ($measurements as $measurement) {
                $data[$device->getId()]['measurements'][] = [
                    'time' => $measurement->getTime()->format(\DateTime::ISO8601),
                    'value' => $measurement->getValue()
                ];
            }
            $data[$device->getId()]['measurements'][] = [
                'time' => $since,
                'value' => end($measurements)->getValue()
            ];
        }
        $this->sendJson($data);
    }

    /**
     * GET /device/delete/<id>
     */
    public function actionDelete(int $id)
    {
        if (!($device = $this->deviceRepository->getDevice($id))) {
            throw new BadRequestException(404);
        }
        $this->deviceRepository->delete($device);
        $this->redirect('default');
    }

}
