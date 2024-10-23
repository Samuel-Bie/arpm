# Task 1

_Sheet Link_ : https://docs.google.com/spreadsheets/d/10nT5p2c-g7Lp5LYXtUtPkSlbICP4WNOhhCbxdpyVrLo/edit?usp=sharing

# Task 2

Question 1: _How would you refactor it to improve its performance and efficiency, ensuring that the code is both optimized for database queries and maintains readability?_

Answer:

1. **Use of Eager Loading**: Eager loading will optimize the database queries and solve the _n+1_ problem.

    instead of this:

    ```php
    Order::all();

    ```

    we can use eager loading like this:

    ```php
    Order::with([‘items,’customer’]);

    ```

2. **Filter everything possible using query builder**:
   This will reduce the ammount of loops for counting, ordering, filtering, etc. for example:

    there is no need to do this:

    ```php

    $itemsCount++;
    ```

    while we can do this:

    ```php
                ->withCount('items as items_count')

    ```

    there is no need to do this:

    ```php

    $completedOrderExists = Order::where('id', $order->id)
                    ->where('status', 'completed')
                    ->exists();
    ```

    while we can do this:

    ```php
                        'completed_order_exists' => $order->status == 'completed' ? true : false,


    ```

    there is no need to do this:

    ```php

    usort($orderData, function($a, $b) {
                $aCompletedAt = Order::where('id', $a['order_id'])
                    ->where('status', 'completed')
                    ->orderByDesc('completed_at')
                    ->first()
                    ->completed_at ?? null;

                $bCompletedAt = Order::where('id', $b['order_id'])
                    ->where('status', 'completed')
                    ->orderByDesc('completed_at')
                    ->first()
                    ->completed_at ?? null;

                return strtotime($bCompletedAt) - strtotime($aCompletedAt);
            });

    ```

    while we can do this:

    ```php

    // order by completed_at in descending order
                ->orderBy('completed_at', 'desc')

    ```

Suggestion on the controller

1. A lot of code can be moved to a resource collection class. This will make the controller cleaner and easier to read.

2. We should consider paginating the results. This will make the application faster and more efficient.


# Task 3

The result of Task 3 is in the file ```SpreadsheetServiceTest.php```
# Task 4

The result of Task 4 is in the file ```Collection.php```

# Task 5
    a) The code shown on the image does:
        1. It schedules a command ```php artisan app:example-command``` to run every hour.

        2. WithOutOverlapping avoids the command to run if the previous instance of the command is still running.

        3. To prevent multiple servers running the same command it usee ```->onOneServer()```

        4. To avoid waiting for other tasks to finish the command it uses ```->runInBackground()```

    b)
    c)