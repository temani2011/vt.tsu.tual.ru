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
 /*blog.php*/
 /*
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
 /*
 var albhash = "";

 $('#content').on('change','#img-upload', function(e) {
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
  var gid = $('#progress_groups').val();
  var sid = $('#progress_subjects').val();
  $.get('ajax/progress.php', {sid: sid, group: gid, global: true}, function(data) {
      //console.log(data);
      $('#content').empty();
      $('#content').append(data);
    });
  window.history.pushState('obj', 'PageTitle', "#progress.php?group="+ gid + "&sid=" + sid + "&global=" + true);
}); 


 $('#content').on('change','#progress_groups', function(e) {
  var group = $('#progress_groups').val();
  $.post('ajax/progress.php', {group: group, command: 'load_combo'} , function(data) {
    data = $.parseJSON(data);
    console.log(data);
    $('#progress_subjects').find('option').remove();
    $.each(data, function(key, value) {   
     $('#progress_subjects')
     .append($("<option></option>")
      .attr("value",value['id1'])
      .text(value['name'])); 
   });
  });
});  

 $('#content').on('click','#btn-progress-accept', function(e) {
  var hash = window.location.hash;
  var sid = location.hash.split('=')[2];
  var count = 0;
  var uid_array = {}, score_array = {}, attend_array = {};
  $('tr[name=progress-user]').each(function() {
    uid_array[count] = $(this).attr('uid');
    if($(this).find('#attendCheckBox').prop('checked'))
      attend_array[count] = 1;
    else attend_array[count] = 0;
    if($(this).find('#scoreSelect').val())
      score_array[count] = $(this).find('#scoreSelect').val();
    else score_array[count] = 0;
    count++;
  });
  if($(this).attr('exist')) command = 'update';
  else command = 'create';
    //console.log(uid_array, sid, score_array, attend_array, command, $(this).attr('exist'));
    $.post('ajax/progress.php', {uid: uid_array, sid: sid, command: command, score_arr: score_array, attend_arr: attend_array} , function(data) {
      console.log(data);
      window.location.hash = '';
      window.location.hash = hash;
    });
  });  


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
 /*docs.php*/
 $('#content').on('click','#btn-delete-cat', function(e) {
  var hash = window.location.hash;
  var cat_id = $(this).attr('catid');
  var cat_name = $(this).parents('tr').children('td:nth-child(2)').text();
  var r = confirm("Вы собираетесь удалить каталог и все его содержимое!");
  if (r == true) 
  {
    $.post('ajax/docs.php', {cat_id: cat_id, cat_name: cat_name, command: 'delete'}, function(data) {
          //console.log(data);
          alert(data);
          window.location.hash = '';
          window.location.hash = hash;
        });
  } 
});

 $('#content').on('click','#btn-update-accept-cat', function(e) {
   var hash = window.location.hash;
   var rolecheck = 'teacher';
   var rwcheck = 'r';
   var old_name = $('input:hidden[name="hdcatname"]').val();
   var new_name = $('#cat-name-text-input').val();
   var cat_id = $('input:hidden[name="hdcatid"]').val();
   if($('input:checkbox[name="rolecheckU"]').prop('checked')) rolecheck = 'student';
   if($('input:checkbox[name="rwcheckU"]').prop('checked')) rwcheck = 'rw';
   console.log(cat_id, old_name, new_name, rolecheck, rwcheck);
   $.post('ajax/docs.php', {cat_id: cat_id, command: 'update', cat_name: old_name, new_name : new_name, cat_rw: rwcheck, cat_role: rolecheck}, function(data) {
    alert(data);
    window.location.hash = '';
    window.location.hash = hash;
  });
   $('#update-cat-dialog-modal').modal('hide');
 });

 $('#content').on('click','#btn-update-cat', function(e) {
  var cat_id = $(this).attr('catid');
  var c1 = $('input:checkbox[name="rolecheckU"]');
  var c2 = $('input:checkbox[name="rwcheckU"]');
  $.post('ajax/docs.php', {cat_id: cat_id, command: 'modal'}, function(data) {
    data = JSON.parse(data);
    $('#cat-name-text-input').val(data.title);
    if(data.cat_role == 'student') c1.prop('checked', true)
      else c1.prop('checked', false);
    if(data.cat_rwgrant == 'rw') c2.prop('checked', true)
      else c2.prop('checked', false);
    $('input[name=hdcatid]').remove();
    $('input[name=hdcatname]').remove();
    $('#update-cat-dialog-modal').append('<input type="hidden" name="hdcatid" value ="' + cat_id + '"/>');
    $('#update-cat-dialog-modal').append('<input type="hidden" name="hdcatname" value ="' + data.title + '"/>');
  });

  $('#update-cat-dialog-modal').modal('show');
});

 $('#content').on('click','#btn-create-catalog', function(e) {
   var hash = window.location.hash;
   var new_name = $('#catalog-name-text-input-create').val();
   var rolecheck = 'teacher';
   var rwcheck = 'r';
   if($('input:checkbox[name="rolecheck"]').prop('checked')) rolecheck = 'student';
   if($('input:checkbox[name="rwcheck"]').prop('checked')) rwcheck = 'rw'
     console.log(new_name, rolecheck, rwcheck);
   $.post('ajax/docs.php', {command: 'create', cat_name : new_name, cat_role: rolecheck, cat_rw: rwcheck}, function(data) {
    alert(data);
    window.location.hash = '';
    window.location.hash = hash;
  });
   $('#create-catalog-modal').modal('hide');
 });

 $('#content').on('change','input:checkbox[name="rolecheckU"]', function() {
  if($(this).prop('checked')) $('input:checkbox[name="rwcheckU"]').attr('disabled', false); 
  else $('input:checkbox[name="rwcheckU"]').attr('disabled', true); 
});

 $('#content').on('change','input:checkbox[name="rolecheck"]', function() {
  if($(this).prop('checked')) $('input:checkbox[name="rwcheck"]').attr('disabled', false); 
  else $('input:checkbox[name="rwcheck"]').attr('disabled', true); 
});
 
