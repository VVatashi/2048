<?php

declare(strict_types=1);

const GRID_WIDTH = 4;
const GRID_HEIGHT = 4;

const ACTION_UP = 'up';
const ACTION_DOWN = 'down';
const ACTION_LEFT = 'left';
const ACTION_RIGHT = 'right';

mt_srand();

$grid = [];

// Init the grid from the request data
for ($x = 0; $x < GRID_WIDTH; $x++) {
    $grid[$x] = [];
    for ($y = 0; $y < GRID_HEIGHT; $y++) {
        $grid[$x][$y] = (int)($_POST["$x,$y"] ?? 0);
    }
}

// Update the game state based on the player action
$merged = [];
$moved = false;
$action = $_POST['action'] ?? '';
switch ($action) {
    case ACTION_UP:
        for ($x = 0; $x < GRID_WIDTH; $x++) {
            for ($y = 1; $y < GRID_HEIGHT; $y++) {
                if ($grid[$x][$y] === 0) {
                    continue;
                }

                for ($y2 = $y - 1; $y2 > 0; $y2--) {
                    if ($grid[$x][$y2] !== 0) {
                        break;
                    }
                }

                if ($grid[$x][$y2] === $grid[$x][$y] && !in_array("$x,$y2", $merged)) {
                    $grid[$x][$y2] += $grid[$x][$y];
                    $grid[$x][$y] = 0;

                    $merged[] = "$x,$y2";
                    $moved = true;
                } elseif ($grid[$x][$y2] === 0) {
                    $grid[$x][$y2] = $grid[$x][$y];
                    $grid[$x][$y] = 0;

                    $moved = true;
                } elseif ($grid[$x][$y2 + 1] === 0) {
                    $grid[$x][$y2 + 1] = $grid[$x][$y];
                    $grid[$x][$y] = 0;

                    $moved = true;
                }
            }
        }
        break;

    case ACTION_DOWN:
        for ($x = 0; $x < GRID_WIDTH; $x++) {
            for ($y = GRID_HEIGHT - 2; $y >= 0; $y--) {
                if ($grid[$x][$y] === 0) {
                    continue;
                }

                for ($y2 = $y + 1; $y2 < GRID_HEIGHT - 1; $y2++) {
                    if ($grid[$x][$y2] !== 0) {
                        break;
                    }
                }

                if ($grid[$x][$y2] === $grid[$x][$y] && !in_array("$x,$y2", $merged)) {
                    $grid[$x][$y2] += $grid[$x][$y];
                    $grid[$x][$y] = 0;

                    $merged[] = "$x,$y2";
                    $moved = true;
                } elseif ($grid[$x][$y2] === 0) {
                    $grid[$x][$y2] = $grid[$x][$y];
                    $grid[$x][$y] = 0;

                    $moved = true;
                } elseif ($grid[$x][$y2 - 1] === 0) {
                    $grid[$x][$y2 - 1] = $grid[$x][$y];
                    $grid[$x][$y] = 0;

                    $moved = true;
                }
            }
        }
        break;

    case ACTION_LEFT:
        for ($y = 0; $y < GRID_HEIGHT; $y++) {
            for ($x = 1; $x < GRID_WIDTH; $x++) {
                if ($grid[$x][$y] === 0) {
                    continue;
                }

                for ($x2 = $x - 1; $x2 > 0; $x2--) {
                    if ($grid[$x2][$y] !== 0) {
                        break;
                    }
                }

                if ($grid[$x2][$y] === $grid[$x][$y] && !in_array("$x2,$y", $merged)) {
                    $grid[$x2][$y] += $grid[$x][$y];
                    $grid[$x][$y] = 0;

                    $merged[] = "$x2,$y";
                    $moved = true;
                } elseif ($grid[$x2][$y] === 0) {
                    $grid[$x2][$y] = $grid[$x][$y];
                    $grid[$x][$y] = 0;

                    $moved = true;
                } elseif ($grid[$x2 + 1][$y] === 0) {
                    $grid[$x2 + 1][$y] = $grid[$x][$y];
                    $grid[$x][$y] = 0;

                    $moved = true;
                }
            }
        }
        break;

    case ACTION_RIGHT:
        for ($y = 0; $y < GRID_HEIGHT; $y++) {
            for ($x = GRID_WIDTH - 2; $x >= 0; $x--) {
                if ($grid[$x][$y] === 0) {
                    continue;
                }

                for ($x2 = $x + 1; $x2 < GRID_HEIGHT - 1; $x2++) {
                    if ($grid[$x2][$y] !== 0) {
                        break;
                    }
                }

                if ($grid[$x2][$y] === $grid[$x][$y] && !in_array("$x2,$y", $merged)) {
                    $grid[$x2][$y] += $grid[$x][$y];
                    $grid[$x][$y] = 0;

                    $merged[] = "$x2,$y";
                    $moved = true;
                } elseif ($grid[$x2][$y] === 0) {
                    $grid[$x2][$y] = $grid[$x][$y];
                    $grid[$x][$y] = 0;

                    $moved = true;
                } elseif ($grid[$x2 - 1][$y] === 0) {
                    $grid[$x2 - 1][$y] = $grid[$x][$y];
                    $grid[$x][$y] = 0;

                    $moved = true;
                }
            }
        }
        break;
}

