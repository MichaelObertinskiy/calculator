<?php

namespace App\Http\Controllers;

use App\Exceptions\BaseException;
use App\Exceptions\CalcException;
use App\Http\Requests\CalculateRequest;
use Exception;
use Illuminate\Http\Response;

class CalculatorController extends Controller
{
    public function index()
    {
        return view('calculator_simple');
    }

    public function calculate(CalculateRequest $request)
    {
        $result = 0;
        switch ($request->get('operator')) {
            case '+':
                $result = $request->get('firstNumber') + ($request->get('secondNumber') ?
                        $request->get('secondNumber') : 0);
                break;
            case '-':
                $result = $request->get('firstNumber') - ($request->get('secondNumber') ?
                        $request->get('secondNumber') : 0);
                break;
            case '*':
                $result = $request->get('firstNumber') * ($request->get('secondNumber') ?
                        $request->get('secondNumber') : 1);
                break;
            case '/':
                if ($request->get('secondNumber') != 0) {
                    $result = $request->get('firstNumber') / $request->get('secondNumber');
                } else {
                    throw new BaseException('secondNumber','Нельзя делить на ноль', 422);
                }
                break;
            case 'factorial':
                $result = 1;
                $count = 1;
                while ($count <= $request->get('firstNumber')) {
                    $result *= $count;
                    $count++;
                }
                break;
            case 'degreeOf':
                $result = 1;
                $count = 1;
                while ($count <= $request->get('secondNumber')) {
                    $result *= $request->get('firstNumber');
                    $count++;
                }

                break;
            case '-%':
                $result = ($request->get('firstNumber') - $request->get('firstNumber') * $request->get('secondNumber') / 100);
                break;
            case '+%':
                $result = $request->get('firstNumber') + ($request->get('firstNumber')
                        * $request->get('secondNumber') / 100);
                break;
            case 'sq':
                $result = sqrt($request->get('firstNumber'));
                break;
        }
        return response($result, Response::HTTP_OK);
    }
}
