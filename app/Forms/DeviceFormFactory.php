<?php
declare(strict_types=1);

namespace Iotubby\Forms;


use Iotubby\Helpers\IpValidator;
use Iotubby\Model\DeviceRepository;
use Iotubby\Model\Entities\Device;
use Iotubby\Model\Enums\DeviceStatus;
use Nette\Forms\Form;

class DeviceFormFactory
{

    /**
     * @var FormFactory
     */
    private $formFactory;
    /**
     * @var DeviceRepository
     */
    private $deviceRepository;

    /**
     * @param FormFactory $formFactory
     * @param DeviceRepository $deviceRepository
     */
    public function __construct(FormFactory $formFactory, DeviceRepository $deviceRepository)
    {
        $this->formFactory = $formFactory;
        $this->deviceRepository = $deviceRepository;
    }

    /**
     * @param callable $callback
     * @param Device|null $device
     * @return Form
     */
    public function createDeviceForm(callable $callback, ?Device $device): Form
    {
        $form = $this->formFactory->createForm();
        $form->addHidden('id');
        $form->addText('name', 'Name:')
            ->setRequired(true)
            ->addRule(Form::MAX_LENGTH, 'Name can only be %value characters long', Device::NAME_MAX_LENGHT);
        $form->addText('address', 'IP Address:')
            ->setRequired(true);
        $form->addInteger('port', 'Port:')
            ->setRequired(true);
        $form->addSubmit('submit', 'Save');
        if ($device) {
            $form->setDefaults([
                'id' => $device->getId(),
                'name' => $device->getName(),
                'address' => $device->getAddress(),
                'port' => $device->getPort()
            ]);
        }

        $form->onSuccess[] = $this->createDeviceFormProcessor($callback);
        return $form;
    }

    /**
     * @param callable $callback
     * @return callable
     */
    private function createDeviceFormProcessor(callable $callback): callable
    {
        return function(Form $form, $values) use ($callback) {
            if ($values['id']) {
                if (!($device = $this->deviceRepository->getDevice(intval($values['id'])))) {
                    $form->addError('Corrupted request');
                    return;
                }
                $device->setName($values['name'])
                    ->setAddress($values['address'])
                    ->setPort($values['port']);
            } else {
                $device = new Device(null, $values['name'], $values['address'], $values['port']);
            }
            $this->deviceRepository->save($device);
            $callback($device);
        };
    }

}