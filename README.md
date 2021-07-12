# api

## Первое задание:
SELECT `users`.`id`, CONCAT(`users`.`first_name`, ' ',`users`.`last_name`) AS `name`,`books`.`author` AS `author`, `books`.`name` AS `book1` FROM `user_books` JOIN `users` ON `user_books`.`user_id` = `users`.`id` JOIN `books` ON `user_books`.`book_id` = `books`.`id` WHERE `age` >= 7 and `age` <=17 GROUP BY `author` HAVING COUNT(*)=2

## Второе задание:

Формат запросов: <your_domain>/api/v1?method=<method_name>&<parameter>=<value>

Токен для доступа -> eyJhbGciOiJIUzI1NiJ9eyJSb2xlIjoiQWRtaW4iLCJJc3N1ZXIiOiJJc3N1ZXIi

Вывод всех курсов (пример): http://aapi.mcdir.me/api/v1?method=rates&token=<token>
Вывод курса рубля (пример): http://aapi.mcdir.me/api/v1?method=rates&currency=RUB&token=<token>

Методы:
1) rates: Получение всех курсов с учетом комиссии = 2% (GET запрос) в формате:
{
	“status”: “success”,
	“code”: 200,
	“data”: {
	“USD” : <rate>,
	...
}
}

В случае ошибки:
{
	“status”: “error”,
	“code”: 403,
	“message”: “Invalid token”
}

Сортировка от меньшего курса к большему курсу.

В качестве параметров может передаваться интересующая валюта, в формате USD,RUB,EUR и тп В этом случае, отдаем указанные в качестве параметра currency значения.

2) convert: Запрос на обмен валюты c учетом комиссии = 2%. POST запрос с параметрами:
currency_from: USD
currency_to: BTC
value: 1.00

или в обратную сторону

currency_from: BTC
currency_to: USD
value: 1.00

В случае успешного запроса, отдаем:

{
	“status”: “success”,
	“code”: 200,
	“data”: {
	“currency_from” : BTC,
	“currency_to” : USD,
	“value”: 1.00,
	“converted_value”: 1.00,
	“rate” : 1.00,
}
}

В случае ошибки:
{
	“status”: “error”,
	“code”: 403,
	“message”: “Invalid token”
}

Важно, минимальный обмен равен 0,01 валюты from
Например: USD = 0.01 меняется на 0.0000005556 (считаем до 10 знаков)
Если идет обмен из BTC в USD - округляем до 0.01
