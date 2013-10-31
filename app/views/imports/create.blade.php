@extends('layouts.scaffold')

@section('main')

<h1>Импортирование</h1>

<h2>Локальный файл базы данных для импорта:</h2>

<p>{{Import::$db_path}}</p>

@if (Import::where('hash', '=', $hash)->first())
	Не надо импортировать
@else
	Надо импортировать
@endif


<h2>Загрузить базу данных для импорта:</h2>

{{ Form::open() }}
    <ul>
        <li>
            {{ Form::label('file', 'Выбрать файл БД:') }}
			{{ Form::file('file')}}
        </li>
    </ul>
{{ Form::close() }}

<div class="status"></div>

{{ Form::open(array('route' => 'imports.performstore')) }}
	{{ Form::hidden('filename') }}
	{{ Form::hidden('offset') }}
	{{ Form::hidden('count') }}
	{{ Form::hidden('type') }}
	{{ Form::hidden('total', $total) }}
	<ul>
        <li>
            {{ Form::label('hash', 'Hash:') }}
            {{ Form::text('hash', $hash) }}
        </li>
	</ul>
{{ Form::close() }}

{{ Form::button('Начать импорт', array('id' => 'importstart', 'class' => 'btn btn-primary')) }}

<div class='progress'></div>

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop

@section('scripts')

<script>
$(document).ready(function(){ 

    var startbutton = $('#importstart'), //кнопка начала импорта
    	uploadInput = $('#file'), // Инпут с файлом
        uploadedfile = $('[name="filename"]'), // Инпут с URL загруженного файла БД
        offset = $('[name="offset"]'), // Инпут со смещение
        count = $('[name="count"]'), // Инпут с количеством записей
        type = $('[name="type"]'), // Инпут с типом БД
        hash = $('[name="hash"]'), // Инпут с хэшем всего файла
        total = $('[name="total"]'), // Инпут с хэшем всего файла
        status = $('div.status'), // Вывод ошибки при загрузке файла
        progress = $('div.progress'); // Вывод прогресса импортирования

        type.val('wigle');
        uploadedfile.val('{{Import::$db_path}}');
        offset.val(0);
        count.val(300);
    
    //Импорт

    startbutton.on('click', function(){
        // Создадим новый объект типа FormData
        var data = new FormData();
        // Добавим в новую форму файл
        data.append('filename', uploadedfile.val());
        data.append('offset', offset.val());
        data.append('count', count.val());
        data.append('type', type.val());


        // Создадим асинхронный запрос
        $.ajax({
            // На какой URL будет послан запрос
            url: '/performstore',
            // Тип запроса
            type: 'POST',
            // Какие данные нужно передать
            data: data,
            // Эта опция не разрешает jQuery изменять данные
            processData: false,		
            // Эта опция не разрешает jQuery изменять типы данных
            contentType: false,		
            // Формат данных ответа с сервера
            dataType: 'json',
            // Функция удачного ответа с сервера
            beforeSend: function() {
            	startbutton.attr('disabled','disabled');
            },
            success: function(result) { 	
                // Получили ответ с сервера (ответ содержится в переменной result)
                // Если в ответе есть объект filelink
                if (result.error) {	
                	// Выведет текст ошибки с сервера
                    progress.text(result.err_message);
                    progress.show();	
                } else {
                    // Сохраним значение в input'е
                    offset.val(parseInt(offset.val()) + parseInt(count.val()));
                    // Скроем ошибку
                    progress.text('Успех: ' + result.new_networks);
                    progress.show();
                    startbutton.removeAttr('disabled');

                    //Если все импортировано
                    if(parseInt(offset.val()) >= parseInt(total.val())) {
                    	startbutton.attr('disabled','disabled');
                    	startbutton.text('Импорт завершен');
                    }
                }
            },
            // Что-то пошло не так
            error: function (result) {
                // Ошибка на стороне сервера
                progress.text("Something very wrong happened");
                progress.show();
            }
        });
    });

	//--Импорт


    //Загрузка файла

    uploadInput.on('change', function(){
        // Создадим новый объект типа FormData
        var data = new FormData();
        // Добавим в новую форму файл
        data.append('file', uploadInput[0].files[0]);

        // Создадим асинхронный запрос
        $.ajax({
            // На какой URL будет послан запрос
            url: '/upload',
            // Тип запроса
            type: 'POST',
            // Какие данные нужно передать
            data: data,
            // Эта опция не разрешает jQuery изменять данные
            processData: false,		
            // Эта опция не разрешает jQuery изменять типы данных
            contentType: false,		
            // Формат данных ответа с сервера
            dataType: 'json',
            // Функция удачного ответа с сервера
            success: function(result) { 	
                // Получили ответ с сервера (ответ содержится в переменной result)
                // Если в ответе есть объект filelink
                if (result.filelink && result.total && result.hash) {		
                    // Сохраним значение в input'е
                    uploadedfile.val(result.filelink);
                    total.val(result.total);
                    hash.val(result.hash);
                    offset.val(0);
        			count.val(300);
                    // Скроем ошибку
                    status.text('Успех: ' + result.filelink);
                    status.show();

                    startbutton.removeAttr('disabled');
                    startbutton.text('Начать импорт загруженного файла');
                } else {
                    // Выведет текст ошибки с сервера
                    status.text(result.message);
                    status.show();
                }
            },
            // Что-то пошло не так
            error: function (result) {
                // Ошибка на стороне сервера
                status.text("Upload impossible");
                status.show();
            }
        });
    });

	//--Загрузка файла

});
</script>
@stop
