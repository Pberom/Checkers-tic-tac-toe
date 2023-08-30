<!-- Привет! Пробегусь по пунктам, которые не удалось реализовать:
 1. "В этой игре пользователь и компьютер" = два пользователя.
 2. "Значки, используемые компьютером и пользователем в каждой партии, определяются случайно перед её началом. =
 не определяются.
 3. "на одну клетку по вертикали или горизонтали в рамках поля" = не на одну, на любую (где НЕТ другого значка)-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Крестики-нолики + шашки</title>
    <style>
        .block {
            width: 50px;
            height: 50px;
            font-size: 24px;
            text-align: center;
            cursor: pointer;
        }
        .black-checker {
            color: black;
        }
        .white-checker {
            color: white;
        }
    </style>
</head>

<body>
<form name="form" action="" method="get" onsubmit="return createAndResetBoard();">
    <input type="number" name="subject" id="subject" placeholder="Введите размер поля (N)">
    <input type="submit" value="Создать поле">
</form>

<div id="current-player">Сейчас ходит: X</div>
<table id="game-board">
</table>

<div id="result"></div>
<div id="stats">Побед: 0 | Поражений: 0</div>

<script>
    var currentPlayer = 'X';
    var moves = 0;
    var gameOver = false;
    var boardSize = 0;
    var stats = { wins: 0, losses: 0 };
    var draggedSymbol = null;

    function createAndResetBoard() {
        var size = parseInt(document.getElementById('subject').value);
        if (size > 0) {
            boardSize = size;
            resetBoard(size);
            return false;
        }
        return true;
    }

    function createBoard(size) {
        var table = document.getElementById('game-board');
        for (var i = 0; i < size; i++) {
            var row = document.createElement('tr');
            for (var j = 0; j < size; j++) {
                var cell = document.createElement('td');
                var button = document.createElement('button');
                button.className = 'block';
                button.dataset.row = i;
                button.dataset.col = j;
                button.setAttribute('draggable', 'true');
                button.addEventListener('click', function() {
                    if (!gameOver && this.textContent === '') {
                        this.textContent = currentPlayer;
                        makeMove(parseInt(this.dataset.row), parseInt(this.dataset.col));
                    }
                });
                button.addEventListener('dragstart', function(event) {
                    if (!gameOver && this.textContent === currentPlayer) {
                        draggedSymbol = this;
                        event.dataTransfer.setData('text/plain', '');
                    }
                });
                button.addEventListener('dragover', function(event) {
                    event.preventDefault();
                });
                button.addEventListener('drop', function(event) {
                    event.preventDefault();
                    var row = parseInt(event.target.dataset.row);
                    var col = parseInt(event.target.dataset.col);
                    if (!gameOver && this.textContent === '') {
                        this.textContent = draggedSymbol.textContent;
                        makeMove(row, col);
                        draggedSymbol.textContent = '';
                        draggedSymbol = null;
                    }
                });
                cell.appendChild(button);
                row.appendChild(cell);
            }
            table.appendChild(row);
        }
    }

    function resetBoard(size) {
        var table = document.getElementById('game-board');
        table.innerHTML = '';
        createBoard(size);
        currentPlayer = 'X';
        moves = 0;
        gameOver = false;
        document.getElementById('result').textContent = '';

        document.getElementById('current-player').textContent = 'Сейчас ходит: X';
    }

    function makeMove(row, col) {
        if (gameOver) return;
        moves++;

        if (checkWin(row, col)) {
            gameOver = true;
            document.getElementById('result').textContent = 'Игрок ' + currentPlayer + ' выиграл!';
            if (currentPlayer === 'X') {
                stats.wins++;
            } else {
                stats.losses++;
            }
            updateStats();
            return;
        }

        if (moves === boardSize * boardSize && !checkWin() && isBoardFull()) {
            gameOver = true;
            document.getElementById('result').textContent = 'Ничья!';
            updateStats();
            return;
        }


        currentPlayer = currentPlayer === 'X' ? 'O' : 'X';
        document.getElementById('current-player').textContent = 'Сейчас ходит: ' + currentPlayer;
    }

    function checkWin(row, col) {
        var symbol = currentPlayer;
        var consecutiveCount = 0;

        // Проверка по горизонтали
        for (var i = 0; i < boardSize; i++) {
            var horizontalButton = document.querySelector('[data-row="' + row + '"][data-col="' + i + '"]');
            if (horizontalButton && horizontalButton.textContent === symbol) {
                consecutiveCount++;
            } else {
                consecutiveCount = 0;
            }
            if (consecutiveCount >= 5) {
                return true;
            }
            if (consecutiveCount >= boardSize) {
                return true;
            }
        }

        // Проверка по вертикали
        consecutiveCount = 0;
        for (var i = 0; i < boardSize; i++) {
            var verticalButton = document.querySelector('[data-row="' + i + '"][data-col="' + col + '"]');
            if (verticalButton && verticalButton.textContent === symbol) {
                consecutiveCount++;
            } else {
                consecutiveCount = 0;
            }
            if (consecutiveCount >= 5) {
                return true;
            }
            if (consecutiveCount >= boardSize) {
                return true;
            }
        }

        // Проверка по диагонали (слева сверху направо снизу)
        consecutiveCount = 0;
        for (var i = 0; i < boardSize; i++) {
            var diagonalButton1 = document.querySelector('[data-row="' + i + '"][data-col="' + (col - row + i) + '"]');
            if (diagonalButton1 && diagonalButton1.textContent === symbol) {
                consecutiveCount++;
            } else {
                consecutiveCount = 0;
            }
            if (consecutiveCount >= 5) {
                return true;
            }
            if (consecutiveCount >= boardSize) {
                return true;
            }
        }

        // Проверка по диагонали (слева снизу направо сверху)
        consecutiveCount = 0;
        for (var i = 0; i < boardSize; i++) {
            var diagonalButton2 = document.querySelector('[data-row="' + i + '"][data-col="' + (col + row - i) + '"]');
            if (diagonalButton2 && diagonalButton2.textContent === symbol) {
                consecutiveCount++;
            } else {
                consecutiveCount = 0;
            }
            if (consecutiveCount >= 5) {
                return true;
            }
            if (consecutiveCount >= boardSize) {
                return true;
            }
        }

        return false;
    }

    function isBoardFull() {
        var buttons = document.querySelectorAll('.block');
        for (var i = 0; i < buttons.length; i++) {
            if (buttons[i].textContent === '') {
                return false;
            }
        }
        return true;
    }

    function updateStats() {
        document.getElementById('stats').textContent = 'Побед: ' + stats.wins + ' | Поражений: ' + stats.losses;
    }
</script>
</body>
</html>
