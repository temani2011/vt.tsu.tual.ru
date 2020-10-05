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
    //console.log(e.data);
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

/*Main.php*/

/*blog.php*/
$(document).on('focusin', function(e) {
  if ($(e.target).closest(".mce-window").length) {
    e.stopImmediatePropagation();
  }
});

$('#content').on('click', '#btn-add-blog', function(e){
  $.get('/ajax/imgesList.php', function(data) {
    console.log(data);
  });
  $.get('/ajax/docsList.php', function(data) {
    console.log(data);
  });
  tinymce = initTinyMCE('#text-modal-create-blog');
  tinymce.get('text-modal-create-blog').setContent('');
});

$('#content').on('click', '#btn-create-blog', function(e){
  var nname = $('#blog-name-text-input-create').val();
  var txt = tinyMCE.get('text-modal-create-blog').getContent();
  var id = window.location.hash.split('=')[1];
  console.log(txt);
  tinyMCE.remove();
  $.post('/ajax/blog.php', {id: id, command: 'create', txt: txt, nname: nname}, function(data) {
          //console.log(data);
          alert(data);
          window.location.hash = '';
          window.location.hash = '#blog.php' + "?id=" + id;
        });
  $('#create-blog-modal').modal('hide');
});

$('#content').on('click', '#btn-update-blog', function(e){
  var nname = $('#blog-name-text-input-update').val();
  var txt = tinyMCE.get('text-modal-update-blog').getContent();
  var pid = $('#btn-update-blog').attr('pid');
  var id = window.location.hash.split('=')[1];
  console.log(txt,pid,nname);
  tinyMCE.remove();
  $.post('/ajax/blog.php', {id: id, pid: pid, command: 'update', txt: txt, nname: nname} , function(data) {
          //console.log(data);
          alert(data);
          window.location.hash = '';
          window.location.hash = '#blog.php' + "?id=" + id;
        });
  $('#update-blog-modal').modal('hide');
});

$('#content').on('click', '#dd-blog-delete', function(e){
  e.preventDefault()
  var pid = $(this).parent().parent().parent().attr('pid');
  var id = window.location.hash.split('=')[1];
  tinyMCE.remove();
  $.post('/ajax/blog.php', {id: id, pid: pid, command: 'delete'} , function(data) {
          //console.log(data);
          alert(data);
          window.location.hash = '';
          window.location.hash = '#blog.php' + "?id=" + id;
        });
});

$('#content').on('click', '#dd-blog-update', function(e){
  e.preventDefault();
  var pid = $(this).parent().parent().parent().attr('pid'); 
    var id = window.location.hash.split('=')[1];
  $.post('/ajax/blog.php', {id: id, pid: pid, command: 'modal'} , function(data) {
    data = JSON.parse(data);
    tinymce = initTinyMCE('#text-modal-update-blog');
    tinymce.get('text-modal-update-blog').setContent(data.text);
    $('#blog-name-text-input-update').val(data.name);
    $('#btn-update-blog').attr('pid', data.id);
  });
  $('#update-blog-modal').modal('show');
});

$('#content').on('change', '#blog-search-bar', function(){
  $('.blog-card').hide();
  var txt = $('#blog-search-bar').val();
  $('.blog-card > div.card-header > h3').each(function(){
   if($(this).text().toUpperCase().indexOf(txt.toUpperCase()) != -1){
            //console.log($(this).text());
            $(this).parents('.blog-card').show();
          }
        });
});
/*album.php*/
var albhash = "";

