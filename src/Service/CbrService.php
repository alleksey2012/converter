<?php

namespace App\Service;
use Psr\Log\LoggerInterface;

class CbrService
{
    private $logger;
    private $path;
    private $url;
    const XML_DAILY = 'http://cbr.ru/scripts/XML_daily.asp';

    public function __construct($path)
    {
        //$this->logger = $logger;
        $this->path = $path;
    }

    /**
     * Скачивает XML с котировками валют из http://cbr.ru/scripts/XML_daily.asp и сохраняет на жесткий диск.
     * повторно загружает через 7200 секунд (2 часа)
     *
     * @throws \Exception в случае ошибки
     *
     * @return array - возвращает массив с котировками валют
     */
    public function getValutes()
    {
        // если отсутствует файл на диске или в кеше файл лежит больше 2 часов
        if (!is_file($this->path) || filemtime($this->path) < time() - 7200)
	    {
	        if ($xml_daily = file_get_contents(self::XML_DAILY))
	        { file_put_contents($this->path, $xml_daily); }
	    }

        $results = array();
        // Формируем массив с котировками
        foreach (simplexml_load_file($this->path) as $el)
        {
            $results[strval($el->CharCode)] = [
                    'ID' => (string) $el->attributes()['ID'],
					'NumCode' => (int) $el->NumCode,
					'CharCode' => (string)$el->CharCode,
					'Nominal' => (int) $el->Nominal,
					'Name' => (string) $el->Name,
					'Value' => (float) (str_replace(',', '.', $el->Value))
                ];
        }
        return $results;
    }

}