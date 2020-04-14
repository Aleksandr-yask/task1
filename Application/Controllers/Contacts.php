<?php

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use MVC\Controller;

class ControllersContacts extends Controller
{
    private $phoneUtil;

    public function __construct() {
        parent::__construct();
        $this->model = $this->model('contacts');

    }

    /**
     * @POST
     * @params: {
     *  source_id: 1,
     *  items: [{
     *  "name": "Анна",
     *  "phone": 9001234453,
     *  "email": "mail1@gmail.com"
     *  }, {
     *  "name": "Иван",
     *  "phone": "+79001234123",
     *  "email": "mail2@gmail.com"
     *  }]
     *  }
     * @throws Exception
     */
    public function addContacts(): void
    {
        $source = (int) $this->data['source_id'];
        $now = time();

        foreach ($this->data['items'] as $item) {
            if (!filter_var($item['email'], FILTER_VALIDATE_EMAIL))
                throw new Exception("Email $item[email] не корректен, ни один номер не записан", 400);

            $phoneUtil = PhoneNumberUtil::getInstance();
            try {
                $swissNumberProto = $phoneUtil->parse($item['phone'], "RU");
            } catch (NumberParseException $e) {
                throw new Exception("Номер телефона $item[phone] не корректен, ни один номер не записан", 400);
            }
            $item['phone'] = (int) $swissNumberProto->getNationalNumber();


            if ($this->model->isContactAdded($item['phone'], $source, $now))
                throw new Exception("Номер $item[phone] уже был добавлен в течении последних 24 часов, ни один из номеров не добавлен", 400);

        }


        $notAddedNumbers = [];
        foreach ($this->data['items'] as $item) {
            if (!$this->model->addContact($item['name'], $item['phone'], $item['email'], $source, $now)) {
                $notAddedNumbers[] = $item['phone'];
            }
        }


        if (count($notAddedNumbers) > 0) {
            $this->response->sendStatus(200);
            $this->response->setContent([
                'status'=> 'NotAllAdded',
                'text' => 'Не все номера были добавлены',
                'body' => $notAddedNumbers
            ]);
        } else {
            $this->response->sendStatus(200);
            $this->response->setContent([
                'status'=> 'Ok',
                'text' => 'Номера добавлены',
                'body' => []
            ]);
        }

        return;
    }

    /**
     * get contacts
     */
    public function getContacts(): void
    {
        $phone = $this->prms['phone'];
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $swissNumberProto = $phoneUtil->parse($phone, "RU");
        } catch (NumberParseException $e) {
            throw new Exception("Номер телефона $phoneUtil не корректен", 400);
        }
        $phoneUtil = (int) $swissNumberProto->getNationalNumber();

        if ($data = $this->model->getByNumber($phoneUtil)) {
            $this->response->sendStatus(200);
            $this->response->setContent([
                'status'=> 'Ok',
                'body' => $data
            ]);
        } else {
            $this->response->setContent(['body' => 'Не найдено'], 'error', 404);
        }
    }

}