$('#content').on('change','#img-upload', function(e) {
    //console.log("asdasd");
    var hash = window.location.hash;
    var user_id = location.hash.split('=')[1];
    var file_data = $('#img-upload').prop('files')[0]; 
    var form_data = new FormData();
    form_data.append('file', file_data);
    form_data.append('uid' , user_id);
    $.ajax({
            url: '/ajax/album.php', // point to server-side PHP script 
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

$('#content').on('click','#btn-add-comment', function(e) {
  var mid = $(this).attr('mid');
  var iid = $(this).attr('iid');
  var uid = $(this).attr('uid');
  var txt = $('#txt-comment').val();
  console.log(iid + uid);
  $.post('/ajax/album.php', { id : mid, img_id: iid, user_id: uid, txt: txt, command : 'comment' }, function(data) {
    console.log(data);
    $('#msg_history').html(data);
  });
});

$('#content').on('click','#btn-like', function(e) {
  var mid = $(this).attr('mid');
  var iid = $(this).attr('iid');
  var uid = $(this).attr('uid');
  console.log(iid + uid);
  $.post('/ajax/album.php', { id : mid, img_id: iid, user_id: uid, command : 'like' }, function(data) {
    data = $.parseJSON(data);
    console.log(data);
    if(data[0] == 1) {
      $('#btn-like').css('color', 'red');
    }
    else{
      $('#btn-like').css('color', '#707070');
    }
    var txt = data[1].toString(); 
    $('#likes_count').text(txt);
  });
});

$('#content').on('hidden.bs.modal','#myModal', function(e) {
  e.preventDefault();
    //console.log('asda');
    $('#carousel-imgs > img').each(function(index, el) {
      $(this).parent().removeClass('active');
    });
    $('#myModal').modal('hide');
    $('#myModal').remove();
    var str = "";
    if(albhash.length > 0)
      str = albhash;
    else str = oldHash;
    window.history.pushState('obj', 'PageTitle', str);
  });

$('#content').on('click','#imgs-column > a', function(e) {
  e.preventDefault();
  var iid = $(this).attr('id');
  var uid = location.hash.split('=')[1];
  $.get('/ajax/album.php', {id : uid, img_id: iid},  function(data) {
    console.log(data);
    $('#content').append(data);
    $('#carousel-imgs > img').each(function(index, el) {
      if($(this).attr('id')==iid)
        $(this).parent().addClass('active');
    });
    albhash = window.location.hash;
    window.history.pushState('obj', 'PageTitle', window.location.hash + "&img_id=" + iid);
    console.log(albhash);
    $('#myModal').modal('show');
  });
});
/*progress.php*/
$('#content').on('click','#btn-progress-select', function(e) {
  var hash = window.location.hash;
  var gid = $(this).attr('gid');
  var sid = $('#progress_subjects').val();
  $.get('ajax/progress.php', {sid: sid, group: gid, global: true}, function(data) {
      //console.log(data);
      $('#progress_sdf').remove();
      $('#content').append(data);
    });
  window.history.pushState('obj', 'PageTitle', "#progress.php?group="+ gid + "&sid=" + sid + "&global=" + true);
}); 

/*profile.php*/
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


/*docs.php*/
$('#content').on('click','#btn-update-accept-doc', function(e, doc_id_for_update) {
   var hash = window.location.hash;
   var new_name = $('#doc-name-text-input').val();
   var doc_id = $('input:hidden[name="hddocid"]').val();
   console.log(new_name, doc_id);
   $.post('ajax/docs.php', {doc_id: doc_id, command: 'update', new_name : new_name}, function(data) {
    alert(data);
    window.location.hash = '';
    window.location.hash = hash;
  });
   $('#update-doc-dialog-modal').modal('hide');
 });

 $('#content').on('click','#btn-update-docs', function(e) {
  doc_id = $(this).attr('docid'); 
  oldname = $(this).closest('tr').find('.doc_bs').text();
  $('#doc-name-text-input').val(oldname);
  $('#update-doc-dialog-modal').modal('show');
  $('input[name=hddocid]').remove();
  $('#update-doc-dialog-modal').append('<input type="hidden" name="hddocid" value ="' + doc_id + '"/>');
});

 $('#content').on('click','#btn-delete-docs', function(e) {
  var hash = window.location.hash;
  var doc_id = $(this).attr('docid');
  var r = confirm("Вы собираетесь удалить файл!");
  if (r == true) 
  {
    $.post('ajax/docs.php', {doc_id: doc_id, command: 'delete'}, function(data) {
          //console.log(data);
          alert(data);
          window.location.hash = '';
          window.location.hash = hash;
        });
  } 
});

 $('#content').on('change','#file-upload', function(e) {
  var hash = window.location.hash;
  var catalog_id = location.hash.split('=')[1];
  var file_data = $('#file-upload').prop('files')[0]; 
  var form_data = new FormData();
  form_data.append('file', file_data);
  form_data.append('catalog_id' , catalog_id);
  $.ajax({
            url: 'ajax/docs.php', // point to server-side PHP script 
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

/*messages.php*/
$('#content').on('click','#btn-add-participants-dialog', function(e)
{
  var array = {};
  var count = 0;
  var did = location.hash.split('=')[1];
  var check = $('#modal-user-checkbox-add-participant:checked');
  check.each(function() {
    array[count] = $(this).val();
    count++;
  });
  if(count < 1){ alert('Вы не выбрали ни одного участника'); return;}
  $.post('/ajax/messages.php', {arr: JSON.stringify(array), did: did, command: "add-part"}, function(data) {
          //console.log(data);
          alert(data);
          window.location.hash = '';
          window.location.hash = '#messages.php';
        });

  $('#add-participants-dialog-modal').modal('hide');
});

$('#content').on('click','#dd-dialog-delete', function(e)
{
  e.preventDefault();
  var did = location.hash.split('=')[1];
  var r = confirm("Вы собираетесь удалить диалог!");
  if (r == true) 
  {
    $.post('/ajax/messages.php', {did: did, command: "delete"}, function(data) {
            //console.log(data);
            alert(data);
            window.location.hash = '';
            window.location.hash = '#messages.php';
          });
  }
});

$('#content').on('click','#btn-update-dialog', function()
{
  var array = {};
  var count = 0;
  var did = location.hash.split('=')[1];
  var uncheck = $('.modal-user-checkbox:not(:checked)');
  var txt = $('#dialog-name-text-input-update').val();
  uncheck.each(function() {
    array[count] = $(this).val();
    count++;
  });
  $.post('/ajax/messages.php', {arr: JSON.stringify(array), did: did, command: "update", dname: txt}, function(data) {
          //console.log(data);
          alert(data);
          window.location.hash = '';
          window.location.hash = '#messages.php';
        });

  $('#settings-dialog-modal').modal('hide');
});

$('#content').on('keydown','#msg', function(e) {
  var id = $('#userId').val();
  var did = $('#dId').val();
  var msg = $(this).val();
  var data = {id : id, did : did, msg : msg };
  if (e.keyCode == 13) {
      //console.log(msg);
      conn.send(JSON.stringify({command: "message", message: msg, id : id ,did: did}));
      $(this).val('');
      return false;
    }
    wsMessage();
  });

$('#content').on('click','.chat_list', function()
{
  var c_did = $(this).attr('did');
  $.get('/ajax/messages.php', {did : c_did}, function(data) {
      //console.log(data);
      $('#content').empty();
      $('#content').append(data);
      var tinymce = initMessageTinyMCE();
    });
  window.history.pushState('obj', 'PageTitle', "#messages.php?did=" + c_did);
  oldHash = location.hash;
  wsOpen(c_did);
});

$('#content').on('click','#btn-create-dialog', function()
{
  var array = {};
  var msg = $('#msg-modal').val();
  var count = 0;
  var check = $('.modal-user-checkbox:checked');
  var txt = $('#dialog-name-text-input-create').val();
  if(check.length == 0) { alert("Выберете хотя бы одного пользователя"); return; }
  if(!txt) { alert("Введите название!"); return; }
  else
  {
    check.each(function() {
      array[count] = $(this).val();
      count++;
    });
    if(count == 1)
    {
      var text1 = check.parent().siblings('h5').text();
      var text2 = $('.card > .inbox_chat > .chat_list > .chat_people > .chat_ib > h5 > div');
      if(text2.text().toUpperCase().indexOf(text1.toUpperCase()) != -1){
        return alert('У вас уже создан диалог с этим пользователем!');
      }
    }
  }
  $.post('/ajax/messages.php', {arr: JSON.stringify(array), command: "create", dname: txt, msg : msg}, function(data) {
            //console.log(data);
            alert(data);
            window.location.hash = '';
            window.location.hash = '#messages.php';
          });
  $('#create-message-dialog-modal').modal('hide');
});

$('#content').on('click','.chat_list_modal', function()
{
  $('#dialog-name-text-input-create').val('');
  var checkbox = $(this).find('input[type="checkbox"]');
  checkbox.prop('checked', !checkbox.prop('checked'));
  count = $('.modal-user-checkbox:checked').length;
  if(count > 1)
  { 
    $('#hidden-dialog-name').show('normal');
  }
  else
  { 
    $('#hidden-dialog-name').hide('fast');
    $('#dialog-name-text-input-create').val($('.modal-user-checkbox:checked').parent().siblings('h5').text());
  }
});

$('#content').on('change', '.modal-user-search', function(){
  $('.chat_list_modal').hide();
  var txt = $('.modal-user-search').val();
  $('div.chat_ib > h5').each(function(){
   if($(this).text().toUpperCase().indexOf(txt.toUpperCase()) != -1){
            //console.log($(this).text());
            $(this).parents('.chat_list_modal').show();
          }
        });
});

$('#content').on('change', '#messages-search-bar', function(){
  $('.chat_list').hide();
  var txt = $('#messages-search-bar').val();
  $('.card > .inbox_chat > .chat_list > .chat_people > .chat_ib > h5 > div').each(function(){
   if($(this).text().toUpperCase().indexOf(txt.toUpperCase()) != -1){
            //console.log($(this).text());
            $(this).parents('.chat_list').show();
          }
        });
});

/*caf.php*/
$('#content').on('change', '#search-criteria', function(){
  $('.row-user').hide();
  var count = 0;
  var txt = $('#search-criteria').val();
  $('.row-user > .center-text-block > .info > .labeled.name > a').each(function(){
   if($(this).text().toUpperCase().indexOf(txt.toUpperCase()) != -1){
						//console.log($(this).text());
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
//function update($worked, op)
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

$('#content').on('click','a.page-link', function(e)
{
  e.preventDefault();
  var page = $(this).attr('href').split('#');
  if($(this).parent().is('[data-day]')) data_day = $(this).parent().attr('data-day');
  if($(this).parent().is('[data-week]')) data_week = $(this).parent().attr('data-week');
  $.get('ajax/' + page[0], {week: data_week, day: data_day}, function(data) {
					//console.log(data);
					if(data){
						$('#containerSchedule').empty();
						$('#containerSchedule').append(data);
					}
				});
  window.history.pushState('obj', 'PageTitle', "#schedule.php?week=" + data_week +"&day=" + data_day);
});

/*news.php*/
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
  console.log(page);
  $.post('/ajax/login.php', function(data) {
          console.log(data);
          window.location.href = "/";
        });
});

/*page hashchager*/
$(window).on('hashchange', function(e){
        //console.log(oldHash);
        var newHash = location.hash;
        if(!newHash) return oldHash;
        var tid1, tid2;
        if(newHash.indexOf('schedule.php') > 0)
        {
          tid1 = setTimeout(update_for_cur_time, 1000);
          tid2 = setTimeout(update_for_remain_time, 1000);
        } 
        else { clearTimeout(tid1); clearTimeout(tid2); } 
        if(oldHash.indexOf('messages.php?did=') > 0) wsClose();       
        oldHash = newHash; 
        if(newHash.indexOf('messages.php?did=') > 0) wsOpen(newHash.split('=')[1]);
        loadContent();
				//$('#content').hide('fast',loadContent);
				$('#content').append('<img src="/img/logo/ajax-loader.gif" height="22" href="#" id="ajax-loader"/>');
				$('#ajax-loader').fadeIn('normal');
				function loadContent() {
          newHash = newHash.substring(1);
          //console.log(newHash.indexOf('main.php'));
          if(newHash.indexOf('album.php') >= 0 || newHash.indexOf('blog.php') >= 0 || newHash.indexOf('main.php') >= 0 || newHash.indexOf('messages.php') >= 0){
            path = "/ajax/";
            //console.log(path);
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
$('#wrapper').on('click','ul li',function(e){
  $(this).addClass('active');
  $(this).siblings().removeClass('active');
});

/*sidebar footer header loaders*/
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
});