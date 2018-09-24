<?php
declare(strict_types=1);

namespace Iotubby\Services;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Iotubby\Model\DeviceRepository;
use Iotubby\Model\Entities\Device;
use Iotubby\Model\Entities\Measurement;
use Iotubby\Model\Entities\Session;
use Iotubby\Model\MeasurementRepository;
use Iotubby\Model\SessionRepository;

class RESTDeviceManager implements DeviceManager
{

    private const MEASUREMENTS_ENDPOINT = '/measurements',
                  ON_ENDPOINT = '/on',
                  OFF_ENDPOINT = '/off',
                  FAILED_ATTEMPTS_TOLERANCE = 3,
                  REQUEST_TIMEOUT = 1;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var SessionRepository
     */
    private $sessionRepository;

    /**
     * @var DeviceRepository
     */
    private $deviceRepository;
    /**
     * @var MeasurementRepository
     */
    private $measurementRepository;

    /**
     * @param DeviceRepository $deviceRepository
     * @param SessionRepository $sessionRepository
     */
    public function __construct(
        DeviceRepository $deviceRepository,
        SessionRepository $sessionRepository,
        MeasurementRepository $measurementRepository)
    {
        $this->deviceRepository = $deviceRepository;
        $this->sessionRepository = $sessionRepository;
        $this->measurementRepository = $measurementRepository;
    }

    /**
     * @return Client
     */
    private function getClient(): Client
    {
        if (!$this->client) {
            $this->client = new Client();
        }
        return $this->client;
    }

    /**
     * @param Device $device
     */
    private function closeSession(Device $device): void
    {
        if ($device->isOn()) {
            $session = $this->sessionRepository->getSession($device->getSessionId());
            $session->endSession();
            $this->sessionRepository->save($session);
        }
    }

    /**
     * @param Device $device
     * @return Session|null
     */
    private function openSession(Device $device): ?Session
    {
        if (!$device->isOn()) {
            $session = new Session(null, $device->getId(), new \DateTime(), null);
            $this->sessionRepository->save($session);
            return $session;
        }
        return null;
    }

    /**
     * @param Device $device
     */
    private function failedAttempt(Device $device): void
    {
        if ($device->getFailedAttempts() < self::FAILED_ATTEMPTS_TOLERANCE) {
            $device->incrementFailedAttempts();
        } else {
            $this->closeSession($device);
            $device->setOffline();
        }
        $this->deviceRepository->save($device);
    }

    /**
     * @param Device $device
     * @param string $path
     * @return array|null
     */
    private function makeRequest(Device $device, string $path): ?array
    {
        try {
            $res = $this->getClient()->get(
                "{$device->getAddress()}:{$device->getPort()}$path",
                ['timeout' => self::REQUEST_TIMEOUT]
            );
        } catch (RequestException $e) {
            $this->failedAttempt($device);
            return null;
        }
        return json_decode($res->getBody()->getContents(), true);
    }

    /**
     * @param Device $device
     */
    public function pullMeasurements(Device $device): void
    {
        if (!($data = $this->makeRequest($device, self::MEASUREMENTS_ENDPOINT))) {
            return;
        }
        if ($data['status'] == 'off') {
            if ($device->isOn()) {
                $this->closeSession($device);
            }
            if (!$device->isOff()) {
                $device->setOff();
                $this->deviceRepository->save($device);
            }
            return;
        } else {
            if (!$device->isOn()) {
                $session = $this->openSession($device);
                $device->setOn($session->getId());
                $this->deviceRepository->save($device);
            }
            $this->processMeasurements($device, $data);
        }
    }

    /**
     * @param Device $device
     * @param $data
     */
    private function processMeasurements(Device $device, $data)
    {
        $measurements = [];
        $sessionStart = \DateTime::createFromFormat(\DateTime::ISO8601, $data['start']);
        $lastMeasurement = $this->measurementRepository->getLastMeasurementInSession($device->getSessionId());
        $consumedTotal = $device->getConsumedTotal();
        foreach ($data['measurements'] as $measurement) {
            $time = clone $sessionStart;
            $time->modify('+'.$measurement['time'].' seconds');
            $measurements[] = $measurement = new Measurement(
                null,
                $device->getId(),
                $time,
                $measurement['value'],
                $device->getSessionId()
            );
            if ($lastMeasurement) {
                $avgCurrent = ($lastMeasurement->getValue() + $measurement->getValue()) / 2;
                $watt = $avgCurrent*230;
                $duration = $measurement->getTime()->getTimestamp() - $lastMeasurement->getTime()->getTimestamp();
                $consumedTotal += ($watt / 1000) * ($duration / 3600);
            }
            $lastMeasurement = $measurement;
        }
        $device->setConsumedTotal($consumedTotal);
        $this->measurementRepository->saveBulk($measurements);
        $this->deviceRepository->save($device);
    }

    /**
     * @param Device $device
     */
    public function turnOn(Device $device): void
    {
        if ($device->isOn()) {
            return;
        }
        if (!($data = $this->makeRequest($device, self::ON_ENDPOINT.'/'.(new \DateTime())->format(\DateTime::ISO8601)))) {
            return;
        }
        if ($data['success']) {
            $session = $this->openSession($device);
            $device->setOn($session->getId());
            $this->deviceRepository->save($device);
        }
    }

    /**
     * @param Device $device
     */
    public function turnOff(Device $device): void
    {
        if (!($data = $this->makeRequest($device, self::OFF_ENDPOINT))) {
            return;
        }
        if ($data['success']) {
            $this->processMeasurements($device, $data);
            $this->closeSession($device);
            $device->setOff();
            $this->deviceRepository->save($device);
        }
    }

}