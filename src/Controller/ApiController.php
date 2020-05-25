<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\CbrService;

class ApiController extends AbstractController
{
    private $cbr;
    private $logger;

    public function __construct(CbrService $cbrService, LoggerInterface $logger)
    {
        $this->cbr = $cbrService;
        $this->logger = $logger;
    }

    /**
     * конвертер валют, конвертирует валюту из параметра base в quote
     *
     * @param base string базавая валюта CharCode вюлюты [A-Z]{3,} 3 латинские буквы в верхнем регистре
     * @param summa int сумма которую необходимо конвертировать
     * @param quoted string катировочная валюта CharCode вюлюты [A-Z]{3,} 3 латинские буквы в верхнем регистре
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse возвращает: success (true|false), summa - результат конвертации, mesg - в случае ошибки сообщение об ошибке
     *
     * @Route("/api/converter", name="app_route_api_converter")
     */

    public function converter(Request $request)
    {
        // обрабатываем входные параметры
        $base = preg_replace('/[^A-Za-z]/', "", $request->query->get('base'));
        $base = strtoupper(substr($base,0,3));
        $quoted = preg_replace('/[^A-Za-z]/', "", $request->query->get('quoted'));
        $quoted = strtoupper(substr($quoted,0,3));
        $summa = intval($request->query->get('summa'));

        // все параметры обязательные
        if(empty($base) || empty($quoted) || $summa==0)
            return $this->json(['success' => false, 'mesg'=>'Bad request']);

        // получаем массив данных по котировкам валют из App\Service\CbrService;
        $valutes = $this->cbr->getValutes();
        if(count($valutes)==0) return $this->json(['success' => false, 'mesg'=>'No data from CBR']);

        // проверяем входные параметры $base на наличии в котировках валют
        if (!array_key_exists($base,$valutes)) return $this->json(['success' => false, 'mesg'=>'key ['.$base.'] not found']);
        if ($valutes[$base]['Value']==0) return $this->json(['success' => false, 'mesg'=>'value ['.$base.'][value] is null']);
        if ($valutes[$base]['Nominal']==0) return $this->json(['success' => false, 'mesg'=>'value ['.$base.'][Nominal] is null']);

        // проверяем входные параметры $quoted на наличии в котировках валют
        if (!array_key_exists($quoted,$valutes)) return $this->json(['success' => false, 'mesg'=>'key ['.$quoted.'] not found']);
        if ($valutes[$quoted]['Value']==0) return $this->json(['success' => false, 'mesg'=>'value ['.$quoted.'][value] is null']);
        if ($valutes[$quoted]['Nominal']==0) return $this->json(['success' => false, 'mesg'=>'value ['.$quoted.'][Nominal] is null']);

        // вычисляем результат
        $result = round($summa*$valutes[$base]['Value']/$valutes[$base]['Nominal']/$valutes[$quoted]['Value']/$valutes[$quoted]['Nominal'],2);
        $this->logger->info('result ['.$result.']');

        // возвращаем JSON
        return $this->json(['success'=>true, 'summa' => $result]);
    }
}
