<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <title>New Project</title>
</head>
<body>
<table border="4">

    <tr>
        <td cosplan="1">
            <input class="display" type="text" size="10" name="Display" value="0">
        </td>

    </tr>
    <tr>
        <td>
            <table>
                <tr>
                    <td><input class="clear-btn" type="button" value="C"></td>
                </tr>
                <tr>
                    <td>
                        <button class="calc-btn" data-role="operator" value="factorial">f</button>
                    </td>
                    <td>
                        <button class="calc-btn" data-role="operator" value="degreeOf">x<sup><small>n</small></sup>
                        </button>
                    </td>
                    <td>
                        <button class="calc-btn" data-role="operator" value="sqrt">&radic;</button>
                    </td>
                    <td>
                        <button class="calculate-btn" data-role="operator" value="persent">%</button>
                    </td>
                </tr>
                <tr>
                    <td><input class="calc-btn" data-role="number" type="button" value="7"></td>
                    <td><input class="calc-btn" data-role="number" type="button" value="8"></td>
                    <td><input class="calc-btn" data-role="number" type="button" value="9"></td>
                    <td><input class="calc-btn" data-role="operator" type="button" value="+"></td>
                </tr>
                <tr>
                    <td><input class="calc-btn" data-role="number" type="button" value="4"></td>
                    <td><input class="calc-btn" data-role="number" type="button" value="5"></td>
                    <td><input class="calc-btn" data-role="number" type="button" value="6"></td>
                    <td><input class="calc-btn" data-role="operator" type="button" value="-"></td>
                </tr>
                <tr>
                    <td><input class="calc-btn" data-role="number" type="button" value="1"></td>
                    <td><input class="calc-btn" data-role="number" type="button" value="2"></td>
                    <td><input class="calc-btn" data-role="number" type="button" value="3"></td>
                    <td><input class="calc-btn" data-role="operator" type="button" value="*">
                    </td>
                </tr>
                <tr>
                    <td><input class="calc-btn" data-role="point" type="button" value="."></td>
                    <td><input class="calc-btn" data-role="number" type="button" value="0"></td>
                    <td><input class="calculate-btn" type="button" value="="></td>
                    <td><input class="calc-btn" data-role="operator" type="button" value="/"></td>
                </tr>
            </table>
        </td>
    </tr>

</table>
<div class="form_vehicle" style="margin: 5px">

    <input class="vehicle_name" value="Enter name vehicle">
    <div>
        <button class="save-btn" data-role="save_name" value="save"></button>
    </div>

</div>
<script>

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(function () {

        let firstNumber = '', secondNumber = '', operator = '',
            decision_result = '', vehicle_name = ''


        $(".calc-btn").on('click', function () {
            if ($(this).attr('data-role') !== 'operator') {
                if ($(this).val() === '.') {
                    $(".display").val().includes('.') ? saveNumber('') : point($(this).val())
                } else {
                    saveNumber($(this).val())
                }
            } else if (operator && secondNumber !== null) {
                $(".display").val('')
                result()
                secondNumber = ''
                operator = $(this).val()
            } else {
                operator = $(this).val()
                $('.display').val('')
            }
        });

        $('[value="persent"]').on('click', function () {
            switch (operator) {
                case '-':
                    operator += '%'
                    result()
                    break
                case '+':
                    operator += '%'
                    result()
                    break
            }
        });
        $('[value="degreeOf"]').on('click', function () {
            operator = 'degreeOf'
        });

        $('[value="sqrt"]').on('click', function () {
            operator = 'sq'
            secondNumber = 1
            result()
        });

        $('[value="factorial"]').on('click', function () {
            operator = $(this).val()
            secondNumber = 0
            result()
        })

        $(".clear-btn").on('click', function () {
            $(".display").val('');
            firstNumber = ''
            secondNumber = ''
            operator = ''
        });

        $(".calculate-btn").on('click', function () {
            result()
        });

        function point(symbol) {
            if (!$(".display").val().charAt(0)) {
                $(".display").val('0.')
            } else {
                saveNumber(symbol)
            }
        }

        function saveNumber(symbol) {
            let str = $(".display").val()
            if (!str.match('^0')) {
                $(".display").val($(".display").val() + symbol)
                operator === '' ? firstNumber += symbol : secondNumber += symbol
            } else if (str.match('^0.')) {
                $(".display").val($(".display").val() + symbol)
                operator === '' ? firstNumber += symbol : secondNumber += symbol
            } else {
                $(".display").val($(".display").val().replace(0, symbol))
                operator === '' ? firstNumber += symbol : secondNumber += symbol
            }
        }


        function result() {

            $.ajax({

                url: '/home',
                type: "POST",
                data: {
                    'firstNumber': firstNumber,
                    'secondNumber': secondNumber,
                    'operator': operator
                },
                success: function (data) {
                    firstNumber = data
                    decision_result = data
                    $(".display").val(data)
                    console.log(decision_result)
                },

                error: function (errors) {
                    if (errors.responseJSON && errors.responseJSON.errors) {
                        for (let [key, value] of Object.entries(errors.responseJSON.errors)) {
                            alert(value[0]);
                        }
                    }
                }

            });

            $(".save-btn").on('click', function () {
                vehicle_name = $(".vehicle_name").val()
                console.log(vehicle_name  + '\n' + decision_result)
                save()
            });

            function save() {
                $.ajax({
                    url: '/vehicle',
                    type: "POST",
                    data: {
                        'vehicle_name': vehicle_name,
                        'decision_result': decision_result
                    },

                    success: function (data) {
                        console.log(data + "Операция успешна")
                    },

                    error: function (errors) {
                        if (errors.responseJSON && errors.responseJSON.errors) {
                            for (let [key, value] of Object.entries(errors.responseJSON.errors)) {
                                alert(value[0]);
                            }
                        }
                    }
                })
            }
        }
    })
</script>
</body>
</html>

