@extends('layouts.scaffold')

@section('main')

<h1>Импортирование</h1>

{{Form::radio('importsource', 'local')}}
<h2>Локальный файл базы данных для импорта:</h2>

<p>{{Import::$db_path}}</p>

@if($hash)

	@if (Import::where('hash', '=', $hash)->first())
		<p>Не надо импортировать</p>
	@else
		<p>Надо импортировать</p>
	@endif

@else
	
	<p>Файла локальной БД нет</p>

@endif

{{Form::radio('importsource', 'uploaded')}}
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

<h2>Информация</h2>

<div class='infoblock'>Похоже локальной БД нет, загрузите файл БД для импорта.</div>

{{ Form::button('Начать импорт', array('id' => 'importstart', 'class' => 'btn btn-primary')) }}

<div class="progress progress-striped active">
  <div class="progress-bar"  role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="0" style="width: 0%">
    <span class="sr-only">В процессе...</span>
  </div>
</div>

<div class='results'>
	<div class='new_networks'></div>
	<div class='exist_networks'></div>

	<div class='new_locations'></div>
	<div class='exist_locations'></div>

	<div class='new_types'></div>
	<div class='exist_types'></div>

	<div class='new_capabilities'></div>
	<div class='exist_capabilities'></div>

	<div class='errors'></div>
</div>

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
        importsource = $('[name="importsource"]'), // Селектор импорта
        status = $('div.status'), // Вывод ошибки при загрузке файла
        results = $('div.results'), // Вывод результатов импортирования
        infoblock = $('div.infoblock'), // Блок информации о БД
        progress = $('div.progress'); // Вывод прогресса импортирования

    var localdata = {},
    	uploadeddata = {},
    	importdata = {};

    	function updateinfo(data) {
    		infoblock.text('');
	    	$.each(data, function(index, value) {
			    infoblock.append('<div id="' + index + '">' + value + '</div>');
			});
    	}

    	function parseprogress(data) {
    		bar = $("div.progress-bar");
    		bar.attr('aria-valuemax', importdata.total);
    		bar.attr('aria-valuenow', parseInt(bar.attr('aria-valuenow')) + parseInt(data.new_networks) + parseInt(data.exist_networks));
    		bar.attr('style', 'width: ' + (parseInt(bar.attr('aria-valuenow')) / importdata.total * 100) + '%');

    		$.each(data, function(index, value) {
			    results.children('.'+index).text(index + ': ' +value);
			});
    	}
    	
    	progress.hide();
    	importsource[0].disabled = true;
    	importsource[1].disabled = true;
    	startbutton.attr('disabled','disabled');
    	startbutton.text('Выберите БД для импорта');

    	importsource.change(function() {
    		if ($("[name='importsource']:checked").val() == 'local') {
                importdata = localdata;
            } else if ($("[name='importsource']:checked").val() == 'uploaded') {
                importdata = uploadeddata;
            } else {
                alert('Ошибка!');
                return false;
            }
            updateinfo(importdata);
            startbutton.removeAttr('disabled');
            startbutton.text('Начать импорт');
    	});

    	@if($hash)
	    	localdata.type = 'wigle';
	    	localdata.uploadedfile = '{{Import::$db_path}}';
	    	localdata.hash = '{{$hash}}';
	    	localdata.offset = 0;
	    	localdata.count = 300;
	    	localdata.total = '{{$total}}';
	    	importsource[0].disabled = false;
	    	infoblock.text('');
    	@endif

   
    //Импорт

    startbutton.on('click', function(){

        // Создадим новый объект типа FormData
        var data = new FormData();
        // Добавим в новую форму файл
        data.append('filename', importdata.uploadedfile);
        data.append('offset', importdata.offset);
        data.append('count', importdata.count);
        data.append('type', importdata.type);


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
            beforeSend: function() {
            	startbutton.attr('disabled','disabled');
            },
            // Функция удачного ответа с сервера
            success: function(result) { 	
                // Получили ответ с сервера (ответ содержится в переменной result)
                // Если в ответе есть объект filelink
                if (result.error) {	
                	// Выведет текст ошибки с сервера
                    progress.text(result.err_message);
                    progress.show();	
                } else {
                    // Сохраним значение в input'е
                    importdata.offset = parseInt(importdata.offset) + parseInt(importdata.count);
                    updateinfo(importdata);
                    // Скроем ошибку
                    progress.show();
                    parseprogress(result);
                    startbutton.removeAttr('disabled');

                    //Если все импортировано
                    if(parseInt(importdata.offset) >= parseInt(importdata.total)) {
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
            beforeSend: function() {
            	status.text('Загружаем... не шевелитесь... ');
                status.show();
            },
            // Функция удачного ответа с сервера
            success: function(result) { 	
                // Получили ответ с сервера (ответ содержится в переменной result)
                // Если в ответе есть объект filelink
                if (result.filelink && result.total && result.hash) {	

                	uploadeddata.type = 'wigle';
			    	uploadeddata.uploadedfile = result.filelink;
			    	uploadeddata.hash = result.hash;
			    	uploadeddata.offset = 0;
			    	uploadeddata.count = 300;
			    	uploadeddata.total = result.total;
			    	importsource[1].disabled = false;
                   
                    // Скроем ошибку
                    status.text('Успех: ' + result.filelink);
                    status.show();

                    startbutton.removeAttr('disabled');
                    startbutton.text('Начать импорт');
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
