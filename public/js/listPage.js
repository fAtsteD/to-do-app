// JS for main page

$(() => {
  // Create click event for every button of remove task
  $('button.remove-task').each(function () {
    $(this).on('click', function () {
      $.post({
        url: '/task/delete/' + $(this).data('taskId'),
        dataType: 'json',
        success: (data, textStatus) => {
          if (data.code == 0) {
            $(this).parent().parent().remove()
          } else {
            console.log('Error ' + data.code + ': ' + data.message);
            alert('Error through delete task.');
          }
        }
      });
    });
  });

  // Create click event for every checkbox of task. Action processes through JSON requests
  $('input.check-task').each(function () {
    $(this).on('click', function () {
      var sendData = {
        id: $(this).data('taskId'),
        isDone: $('input.check-task').is(':checked')
      };
      $.post({
        url: '/task/check/' + $(this).data('taskId'),
        data: JSON.stringify(sendData),
        dataType: 'json',
        success: (data, textStatus) => {
          if (data.code == 0) {
            if (sendData.isDone) {
              $('ul#done-tasks').append($(this).parent().parent().parent().parent());
            } else {
              $('ul#undone-tasks').append($(this).parent().parent().parent().parent());
            }
          } else {
            console.log('Error ' + data.code + ': ' + data.message);
            alert('Error through check task.');
          }
        }
      });
    });
  });

  // Create click event for every button of remove list
  $('button.remove-list').each(function () {
    $(this).on('click', function () {
      // TODO: Confirm delete
      fetch('/list/delete/' + $(this).data('listId'), {
        'method': 'DELETE'
      }).then(function (response) {
        if (response.status == 200) {
          return response.json();
        } else {
          alert('Error through delete list.');
          return;
        }
      }).then(function (response) {
        if (response.code == 0) {
          // FIXME: Do not work delete
          $(this).parent().remove();
          console.log(response.message);
        } else {
          console.log('Error ' + response.code + ': ' + response.message);
          alert('Error through delete list.');
        }
      });
    });
  });
});