/*
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
 /*
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
  $.post('ajax/messages.php', {arr: JSON.stringify(array), did: did, command: "add-part"}, function(data) {
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
    $.post('ajax/messages.php', {did: did, command: "delete"}, function(data) {
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
  $.post('ajax/messages.php', {arr: JSON.stringify(array), did: did, command: "update", dname: txt}, function(data) {
          //console.log(data);
          alert(data);
          window.location.hash = '';
          window.location.hash = '#messages.php';
        });

  $('#settings-dialog-modal').modal('hide');
});
  /*
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
  */
  /*
  $('#content').on('click','.chat_list', function()
  {
    var c_did = $(this).attr('did');
    $.get('ajax/messages.php', {did : c_did}, function(data) {
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
    $.post('ajax/messages.php', {arr: JSON.stringify(array), command: "create", dname: txt, msg : msg}, function(data) {
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
  $(document).on('focusin', function(e) {
    if ($(e.target).closest(".mce-window").length) {
      e.stopImmediatePropagation();
    }
  });

  $('#content').on('click', '#btn-add-news', function(e){
    $.get('/ajax/imgesList.php', function(data) {
      console.log(data);
    });
    $.get('/ajax/docsList.php', function(data) {
      console.log(data);
    });
    tinymce = initTinyMCE('#text-modal-create-news');
    tinymce.get('text-modal-create-news').setContent('');
  });

  $('#content').on('click', '#btn-create-news', function(e){
    var nname = $('#news-name-text-input-create').val();
    var txt = tinyMCE.get('text-modal-create-news').getContent();
    console.log(txt);
    tinyMCE.remove();
    $.post('ajax/news.php', {command: 'create', txt: txt, nname: nname}, function(data) {
          //console.log(data);
          alert(data);
          window.location.hash = '';
          window.location.hash = '#news.php';
        });
    $('#create-news-modal').modal('hide');
  });

  $('#content').on('click', '#btn-update-news', function(e){
    var nname = $('#news-name-text-input-update').val();
    var txt = tinyMCE.get('text-modal-update-news').getContent();
    var nid = $('#btn-update-news').attr('nid');
    console.log(txt,nid,nname);
    tinyMCE.remove();
    $.post('ajax/news.php', {nid: nid, command: 'update', txt: txt, nname: nname} , function(data) {
          //console.log(data);
          alert(data);
          window.location.hash = '';
          window.location.hash = '#news.php';
        });
    $('#update-news-modal').modal('hide');
  });

  $('#content').on('click', '#dd-news-delete', function(e){
    e.preventDefault()
    var nid = $(this).parent().parent().parent().attr('nid');
    tinyMCE.remove();
    $.post('ajax/news.php', {nid: nid, command: 'delete'} , function(data) {
          //console.log(data);
          alert(data);
          window.location.hash = '';
          window.location.hash = '#news.php';
        });
  });

  $('#content').on('click', '#dd-news-update', function(e){
    e.preventDefault();
    var nid = $(this).parent().parent().parent().attr('nid'); 
    $.post('ajax/news.php', {nid: nid, command: 'modal'} , function(data) {
      data = JSON.parse(data);
      tinymce = initTinyMCE('#text-modal-update-news');
      tinymce.get('text-modal-update-news').setContent(data.text);
      $('#news-name-text-input-update').val(data.name);
      $('#btn-update-news').attr('nid', data.id);
    });
    $('#update-news-modal').modal('show');
  });
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
  /*
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
          if(newHash.indexOf('album.php') >= 0 || newHash.indexOf('blog.php') >= 0 || newHash.indexOf('main.php') >= 0){
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