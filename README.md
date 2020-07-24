# MySQLWork
Simple and easy wrapper of mysqli PHP.

Main functions:
1. conn - creating a mysqli connection
2. close - closing the mysqli connection
3. query - executing mysql queries
4. oneValue - getting a single value from mysqli_result. Used, for example, to get the result of executing an aggregate function
5. oneValueSQL - getting a single value from the result of executing an sql query
6. fieldsArray - an array of fields mysqli_result
7. htmlTable - returns mysqli_result as an html table
8. array - returns mysqli_result as an array
9. arraySQL - returns the result of executing an sql query as an array
10. array2 - returns the result of executing an sql query as a two-dimensional associative array.
    For example, SELECT id, value FROM spr ORDER BY id; = > array[id] = value
11. multiQuery - returns the mysqli_result array obtained as a result of executing the multiquery
12. test - formating/testing the value of a variable/string for working with sql
13. TIP - "test input post" - testing/fomating and set the default value of a variable from the $_POST array
14. mysqliTest - simple mysqli connection check

Простая и удобная обвертка mysqli PHP.

Основные функции:
1. conn - создание подключения
2. close - закрытие подключения
3. query - выполнение mysql запросов
4. oneValue - получение одного значение из mysqli_result. Используется, например, для получения результата выполнения агрегатной функции
5. oneValueSQL -получение одного значения из результата выполнения sql запроса
6. fieldsArray - массив полей mysqli_result
7. htmlTable - возвращает mysqli_result в виде html таблицы
8. array - возвращает mysqli_result в виде массива
9. arraySQL - возвращает результат выполнения sql запроса в виде массива
10. array2 - возвращает результат выполнения sql запроса в виде двумерного ассоциативного массива.
    Например, SELECT id, value FROM spr ORDER BY id; => array[id] = value
11. multiQuery - возвращает массив mysqli_result, полученный в результате выполнения мультизапроса
12. test - форматирует/тустирует значение переменной/строки для работы с sql
13. TIP - test input post - тестирует/фоматирует и задает дефолтное значение переменной из массива $_POST
14. mysqliTest - простая проверка mysqli соединения
