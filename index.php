<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora Avançada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .calculator {
            width: 300px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        input[type="text"] {
            width: calc(100% - 20px);
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="calculator">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="text" name="expression" placeholder="Digite a expressão matemática" required>
            <input type="submit" value="Calcular">
        </form>
        <?php
        function evaluateExpression($expression) {
            $precedence = [
                '+' => 1,
                '-' => 1,
                '*' => 2,
                '/' => 2,
                '**' => 3,
                'sqrt' => 3,
            ];

            $allowedOperators = ['+', '-', '*', '/', '**', 'sqrt'];
            $expression = str_replace(' ', '', $expression); 
            $tokens = preg_split('/([\+\-\*\/\(\)])/', $expression, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
            
            $output = [];
            $operators = [];
            
            foreach ($tokens as $token) {
                if (is_numeric($token)) {
                    $output[] = $token;
                } elseif (in_array($token, $allowedOperators)) {
                    while (!empty($operators) && $operators[count($operators) - 1] != '(' && $precedence[$operators[count($operators) - 1]] >= $precedence[$token]) {
                        $output[] = array_pop($operators);
                    }
                    $operators[] = $token;
                } elseif ($token == '(') {
                    $operators[] = $token;
                } elseif ($token == ')') {
                    while (!empty($operators) && end($operators) != '(') {
                        $output[] = array_pop($operators);
                    }
                    array_pop($operators); 
                } else {
                    die("Erro: Caractere inválido encontrado na expressão.");
                }
            }
            
            while (!empty($operators)) {
                $output[] = array_pop($operators);
            }
            
            $resultStack = [];
            foreach ($output as $token) {
                if (is_numeric($token)) {
                    $resultStack[] = $token;
                } elseif (in_array($token, $allowedOperators)) {
                    $b = array_pop($resultStack);
                    $a = array_pop($resultStack);
                    switch ($token) {
                        case '+':
                            $resultStack[] = $a + $b;
                            break;
                        case '-':
                            $resultStack[] = $a - $b;
                            break;
                        case '*':
                            $resultStack[] = $a * $b;
                            break;
                        case '/':
                            if ($b == 0) {
                                die("Erro: Divisão por zero.");
                            }
                            $resultStack[] = $a / $b;
                            break;
                        case '**':
                            $resultStack[] = $a ** $b;
                            break;
                        case 'sqrt':
                            $resultStack[] = sqrt($b);
                            break;
                    }
                }
            }
            if (count($resultStack) != 1) {
                die("Erro: Expressão inválida.");
            }
            return $resultStack[0];
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $expression = $_POST["expression"];
            $result = evaluateExpression($expression);
            echo "<p>O resultado é: " . $result . "</p>";
        }
        ?>
    </div>
</body>
</html>
