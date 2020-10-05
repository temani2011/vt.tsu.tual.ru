$(function(){

  var data_day = 0;
  var data_week = 'both';
  var conn;
  var oldHash = '';
  
  function wsOpen(c_did)
  {
    conn = new WebSocket('ws://localhost:8080');
    conn.onopen = () => conn.send(JSON.stringify({command: "subscribe", channel: c_did}));
    console.log("Connection established!");
  }
  function wsClose()
  {
    conn.close();
    console.log("Connection lost!");
  }
  function wsMessage()
  {
    conn.onmessage = function(e) {
      console.log(e.data);
      $('#messages').append(e.data);
      $('#msg_history').scrollTop($('#msg_history')[0].scrollHeight);
    };
  }
  function initTinyMCE(textarea)
  {
    tinymce.init({
      selector: textarea,
      language_url : '../vendor/tinymce/tinymce/ru.js',
      theme: 'modern',
      height: 300,
      branding: false,
      plugins : 'advlist autolink link image lists charmap print preview fullscreen',
      image_advtab: true,
      image_caption: true,
      image_list : "/ajax/imgesList.php",
      link_list : "/ajax/docsList.php"
    });
    return tinymce;   
  }
  function initMessageTinyMCE()
  {
   tinymce.init({
    selector: '#msg',
    language_url : '../vendor/tinymce/tinymce/ru.js',
    statusbar: true,
    menubar: false,
    theme: 'modern',
    branding: false,
    plugins : 'link image emoticons',
    toolbar: "bold italic | link image emoticons",
    image_list : "/ajax/imgesList.php",
    link_list : "/ajax/docsList.php",
    setup: function(editor) {
      editor.on('keydown', function(e) 
      { 
        var id = $('#userId').val();
        var did = $('#dId').val();
        var msg = this.getContent();
        var data = {id : id, did : did, msg : msg };
        if(e.ctrlKey && e.keyCode == 13)
        {
          e.preventDefault();
          this.getContent().execCommand('mceInsertContent', false, "\n");
        }
        else if (e.keyCode == 13){
          e.preventDefault();
          console.log(msg);
          conn.send(JSON.stringify({command: "message", message: msg, id : id ,did: did}));
          $(this).val('');
        }
        wsMessage();
      });
    }
  });
   return tinymce;
 }

  /*journal.php*/
  $('#content').on('click', '#btn-delete-journal-group', function(e){
    e.preventDefault();
    hash = window.location.hash;
    command = 'delete_group';
    var gid = $(this).attr('gid');
    var r = confirm("Вы собираетесь удалить группу и ее студентов!");
    if (r == true) 
    {
      $.post('ajax/profile.php', {id: gid, command: command} , function(data) {
        alert(data);
        window.location.hash = '';
        window.location.hash = hash;
      });
    }
  });

  $('#content').on('click', '#btn-update-journal-group', function(e){
    e.preventDefault();
    command = 'update_group';
    var gid = $(this).attr('gid');
    $.get('ajax/profile.php', {id: gid, command: command} , function(data) {
      $('#content').empty();
      $('#content').append(data);
      $('.group').mask("000000");
    });
    window.history.pushState('obj', 'PageTitle', "#profile.php?command="+ command + "&id=" + gid);
  });

  /*profile.php*/
  /*
  $('#content').on('change','#user-img-upload', function(e) {
  var hash = window.location.hash;
  var user_id = location.hash.split('=')[2];
  var file_data = $('#user-img-upload').prop('files')[0]; 
  var form_data = new FormData();
  form_data.append('file', file_data);
  form_data.append('uid' , user_id);
  $.ajax({
            url: 'ajax/profile.php', // point to server-side PHP script 
            dataType: 'text',  // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,                          
            type: 'post',
            success: function(data){
                alert(data); // display response from the PHP script, if any
                window.location.hash = '';
                window.location.hash = hash;
              }
            });
  });
  */
  $('#content').on('click','#btn-update-user, #btn-update-group', function(e) {
    var user_id = location.hash.split('=')[2];
    if($(this).attr('id') == 'btn-update-user') command = 'update_user';
    else command = 'update_group';
    var array_text = new Array();
    var array_combobox = new Array();
    $('input[type=text]:gt(0)').each(function(){
      array_text.push($(this).val());
    });
    $('select').each(function(){
      array_combobox.push($(this).val());
    });
    $.post('ajax/profile.php', {arr_t: array_text, arr_c: array_combobox, id: user_id, command: command}, function(data) {
      alert(data);
      window.location.hash = '';
      if(command == 'update_user') window.location.hash = '#caf.php';
      else window.location.hash = '#journal.php';
    });
  });

  $('#content').on('click','#btn-create-user, #btn-create-group', function(e) {
    var command;
    if($(this).attr('id') == 'btn-create-user') command = 'create_user';
    else command = 'create_group';
    var array_text = new Array();
    var array_combobox = new Array();
    $('input[type=text]:gt(0)').each(function(){
        array_text.push($(this).val());
      console.log($(this).val());
    });
    $('select').each(function(){
        array_combobox.push($(this).val());
      console.log($(this).val());
    });

    $.post('ajax/profile.php', {arr_t: array_text, arr_c: array_combobox, command: command}, function(data) {
      alert(data);
      window.location.hash = '';
      window.location.hash = '#caf.php';
    });
  });

  $('#content').on('change','#groups_profile', function(e) {
    if($(this).prop('value') == 'student') $('.hidden-combo-profile').show();
    else $('.hidden-combo-profile').hide();
  });

  /*caf.php*/

  $('#content').on('click', '#dd-caf-update, #btn-update-journal-user, #btn-update-profile-user', function(e){
    e.preventDefault();
    command = 'update_user';
    var uid = $(this).attr('uid');
    $.get('ajax/profile.php', {id: uid, command: command} , function(data) {
      $('#content').empty();
      $('#content').append(data);
      $('.date').mask("00.00.0000");
      $('.phone').mask("0(000)-000-00-00");
    });
    window.history.pushState('obj', 'PageTitle', "#profile.php?command="+ command + "&id=" + uid);
  });

  $('#content').on('click', '#dd-caf-delete, #btn-delete-journal-user, #btn-delete-profile-user', function(e){
    e.preventDefault();
    var hash = window.location.hash; 
    var uid = $(this).attr('uid'); 
    var r = confirm("Вы собираетесь удалить пользователя!");
    if (r == true) 
    {
      $.post('ajax/profile.php', {id: uid, command: 'delete_user'} , function(data) {
        alert(data);
        window.location.hash = '';
        if($(this).attr('id') == 'btn-delete-profile-user') window.location.hash = '#caf.php';
        else window.location.hash = hash;
      });
    }
  });

  $('#content').on('click', '#btn-create-profile', function(){
    var command;
    if($('input:radio[name="example-radios2"]:checked').val() == 'group') command = 'create_group';
    else command = 'create_user';
    $.get('ajax/profile.php', {command: command}, function(data) {
      //console.log(data);
      $('#content').empty();
      $('#content').append(data);
      $('.date').mask("00.00.0000");
      $('.phone').mask("0(000)-000-00-00");
    });
    window.history.pushState('obj', 'PageTitle', "#profile.php?command="+ command);
  });
  /*
  $('#content').on('change', '#search-criteria', function(){
    $('.row-user').hide();
    var count = 0;
    var txt = $('#search-criteria').val();
    $('.row-user > .center-text-block > .info > .labeled.name > a').each(function(){
     if($(this).text().toUpperCase().indexOf(txt.toUpperCase()) != -1){
      count++;
      $(this).parents('.row-user').show();
    }
  });
    $('#caf-header').text('Кафедра ' + count);
  });

  $('#content').on('change','#groups', function() {
    group = $(this).val();
    $.get('ajax/caf.php', {role : 'student', group : group}, function(data) {
     var foo = $('<div />').html(data).find('#main-column')
     $('#main-column').empty();
     $('#main-column').append(foo);
     $('#main-column').children(':first').unwrap();
   });
    window.history.pushState('obj', 'PageTitle', "#caf.php?role=student&group=" + group);
  });

  $('#content').on('change','input:radio[name="example-radios"]', function() {
    role = $(this).val();
    if ($(this).val()=='student') {
     $('#groups').attr('disabled', false);
   } 
   else{
     $('#groups').attr('disabled', true);
     $('#groups').val('all');
   }

   $.get('ajax/caf.php', {role : role}, function(data) {
     var foo = $('<div />').html(data).find('#main-column')
     $('#main-column').empty();
     $('#main-column').append(foo);
     $('#main-column').children(':first').unwrap();
   });
   window.history.pushState('obj', 'PageTitle', "#caf.php?role=" + role +"&group=all");
 });
  
  /*Schedule.php*/

  /*
  function update_for_remain_time()
  {

    var $worked = $('#remainsBlock');
    var myTime = $worked.html();
    var ss = myTime.split(":");
    var dt = new Date();
    dt.setHours(ss[0]);
    dt.setMinutes(ss[1]);
    dt.setSeconds(ss[2]);
    var dt2 = new Date(dt.valueOf() - 1000);
    var ts = dt2.toTimeString().split(" ")[0];
    $worked.html(ts);
    setTimeout(update_for_remain_time, 1000);  
  }
  function update_for_cur_time()
  {
    var $worked = $('#timeBlock');
    var myTime = $worked.html();
    var ss = myTime.split(":");
    var dt = new Date();
    dt.setHours(ss[0]);
    dt.setMinutes(ss[1]);
    dt.setSeconds(ss[2]);
    var dt2 = new Date(dt.valueOf() + 1000);
    var ts = dt2.toTimeString().split(" ")[0];
    $worked.html(ts);
    setTimeout(update_for_cur_time, 1000);  
  }
  */
  
  $('#content').on('click','#btn-accept-update-schedule', function(e){
    var schedid = $(this).attr('schedid');
    var array_text = new Array(),
    array_combobox = new Array();
    $('.update-modal-custom-input').each(function(){
      array_text.push($(this).val());
    });
    $('.update-modal-custom-select').each(function(){
      array_combobox.push($(this).val());
    });
    $.post('ajax/schedule.php', {arr_t: array_text, arr_c: array_combobox, id: schedid, command: 'update'} , function(data) {
      //console.log(data);
      alert(data);
      hash = window.location.hash;
      window.location.hash = '';
      window.location.hash = hash;
    });

    $('#update-schedule-modal').modal('hide');
  }); 


  $('#content').on('click','#btn-update-schedule', function(e){
    var schedid = $(this).attr('schedid');
    $.post('ajax/schedule.php', {id: schedid, command: 'modal'} , function(data) {
      //console.log(data);
      data=JSON.parse(data);
      var ti = 0;
      $('.update-modal-custom-select').each(function(){
        ti++;
        if(ti==5) ti = 7;
          $(this).children('option').each(function() {
            if($(this).val() == data[ti])
              $(this).attr("selected", true);
            else $(this).attr("selected", false);
          });
      });
      ti = 5;
      $('.update-modal-custom-input').each(function(){
        $(this).val(data[ti]);
        ti++;
      });
    });
    $('#btn-accept-update-schedule').attr('schedid', schedid);
    $('#update-schedule-modal').modal('show');
  }); 

  $('#content').on('click','#btn-delete-schedule', function(e){
    e.preventDefault();
    hash = window.location.hash;
    var schedid = $(this).attr('schedid');
    var r = confirm("Вы собираетесь удалить занятие!");
    if (r == true) 
    {
      $.post('ajax/schedule.php', {id: schedid, command: 'delete'} , function(data) {
        alert(data);
        window.location.hash = '';
        window.location.hash = hash;
      });
    }
  });

  $('#content').on('click','#btn-create-schedule', function(e){
    var array = window.location.hash.split('='); 
    var array_text = new Array(),
        array_combobox = new Array();
    $('input[type=text]:gt(0)').each(function(){
      array_text.push($(this).val());
    });
    $('.modal-custom-select').each(function(){
      array_combobox.push($(this).val());
    });
    $.post('ajax/schedule.php', {arr_t: array_text, arr_c: array_combobox, command: 'create' }, function(data) {
      console.log(data);
      if(data)
      {
        if(data.indexOf('wrongPT') > 0) data = 'У преподавателя уже есть занятие на выбранной неделе в выбранный день';
        if(data.indexOf('wrongPG') > 0) data = 'У группы уже есть занятие на выбранной неделе в выбранный день';
        alert(data);
      }
    });
  });

  var windowPushStateHash = location.hash;

  $('#content').on('change','#schedule_groups, #schedule_teachers', function(e)
  {
    var role_id = $(this).val(),
    week = $(this).attr('weekValue'),
    role = '';
    if($(this).attr('id') == 'schedule_groups') role = 'student';
    else if($(this).attr('id') == 'schedule_teachers') role = 'teacher';
    //var optText = $("option:selected", this).text();
    $.get('ajax/schedule.php', {week: week, role_id: role_id, role: role}, function(data) {
      //console.log(data);
      if(data){
        $('#schedule_week').remove();
        $('#content').append(data);
        //if(role == 'student') $('#Schedule-header-title').text('Расписание группы ' + optText);
        //else if (role == 'teacher') $('#Schedule-header-title').text('Расписание преподавателя ' + optText);
      }
    });
    window.history.pushState('obj', 'PageTitle', "#schedule.php?role_id=" + role_id +"&week=" + week + "&role=" + role);
    windowPushStateHash = "#schedule.php?role_id=" + role_id +"&week=" + week + "&role=" + role;
  });

  $('#content').on('click','a.page-link', function(e)
  {
    e.preventDefault();
    var data_week = $(this).parent().attr('data-week'),
        role_id = getHashValue('role_id'),
        role = getHashValue('role');
 
    $.get('ajax/schedule.php', {week: data_week, role_id: role_id, role: role }, function(data) {
      if(data){
        $result = $(data).find('#underContainerSchedule');
        //console.log(data);
        $('#containerSchedule').empty();
        $('#containerSchedule').append($result);
      }
    });
    window.history.pushState('obj', 'PageTitle', "#schedule.php?role_id=" + role_id +"&week=" + data_week + "&role=" + role);
    windowPushStateHash = "#schedule.php?role_id=" + role_id +"&week=" + data_week + "&role=" + role;
  });

  function getHashValue(key) {
    console.log(location.hash);
    console.log(windowPushStateHash);
    var matches = windowPushStateHash.match(new RegExp(key+'=([^&]*)'));
    console.log(matches);
    return matches ? matches[1] : null;
  }

  // usage
  //var hash = getHashValue('hash');
  /*news.php*/
  /*
  $('#content').on('change', '#news-search-bar', function(){
    $('.news-card').hide();
    var txt = $('#news-search-bar').val();
    $('.news-card > div.card-header > h3').each(function(){
     if($(this).text().toUpperCase().indexOf(txt.toUpperCase()) != -1){
            //console.log($(this).text());
            $(this).parents('.news-card').show();
          }
        });
  });

  /*login.php*/
  $('#header1').on('click','#a-out', function(e)
  {
    e.preventDefault();
    var page = $(this).attr('href');
    $.post('../ajax/login.php', function(data) {
          //console.log(data);
        });
    window.location.href = page;
  });

  /*page hashchager*/
  /*
  $(window).on('hashchange', function(e){
        //console.log(oldHash);
        tinyMCE.remove();
        var newHash = location.hash;
        if(!newHash) return e.oldURL;
        /*
        var tid1, tid2;
        if(newHash.indexOf('schedule.php') > 0)
        {
          tid1 = setTimeout(update_for_cur_time, 1000);
          tid2 = setTimeout(update_for_remain_time, 1000);
        } 
        else { clearTimeout(tid1); clearTimeout(tid2); } 
        if(oldHash.indexOf('messages.php?did=') > 0) wsClose();       
        oldHash = newHash; 
        if(newHash.indexOf('messages.php?did=') > 0) { wsOpen(newHash.split('=')[1]); var tinymce = initMessageTinyMCE(); }
        
        loadContent();
        //$('#content').hide('fast',loadContent);
        $('#content').append('<img src="/img/logo/ajax-loader.gif" height="22" href="#" id="ajax-loader"/>');
        $('#ajax-loader').fadeIn('normal');
        function loadContent() {
          newHash = newHash.substring(1);
          //console.log(newHash.indexOf('main.php'));
          if(newHash.indexOf('album.php') >= 0 || newHash.indexOf('blog.php') >= 0 || newHash.indexOf('main.php') >= 0 || newHash.indexOf('messages.php') >= 0 ){
            path = "/ajax/";
          }
          else path = 'ajax/';
          $('#content').load(path + newHash,'',showNewContent);
        }
        function showNewContent() {
        //$('#content').show('normal',hideLoader);
        hideLoader();
      }
      function hideLoader() {
        $('#ajax-loader').fadeOut('fast');
      }
    });

  if (window.location.hash) {
    $(window).trigger('hashchange');
  }

  /*sidebar toggler and active hover*/
  /*
  $('#wrapper').on('click','ul li',function(e){
    $(this).addClass('active');
    $(this).siblings().removeClass('active');
  });

  /*sidebar footer header loaders*/
  /*
  $("#header1").load("../view/header.php #header", function(){
    $('#sidebarCollapse').click(function () {
     $('#sidebar').toggleClass('toggle');
   });
  });

  $("#footer1").load("../view/footer.php #footer"); 

  $("#sidebar1").load("../view/sidebar.php #sidebar", function (e) {
    $("#list > li > a").each(function(){
      if ($(this).attr("href") == window.location.hash){
       $(this).parent().addClass("active");
     }
   });
  });
  */
});