// Find empty cells
$emptyCells = [];
for ($x = 0; $x < GRID_WIDTH; $x++) {
    for ($y = 0; $y < GRID_HEIGHT; $y++) {
        if ($grid[$x][$y] === 0) {
            $emptyCells[] = [$x, $y];
        }
    }
}

// Put a new value in a random empty cell
if (!empty($emptyCells) && ($moved || empty($action))) {
    $index = mt_rand(0, count($emptyCells) - 1);
    [$x, $y] = $emptyCells[$index];
    $grid[$x][$y] = 2;
}

// Render the game state
?>
<style>
    * {
        box-sizing: border-box;
    }

    html,
    body {
        margin: 0;
        padding: 0;
    }

    .layout {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        min-height: 100vh;
    }

    .grid .row {
        display: flex;
        flex-flow: row nowrap;
    }

    .grid .cell {
        background: #edc22e;
        color: #f9f6f2;
        font-family: monospace;
        font-size: 18px;
        font-weight: 900;
        text-align: center;
        width: 48px;
        height: 48px;
    }

    .grid .cell.cell_0 {
        background: #cdc1b4;
        color: transparent;
    }

    .grid .cell.cell_2 {
        background: #eee4da;
        color: #776e65;
    }

    .grid .cell.cell_4 {
        background: #ede0c8;
        color: #776e65;
    }

    .grid .cell.cell_8 {
        background: #f2b179;
    }

    .grid .cell.cell_16 {
        background: #f59563;
    }

    .grid .cell.cell_32 {
        background: #f67c5f;
    }

    .grid .cell.cell_64 {
        background: #f65e3b;
    }

    .grid .cell.cell_128 {
        background: #edcf72;
    }

    .grid .cell.cell_256 {
        background: #edcc61;
    }

    .grid .cell.cell_512 {
        background: #edc850;
    }

    .grid .cell.cell_1024 {
        background: #edc53f;
    }

    .controls {
        display: flex;
        flex-flow: column nowrap;
        align-items: center;
        gap: 8px;
    }

    .controls .row {
        display: flex;
        flex-flow: row nowrap;
        gap: 8px;
    }

    .button {
        width: 48px;
        height: 48px;
    }
</style>

<form class="layout" method="POST" action="" enctype="multipart/form-data">
    <div class="grid">
        <?php for ($y = 0; $y < GRID_HEIGHT; $y++) : ?>
            <div class="row">
                <?php for ($x = 0; $x < GRID_WIDTH; $x++) : ?>
                    <input class="cell <?php echo 'cell_' . $grid[$x][$y] ?>" name="<?php echo "$x,$y" ?>" value="<?php echo $grid[$x][$y] ?>" readonly />
                <?php endfor; ?>
            </div>
        <?php endfor; ?>
    </div>

    <div class="controls">
        <button class="button" type="submit" name="action" value="<?php echo ACTION_UP ?>">Up</button>

        <div class="row">
            <button class="button" type="submit" name="action" value="<?php echo ACTION_LEFT ?>">Left</button>
            <button class="button" type="submit" name="action" value="<?php echo ACTION_RIGHT ?>">Right</button>
        </div>

        <button class="button" type="submit" name="action" value="<?php echo ACTION_DOWN ?>">Down</button>
    </div>
</form>
